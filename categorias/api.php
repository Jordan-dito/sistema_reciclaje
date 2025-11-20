<?php
/**
 * API para gestión de categorías
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
                    SELECT * FROM categorias 
                    WHERE estado = 'activo'
                    ORDER BY nombre ASC
                ");
                $categorias = $stmt->fetchAll();
                
                ob_end_clean();
                echo json_encode(['success' => true, 'data' => $categorias], JSON_UNESCAPED_UNICODE);
            } elseif ($action === 'obtener') {
                $id = $_GET['id'] ?? 0;
                $stmt = $db->prepare("SELECT * FROM categorias WHERE id = ?");
                $stmt->execute([$id]);
                $categoria = $stmt->fetch();
                
                ob_end_clean();
                if ($categoria) {
                    echo json_encode(['success' => true, 'data' => $categoria], JSON_UNESCAPED_UNICODE);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Categoría no encontrada']);
                }
            }
            break;
            
        case 'POST':
            if ($action === 'crear') {
                $nombre = trim($_POST['nombre'] ?? '');
                $descripcion = trim($_POST['descripcion'] ?? '');
                $estado = $_POST['estado'] ?? 'activo';
                
                // Validar nombre: no solo espacios
                $validacionNombre = validarNoSoloEspacios($nombre, 'Nombre');
                if (!$validacionNombre['valid']) {
                    throw new Exception($validacionNombre['message']);
                }
                $nombre = limpiarEspacios($nombre);
                $descripcion = limpiarEspacios($descripcion);
                
                // Verificar que no exista una categoría con el mismo nombre (case-insensitive)
                $stmt = $db->prepare("
                    SELECT id FROM categorias 
                    WHERE LOWER(TRIM(nombre)) = LOWER(TRIM(?)) 
                    AND estado = 'activo'
                ");
                $stmt->execute([$nombre]);
                $categoriaExistente = $stmt->fetch();
                
                if ($categoriaExistente) {
                    throw new Exception('Ya existe una categoría activa con el nombre "' . $nombre . '"');
                }
                
                // Crear categoría
                $stmt = $db->prepare("
                    INSERT INTO categorias (nombre, descripcion, estado) 
                    VALUES (?, ?, ?)
                ");
                $stmt->execute([$nombre, $descripcion ?: null, $estado]);
                $categoria_id = $db->lastInsertId();
                
                ob_end_clean();
                echo json_encode([
                    'success' => true,
                    'message' => 'Categoría creada exitosamente',
                    'id' => $categoria_id
                ]);
            } elseif ($action === 'actualizar') {
                $id = $_POST['id'] ?? 0;
                $nombre = trim($_POST['nombre'] ?? '');
                $descripcion = trim($_POST['descripcion'] ?? '');
                $estado = $_POST['estado'] ?? 'activo';
                
                if (empty($nombre)) {
                    throw new Exception('El nombre es obligatorio');
                }
                
                $nombre = limpiarEspacios($nombre);
                $descripcion = limpiarEspacios($descripcion);
                
                // Verificar que no exista otra categoría con el mismo nombre (case-insensitive)
                $stmt = $db->prepare("
                    SELECT id FROM categorias 
                    WHERE LOWER(TRIM(nombre)) = LOWER(TRIM(?)) 
                    AND id != ? 
                    AND estado = 'activo'
                ");
                $stmt->execute([$nombre, $id]);
                $categoriaExistente = $stmt->fetch();
                
                if ($categoriaExistente) {
                    throw new Exception('Ya existe otra categoría activa con el nombre "' . $nombre . '"');
                }
                
                $stmt = $db->prepare("
                    UPDATE categorias 
                    SET nombre = ?, descripcion = ?, estado = ? 
                    WHERE id = ?
                ");
                $stmt->execute([$nombre, $descripcion ?: null, $estado, $id]);
                
                ob_end_clean();
                echo json_encode(['success' => true, 'message' => 'Categoría actualizada exitosamente']);
            } elseif ($action === 'eliminar') {
                $id = $_POST['id'] ?? 0;
                
                // Verificar si la categoría ya está inactiva
                $stmt = $db->prepare("SELECT estado FROM categorias WHERE id = ?");
                $stmt->execute([$id]);
                $categoria = $stmt->fetch();
                
                if (!$categoria) {
                    throw new Exception('Categoría no encontrada');
                }
                
                if ($categoria['estado'] === 'inactivo') {
                    ob_end_clean();
                    echo json_encode([
                        'success' => false,
                        'message' => 'La categoría ya está inactiva'
                    ]);
                    exit;
                }
                
                // Siempre cambiar estado a inactivo (no se elimina físicamente)
                $stmt = $db->prepare("UPDATE categorias SET estado = 'inactivo' WHERE id = ?");
                $stmt->execute([$id]);
                
                ob_end_clean();
                echo json_encode(['success' => true, 'message' => 'Categoría desactivada exitosamente']);
            }
            break;
            
        default:
            ob_end_clean();
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    }
    
} catch (PDOException $e) {
    ob_end_clean();
    error_log("Error en categorias/api.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error en la base de datos: ' . (APP_DEBUG ? $e->getMessage() : 'Error al procesar la solicitud')
    ]);
} catch (Exception $e) {
    ob_end_clean();
    error_log("Error en categorias/api.php: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>

