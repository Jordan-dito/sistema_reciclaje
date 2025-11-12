<?php
/**
 * API para gestión de ventas
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
                    SELECT v.*, c.nombre as cliente_nombre, s.nombre as sucursal_nombre 
                    FROM ventas v 
                    INNER JOIN clientes c ON v.cliente_id = c.id 
                    INNER JOIN sucursales s ON v.sucursal_id = s.id 
                    WHERE v.estado <> 'cancelada'
                ";
                $params = [];
                
                if ($sucursal_id) {
                    $sql .= " AND v.sucursal_id = ?";
                    $params[] = $sucursal_id;
                }
                
                $sql .= " ORDER BY v.fecha_venta DESC, v.id DESC";
                
                $stmt = $db->prepare($sql);
                $stmt->execute($params);
                $ventas = $stmt->fetchAll();
                
                ob_end_clean();
                echo json_encode(['success' => true, 'data' => $ventas]);
            } elseif ($action === 'obtener') {
                $id = $_GET['id'] ?? 0;
                
                // Obtener venta
                $stmt = $db->prepare("
                    SELECT v.*, c.nombre as cliente_nombre, s.nombre as sucursal_nombre 
                    FROM ventas v 
                    INNER JOIN clientes c ON v.cliente_id = c.id 
                    INNER JOIN sucursales s ON v.sucursal_id = s.id 
                    WHERE v.id = ?
                ");
                $stmt->execute([$id]);
                $venta = $stmt->fetch();
                
                if ($venta) {
                    // Obtener detalles
                    $stmt = $db->prepare("SELECT * FROM ventas_detalle WHERE venta_id = ?");
                    $stmt->execute([$id]);
                    $venta['detalles'] = $stmt->fetchAll();
                }
                
                ob_end_clean();
                if ($venta) {
                    echo json_encode(['success' => true, 'data' => $venta]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Venta no encontrada']);
                }
            }
            break;
            
        case 'POST':
            if ($action === 'crear') {
                $db->beginTransaction();
                
                try {
                    $cliente_id = intval($_POST['cliente_id'] ?? 0);
                    $sucursal_id = intval($_POST['sucursal_id'] ?? 0);
                    $fecha_venta = $_POST['fecha_venta'] ?? date('Y-m-d');
                    $numero_factura = trim($_POST['numero_factura'] ?? '');
                    $tipo_comprobante = $_POST['tipo_comprobante'] ?? 'factura';
                    $subtotal = floatval($_POST['subtotal'] ?? 0);
                    $iva = floatval($_POST['iva'] ?? 0);
                    $descuento = floatval($_POST['descuento'] ?? 0);
                    $total = floatval($_POST['total'] ?? 0);
                    $metodo_pago = $_POST['metodo_pago'] ?? 'efectivo';
                    $estado = $_POST['estado'] ?? 'pendiente';
                    $notas = trim($_POST['notas'] ?? '');
                    
                    if ($cliente_id <= 0 || $sucursal_id <= 0) {
                        throw new Exception('Cliente y sucursal son obligatorios');
                    }
                    
                    // Insertar venta
                    $stmt = $db->prepare("
                        INSERT INTO ventas 
                        (numero_factura, cliente_id, sucursal_id, fecha_venta, tipo_comprobante, subtotal, iva, descuento, total, metodo_pago, estado, notas, creado_por) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                    ");
                    
                    $stmt->execute([
                        $numero_factura ?: null,
                        $cliente_id,
                        $sucursal_id,
                        $fecha_venta,
                        $tipo_comprobante,
                        $subtotal,
                        $iva,
                        $descuento,
                        $total,
                        $metodo_pago,
                        $estado,
                        $notas ?: null,
                        $usuario_id
                    ]);
                    
                    $venta_id = $db->lastInsertId();
                    
                    // Insertar detalles y actualizar inventario
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
                            $inventario_id = intval($detalle['inventario_id'] ?? 0);
                            $nombre_producto = trim($detalle['nombre_producto'] ?? '');
                            $categoria = $detalle['categoria'] ?? '';
                            $cantidad = floatval($detalle['cantidad'] ?? 0);
                            $unidad = $detalle['unidad'] ?? 'kg';
                            $precio_unitario = floatval($detalle['precio_unitario'] ?? 0);
                            $subtotal_detalle = floatval($detalle['subtotal'] ?? 0);
                            
                            if ($inventario_id <= 0) {
                                throw new Exception('El inventario es obligatorio para cada detalle');
                            }
                            
                            // Verificar stock disponible
                            $stmt = $db->prepare("SELECT cantidad FROM inventarios WHERE id = ?");
                            $stmt->execute([$inventario_id]);
                            $inventario = $stmt->fetch();
                            
                            if (!$inventario) {
                                throw new Exception('Inventario no encontrado');
                            }
                            
                            if ($inventario['cantidad'] < $cantidad && $estado === 'completada') {
                                throw new Exception('Stock insuficiente para el producto: ' . $nombre_producto);
                            }
                            
                            $stmt = $db->prepare("
                                INSERT INTO ventas_detalle 
                                (venta_id, inventario_id, nombre_producto, categoria, cantidad, unidad, precio_unitario, subtotal) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                            ");
                            
                            $stmt->execute([
                                $venta_id,
                                $inventario_id,
                                $nombre_producto,
                                $categoria,
                                $cantidad,
                                $unidad,
                                $precio_unitario,
                                $subtotal_detalle
                            ]);
                            
                            // Actualizar inventario si la venta está completada
                            if ($estado === 'completada') {
                                $stmt = $db->prepare("UPDATE inventarios SET cantidad = cantidad - ? WHERE id = ?");
                                $stmt->execute([$cantidad, $inventario_id]);
                            }
                        }
                    }
                    
                    $db->commit();
                    
                    ob_end_clean();
                    echo json_encode([
                        'success' => true,
                        'message' => 'Venta creada exitosamente',
                        'id' => $venta_id
                    ]);
                } catch (Exception $e) {
                    $db->rollBack();
                    throw $e;
                }
            } elseif ($action === 'actualizar') {
                $id = intval($_POST['id'] ?? 0);
                $cliente_id = intval($_POST['cliente_id'] ?? 0);
                $sucursal_id = intval($_POST['sucursal_id'] ?? 0);
                $fecha_venta = $_POST['fecha_venta'] ?? date('Y-m-d');
                $numero_factura = trim($_POST['numero_factura'] ?? '');
                $tipo_comprobante = $_POST['tipo_comprobante'] ?? 'factura';
                $subtotal = floatval($_POST['subtotal'] ?? 0);
                $iva = floatval($_POST['iva'] ?? 0);
                $descuento = floatval($_POST['descuento'] ?? 0);
                $total = floatval($_POST['total'] ?? 0);
                $metodo_pago = $_POST['metodo_pago'] ?? 'efectivo';
                $estado = $_POST['estado'] ?? 'pendiente';
                $notas = trim($_POST['notas'] ?? '');
                
                $stmt = $db->prepare("
                    UPDATE ventas 
                    SET numero_factura = ?, cliente_id = ?, sucursal_id = ?, fecha_venta = ?, tipo_comprobante = ?, 
                        subtotal = ?, iva = ?, descuento = ?, total = ?, metodo_pago = ?, estado = ?, notas = ?
                    WHERE id = ?
                ");
                
                $stmt->execute([
                    $numero_factura ?: null,
                    $cliente_id,
                    $sucursal_id,
                    $fecha_venta,
                    $tipo_comprobante,
                    $subtotal,
                    $iva,
                    $descuento,
                    $total,
                    $metodo_pago,
                    $estado,
                    $notas ?: null,
                    $id
                ]);
                
                ob_end_clean();
                echo json_encode(['success' => true, 'message' => 'Venta actualizada exitosamente']);
            } elseif ($action === 'eliminar') {
                $id = intval($_POST['id'] ?? 0);
                
                if ($id <= 0) {
                    throw new Exception('ID de venta inválido');
                }
                
                $db->beginTransaction();
                try {
                    $stmt = $db->prepare("SELECT estado FROM ventas WHERE id = ?");
                    $stmt->execute([$id]);
                    $venta = $stmt->fetch();
                    
                    if (!$venta) {
                        throw new Exception('Venta no encontrada');
                    }
                    
                    if ($venta['estado'] === 'cancelada') {
                        throw new Exception('La venta ya está cancelada');
                    }
                    
                    if (in_array($venta['estado'], ['pendiente', 'completada', 'devuelta'])) {
                        $stmt = $db->prepare("SELECT inventario_id, cantidad FROM ventas_detalle WHERE venta_id = ?");
                        $stmt->execute([$id]);
                        $detalles = $stmt->fetchAll();
                        
                        foreach ($detalles as $detalle) {
                            if (!empty($detalle['inventario_id'])) {
                                $stmt = $db->prepare("UPDATE inventarios SET cantidad = cantidad + ? WHERE id = ?");
                                $stmt->execute([$detalle['cantidad'], $detalle['inventario_id']]);
                            }
                        }
                    }
                    
                    $stmt = $db->prepare("UPDATE ventas SET estado = 'cancelada', fecha_actualizacion = NOW() WHERE id = ?");
                    $stmt->execute([$id]);
                    
                    $db->commit();
                    
                    ob_end_clean();
                    echo json_encode(['success' => true, 'message' => 'Venta cancelada exitosamente']);
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
    error_log("Error en ventas/api.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error de base de datos: ' . (APP_DEBUG ? $e->getMessage() : 'Error al procesar la solicitud')
    ]);
} catch (Exception $e) {
    ob_end_clean();
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>

