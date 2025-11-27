<?php
/**
 * API para restablecimiento de contraseña
 * Sistema de Gestión de Reciclaje
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Manejar preflight OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/database.php';

// Obtener acción
$action = $_GET['action'] ?? $_POST['action'] ?? '';

try {
    $auth = new Auth();
    
    switch ($action) {
        case 'solicitar':
            // Solicitar recuperación de contraseña
            $email = $_POST['email'] ?? '';
            
            if (empty($email)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'El correo electrónico es requerido'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }
            
            $resultado = $auth->solicitarRecuperacion($email);
            echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
            break;
            
        case 'verificar':
            // Verificar token
            $token = $_GET['token'] ?? '';
            
            if (empty($token)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Token requerido'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }
            
            $resultado = $auth->verificarToken($token);
            echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
            break;
            
        case 'restablecer':
            // Restablecer contraseña
            $token = $_POST['token'] ?? '';
            $password = $_POST['password'] ?? '';
            
            if (empty($token) || empty($password)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Token y contraseña son requeridos'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }
            
            $resultado = $auth->restablecerPassword($token, $password);
            echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
            break;
            
        default:
            echo json_encode([
                'success' => false,
                'message' => 'Acción no válida'
            ], JSON_UNESCAPED_UNICODE);
            break;
    }
    
} catch (Exception $e) {
    error_log("Error en password-reset.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error al procesar la solicitud'
    ], JSON_UNESCAPED_UNICODE);
}

?>

