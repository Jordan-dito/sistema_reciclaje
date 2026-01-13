<?php
/**
 * Endpoint para el login
 * Sistema de Gestión de Reciclaje
 */

// Desactivar mostrar errores directamente (solo log)
error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');

// Configurar headers CORS para permitir peticiones desde Flutter/móviles
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json; charset=utf-8');

// Manejar preflight OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

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

// Obtener datos - soporta tanto form-data como JSON
$email = '';
$password = '';

// Intentar leer JSON primero (para Flutter)
$contentType = $_SERVER['CONTENT_TYPE'] ?? '';
if (strpos($contentType, 'application/json') !== false) {
    // Flutter/envío JSON
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    if ($data) {
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';
    }
} else {
    // Web/envío form-data (FormData) - compatibilidad con login web existente
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
}

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
    
    // Si el login fue exitoso, mejorar la URL de la foto de perfil
    if ($resultado['success'] && isset($resultado['usuario']['foto_perfil_ruta']) && !empty($resultado['usuario']['foto_perfil_ruta'])) {
        // Construir URL completa de la foto
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || 
                    (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')) 
                    ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? 'localhost';
        
        // Obtener el path base del proyecto
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        $scriptDir = dirname($scriptName); // config/
        $scriptDir = dirname($scriptDir);  // raíz del proyecto
        $scriptDir = rtrim($scriptDir, '/');
        
        $baseUrl = $protocol . '://' . $host . $scriptDir;
        $fotoRuta = $resultado['usuario']['foto_perfil_ruta'];
        $resultado['usuario']['foto_perfil'] = $baseUrl . '/' . ltrim($fotoRuta, '/');
    }
    
    echo json_encode($resultado, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
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

