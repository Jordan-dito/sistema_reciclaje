<?php
/**
 * API para gestión de roles
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
    
    switch ($method) {
        case 'GET':
            if ($action === 'listar') {
                $stmt = $db->query("SELECT * FROM roles WHERE estado <> 'inactivo' ORDER BY id");
                $roles = $stmt->fetchAll();
                
                ob_end_clean();
                echo json_encode(['success' => true, 'data' => $roles]);
            } elseif ($action === 'obtener') {
                $id = $_GET['id'] ?? 0;
                $stmt = $db->prepare("SELECT * FROM roles WHERE id = ?");
                $stmt->execute([$id]);
                $rol = $stmt->fetch();
                
                ob_end_clean();
                if ($rol) {
                    echo json_encode(['success' => true, 'data' => $rol]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Rol no encontrado']);
                }
            }
            break;
            
        case 'POST':
            if ($action === 'crear') {
                $nombre = trim($_POST['nombre'] ?? '');
                $descripcion = trim($_POST['descripcion'] ?? '');
                $permisos = $_POST['permisos'] ?? '{}';
                $estado = $_POST['estado'] ?? 'activo';
                
                if (empty($nombre)) {
                    throw new Exception('El nombre es obligatorio');
                }
                
                // Validar que el nombre no exista
                $stmt = $db->prepare("SELECT id FROM roles WHERE nombre = ?");
                $stmt->execute([$nombre]);
                if ($stmt->fetch()) {
                    throw new Exception('El nombre del rol ya existe');
                }
                
                // Validar JSON de permisos
                if (is_string($permisos)) {
                    $permisos_array = json_decode($permisos, true);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        throw new Exception('Formato de permisos inválido');
                    }
                    $permisos = json_encode($permisos_array);
                } else {
                    $permisos = json_encode($permisos);
                }
                
                $stmt = $db->prepare("
                    INSERT INTO roles (nombre, descripcion, permisos, estado) 
                    VALUES (?, ?, ?, ?)
                ");
                
                $stmt->execute([
                    $nombre,
                    $descripcion ?: null,
                    $permisos,
                    $estado
                ]);
                
                ob_end_clean();
                echo json_encode([
                    'success' => true,
                    'message' => 'Rol creado exitosamente',
                    'id' => $db->lastInsertId()
                ]);
            } elseif ($action === 'actualizar') {
                $id = intval($_POST['id'] ?? 0);
                $nombre = trim($_POST['nombre'] ?? '');
                $descripcion = trim($_POST['descripcion'] ?? '');
                $permisos = $_POST['permisos'] ?? '{}';
                $estado = $_POST['estado'] ?? 'activo';
                
                if (empty($nombre)) {
                    throw new Exception('El nombre es obligatorio');
                }
                
                // Validar que el nombre no exista en otro rol
                $stmt = $db->prepare("SELECT id FROM roles WHERE nombre = ? AND id != ?");
                $stmt->execute([$nombre, $id]);
                if ($stmt->fetch()) {
                    throw new Exception('El nombre del rol ya existe');
                }
                
                // Validar JSON de permisos
                if (is_string($permisos)) {
                    $permisos_array = json_decode($permisos, true);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        throw new Exception('Formato de permisos inválido');
                    }
                    $permisos = json_encode($permisos_array);
                } else {
                    $permisos = json_encode($permisos);
                }
                
                $stmt = $db->prepare("
                    UPDATE roles 
                    SET nombre = ?, descripcion = ?, permisos = ?, estado = ?
                    WHERE id = ?
                ");
                
                $stmt->execute([
                    $nombre,
                    $descripcion ?: null,
                    $permisos,
                    $estado,
                    $id
                ]);
                
                ob_end_clean();
                echo json_encode(['success' => true, 'message' => 'Rol actualizado exitosamente']);
            } elseif ($action === 'eliminar') {
                $id = intval($_POST['id'] ?? 0);
                
                // Verificar si tiene usuarios asociados
                $stmt = $db->prepare("SELECT COUNT(*) as total FROM usuarios WHERE rol_id = ?");
                $stmt->execute([$id]);
                $usuarios = $stmt->fetch()['total'];
                
                if ($usuarios > 0) {
                    throw new Exception('No se puede eliminar el rol porque tiene usuarios asociados');
                }
                
                $stmt = $db->prepare("SELECT estado FROM roles WHERE id = ?");
                $stmt->execute([$id]);
                $rol = $stmt->fetch();
                
                if (!$rol) {
                    throw new Exception('Rol no encontrado');
                }
                
                if ($rol['estado'] === 'inactivo') {
                    throw new Exception('El rol ya está inactivo');
                }
                
                $stmt = $db->prepare("UPDATE roles SET estado = 'inactivo', fecha_actualizacion = NOW() WHERE id = ?");
                $stmt->execute([$id]);
                
                ob_end_clean();
                echo json_encode(['success' => true, 'message' => 'Rol desactivado exitosamente']);
            }
            break;
            
        default:
            ob_end_clean();
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    }
    
} catch (PDOException $e) {
    ob_end_clean();
    error_log("Error en roles/api.php: " . $e->getMessage());
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

