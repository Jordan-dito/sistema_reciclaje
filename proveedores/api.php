<?php
/**
 * API para gestión de proveedores
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
    $usuario_id = $_SESSION['usuario_id'] ?? null;
    
    switch ($method) {
        case 'GET':
            if ($action === 'listar') {
                $stmt = $db->query("
                    SELECT p.*, u.nombre as creado_por_nombre 
                    FROM proveedores p 
                    LEFT JOIN usuarios u ON p.creado_por = u.id 
                    WHERE p.estado <> 'inactivo'
                    ORDER BY p.id DESC
                ");
                $proveedores = $stmt->fetchAll();
                
                ob_end_clean();
                echo json_encode(['success' => true, 'data' => $proveedores]);
            } elseif ($action === 'obtener') {
                $id = $_GET['id'] ?? 0;
                $stmt = $db->prepare("SELECT * FROM proveedores WHERE id = ?");
                $stmt->execute([$id]);
                $proveedor = $stmt->fetch();
                
                ob_end_clean();
                if ($proveedor) {
                    echo json_encode(['success' => true, 'data' => $proveedor]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Proveedor no encontrado']);
                }
            }
            break;
            
        case 'POST':
            if ($action === 'crear') {
                $nombre = trim($_POST['nombre'] ?? '');
                $cedula_ruc = trim($_POST['cedula_ruc'] ?? '');
                $tipo_documento = $_POST['tipo_documento'] ?? 'ruc';
                $direccion = trim($_POST['direccion'] ?? '');
                $telefono = trim($_POST['telefono'] ?? '');
                $email = trim($_POST['email'] ?? '');
                $contacto = trim($_POST['contacto'] ?? '');
                $tipo_proveedor = $_POST['tipo_proveedor'] ?? 'recolector';
                $materiales_suministra = trim($_POST['materiales_suministra'] ?? '');
                $estado = $_POST['estado'] ?? 'activo';
                $notas = trim($_POST['notas'] ?? '');
                
                if (empty($nombre)) {
                    throw new Exception('El nombre es obligatorio');
                }
                
                if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    throw new Exception('Email inválido');
                }
                
                // Validar cédula/RUC si se proporciona
                if (!empty($cedula_ruc)) {
                    $validacionDoc = validarDocumentoEcuatoriano($cedula_ruc, $tipo_documento);
                    if (!$validacionDoc['valid']) {
                        throw new Exception($validacionDoc['message']);
                    }
                    
                    // Verificar si ya existe
                    $stmt = $db->prepare("SELECT id FROM proveedores WHERE cedula_ruc = ?");
                    $stmt->execute([$cedula_ruc]);
                    if ($stmt->fetch()) {
                        throw new Exception('La cédula/RUC ya está registrada');
                    }
                }
                
                $stmt = $db->prepare("
                    INSERT INTO proveedores 
                    (nombre, cedula_ruc, tipo_documento, direccion, telefono, email, contacto, tipo_proveedor, materiales_suministra, estado, notas, creado_por) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");
                
                $stmt->execute([
                    $nombre,
                    $cedula_ruc ?: null,
                    $tipo_documento,
                    $direccion ?: null,
                    $telefono ?: null,
                    $email ?: null,
                    $contacto ?: null,
                    $tipo_proveedor,
                    $materiales_suministra ?: null,
                    $estado,
                    $notas ?: null,
                    $usuario_id
                ]);
                
                ob_end_clean();
                echo json_encode([
                    'success' => true,
                    'message' => 'Proveedor creado exitosamente',
                    'id' => $db->lastInsertId()
                ]);
            } elseif ($action === 'actualizar') {
                $id = intval($_POST['id'] ?? 0);
                $nombre = trim($_POST['nombre'] ?? '');
                $cedula_ruc = trim($_POST['cedula_ruc'] ?? '');
                $tipo_documento = $_POST['tipo_documento'] ?? 'ruc';
                $direccion = trim($_POST['direccion'] ?? '');
                $telefono = trim($_POST['telefono'] ?? '');
                $email = trim($_POST['email'] ?? '');
                $contacto = trim($_POST['contacto'] ?? '');
                $tipo_proveedor = $_POST['tipo_proveedor'] ?? 'recolector';
                $materiales_suministra = trim($_POST['materiales_suministra'] ?? '');
                $estado = $_POST['estado'] ?? 'activo';
                $notas = trim($_POST['notas'] ?? '');
                
                if (empty($nombre)) {
                    throw new Exception('El nombre es obligatorio');
                }
                
                if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    throw new Exception('Email inválido');
                }
                
                // Validar cédula/RUC si se proporciona
                if (!empty($cedula_ruc)) {
                    $validacionDoc = validarDocumentoEcuatoriano($cedula_ruc, $tipo_documento);
                    if (!$validacionDoc['valid']) {
                        throw new Exception($validacionDoc['message']);
                    }
                    
                    // Verificar si ya existe en otro proveedor
                    $stmt = $db->prepare("SELECT id FROM proveedores WHERE cedula_ruc = ? AND id != ?");
                    $stmt->execute([$cedula_ruc, $id]);
                    if ($stmt->fetch()) {
                        throw new Exception('La cédula/RUC ya está registrada en otro proveedor');
                    }
                }
                
                $stmt = $db->prepare("
                    UPDATE proveedores 
                    SET nombre = ?, cedula_ruc = ?, tipo_documento = ?, direccion = ?, telefono = ?, email = ?, 
                        contacto = ?, tipo_proveedor = ?, materiales_suministra = ?, estado = ?, notas = ?
                    WHERE id = ?
                ");
                
                $stmt->execute([
                    $nombre,
                    $cedula_ruc ?: null,
                    $tipo_documento,
                    $direccion ?: null,
                    $telefono ?: null,
                    $email ?: null,
                    $contacto ?: null,
                    $tipo_proveedor,
                    $materiales_suministra ?: null,
                    $estado,
                    $notas ?: null,
                    $id
                ]);
                
                ob_end_clean();
                echo json_encode(['success' => true, 'message' => 'Proveedor actualizado exitosamente']);
            } elseif ($action === 'eliminar') {
                $id = intval($_POST['id'] ?? 0);
                
                if ($id <= 0) {
                    throw new Exception('ID de proveedor inválido');
                }
                
                $stmt = $db->prepare("SELECT estado FROM proveedores WHERE id = ?");
                $stmt->execute([$id]);
                $proveedor = $stmt->fetch();
                
                if (!$proveedor) {
                    throw new Exception('Proveedor no encontrado');
                }
                
                if ($proveedor['estado'] === 'inactivo') {
                    throw new Exception('El proveedor ya está inactivo');
                }
                
                $stmt = $db->prepare("UPDATE proveedores SET estado = 'inactivo', fecha_actualizacion = NOW() WHERE id = ?");
                $stmt->execute([$id]);
                
                ob_end_clean();
                echo json_encode(['success' => true, 'message' => 'Proveedor desactivado exitosamente']);
            }
            break;
            
        default:
            ob_end_clean();
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    }
    
} catch (PDOException $e) {
    ob_end_clean();
    error_log("Error en proveedores/api.php: " . $e->getMessage());
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

