<?php
/**
 * API para gestión de sucursales
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
                    SELECT s.*, u.nombre as responsable_nombre 
                    FROM sucursales s 
                    LEFT JOIN usuarios u ON s.responsable_id = u.id 
                    ORDER BY s.id ASC
                ");
                $sucursales = $stmt->fetchAll();
                
                ob_end_clean();
                echo json_encode(['success' => true, 'data' => $sucursales], JSON_UNESCAPED_UNICODE);
            } elseif ($action === 'obtener') {
                $id = $_GET['id'] ?? 0;
                $stmt = $db->prepare("
                    SELECT s.*, u.nombre as responsable_nombre 
                    FROM sucursales s 
                    LEFT JOIN usuarios u ON s.responsable_id = u.id 
                    WHERE s.id = ?
                ");
                $stmt->execute([$id]);
                $sucursal = $stmt->fetch();
                
                ob_end_clean();
                if ($sucursal) {
                    echo json_encode(['success' => true, 'data' => $sucursal]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Sucursal no encontrada']);
                }
            } elseif ($action === 'activas') {
                // Obtener solo sucursales activas (para filtros)
                $stmt = $db->query("
                    SELECT id, nombre 
                    FROM sucursales 
                    WHERE estado = 'activa' 
                    ORDER BY nombre
                ");
                $sucursales = $stmt->fetchAll();
                
                ob_end_clean();
                echo json_encode(['success' => true, 'data' => $sucursales]);
            }
            break;
            
        case 'POST':
            if ($action === 'crear') {
                $nombre = trim($_POST['nombre'] ?? '');
                $direccion = trim($_POST['direccion'] ?? '');
                $telefono = trim($_POST['telefono'] ?? '');
                $email = trim($_POST['email'] ?? '');
                $responsable_id = !empty($_POST['responsable_id']) ? intval($_POST['responsable_id']) : null;
                $estado = $_POST['estado'] ?? 'activa';
                
                if (empty($nombre)) {
                    throw new Exception('El nombre es obligatorio');
                }
                
                if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    throw new Exception('Email inválido');
                }
                
                // Validar teléfono: debe tener 10 dígitos si se proporciona
                if (!empty($telefono)) {
                    $telefono = preg_replace('/[^0-9]/', '', $telefono); // Solo números
                    $validacionTelefono = validarTelefono10Digitos($telefono);
                    if (!$validacionTelefono['valid']) {
                        throw new Exception($validacionTelefono['message']);
                    }
                }
                
                if ($responsable_id) {
                    $stmt = $db->prepare("SELECT id FROM usuarios WHERE id = ?");
                    $stmt->execute([$responsable_id]);
                    if (!$stmt->fetch()) {
                        throw new Exception('Responsable inválido');
                    }
                }
                
                $stmt = $db->prepare("
                    INSERT INTO sucursales (nombre, direccion, telefono, email, responsable_id, estado) 
                    VALUES (?, ?, ?, ?, ?, ?)
                ");
                
                $stmt->execute([
                    $nombre,
                    $direccion ?: null,
                    $telefono ?: null,
                    $email ?: null,
                    $responsable_id,
                    $estado
                ]);
                
                ob_end_clean();
                echo json_encode([
                    'success' => true,
                    'message' => 'Sucursal creada exitosamente',
                    'id' => $db->lastInsertId()
                ]);
            } elseif ($action === 'actualizar') {
                $id = intval($_POST['id'] ?? 0);
                $nombre = trim($_POST['nombre'] ?? '');
                $direccion = trim($_POST['direccion'] ?? '');
                $telefono = trim($_POST['telefono'] ?? '');
                $email = trim($_POST['email'] ?? '');
                $responsable_id = !empty($_POST['responsable_id']) ? intval($_POST['responsable_id']) : null;
                $estado = $_POST['estado'] ?? 'activa';
                
                if (empty($nombre)) {
                    throw new Exception('El nombre es obligatorio');
                }
                
                if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    throw new Exception('Email inválido');
                }
                
                // Validar teléfono: debe tener 10 dígitos si se proporciona
                if (!empty($telefono)) {
                    $telefono = preg_replace('/[^0-9]/', '', $telefono); // Solo números
                    $validacionTelefono = validarTelefono10Digitos($telefono);
                    if (!$validacionTelefono['valid']) {
                        throw new Exception($validacionTelefono['message']);
                    }
                }
                
                if ($responsable_id) {
                    $stmt = $db->prepare("SELECT id FROM usuarios WHERE id = ?");
                    $stmt->execute([$responsable_id]);
                    if (!$stmt->fetch()) {
                        throw new Exception('Responsable inválido');
                    }
                }
                
                $stmt = $db->prepare("
                    UPDATE sucursales 
                    SET nombre = ?, direccion = ?, telefono = ?, email = ?, responsable_id = ?, estado = ?
                    WHERE id = ?
                ");
                
                $stmt->execute([
                    $nombre,
                    $direccion ?: null,
                    $telefono ?: null,
                    $email ?: null,
                    $responsable_id,
                    $estado,
                    $id
                ]);
                
                ob_end_clean();
                echo json_encode(['success' => true, 'message' => 'Sucursal actualizada exitosamente']);
            } elseif ($action === 'eliminar' || $action === 'desactivar') {
                $id = intval($_POST['id'] ?? 0);
                
                if ($id <= 0) {
                    throw new Exception('ID de sucursal inválido');
                }
                
                $stmt = $db->prepare("SELECT estado FROM sucursales WHERE id = ?");
                $stmt->execute([$id]);
                $sucursal = $stmt->fetch();
                
                if (!$sucursal) {
                    throw new Exception('Sucursal no encontrada');
                }
                
                if ($sucursal['estado'] === 'inactiva') {
                    throw new Exception('La sucursal ya está inactiva');
                }
                
                $stmt = $db->prepare("UPDATE sucursales SET estado = 'inactiva', fecha_actualizacion = NOW() WHERE id = ?");
                $stmt->execute([$id]);
                
                ob_end_clean();
                echo json_encode(['success' => true, 'message' => 'Sucursal desactivada exitosamente']);
            } elseif ($action === 'activar') {
                $id = intval($_POST['id'] ?? 0);
                
                if ($id <= 0) {
                    throw new Exception('ID de sucursal inválido');
                }
                
                $stmt = $db->prepare("SELECT estado FROM sucursales WHERE id = ?");
                $stmt->execute([$id]);
                $sucursal = $stmt->fetch();
                
                if (!$sucursal) {
                    throw new Exception('Sucursal no encontrada');
                }
                
                if ($sucursal['estado'] === 'activa') {
                    throw new Exception('La sucursal ya está activa');
                }
                
                $stmt = $db->prepare("UPDATE sucursales SET estado = 'activa', fecha_actualizacion = NOW() WHERE id = ?");
                $stmt->execute([$id]);
                
                ob_end_clean();
                echo json_encode(['success' => true, 'message' => 'Sucursal activada exitosamente']);
            }
            break;
            
        default:
            ob_end_clean();
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    }
    
} catch (PDOException $e) {
    ob_end_clean();
    $errorInfo = ErrorHandler::handleDatabaseError($e, 'sucursales/api.php');
    ErrorHandler::logError($e->getMessage(), ['exception' => $e->getMessage(), 'code' => $e->getCode()]);
    $debug = defined('APP_DEBUG') && APP_DEBUG;
    echo ErrorHandler::jsonResponse($errorInfo, 500, $debug);
} catch (Exception $e) {
    ob_end_clean();
    $errorInfo = ErrorHandler::handleException($e, 'sucursales/api.php');
    ErrorHandler::logError($e->getMessage(), ['exception' => $e->getMessage()]);
    $debug = defined('APP_DEBUG') && APP_DEBUG;
    echo ErrorHandler::jsonResponse($errorInfo, 400, $debug);
}
?>

