<?php
/**
 * API para gestión de productos
 * Sistema de Gestión de Reciclaje
 */

error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');

header('Content-Type: application/json; charset=utf-8');
ob_start();

try {
    require_once __DIR__ . '/../config/auth.php';
    require_once __DIR__ . '/../config/validaciones.php';

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
    
    switch ($method) {
        case 'GET':
            if ($action === 'listar') {
                $stmt = $db->query("
                    SELECT p.*, 
                           m.nombre as material_nombre,
                           c.nombre as categoria_nombre,
                           u.nombre as unidad_nombre,
                           u.simbolo as unidad_simbolo,
                           (SELECT precio_unitario FROM precios WHERE producto_id = p.id AND tipo_precio = 'venta' AND estado = 'activo' LIMIT 1) as precio_venta,
                           (SELECT precio_unitario FROM precios WHERE producto_id = p.id AND tipo_precio = 'compra' AND estado = 'activo' LIMIT 1) as precio_compra
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
            } elseif ($action === 'obtener') {
                $id = $_GET['id'] ?? 0;
                $stmt = $db->prepare("
                    SELECT p.*, 
                           m.nombre as material_nombre,
                           c.nombre as categoria_nombre,
                           u.nombre as unidad_nombre,
                           u.simbolo as unidad_simbolo
                    FROM productos p 
                    INNER JOIN materiales m ON p.material_id = m.id 
                    LEFT JOIN categorias c ON m.categoria_id = c.id
                    INNER JOIN unidades u ON p.unidad_id = u.id
                    WHERE p.id = ?
                ");
                $stmt->execute([$id]);
                $producto = $stmt->fetch();
                
                if ($producto) {
                    // Obtener precios
                    $stmtPrecios = $db->prepare("
                        SELECT id, precio_unitario, tipo_precio, estado 
                        FROM precios 
                        WHERE producto_id = ?
                    ");
                    $stmtPrecios->execute([$id]);
                    $producto['precios'] = $stmtPrecios->fetchAll();
                }
                
                ob_end_clean();
                if ($producto) {
                    echo json_encode(['success' => true, 'data' => $producto], JSON_UNESCAPED_UNICODE);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Producto no encontrado']);
                }
            } elseif ($action === 'materiales') {
                $stmt = $db->query("SELECT id, nombre FROM materiales WHERE estado = 'activo' ORDER BY nombre ASC");
                $materiales = $stmt->fetchAll();
                ob_end_clean();
                echo json_encode(['success' => true, 'data' => $materiales], JSON_UNESCAPED_UNICODE);
            } elseif ($action === 'unidades') {
                $stmt = $db->query("SELECT id, nombre, simbolo FROM unidades WHERE estado = 'activo' ORDER BY nombre ASC");
                $unidades = $stmt->fetchAll();
                ob_end_clean();
                echo json_encode(['success' => true, 'data' => $unidades], JSON_UNESCAPED_UNICODE);
            }
            break;
            
        case 'POST':
            if ($action === 'crear') {
                $db->beginTransaction();
                try {
                    $nombre = trim($_POST['nombre'] ?? '');
                    $material_id = $_POST['material_id'] ?? 0;
                    $unidad_id = $_POST['unidad_id'] ?? 0;
                    $descripcion = trim($_POST['descripcion'] ?? '');
                    $estado = $_POST['estado'] ?? 'activo';
                    $precio_venta = $_POST['precio_venta'] ?? 0;
                    $precio_compra = $_POST['precio_compra'] ?? 0;
                    
                    // Validar nombre: no solo espacios
                    $validacionNombre = validarNoSoloEspacios($nombre, 'Nombre');
                    if (!$validacionNombre['valid']) {
                        throw new Exception($validacionNombre['message']);
                    }
                    $nombre = limpiarEspacios($nombre);
                    $descripcion = limpiarEspacios($descripcion);
                    
                    // Validar precios: solo números (decimales permitidos)
                    if (!empty($precio_venta)) {
                        $validacionPrecioVenta = validarSoloNumeros($precio_venta, 'Precio de Venta', true);
                        if (!$validacionPrecioVenta['valid']) {
                            throw new Exception($validacionPrecioVenta['message']);
                        }
                    }
                    if (!empty($precio_compra)) {
                        $validacionPrecioCompra = validarSoloNumeros($precio_compra, 'Precio de Compra', true);
                        if (!$validacionPrecioCompra['valid']) {
                            throw new Exception($validacionPrecioCompra['message']);
                        }
                    }
                    
                    if (empty($material_id) || empty($unidad_id)) {
                        throw new Exception('Material y unidad son obligatorios');
                    }
                    
                    // Crear producto
                    $stmt = $db->prepare("
                        INSERT INTO productos (nombre, material_id, unidad_id, descripcion, estado) 
                        VALUES (?, ?, ?, ?, ?)
                    ");
                    $stmt->execute([$nombre, $material_id, $unidad_id, $descripcion ?: null, $estado]);
                    $producto_id = $db->lastInsertId();
                    
                    // Crear precios
                    if ($precio_venta > 0) {
                        $stmt = $db->prepare("
                            INSERT INTO precios (producto_id, precio_unitario, tipo_precio, estado) 
                            VALUES (?, ?, 'venta', 'activo')
                        ");
                        $stmt->execute([$producto_id, $precio_venta]);
                    }
                    
                    if ($precio_compra > 0) {
                        $stmt = $db->prepare("
                            INSERT INTO precios (producto_id, precio_unitario, tipo_precio, estado) 
                            VALUES (?, ?, 'compra', 'activo')
                        ");
                        $stmt->execute([$producto_id, $precio_compra]);
                    }
                    
                    $db->commit();
                    ob_end_clean();
                    echo json_encode([
                        'success' => true,
                        'message' => 'Producto creado exitosamente',
                        'id' => $producto_id
                    ]);
                } catch (Exception $e) {
                    $db->rollBack();
                    throw $e;
                }
            } elseif ($action === 'actualizar') {
                $db->beginTransaction();
                try {
                    $id = $_POST['id'] ?? 0;
                    $nombre = trim($_POST['nombre'] ?? '');
                    $material_id = $_POST['material_id'] ?? 0;
                    $unidad_id = $_POST['unidad_id'] ?? 0;
                    $descripcion = trim($_POST['descripcion'] ?? '');
                    $estado = $_POST['estado'] ?? 'activo';
                    $precio_venta = $_POST['precio_venta'] ?? 0;
                    $precio_compra = $_POST['precio_compra'] ?? 0;
                    
                    if (empty($nombre) || empty($material_id) || empty($unidad_id)) {
                        throw new Exception('Nombre, material y unidad son obligatorios');
                    }
                    
                    // Actualizar producto
                    $stmt = $db->prepare("
                        UPDATE productos 
                        SET nombre = ?, material_id = ?, unidad_id = ?, descripcion = ?, estado = ? 
                        WHERE id = ?
                    ");
                    $stmt->execute([$nombre, $material_id, $unidad_id, $descripcion ?: null, $estado, $id]);
                    
                    // Actualizar o crear precios
                    if ($precio_venta > 0) {
                        $stmt = $db->prepare("
                            SELECT id FROM precios WHERE producto_id = ? AND tipo_precio = 'venta' LIMIT 1
                        ");
                        $stmt->execute([$id]);
                        $precioExistente = $stmt->fetch();
                        
                        if ($precioExistente) {
                            $stmt = $db->prepare("
                                UPDATE precios SET precio_unitario = ? WHERE id = ?
                            ");
                            $stmt->execute([$precio_venta, $precioExistente['id']]);
                        } else {
                            $stmt = $db->prepare("
                                INSERT INTO precios (producto_id, precio_unitario, tipo_precio, estado) 
                                VALUES (?, ?, 'venta', 'activo')
                            ");
                            $stmt->execute([$id, $precio_venta]);
                        }
                    }
                    
                    if ($precio_compra > 0) {
                        $stmt = $db->prepare("
                            SELECT id FROM precios WHERE producto_id = ? AND tipo_precio = 'compra' LIMIT 1
                        ");
                        $stmt->execute([$id]);
                        $precioExistente = $stmt->fetch();
                        
                        if ($precioExistente) {
                            $stmt = $db->prepare("
                                UPDATE precios SET precio_unitario = ? WHERE id = ?
                            ");
                            $stmt->execute([$precio_compra, $precioExistente['id']]);
                        } else {
                            $stmt = $db->prepare("
                                INSERT INTO precios (producto_id, precio_unitario, tipo_precio, estado) 
                                VALUES (?, ?, 'compra', 'activo')
                            ");
                            $stmt->execute([$id, $precio_compra]);
                        }
                    }
                    
                    $db->commit();
                    ob_end_clean();
                    echo json_encode(['success' => true, 'message' => 'Producto actualizado exitosamente']);
                } catch (Exception $e) {
                    $db->rollBack();
                    throw $e;
                }
            } elseif ($action === 'eliminar') {
                $id = $_POST['id'] ?? 0;
                
                // Verificar si el producto ya está inactivo
                $stmt = $db->prepare("SELECT estado FROM productos WHERE id = ?");
                $stmt->execute([$id]);
                $producto = $stmt->fetch();
                
                if (!$producto) {
                    throw new Exception('Producto no encontrado');
                }
                
                if ($producto['estado'] === 'inactivo') {
                    ob_end_clean();
                    echo json_encode([
                        'success' => false,
                        'message' => 'El producto ya está inactivo'
                    ]);
                    exit;
                }
                
                // Siempre cambiar estado a inactivo (no se elimina físicamente)
                $stmt = $db->prepare("UPDATE productos SET estado = 'inactivo' WHERE id = ?");
                $stmt->execute([$id]);
                
                ob_end_clean();
                echo json_encode(['success' => true, 'message' => 'Producto desactivado exitosamente']);
            }
            break;
            
        default:
            ob_end_clean();
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    }
    
} catch (PDOException $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    ob_end_clean();
    error_log("Error en productos/api.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error en la base de datos: ' . (APP_DEBUG ? $e->getMessage() : 'Error al procesar la solicitud')
    ]);
} catch (Exception $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    ob_end_clean();
    error_log("Error en productos/api.php: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>

