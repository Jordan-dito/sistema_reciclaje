<?php
/**
 * API para gestión de clientes
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
    require_once __DIR__ . '/../config/ErrorHandler.php';

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
    
    // Verificar que la tabla clientes existe
    try {
        $db->query("SELECT 1 FROM clientes LIMIT 1");
    } catch (PDOException $e) {
        if ($e->getCode() == '42S02') { // Table doesn't exist
            ob_end_clean();
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'La tabla de clientes no existe. Por favor, ejecuta el script crear_tabla_clientes.sql en tu base de datos.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }
        throw $e;
    }
    $usuario_id = $_SESSION['usuario_id'] ?? null;
    
    switch ($method) {
        case 'GET':
            if ($action === 'listar') {
                $estado = $_GET['estado'] ?? 'activos';
                
                $sql = "SELECT c.*, u.nombre as creado_por_nombre 
                        FROM clientes c 
                        LEFT JOIN usuarios u ON c.creado_por = u.id 
                        WHERE 1=1";
                
                if ($estado === 'activos') {
                    $sql .= " AND c.estado = 'activo'";
                } elseif ($estado === 'inactivos') {
                    $sql .= " AND c.estado = 'inactivo'";
                }
                
                $sql .= " ORDER BY c.id ASC";
                
                $stmt = $db->query($sql);
                $clientes = $stmt->fetchAll();
                
                ob_end_clean();
                echo json_encode(['success' => true, 'data' => $clientes]);
            } elseif ($action === 'obtener') {
                $id = $_GET['id'] ?? 0;
                $stmt = $db->prepare("SELECT * FROM clientes WHERE id = ?");
                $stmt->execute([$id]);
                $cliente = $stmt->fetch();
                
                ob_end_clean();
                if ($cliente) {
                    echo json_encode(['success' => true, 'data' => $cliente]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Cliente no encontrado']);
                }
            }
            break;
            
        case 'POST':
            if ($action === 'crear') {
                $nombre = trim($_POST['nombre'] ?? '');
                $cedula_ruc = trim($_POST['cedula_ruc'] ?? '');
                $tipo_documento = $_POST['tipo_documento'] ?? 'cedula';
                $direccion = trim($_POST['direccion'] ?? '');
                $telefono = trim($_POST['telefono'] ?? '');
                $email = trim($_POST['email'] ?? '');
                $contacto = trim($_POST['contacto'] ?? '');
                $tipo_cliente = $_POST['tipo_cliente'] ?? 'minorista';
                $estado = 'activo';
                $notas = trim($_POST['notas'] ?? '');
                
                // Validar nombre: no solo espacios
                $validacionNombre = validarNoSoloEspacios($nombre, 'Nombre');
                if (!$validacionNombre['valid']) {
                    throw new Exception($validacionNombre['message']);
                }
                $nombre = limpiarEspacios($nombre);
                
                // Verificar si el nombre ya existe
                $stmt = $db->prepare("SELECT id FROM clientes WHERE LOWER(nombre) = LOWER(?) AND estado = 'activo'");
                $stmt->execute([$nombre]);
                if ($stmt->fetch()) {
                    throw new Exception('Ya existe un cliente activo con este nombre');
                }
                
                // Validar cédula/RUC (obligatorio)
                if (empty($cedula_ruc)) {
                    throw new Exception('La cédula/RUC es obligatoria');
                }
                $cedula_ruc = preg_replace('/[^0-9]/', '', $cedula_ruc);
                $validacionDoc = validarDocumentoEcuatoriano($cedula_ruc, $tipo_documento);
                if (!$validacionDoc['valid']) {
                    throw new Exception($validacionDoc['message']);
                }
                
                // Verificar si ya existe un cliente con esta cédula/RUC (activo o inactivo)
                $stmt = $db->prepare("SELECT id, estado FROM clientes WHERE cedula_ruc = ?");
                $stmt->execute([$cedula_ruc]);
                $clienteExistente = $stmt->fetch();
                
                if ($clienteExistente) {
                    if ($clienteExistente['estado'] === 'activo') {
                        throw new Exception('La cédula/RUC ya está registrada en otro cliente activo');
                    } else {
                        throw new Exception('La cédula/RUC ya está registrada en un cliente INACTIVO. Por favor, búscala en la pestaña de "Inactivos" y actívala.');
                    }
                }
                
                // Validar email (obligatorio)
                if (empty($email)) {
                    throw new Exception('El email es obligatorio');
                }
                $email = limpiarEspacios($email);
                $validacionEmail = validarEmail($email);
                if (!$validacionEmail['valid']) {
                    throw new Exception($validacionEmail['message']);
                }
                
                // Verificar si el email ya existe
                $stmt = $db->prepare("SELECT id FROM clientes WHERE LOWER(email) = LOWER(?) AND estado = 'activo'");
                $stmt->execute([$email]);
                if ($stmt->fetch()) {
                    throw new Exception('El email ya está registrado en otro cliente activo');
                }
                
                // Validar teléfono (obligatorio)
                if (empty($telefono)) {
                    throw new Exception('El teléfono es obligatorio');
                }
                $telefono = preg_replace('/[^0-9]/', '', $telefono);
                $validacionTelefono = validarTelefonoEcuatoriano($telefono);
                if (!$validacionTelefono['valid']) {
                    throw new Exception($validacionTelefono['message']);
                }
                
                // Validar contacto si se proporciona
                if (!empty($contacto)) {
                    $validacionContacto = validarSoloLetras($contacto, 'Contacto', true, true);
                    if (!$validacionContacto['valid']) {
                        throw new Exception($validacionContacto['message']);
                    }
                    $contacto = limpiarEspacios($contacto);
                }
                
                // Limpiar campos de texto
                $direccion = limpiarEspacios($direccion);
                $notas = limpiarEspacios($notas);
                
                $stmt = $db->prepare("
                    INSERT INTO clientes 
                    (nombre, cedula_ruc, tipo_documento, direccion, telefono, email, contacto, tipo_cliente, estado, notas, creado_por) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");
                
                $stmt->execute([
                    $nombre,
                    $cedula_ruc ?: null,
                    $tipo_documento,
                    $direccion ?: null,
                    $telefono ?: null,
                    $email ?: null,
                    $contacto ?: null,
                    $tipo_cliente,
                    $estado,
                    $notas ?: null,
                    $usuario_id
                ]);
                
                ob_end_clean();
                echo json_encode([
                    'success' => true,
                    'message' => 'Cliente creado exitosamente',
                    'id' => $db->lastInsertId()
                ]);
            } elseif ($action === 'actualizar' || $action === 'editar') {
                $id = intval($_POST['id'] ?? 0);
                $nombre = trim($_POST['nombre'] ?? '');
                $cedula_ruc = trim($_POST['cedula_ruc'] ?? '');
                $tipo_documento = $_POST['tipo_documento'] ?? 'cedula';
                $direccion = trim($_POST['direccion'] ?? '');
                $telefono = trim($_POST['telefono'] ?? '');
                $email = trim($_POST['email'] ?? '');
                $contacto = trim($_POST['contacto'] ?? '');
                $tipo_cliente = $_POST['tipo_cliente'] ?? 'minorista';
                $notas = trim($_POST['notas'] ?? '');
                
                // Validar nombre: no solo espacios
                $validacionNombre = validarNoSoloEspacios($nombre, 'Nombre');
                if (!$validacionNombre['valid']) {
                    throw new Exception($validacionNombre['message']);
                }
                $nombre = limpiarEspacios($nombre);
                
                // Validar cédula/RUC (obligatorio)
                if (empty($cedula_ruc)) {
                    throw new Exception('La cédula/RUC es obligatoria');
                }
                $cedula_ruc = preg_replace('/[^0-9]/', '', $cedula_ruc);
                $validacionDoc = validarDocumentoEcuatoriano($cedula_ruc, $tipo_documento);
                if (!$validacionDoc['valid']) {
                    throw new Exception($validacionDoc['message']);
                }
                
                // Verificar si ya existe en otro cliente (activo o inactivo)
                $stmt = $db->prepare("SELECT id, estado FROM clientes WHERE cedula_ruc = ? AND id != ?");
                $stmt->execute([$cedula_ruc, $id]);
                $clienteExistente = $stmt->fetch();
                
                if ($clienteExistente) {
                    if ($clienteExistente['estado'] === 'activo') {
                        throw new Exception('La cédula/RUC ya está registrada en otro cliente activo');
                    } else {
                        throw new Exception('La cédula/RUC ya está registrada en un cliente INACTIVO. Por favor, búscala en la pestaña de "Inactivos" para restaurarla o cámbiala.');
                    }
                }
                
                // Validar email (obligatorio)
                if (empty($email)) {
                    throw new Exception('El email es obligatorio');
                }
                $email = limpiarEspacios($email);
                $validacionEmail = validarEmail($email);
                if (!$validacionEmail['valid']) {
                    throw new Exception($validacionEmail['message']);
                }
                
                // Verificar si el email ya existe en otro cliente
                $stmt = $db->prepare("SELECT id FROM clientes WHERE LOWER(email) = LOWER(?) AND id != ? AND estado = 'activo'");
                $stmt->execute([$email, $id]);
                if ($stmt->fetch()) {
                    throw new Exception('El email ya está registrado en otro cliente activo');
                }
                
                // Validar teléfono (obligatorio)
                if (empty($telefono)) {
                    throw new Exception('El teléfono es obligatorio');
                }
                $telefono = preg_replace('/[^0-9]/', '', $telefono);
                $validacionTelefono = validarTelefonoEcuatoriano($telefono);
                if (!$validacionTelefono['valid']) {
                    throw new Exception($validacionTelefono['message']);
                }
                
                // Validar contacto si se proporciona
                if (!empty($contacto)) {
                    $validacionContacto = validarSoloLetras($contacto, 'Contacto', true, true);
                    if (!$validacionContacto['valid']) {
                        throw new Exception($validacionContacto['message']);
                    }
                    $contacto = limpiarEspacios($contacto);
                }
                
                // Limpiar campos de texto
                $direccion = limpiarEspacios($direccion);
                $notas = limpiarEspacios($notas);
                
                $stmt = $db->prepare("
                    UPDATE clientes 
                    SET nombre = ?, cedula_ruc = ?, tipo_documento = ?, direccion = ?, telefono = ?, email = ?, 
                        contacto = ?, tipo_cliente = ?, notas = ?
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
                    $tipo_cliente,
                    $notas ?: null,
                    $id
                ]);
                
                ob_end_clean();
                echo json_encode(['success' => true, 'message' => 'Cliente actualizado exitosamente']);
            } elseif ($action === 'eliminar') {
                $id = intval($_POST['id'] ?? 0);
                
                if ($id <= 0) {
                    throw new Exception('ID de cliente inválido');
                }
                
                $stmt = $db->prepare("SELECT estado FROM clientes WHERE id = ?");
                $stmt->execute([$id]);
                $cliente = $stmt->fetch();
                
                if (!$cliente) {
                    throw new Exception('Cliente no encontrado');
                }
                
                if ($cliente['estado'] === 'inactivo') {
                    throw new Exception('El cliente ya está inactivo');
                }
                
                $stmt = $db->prepare("UPDATE clientes SET estado = 'inactivo', fecha_actualizacion = NOW() WHERE id = ?");
                $stmt->execute([$id]);
                
                ob_end_clean();
                echo json_encode(['success' => true, 'message' => 'Cliente desactivado exitosamente']);
            } elseif ($action === 'activar') {
                $id = intval($_POST['id'] ?? 0);
                
                if ($id <= 0) {
                    throw new Exception('ID de cliente inválido');
                }
                
                $stmt = $db->prepare("SELECT estado FROM clientes WHERE id = ?");
                $stmt->execute([$id]);
                $cliente = $stmt->fetch();
                
                if (!$cliente) {
                    throw new Exception('Cliente no encontrado');
                }
                
                $stmt = $db->prepare("UPDATE clientes SET estado = 'activo', fecha_actualizacion = NOW() WHERE id = ?");
                $stmt->execute([$id]);
                
                ob_end_clean();
                echo json_encode(['success' => true, 'message' => 'Cliente activado exitosamente']);
            }
            break;
            
        default:
            ob_end_clean();
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    }
    
} catch (PDOException $e) {
    ob_end_clean();
    $errorInfo = ErrorHandler::handleDatabaseError($e, 'clientes/api.php');
    ErrorHandler::logError($e->getMessage(), ['exception' => $e->getMessage(), 'code' => $e->getCode()]);
    $debug = defined('APP_DEBUG') && APP_DEBUG;
    echo ErrorHandler::jsonResponse($errorInfo, 500, $debug);
} catch (Exception $e) {
    ob_end_clean();
    $errorInfo = ErrorHandler::handleException($e, 'clientes/api.php');
    ErrorHandler::logError($e->getMessage(), ['exception' => $e->getMessage()]);
    $debug = defined('APP_DEBUG') && APP_DEBUG;
    echo ErrorHandler::jsonResponse($errorInfo, 400, $debug);
}
?>

