<?php
/**
 * API para gestión de unidades
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
                    SELECT * FROM unidades 
                    WHERE estado = 'activo'
                    ORDER BY nombre ASC
                ");
                $unidades = $stmt->fetchAll();
                
                ob_end_clean();
                echo json_encode(['success' => true, 'data' => $unidades], JSON_UNESCAPED_UNICODE);
            } elseif ($action === 'obtener') {
                $id = $_GET['id'] ?? 0;
                $stmt = $db->prepare("SELECT * FROM unidades WHERE id = ?");
                $stmt->execute([$id]);
                $unidad = $stmt->fetch();
                
                ob_end_clean();
                if ($unidad) {
                    echo json_encode(['success' => true, 'data' => $unidad], JSON_UNESCAPED_UNICODE);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Unidad no encontrada']);
                }
            }
            break;
            
        case 'POST':
            if ($action === 'crear') {
                $nombre = trim($_POST['nombre'] ?? '');
                $simbolo = trim($_POST['simbolo'] ?? '');
                $tipo = $_POST['tipo'] ?? 'peso';
                $estado = $_POST['estado'] ?? 'activo';
                
                // Validar nombre: no solo espacios
                $validacionNombre = validarNoSoloEspacios($nombre, 'Nombre');
                if (!$validacionNombre['valid']) {
                    throw new Exception($validacionNombre['message']);
                }
                $nombre = limpiarEspacios($nombre);
                $simbolo = limpiarEspacios($simbolo);
                
                // Verificar que no exista una unidad con el mismo nombre (case-insensitive)
                $stmt = $db->prepare("
                    SELECT id FROM unidades 
                    WHERE LOWER(TRIM(nombre)) = LOWER(TRIM(?)) 
                    AND estado = 'activo'
                ");
                $stmt->execute([$nombre]);
                $unidadExistente = $stmt->fetch();
                
                if ($unidadExistente) {
                    throw new Exception('Ya existe una unidad activa con el nombre "' . $nombre . '"');
                }
                
                $stmt = $db->prepare("
                    INSERT INTO unidades (nombre, simbolo, tipo, estado) 
                    VALUES (?, ?, ?, ?)
                ");
                $stmt->execute([$nombre, $simbolo ?: null, $tipo, $estado]);
                
                ob_end_clean();
                echo json_encode([
                    'success' => true,
                    'message' => 'Unidad creada exitosamente',
                    'id' => $db->lastInsertId()
                ]);
            } elseif ($action === 'actualizar') {
                $id = $_POST['id'] ?? 0;
                $nombre = trim($_POST['nombre'] ?? '');
                $simbolo = trim($_POST['simbolo'] ?? '');
                $tipo = $_POST['tipo'] ?? 'peso';
                $estado = $_POST['estado'] ?? 'activo';
                
                // Validar nombre: no solo espacios
                $validacionNombre = validarNoSoloEspacios($nombre, 'Nombre');
                if (!$validacionNombre['valid']) {
                    throw new Exception($validacionNombre['message']);
                }
                $nombre = limpiarEspacios($nombre);
                $simbolo = limpiarEspacios($simbolo);
                
                // Verificar que no exista otra unidad con el mismo nombre (case-insensitive)
                $stmt = $db->prepare("
                    SELECT id FROM unidades 
                    WHERE LOWER(TRIM(nombre)) = LOWER(TRIM(?)) 
                    AND id != ? 
                    AND estado = 'activo'
                ");
                $stmt->execute([$nombre, $id]);
                $unidadExistente = $stmt->fetch();
                
                if ($unidadExistente) {
                    throw new Exception('Ya existe otra unidad activa con el nombre "' . $nombre . '"');
                }
                
                $stmt = $db->prepare("
                    UPDATE unidades 
                    SET nombre = ?, simbolo = ?, tipo = ?, estado = ? 
                    WHERE id = ?
                ");
                $stmt->execute([$nombre, $simbolo ?: null, $tipo, $estado, $id]);
                
                ob_end_clean();
                echo json_encode(['success' => true, 'message' => 'Unidad actualizada exitosamente']);
            } elseif ($action === 'eliminar') {
                $id = $_POST['id'] ?? 0;
                
                // Verificar si la unidad ya está inactiva
                $stmt = $db->prepare("SELECT estado FROM unidades WHERE id = ?");
                $stmt->execute([$id]);
                $unidad = $stmt->fetch();
                
                if (!$unidad) {
                    throw new Exception('Unidad no encontrada');
                }
                
                if ($unidad['estado'] === 'inactivo') {
                    ob_end_clean();
                    echo json_encode([
                        'success' => false,
                        'message' => 'La unidad ya está inactiva'
                    ]);
                    exit;
                }
                
                // Siempre cambiar estado a inactivo (no se elimina físicamente)
                $stmt = $db->prepare("UPDATE unidades SET estado = 'inactivo' WHERE id = ?");
                $stmt->execute([$id]);
                
                ob_end_clean();
                echo json_encode(['success' => true, 'message' => 'Unidad desactivada exitosamente']);
            }
            break;
            
        default:
            ob_end_clean();
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    }
    
} catch (PDOException $e) {
    ob_end_clean();
    error_log("Error en unidades/api.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error en la base de datos: ' . (APP_DEBUG ? $e->getMessage() : 'Error al procesar la solicitud')
    ]);
} catch (Exception $e) {
    ob_end_clean();
    error_log("Error en unidades/api.php: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>

