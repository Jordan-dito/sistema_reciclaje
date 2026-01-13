<?php
/**
 * API para gestión de inventarios
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
                    SELECT i.*, 
                           s.nombre as sucursal_nombre,
                           p.nombre as producto_nombre,
                           m.nombre as material_nombre,
                           c.nombre as categoria_nombre,
                           u.nombre as unidad_nombre,
                           u.simbolo as unidad_simbolo,
                           (SELECT precio_unitario FROM precios WHERE producto_id = p.id AND tipo_precio = 'venta' AND estado = 'activo' LIMIT 1) as precio_venta
                    FROM inventarios i 
                    INNER JOIN sucursales s ON i.sucursal_id = s.id 
                    INNER JOIN productos p ON i.producto_id = p.id
                    INNER JOIN materiales m ON p.material_id = m.id
                    LEFT JOIN categorias c ON m.categoria_id = c.id
                    INNER JOIN unidades u ON p.unidad_id = u.id
                    WHERE i.estado <> 'inactivo'
                ";
                $params = [];
                
                if ($sucursal_id) {
                    $sql .= " AND i.sucursal_id = ?";
                    $params[] = $sucursal_id;
                }
                
                $sql .= " ORDER BY i.id ASC";
                
                $stmt = $db->prepare($sql);
                $stmt->execute($params);
                $inventarios = $stmt->fetchAll();
                
                ob_end_clean();
                echo json_encode(['success' => true, 'data' => $inventarios], JSON_UNESCAPED_UNICODE);
            } elseif ($action === 'productos') {
                // Obtener productos activos con categoría
                $stmt = $db->query("
                    SELECT p.id, 
                           p.nombre, 
                           m.nombre as material_nombre,
                           c.nombre as categoria_nombre,
                           u.simbolo as unidad
                    FROM productos p 
                    INNER JOIN materiales m ON p.material_id = m.id
                    LEFT JOIN categorias c ON m.categoria_id = c.id
                    INNER JOIN unidades u ON p.unidad_id = u.id
                    WHERE p.estado = 'activo'
                    ORDER BY p.nombre ASC
                ");
                $productos = $stmt->fetchAll();
                
                ob_end_clean();
                echo json_encode(['success' => true, 'data' => $productos], JSON_UNESCAPED_UNICODE);
            } elseif ($action === 'sucursales') {
                // Obtener sucursales activas
                $stmt = $db->query("
                    SELECT id, nombre FROM sucursales WHERE estado = 'activo' ORDER BY nombre ASC
                ");
                $sucursales = $stmt->fetchAll();
                
                ob_end_clean();
                echo json_encode(['success' => true, 'data' => $sucursales], JSON_UNESCAPED_UNICODE);
            } elseif ($action === 'obtener') {
                $id = $_GET['id'] ?? 0;
                $stmt = $db->prepare("
                    SELECT i.*, 
                           s.nombre as sucursal_nombre,
                           p.nombre as producto_nombre,
                           m.nombre as material_nombre,
                           c.nombre as categoria_nombre,
                           u.nombre as unidad_nombre,
                           u.simbolo as unidad_simbolo
                    FROM inventarios i 
                    INNER JOIN sucursales s ON i.sucursal_id = s.id 
                    INNER JOIN productos p ON i.producto_id = p.id
                    INNER JOIN materiales m ON p.material_id = m.id
                    LEFT JOIN categorias c ON m.categoria_id = c.id
                    INNER JOIN unidades u ON p.unidad_id = u.id
                    WHERE i.id = ?
                ");
                $stmt->execute([$id]);
                $inventario = $stmt->fetch();
                
                ob_end_clean();
                if ($inventario) {
                    echo json_encode(['success' => true, 'data' => $inventario], JSON_UNESCAPED_UNICODE);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Inventario no encontrado']);
                }
            }
            break;
            
        case 'POST':
            if ($action === 'crear') {
                $sucursal_id = intval($_POST['sucursal_id'] ?? 0);
                $producto_id = intval($_POST['producto_id'] ?? 0);
                // Permitir NULL en cantidad (si viene vacío o como string 'null', tratarlo como NULL)
                $cantidadInput = $_POST['cantidad'] ?? null;
                $cantidad = null;
                if ($cantidadInput !== null && $cantidadInput !== '' && $cantidadInput !== 'null' && $cantidadInput !== 'NULL') {
                    $cantidad = floatval($cantidadInput);
                }
                $stock_minimo = floatval($_POST['stock_minimo'] ?? 0);
                $stock_maximo = floatval($_POST['stock_maximo'] ?? 0);
                $estado = $_POST['estado'] ?? 'disponible';
                
                if ($sucursal_id <= 0 || $producto_id <= 0) {
                    throw new Exception('Sucursal y producto son obligatorios');
                }
                
                // Verificar que la sucursal existe
                $stmt = $db->prepare("SELECT id FROM sucursales WHERE id = ?");
                $stmt->execute([$sucursal_id]);
                if (!$stmt->fetch()) {
                    throw new Exception('Sucursal inválida');
                }
                
                // Verificar que el producto existe
                $stmt = $db->prepare("SELECT id FROM productos WHERE id = ? AND estado = 'activo'");
                $stmt->execute([$producto_id]);
                if (!$stmt->fetch()) {
                    throw new Exception('Producto inválido');
                }
                
                // Verificar si ya existe inventario para este producto en esta sucursal
                // Buscar sin importar el estado (porque hay UNIQUE KEY en producto_id, sucursal_id)
                $stmt = $db->prepare("SELECT id, cantidad, estado FROM inventarios WHERE producto_id = ? AND sucursal_id = ?");
                $stmt->execute([$producto_id, $sucursal_id]);
                $inventarioExistente = $stmt->fetch();
                
                if ($inventarioExistente) {
                    // Actualizar inventario existente
                    // Si cantidad es NULL, mantener la cantidad existente
                    if ($cantidad !== null) {
                        $cantidadExistente = floatval($inventarioExistente['cantidad'] ?? 0);
                        $nuevaCantidad = $cantidadExistente + $cantidad;
                    } else {
                        $nuevaCantidad = floatval($inventarioExistente['cantidad'] ?? 0);
                    }
                    
                    // Si estaba inactivo, reactivarlo
                    $nuevoEstado = $inventarioExistente['estado'] === 'inactivo' ? 'disponible' : $estado;
                    
                    $stmt = $db->prepare("
                        UPDATE inventarios 
                        SET cantidad = ?, stock_minimo = ?, stock_maximo = ?, estado = ?, fecha_actualizacion = NOW()
                        WHERE id = ?
                    ");
                    $stmt->execute([
                        $nuevaCantidad,
                        $stock_minimo,
                        $stock_maximo,
                        $nuevoEstado,
                        $inventarioExistente['id']
                    ]);
                    $inventario_id = $inventarioExistente['id'];
                } else {
                    // Crear nuevo inventario
                    // Asegurar que cantidad no sea NULL (la tabla requiere NOT NULL)
                    $cantidadFinal = $cantidad !== null ? floatval($cantidad) : 0.00;
                    
                    try {
                        $stmt = $db->prepare("
                            INSERT INTO inventarios 
                            (sucursal_id, producto_id, cantidad, stock_minimo, stock_maximo, estado) 
                            VALUES (?, ?, ?, ?, ?, ?)
                        ");
                        $stmt->execute([
                            $sucursal_id,
                            $producto_id,
                            $cantidadFinal,
                            $stock_minimo,
                            $stock_maximo,
                            $estado
                        ]);
                        $inventario_id = $db->lastInsertId();
                    } catch (PDOException $e) {
                        // Si es error de clave duplicada, intentar actualizar
                        if ($e->getCode() == 23000 || strpos($e->getMessage(), 'Duplicate entry') !== false) {
                            // Reintentar búsqueda y actualización
                            $stmt = $db->prepare("SELECT id, cantidad FROM inventarios WHERE producto_id = ? AND sucursal_id = ?");
                            $stmt->execute([$producto_id, $sucursal_id]);
                            $inventarioExistente = $stmt->fetch();
                            
                            if ($inventarioExistente) {
                                $nuevaCantidad = floatval($inventarioExistente['cantidad'] ?? 0);
                                $stmt = $db->prepare("
                                    UPDATE inventarios 
                                    SET cantidad = ?, stock_minimo = ?, stock_maximo = ?, estado = ?, fecha_actualizacion = NOW()
                                    WHERE id = ?
                                ");
                                $stmt->execute([
                                    $nuevaCantidad,
                                    $stock_minimo,
                                    $stock_maximo,
                                    $estado,
                                    $inventarioExistente['id']
                                ]);
                                $inventario_id = $inventarioExistente['id'];
                            } else {
                                throw new Exception('Error al crear inventario: ' . $e->getMessage());
                            }
                        } else {
                            throw $e;
                        }
                    }
                }
                
                ob_end_clean();
                echo json_encode([
                    'success' => true,
                    'message' => 'Inventario creado/actualizado exitosamente',
                    'id' => $inventario_id
                ]);
            } elseif ($action === 'actualizar') {
                $id = intval($_POST['id'] ?? 0);
                $sucursal_id = intval($_POST['sucursal_id'] ?? 0);
                $producto_id = intval($_POST['producto_id'] ?? 0);
                // Permitir NULL en cantidad
                $cantidad = isset($_POST['cantidad']) && $_POST['cantidad'] !== '' && $_POST['cantidad'] !== null 
                    ? floatval($_POST['cantidad']) 
                    : null;
                $stock_minimo = floatval($_POST['stock_minimo'] ?? 0);
                $stock_maximo = floatval($_POST['stock_maximo'] ?? 0);
                $estado = $_POST['estado'] ?? 'disponible';
                
                if ($sucursal_id <= 0 || $producto_id <= 0) {
                    throw new Exception('Sucursal y producto son obligatorios');
                }
                
                $stmt = $db->prepare("
                    UPDATE inventarios 
                    SET sucursal_id = ?, producto_id = ?, cantidad = ?, stock_minimo = ?, stock_maximo = ?, estado = ?
                    WHERE id = ?
                ");
                
                $stmt->execute([
                    $sucursal_id,
                    $producto_id,
                    $cantidad,
                    $stock_minimo,
                    $stock_maximo,
                    $estado,
                    $id
                ]);
                
                ob_end_clean();
                echo json_encode(['success' => true, 'message' => 'Inventario actualizado exitosamente']);
            } elseif ($action === 'eliminar') {
                $id = intval($_POST['id'] ?? 0);
                
                $stmt = $db->prepare("SELECT estado FROM inventarios WHERE id = ?");
                $stmt->execute([$id]);
                $inventario = $stmt->fetch();
                
                if (!$inventario) {
                    throw new Exception('Inventario no encontrado');
                }
                
                if ($inventario['estado'] === 'inactivo') {
                    throw new Exception('El inventario ya está inactivo');
                }
                
                $stmt = $db->prepare("UPDATE inventarios SET estado = 'inactivo', fecha_actualizacion = NOW() WHERE id = ?");
                $stmt->execute([$id]);
                
                ob_end_clean();
                echo json_encode(['success' => true, 'message' => 'Inventario desactivado exitosamente']);
            }
            break;
            
        default:
            ob_end_clean();
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    }
    
} catch (PDOException $e) {
    ob_end_clean();
    $errorInfo = ErrorHandler::handleDatabaseError($e, 'inventarios/api.php');
    ErrorHandler::logError($e->getMessage(), ['exception' => $e->getMessage(), 'code' => $e->getCode()]);
    $debug = defined('APP_DEBUG') && APP_DEBUG;
    echo ErrorHandler::jsonResponse($errorInfo, 500, $debug);
} catch (Exception $e) {
    ob_end_clean();
    $errorInfo = ErrorHandler::handleException($e, 'inventarios/api.php');
    ErrorHandler::logError($e->getMessage(), ['exception' => $e->getMessage()]);
    $debug = defined('APP_DEBUG') && APP_DEBUG;
    echo ErrorHandler::jsonResponse($errorInfo, 400, $debug);
}
?>

