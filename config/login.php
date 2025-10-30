<?php
/**
 * Endpoint para el login
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
    require_once __DIR__ . '/auth.php';
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
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

// Validación básica
if (empty($email) || empty($password)) {
    echo json_encode([
        'success' => false,
        'message' => 'Por favor, completa todos los campos'
    ]);
    exit;
}

// Validar formato de email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        'success' => false,
        'message' => 'Por favor, ingresa un correo electrónico válido'
    ]);
    exit;
}

// Limpiar cualquier salida no deseada antes de enviar JSON
ob_end_clean();

try {
    // Intentar login
    $auth = new Auth();
    $resultado = $auth->login($email, $password);
    
    echo json_encode($resultado);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al iniciar sesión. Por favor, intenta más tarde.'
    ]);
    error_log("Error en login.php: " . $e->getMessage());
} catch (Error $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error del servidor. Por favor, intenta más tarde.'
    ]);
    error_log("Error fatal en login.php: " . $e->getMessage());
}
?>

