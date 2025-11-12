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
                    SELECT i.*, s.nombre as sucursal_nombre 
                    FROM inventarios i 
                    INNER JOIN sucursales s ON i.sucursal_id = s.id 
                    WHERE i.estado <> 'inactivo'
                ";
                $params = [];
                
                if ($sucursal_id) {
                    $sql .= " AND i.sucursal_id = ?";
                    $params[] = $sucursal_id;
                }
                
                $sql .= " ORDER BY i.id DESC";
                
                $stmt = $db->prepare($sql);
                $stmt->execute($params);
                $inventarios = $stmt->fetchAll();
                
                ob_end_clean();
                echo json_encode(['success' => true, 'data' => $inventarios]);
            } elseif ($action === 'obtener') {
                $id = $_GET['id'] ?? 0;
                $stmt = $db->prepare("
                    SELECT i.*, s.nombre as sucursal_nombre 
                    FROM inventarios i 
                    INNER JOIN sucursales s ON i.sucursal_id = s.id 
                    WHERE i.id = ?
                ");
                $stmt->execute([$id]);
                $inventario = $stmt->fetch();
                
                ob_end_clean();
                if ($inventario) {
                    echo json_encode(['success' => true, 'data' => $inventario]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Inventario no encontrado']);
                }
            }
            break;
            
        case 'POST':
            if ($action === 'crear') {
                $sucursal_id = intval($_POST['sucursal_id'] ?? 0);
                $nombre_producto = trim($_POST['nombre_producto'] ?? '');
                $categoria = $_POST['categoria'] ?? '';
                $cantidad = floatval($_POST['cantidad'] ?? 0);
                $unidad = $_POST['unidad'] ?? 'kg';
                $precio_unitario = floatval($_POST['precio_unitario'] ?? 0);
                $stock_minimo = floatval($_POST['stock_minimo'] ?? 0);
                $stock_maximo = floatval($_POST['stock_maximo'] ?? 0);
                $descripcion = trim($_POST['descripcion'] ?? '');
                $estado = $_POST['estado'] ?? 'disponible';
                
                if (empty($nombre_producto) || $sucursal_id <= 0) {
                    throw new Exception('Nombre del producto y sucursal son obligatorios');
                }
                
                // Verificar que la sucursal existe
                $stmt = $db->prepare("SELECT id FROM sucursales WHERE id = ?");
                $stmt->execute([$sucursal_id]);
                if (!$stmt->fetch()) {
                    throw new Exception('Sucursal inválida');
                }
                
                $stmt = $db->prepare("
                    INSERT INTO inventarios 
                    (sucursal_id, nombre_producto, categoria, cantidad, unidad, precio_unitario, stock_minimo, stock_maximo, descripcion, estado, creado_por) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");
                
                $stmt->execute([
                    $sucursal_id,
                    $nombre_producto,
                    $categoria,
                    $cantidad,
                    $unidad,
                    $precio_unitario,
                    $stock_minimo,
                    $stock_maximo,
                    $descripcion ?: null,
                    $estado,
                    $usuario_id
                ]);
                
                ob_end_clean();
                echo json_encode([
                    'success' => true,
                    'message' => 'Inventario creado exitosamente',
                    'id' => $db->lastInsertId()
                ]);
            } elseif ($action === 'actualizar') {
                $id = intval($_POST['id'] ?? 0);
                $sucursal_id = intval($_POST['sucursal_id'] ?? 0);
                $nombre_producto = trim($_POST['nombre_producto'] ?? '');
                $categoria = $_POST['categoria'] ?? '';
                $cantidad = floatval($_POST['cantidad'] ?? 0);
                $unidad = $_POST['unidad'] ?? 'kg';
                $precio_unitario = floatval($_POST['precio_unitario'] ?? 0);
                $stock_minimo = floatval($_POST['stock_minimo'] ?? 0);
                $stock_maximo = floatval($_POST['stock_maximo'] ?? 0);
                $descripcion = trim($_POST['descripcion'] ?? '');
                $estado = $_POST['estado'] ?? 'disponible';
                
                if (empty($nombre_producto) || $sucursal_id <= 0) {
                    throw new Exception('Nombre del producto y sucursal son obligatorios');
                }
                
                $stmt = $db->prepare("
                    UPDATE inventarios 
                    SET sucursal_id = ?, nombre_producto = ?, categoria = ?, cantidad = ?, unidad = ?, 
                        precio_unitario = ?, stock_minimo = ?, stock_maximo = ?, descripcion = ?, estado = ?
                    WHERE id = ?
                ");
                
                $stmt->execute([
                    $sucursal_id,
                    $nombre_producto,
                    $categoria,
                    $cantidad,
                    $unidad,
                    $precio_unitario,
                    $stock_minimo,
                    $stock_maximo,
                    $descripcion ?: null,
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
    error_log("Error en inventarios/api.php: " . $e->getMessage());
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

