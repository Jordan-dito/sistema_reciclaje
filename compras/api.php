<?php
/**
 * API para gestión de compras
 * Sistema de Gestión de Reciclaje
 */

error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');

header('Content-Type: application/json; charset=utf-8');
ob_start();

try {
    require_once __DIR__ . '/../config/auth.php';

    $auth = new Auth();
    if (!$auth->isAuthenticated()) {
        ob_end_clean();
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'No autorizado']);
        exit;
    }

    $method = $_SERVER['REQUEST_METHOD'];
    $action = $_GET['action'] ?? $_POST['action'] ?? '';
    
    if (empty($action)) {
        throw new Exception('Acción no especificada');
    }

    $db = getDB();
    $usuario_id = $_SESSION['usuario_id'] ?? null;
    
    switch ($method) {
        case 'GET':
            if ($action === 'listar') {
                $sucursal_id = $_GET['sucursal_id'] ?? null;
                
                $sql = "
                    SELECT c.*, p.nombre as proveedor_nombre, s.nombre as sucursal_nombre 
                    FROM compras c 
                    INNER JOIN proveedores p ON c.proveedor_id = p.id 
                    INNER JOIN sucursales s ON c.sucursal_id = s.id 
                    WHERE c.estado <> 'cancelada'
                ";
                $params = [];
                
                if ($sucursal_id) {
                    $sql .= " AND c.sucursal_id = ?";
                    $params[] = $sucursal_id;
                }
                
                $sql .= " ORDER BY c.fecha_compra DESC, c.id DESC";
                
                $stmt = $db->prepare($sql);
                $stmt->execute($params);
                $compras = $stmt->fetchAll();
                
                ob_end_clean();
                echo json_encode(['success' => true, 'data' => $compras]);
            } elseif ($action === 'productos') {
                // Obtener productos activos con precios de compra
                $stmt = $db->query("
                    SELECT p.id, 
                           p.nombre, 
                           m.nombre as material_nombre,
                           c.nombre as categoria_nombre,
                           u.simbolo as unidad,
                           pr.id as precio_id,
                           pr.precio_unitario
                    FROM productos p 
                    INNER JOIN materiales m ON p.material_id = m.id
                    LEFT JOIN categorias c ON m.categoria_id = c.id
                    INNER JOIN unidades u ON p.unidad_id = u.id
                    LEFT JOIN precios pr ON p.id = pr.producto_id AND pr.tipo_precio = 'compra' AND pr.estado = 'activo'
                    WHERE p.estado = 'activo'
                    ORDER BY p.nombre ASC
                ");
                $productos = $stmt->fetchAll();
                
                ob_end_clean();
                echo json_encode(['success' => true, 'data' => $productos], JSON_UNESCAPED_UNICODE);
            } elseif ($action === 'obtener') {
                $id = $_GET['id'] ?? 0;
                
                // Obtener compra
                $stmt = $db->prepare("
                    SELECT c.*, p.nombre as proveedor_nombre, s.nombre as sucursal_nombre 
                    FROM compras c 
                    INNER JOIN proveedores p ON c.proveedor_id = p.id 
                    INNER JOIN sucursales s ON c.sucursal_id = s.id 
                    WHERE c.id = ?
                ");
                $stmt->execute([$id]);
                $compra = $stmt->fetch();
                
                if ($compra) {
                    // Obtener detalles con información de productos
                    $stmt = $db->prepare("
                        SELECT cd.*, 
                               p.nombre as producto_nombre,
                               m.nombre as material_nombre,
                               c.nombre as categoria_nombre,
                               u.nombre as unidad_nombre,
                               u.simbolo as unidad_simbolo,
                               pr.precio_unitario
                        FROM compras_detalle cd
                        INNER JOIN productos p ON cd.producto_id = p.id
                        INNER JOIN materiales m ON p.material_id = m.id
                        LEFT JOIN categorias c ON m.categoria_id = c.id
                        INNER JOIN unidades u ON p.unidad_id = u.id
                        LEFT JOIN precios pr ON cd.precio_id = pr.id
                        WHERE cd.compra_id = ?
                    ");
                    $stmt->execute([$id]);
                    $compra['detalles'] = $stmt->fetchAll();
                }
                
                ob_end_clean();
                if ($compra) {
                    echo json_encode(['success' => true, 'data' => $compra]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Compra no encontrada']);
                }
            }
            break;
            
        case 'POST':
            if ($action === 'crear') {
                $db->beginTransaction();
                
                try {
                    $proveedor_id = intval($_POST['proveedor_id'] ?? 0);
                    $sucursal_id = intval($_POST['sucursal_id'] ?? 0);
                    $fecha_compra = $_POST['fecha_compra'] ?? date('Y-m-d');
                    $numero_factura = trim($_POST['numero_factura'] ?? '');
                    $tipo_comprobante = $_POST['tipo_comprobante'] ?? 'factura';
                    $subtotal = floatval($_POST['subtotal'] ?? 0);
                    $iva = floatval($_POST['iva'] ?? 0);
                    $descuento = floatval($_POST['descuento'] ?? 0);
                    $total = floatval($_POST['total'] ?? 0);
                    $estado = $_POST['estado'] ?? 'pendiente';
                    $notas = trim($_POST['notas'] ?? '');
                    
                    if ($proveedor_id <= 0 || $sucursal_id <= 0) {
                        throw new Exception('Proveedor y sucursal son obligatorios');
                    }
                    
                    // Insertar compra
                    $stmt = $db->prepare("
                        INSERT INTO compras 
                        (numero_factura, proveedor_id, sucursal_id, fecha_compra, tipo_comprobante, subtotal, iva, descuento, total, estado, notas, creado_por) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                    ");
                    
                    $stmt->execute([
                        $numero_factura ?: null,
                        $proveedor_id,
                        $sucursal_id,
                        $fecha_compra,
                        $tipo_comprobante,
                        $subtotal,
                        $iva,
                        $descuento,
                        $total,
                        $estado,
                        $notas ?: null,
                        $usuario_id
                    ]);
                    
                    $compra_id = $db->lastInsertId();
                    
                    // Insertar detalles
                    $detalles = [];
                    if (isset($_POST['detalles'])) {
                        if (is_string($_POST['detalles'])) {
                            $detalles = json_decode($_POST['detalles'], true);
                            if (!is_array($detalles)) {
                                $detalles = [];
                            }
                        } elseif (is_array($_POST['detalles'])) {
                            $detalles = $_POST['detalles'];
                        }
                    }
                    
                    if (!empty($detalles)) {
                        foreach ($detalles as $detalle) {
                            $producto_id = intval($detalle['producto_id'] ?? 0);
                            $precio_id = !empty($detalle['precio_id']) ? intval($detalle['precio_id']) : null;
                            $cantidad = floatval($detalle['cantidad'] ?? 0);
                            $subtotal_detalle = floatval($detalle['subtotal'] ?? 0);
                            
                            if ($producto_id <= 0) {
                                throw new Exception('Producto es obligatorio en los detalles');
                            }
                            
                            // Obtener precio de compra si no se proporciona precio_id
                            if (!$precio_id) {
                                $stmt = $db->prepare("
                                    SELECT id, precio_unitario FROM precios 
                                    WHERE producto_id = ? AND tipo_precio = 'compra' AND estado = 'activo' 
                                    LIMIT 1
                                ");
                                $stmt->execute([$producto_id]);
                                $precio = $stmt->fetch();
                                if ($precio) {
                                    $precio_id = $precio['id'];
                                }
                            }
                            
                            $stmt = $db->prepare("
                                INSERT INTO compras_detalle 
                                (compra_id, producto_id, precio_id, cantidad, subtotal) 
                                VALUES (?, ?, ?, ?, ?)
                            ");
                            
                            $stmt->execute([
                                $compra_id,
                                $producto_id,
                                $precio_id,
                                $cantidad,
                                $subtotal_detalle
                            ]);
                            
                            // Si la compra está completada, actualizar inventario (el trigger lo hará automáticamente)
                            // Pero por si acaso, también lo hacemos aquí
                            if ($estado === 'completada') {
                                // Buscar o crear inventario para este producto en esta sucursal
                                $stmt = $db->prepare("
                                    SELECT id, cantidad FROM inventarios 
                                    WHERE producto_id = ? AND sucursal_id = ? AND estado <> 'inactivo'
                                ");
                                $stmt->execute([$producto_id, $sucursal_id]);
                                $inventario = $stmt->fetch();
                                
                                if ($inventario) {
                                    $stmt = $db->prepare("UPDATE inventarios SET cantidad = cantidad + ? WHERE id = ?");
                                    $stmt->execute([$cantidad, $inventario['id']]);
                                } else {
                                    $stmt = $db->prepare("
                                        INSERT INTO inventarios (sucursal_id, producto_id, cantidad, estado) 
                                        VALUES (?, ?, ?, 'disponible')
                                    ");
                                    $stmt->execute([$sucursal_id, $producto_id, $cantidad]);
                                }
                            }
                        }
                    }
                    
                    $db->commit();
                    
                    ob_end_clean();
                    echo json_encode([
                        'success' => true,
                        'message' => 'Compra creada exitosamente',
                        'id' => $compra_id
                    ]);
                } catch (Exception $e) {
                    $db->rollBack();
                    throw $e;
                }
            } elseif ($action === 'actualizar') {
                $id = intval($_POST['id'] ?? 0);
                $proveedor_id = intval($_POST['proveedor_id'] ?? 0);
                $sucursal_id = intval($_POST['sucursal_id'] ?? 0);
                $fecha_compra = $_POST['fecha_compra'] ?? date('Y-m-d');
                $numero_factura = trim($_POST['numero_factura'] ?? '');
                $tipo_comprobante = $_POST['tipo_comprobante'] ?? 'factura';
                $subtotal = floatval($_POST['subtotal'] ?? 0);
                $iva = floatval($_POST['iva'] ?? 0);
                $descuento = floatval($_POST['descuento'] ?? 0);
                $total = floatval($_POST['total'] ?? 0);
                $estado = $_POST['estado'] ?? 'pendiente';
                $notas = trim($_POST['notas'] ?? '');
                
                $stmt = $db->prepare("
                    UPDATE compras 
                    SET numero_factura = ?, proveedor_id = ?, sucursal_id = ?, fecha_compra = ?, tipo_comprobante = ?, 
                        subtotal = ?, iva = ?, descuento = ?, total = ?, estado = ?, notas = ?
                    WHERE id = ?
                ");
                
                $stmt->execute([
                    $numero_factura ?: null,
                    $proveedor_id,
                    $sucursal_id,
                    $fecha_compra,
                    $tipo_comprobante,
                    $subtotal,
                    $iva,
                    $descuento,
                    $total,
                    $estado,
                    $notas ?: null,
                    $id
                ]);
                
                ob_end_clean();
                echo json_encode(['success' => true, 'message' => 'Compra actualizada exitosamente']);
            } elseif ($action === 'eliminar') {
                $id = intval($_POST['id'] ?? 0);
                
                if ($id <= 0) {
                    throw new Exception('ID de compra inválido');
                }
                
                $db->beginTransaction();
                try {
                    $stmt = $db->prepare("SELECT estado FROM compras WHERE id = ?");
                    $stmt->execute([$id]);
                    $compra = $stmt->fetch();
                    
                    if (!$compra) {
                        throw new Exception('Compra no encontrada');
                    }
                    
                    if ($compra['estado'] === 'cancelada') {
                        throw new Exception('La compra ya está cancelada');
                    }
                    
                    if ($compra['estado'] === 'completada') {
                        $stmt = $db->prepare("SELECT inventario_id, cantidad FROM compras_detalle WHERE compra_id = ?");
                        $stmt->execute([$id]);
                        $detalles = $stmt->fetchAll();
                        
                        foreach ($detalles as $detalle) {
                            if (!empty($detalle['inventario_id'])) {
                                $stmt = $db->prepare("UPDATE inventarios SET cantidad = GREATEST(cantidad - ?, 0) WHERE id = ?");
                                $stmt->execute([$detalle['cantidad'], $detalle['inventario_id']]);
                            }
                        }
                    }
                    
                    $stmt = $db->prepare("UPDATE compras SET estado = 'cancelada', fecha_actualizacion = NOW() WHERE id = ?");
                    $stmt->execute([$id]);
                    
                    $db->commit();
                    
                    ob_end_clean();
                    echo json_encode(['success' => true, 'message' => 'Compra cancelada exitosamente']);
                } catch (Exception $e) {
                    $db->rollBack();
                    throw $e;
                }
            }
            break;
            
        default:
            ob_end_clean();
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    }
    
} catch (PDOException $e) {
    ob_end_clean();
    $errorInfo = ErrorHandler::handleDatabaseError($e, 'compras/api.php');
    ErrorHandler::logError($e->getMessage(), ['exception' => $e->getMessage(), 'code' => $e->getCode()]);
    $debug = defined('APP_DEBUG') && APP_DEBUG;
    echo ErrorHandler::jsonResponse($errorInfo, 500, $debug);
} catch (Exception $e) {
    ob_end_clean();
    $errorInfo = ErrorHandler::handleException($e, 'compras/api.php');
    ErrorHandler::logError($e->getMessage(), ['exception' => $e->getMessage()]);
    $debug = defined('APP_DEBUG') && APP_DEBUG;
    echo ErrorHandler::jsonResponse($errorInfo, 400, $debug);
}
?>

