<?php
/**
 * API para gestión de usuarios
 * Sistema de Gestión de Reciclaje
 */

// Desactivar mostrar errores directamente (solo log)
error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');

// Configurar headers JSON
header('Content-Type: application/json; charset=utf-8');

// Capturar cualquier salida no deseada
ob_start();

try {
    // Verificar autenticación
    require_once __DIR__ . '/../config/auth.php';
    require_once __DIR__ . '/../config/validaciones.php';

    $auth = new Auth();
    if (!$auth->isAuthenticated()) {
        ob_end_clean();
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'No autorizado']);
        exit;
    }

    // Obtener método y acción
    $method = $_SERVER['REQUEST_METHOD'];
    
    // La acción puede venir en GET o POST
    $action = $_GET['action'] ?? $_POST['action'] ?? '';
    
    if (empty($action)) {
        throw new Exception('Acción no especificada');
    }

    $db = getDB();
    
    switch ($method) {
        case 'GET':
            if ($action === 'listar') {
                // Listar todos los usuarios
                $stmt = $db->query("
                    SELECT u.*, r.nombre as rol_nombre 
                    FROM usuarios u 
                    INNER JOIN roles r ON u.rol_id = r.id 
                    WHERE u.estado <> 'inactivo'
                    ORDER BY u.id DESC
                ");
                $usuarios = $stmt->fetchAll();
                
                // Limpiar cualquier salida no deseada antes de enviar JSON
                ob_end_clean();
                
                echo json_encode([
                    'success' => true,
                    'data' => $usuarios
                ]);
            } elseif ($action === 'obtener') {
                // Obtener un usuario por ID
                $id = $_GET['id'] ?? 0;
                $stmt = $db->prepare("
                    SELECT u.*, r.nombre as rol_nombre 
                    FROM usuarios u 
                    INNER JOIN roles r ON u.rol_id = r.id 
                    WHERE u.id = ?
                ");
                $stmt->execute([$id]);
                $usuario = $stmt->fetch();
                
                // Limpiar cualquier salida no deseada antes de enviar JSON
                ob_end_clean();
                
                if ($usuario) {
                    // No enviar la contraseña
                    unset($usuario['password']);
                    echo json_encode([
                        'success' => true,
                        'data' => $usuario
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Usuario no encontrado'
                    ]);
                }
            }
            break;
            
        case 'POST':
            if ($action === 'crear') {
                // Crear nuevo usuario
                $nombre = trim($_POST['nombre'] ?? '');
                $cedula = trim($_POST['cedula'] ?? '');
                $email = trim($_POST['email'] ?? '');
                $telefono = trim($_POST['telefono'] ?? '');
                $password = $_POST['password'] ?? '';
                $rol_id = intval($_POST['rol_id'] ?? 0);
                $estado = $_POST['estado'] ?? 'activo';
                
                // Validaciones
                if (empty($nombre) || empty($cedula) || empty($email) || empty($password)) {
                    throw new Exception('Todos los campos obligatorios deben estar completos');
                }
                
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    throw new Exception('Email inválido');
                }
                
                if (strlen($password) < 8) {
                    throw new Exception('La contraseña debe tener al menos 8 caracteres');
                }
                
                // Validar cédula ecuatoriana
                $validacionCedula = validarCedulaEcuatoriana($cedula);
                if (!$validacionCedula['valid']) {
                    throw new Exception($validacionCedula['message']);
                }
                
                // Verificar si el email ya existe
                $stmt = $db->prepare("SELECT id FROM usuarios WHERE email = ?");
                $stmt->execute([$email]);
                if ($stmt->fetch()) {
                    throw new Exception('El correo electrónico ya está registrado');
                }
                
                // Verificar si la cédula ya existe
                $stmt = $db->prepare("SELECT id FROM usuarios WHERE cedula = ?");
                $stmt->execute([$cedula]);
                if ($stmt->fetch()) {
                    throw new Exception('La cédula ya está registrada');
                }
                
                // Verificar que el rol existe
                $stmt = $db->prepare("SELECT id FROM roles WHERE id = ?");
                $stmt->execute([$rol_id]);
                if (!$stmt->fetch()) {
                    throw new Exception('Rol inválido');
                }
                
                // Hash de la contraseña
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                
                // Insertar usuario
                $stmt = $db->prepare("
                    INSERT INTO usuarios (nombre, email, cedula, password, telefono, rol_id, estado) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)
                ");
                
                $stmt->execute([
                    $nombre,
                    $email,
                    $cedula,
                    $password_hash,
                    $telefono ?: null,
                    $rol_id,
                    $estado
                ]);
                
                $usuario_id = $db->lastInsertId();
                
                // Limpiar cualquier salida no deseada antes de enviar JSON
                ob_end_clean();
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Usuario creado exitosamente',
                    'id' => $usuario_id
                ]);
            } elseif ($action === 'actualizar') {
                // Actualizar usuario
                $id = intval($_POST['id'] ?? 0);
                $nombre = trim($_POST['nombre'] ?? '');
                $cedula = trim($_POST['cedula'] ?? '');
                $email = trim($_POST['email'] ?? '');
                $telefono = trim($_POST['telefono'] ?? '');
                $password = $_POST['password'] ?? '';
                $rol_id = intval($_POST['rol_id'] ?? 0);
                $estado = $_POST['estado'] ?? 'activo';
                
                // Validaciones
                if (empty($nombre) || empty($cedula) || empty($email)) {
                    throw new Exception('Todos los campos obligatorios deben estar completos');
                }
                
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    throw new Exception('Email inválido');
                }
                
                // Validar cédula ecuatoriana
                $validacionCedula = validarCedulaEcuatoriana($cedula);
                if (!$validacionCedula['valid']) {
                    throw new Exception($validacionCedula['message']);
                }
                
                // Verificar si el email ya existe en otro usuario
                $stmt = $db->prepare("SELECT id FROM usuarios WHERE email = ? AND id != ?");
                $stmt->execute([$email, $id]);
                if ($stmt->fetch()) {
                    throw new Exception('El correo electrónico ya está registrado en otro usuario');
                }
                
                // Verificar si la cédula ya existe en otro usuario
                $stmt = $db->prepare("SELECT id FROM usuarios WHERE cedula = ? AND id != ?");
                $stmt->execute([$cedula, $id]);
                if ($stmt->fetch()) {
                    throw new Exception('La cédula ya está registrada en otro usuario');
                }
                
                // Verificar que el rol existe
                $stmt = $db->prepare("SELECT id FROM roles WHERE id = ?");
                $stmt->execute([$rol_id]);
                if (!$stmt->fetch()) {
                    throw new Exception('Rol inválido');
                }
                
                // Actualizar usuario
                if (!empty($password)) {
                    if (strlen($password) < 8) {
                        throw new Exception('La contraseña debe tener al menos 8 caracteres');
                    }
                    $password_hash = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $db->prepare("
                        UPDATE usuarios 
                        SET nombre = ?, email = ?, cedula = ?, password = ?, telefono = ?, rol_id = ?, estado = ?
                        WHERE id = ?
                    ");
                    $stmt->execute([
                        $nombre,
                        $email,
                        $cedula,
                        $password_hash,
                        $telefono ?: null,
                        $rol_id,
                        $estado,
                        $id
                    ]);
                } else {
                    $stmt = $db->prepare("
                        UPDATE usuarios 
                        SET nombre = ?, email = ?, cedula = ?, telefono = ?, rol_id = ?, estado = ?
                        WHERE id = ?
                    ");
                    $stmt->execute([
                        $nombre,
                        $email,
                        $cedula,
                        $telefono ?: null,
                        $rol_id,
                        $estado,
                        $id
                    ]);
                }
                
                // Limpiar cualquier salida no deseada antes de enviar JSON
                ob_end_clean();
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Usuario actualizado exitosamente'
                ]);
            } elseif ($action === 'eliminar') {
                // Eliminar usuario (soft delete - cambiar estado a inactivo)
                $id = intval($_POST['id'] ?? 0);
                
                if ($id <= 0) {
                    throw new Exception('ID de usuario inválido');
                }
                
                // No permitir eliminar el último administrador
                $stmt = $db->prepare("
                    SELECT COUNT(*) as total 
                    FROM usuarios u 
                    INNER JOIN roles r ON u.rol_id = r.id 
                    WHERE r.nombre = 'Administrador' AND u.estado = 'activo'
                ");
                $stmt->execute();
                $admin_count = $stmt->fetch()['total'];
                
                $stmt = $db->prepare("
                    SELECT r.nombre as rol_nombre 
                    FROM usuarios u 
                    INNER JOIN roles r ON u.rol_id = r.id 
                    WHERE u.id = ?
                ");
                $stmt->execute([$id]);
                $usuario = $stmt->fetch();
                
                if ($usuario && $usuario['rol_nombre'] === 'Administrador' && $admin_count <= 1) {
                    throw new Exception('No se puede eliminar el último administrador activo');
                }
                
                // Cambiar estado a inactivo (soft delete)
                $stmt = $db->prepare("UPDATE usuarios SET estado = 'inactivo' WHERE id = ?");
                $stmt->execute([$id]);
                
                // Limpiar cualquier salida no deseada antes de enviar JSON
                ob_end_clean();
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Usuario desactivado exitosamente'
                ]);
            } else {
                throw new Exception('Acción no válida');
            }
            break;
            
        default:
            ob_end_clean();
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    }
    
} catch (PDOException $e) {
    ob_end_clean();
    error_log("Error en usuarios/api.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error de base de datos: ' . (APP_DEBUG ? $e->getMessage() : 'Error al procesar la solicitud')
    ]);
} catch (Exception $e) {
    ob_end_clean();
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>

