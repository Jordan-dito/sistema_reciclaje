<?php
/**
 * API para gestión de materiales
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
                    SELECT m.*, c.nombre as categoria_nombre 
                    FROM materiales m 
                    LEFT JOIN categorias c ON m.categoria_id = c.id 
                    WHERE m.estado = 'activo'
                    ORDER BY m.nombre ASC
                ");
                $materiales = $stmt->fetchAll();
                
                ob_end_clean();
                echo json_encode(['success' => true, 'data' => $materiales], JSON_UNESCAPED_UNICODE);
            } elseif ($action === 'obtener') {
                $id = $_GET['id'] ?? 0;
                $stmt = $db->prepare("
                    SELECT m.*, c.nombre as categoria_nombre 
                    FROM materiales m 
                    LEFT JOIN categorias c ON m.categoria_id = c.id 
                    WHERE m.id = ?
                ");
                $stmt->execute([$id]);
                $material = $stmt->fetch();
                
                ob_end_clean();
                if ($material) {
                    echo json_encode(['success' => true, 'data' => $material], JSON_UNESCAPED_UNICODE);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Material no encontrado']);
                }
            } elseif ($action === 'categorias') {
                // Obtener todas las categorías activas
                $stmt = $db->query("SELECT id, nombre FROM categorias WHERE estado = 'activo' ORDER BY nombre ASC");
                $categorias = $stmt->fetchAll();
                
                ob_end_clean();
                echo json_encode(['success' => true, 'data' => $categorias], JSON_UNESCAPED_UNICODE);
            }
            break;
            
        case 'POST':
            if ($action === 'crear') {
                $nombre = trim($_POST['nombre'] ?? '');
                $categoria_id = $_POST['categoria_id'] ?? null;
                $descripcion = trim($_POST['descripcion'] ?? '');
                $icono = trim($_POST['icono'] ?? '');
                $estado = $_POST['estado'] ?? 'activo';
                
                if (empty($nombre)) {
                    throw new Exception('El nombre es obligatorio');
                }
                
                $stmt = $db->prepare("
                    INSERT INTO materiales (nombre, categoria_id, descripcion, icono, estado) 
                    VALUES (?, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    $nombre, 
                    $categoria_id ?: null, 
                    $descripcion ?: null, 
                    $icono ?: null, 
                    $estado
                ]);
                
                ob_end_clean();
                echo json_encode([
                    'success' => true,
                    'message' => 'Material creado exitosamente',
                    'id' => $db->lastInsertId()
                ]);
            } elseif ($action === 'actualizar') {
                $id = $_POST['id'] ?? 0;
                $nombre = trim($_POST['nombre'] ?? '');
                $categoria_id = $_POST['categoria_id'] ?? null;
                $descripcion = trim($_POST['descripcion'] ?? '');
                $icono = trim($_POST['icono'] ?? '');
                $estado = $_POST['estado'] ?? 'activo';
                
                if (empty($nombre)) {
                    throw new Exception('El nombre es obligatorio');
                }
                
                $stmt = $db->prepare("
                    UPDATE materiales 
                    SET nombre = ?, categoria_id = ?, descripcion = ?, icono = ?, estado = ? 
                    WHERE id = ?
                ");
                $stmt->execute([
                    $nombre, 
                    $categoria_id ?: null, 
                    $descripcion ?: null, 
                    $icono ?: null, 
                    $estado, 
                    $id
                ]);
                
                ob_end_clean();
                echo json_encode(['success' => true, 'message' => 'Material actualizado exitosamente']);
            } elseif ($action === 'eliminar') {
                $id = $_POST['id'] ?? 0;
                
                // Verificar si hay productos usando este material
                $stmt = $db->prepare("SELECT COUNT(*) as total FROM productos WHERE material_id = ?");
                $stmt->execute([$id]);
                $result = $stmt->fetch();
                
                if ($result['total'] > 0) {
                    ob_end_clean();
                    echo json_encode([
                        'success' => false,
                        'message' => 'No se puede eliminar: hay productos asociados a este material'
                    ]);
                    exit;
                }
                
                // Cambiar estado a inactivo
                $stmt = $db->prepare("UPDATE materiales SET estado = 'inactivo' WHERE id = ?");
                $stmt->execute([$id]);
                
                ob_end_clean();
                echo json_encode(['success' => true, 'message' => 'Material eliminado exitosamente']);
            }
            break;
            
        default:
            ob_end_clean();
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    }
    
} catch (PDOException $e) {
    ob_end_clean();
    error_log("Error en materiales/api.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error en la base de datos: ' . (APP_DEBUG ? $e->getMessage() : 'Error al procesar la solicitud')
    ]);
} catch (Exception $e) {
    ob_end_clean();
    error_log("Error en materiales/api.php: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>

