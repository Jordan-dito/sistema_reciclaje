<?php
/**
 * Endpoint para el registro de usuarios
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
    require_once __DIR__ . '/database.php';
} catch (Exception $e) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error del servidor: ' . (APP_DEBUG ? $e->getMessage() : 'No se pudo conectar al servidor')
    ]);
    exit;
}

// Solo permitir método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido'
    ]);
    exit;
}

// Obtener datos del formulario
$nombre = trim($_POST['nombre'] ?? '');
$cedula = trim($_POST['cedula'] ?? '');
$email = trim($_POST['email'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');
$password = $_POST['password'] ?? '';

// Validación básica
if (empty($nombre) || empty($cedula) || empty($email) || empty($password)) {
    ob_end_clean();
    echo json_encode([
        'success' => false,
        'message' => 'Por favor, completa todos los campos obligatorios'
    ]);
    exit;
}

// Validar formato de email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    ob_end_clean();
    echo json_encode([
        'success' => false,
        'message' => 'Por favor, ingresa un correo electrónico válido'
    ]);
    exit;
}

// Validar cédula (solo números, 10-20 dígitos)
if (!preg_match('/^[0-9]{10,20}$/', $cedula)) {
    ob_end_clean();
    echo json_encode([
        'success' => false,
        'message' => 'La cédula debe contener solo números (10-20 dígitos)'
    ]);
    exit;
}

// Validar contraseña
if (strlen($password) < 8) {
    ob_end_clean();
    echo json_encode([
        'success' => false,
        'message' => 'La contraseña debe tener al menos 8 caracteres'
    ]);
    exit;
}

// Limpiar cualquier salida no deseada antes de enviar JSON
ob_end_clean();

try {
    $db = getDB();
    
    // Verificar si el email ya existe
    $stmt = $db->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        echo json_encode([
            'success' => false,
            'message' => 'El correo electrónico ya está registrado'
        ]);
        exit;
    }
    
    // Verificar si la cédula ya existe
    $stmt = $db->prepare("SELECT id FROM usuarios WHERE cedula = ?");
    $stmt->execute([$cedula]);
    if ($stmt->fetch()) {
        echo json_encode([
            'success' => false,
            'message' => 'La cédula ya está registrada'
        ]);
        exit;
    }
    
    // Obtener el rol por defecto (Administrador - rol_id = 1)
    $stmt = $db->prepare("SELECT id FROM roles WHERE nombre = 'Administrador' LIMIT 1");
    $stmt->execute();
    $rol = $stmt->fetch();
    
    if (!$rol) {
        // Si no existe el rol Administrador, buscar por id = 1
        $stmt = $db->prepare("SELECT id FROM roles WHERE id = 1 LIMIT 1");
        $stmt->execute();
        $rol = $stmt->fetch();
    }
    
    if (!$rol) {
        // Si no existe, usar el primer rol disponible
        $stmt = $db->prepare("SELECT id FROM roles ORDER BY id LIMIT 1");
        $stmt->execute();
        $rol = $stmt->fetch();
    }
    
    if (!$rol) {
        echo json_encode([
            'success' => false,
            'message' => 'Error: No se encontró un rol válido para asignar'
        ]);
        exit;
    }
    
    $rol_id = $rol['id'];
    
    // Hash de la contraseña
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    
    // Insertar nuevo usuario
    $stmt = $db->prepare("
        INSERT INTO usuarios (nombre, email, cedula, password, telefono, rol_id, estado) 
        VALUES (?, ?, ?, ?, ?, ?, 'activo')
    ");
    
    $stmt->execute([$nombre, $email, $cedula, $password_hash, $telefono ?: null, $rol_id]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Registro exitoso. Ahora puedes iniciar sesión.'
    ]);
    
} catch (PDOException $e) {
    error_log("Error en register.php: " . $e->getMessage());
    
    // Verificar si es error de duplicado
    if ($e->getCode() == 23000) {
        $errorMsg = $e->getMessage();
        if (strpos($errorMsg, 'email') !== false) {
            $message = 'El correo electrónico ya está registrado';
        } elseif (strpos($errorMsg, 'cedula') !== false) {
            $message = 'La cédula ya está registrada';
        } else {
            $message = 'El usuario ya existe en el sistema';
        }
    } else {
        $message = 'Error al registrar usuario. Por favor, intenta más tarde.';
    }
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $message
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error del servidor. Por favor, intenta más tarde.'
    ]);
    error_log("Error en register.php: " . $e->getMessage());
}
?>


