<?php
/**
 * Endpoint para obtener datos del usuario actual
 * Sistema de Gestión de Reciclaje
 * Para uso con Flutter y actualización de datos en tiempo real
 */

// Desactivar mostrar errores directamente (solo log)
error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');

// Configurar headers CORS para permitir peticiones desde Flutter/móviles
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
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
    require_once __DIR__ . '/database.php';
} catch (Exception $e) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error del servidor: ' . (defined('APP_DEBUG') && APP_DEBUG ? $e->getMessage() : 'No se pudo conectar al servidor')
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// Limpiar cualquier salida no deseada antes de enviar JSON
ob_end_clean();

try {
    // Verificar autenticación
    $auth = new Auth();
    
    // Intentar autenticación por sesión (web) o por token/ID (Flutter)
    $usuarioId = null;
    
    // Método 1: Sesión (para web)
    if ($auth->isAuthenticated() && isset($_SESSION['usuario_id'])) {
        $usuarioId = $_SESSION['usuario_id'];
    } 
    // Método 2: Token o ID en POST/GET (para Flutter)
    else {
        // Intentar obtener desde POST (JSON o form-data)
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (strpos($contentType, 'application/json') !== false) {
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);
            if ($data) {
                $usuarioId = $data['usuario_id'] ?? $data['id'] ?? null;
            }
        } else {
            $usuarioId = $_POST['usuario_id'] ?? $_POST['id'] ?? null;
        }
        
        // Si no está en POST, intentar GET
        if (!$usuarioId) {
            $usuarioId = $_GET['usuario_id'] ?? $_GET['id'] ?? null;
        }
    }
    
    if (!$usuarioId) {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'message' => 'No autorizado. Debe iniciar sesión o proporcionar un ID de usuario válido.'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    // Obtener datos actualizados del usuario desde la base de datos
    $db = getDB();
    $stmt = $db->prepare("
        SELECT u.*, r.nombre as rol_nombre, r.id as rol_id
        FROM usuarios u 
        INNER JOIN roles r ON u.rol_id = r.id 
        WHERE u.id = ? AND u.estado = 'activo'
    ");
    $stmt->execute([$usuarioId]);
    $usuario = $stmt->fetch();
    
    if (!$usuario) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Usuario no encontrado o inactivo'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    // Obtener foto de perfil (puede estar actualizada)
    $fotoPerfil = isset($usuario['foto_perfil']) && !empty($usuario['foto_perfil']) 
        ? $usuario['foto_perfil'] 
        : null;
    
    // Construir URL completa de la foto de perfil
    $fotoPerfilUrl = null;
    if ($fotoPerfil) {
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
        $fotoPerfilUrl = $baseUrl . '/' . ltrim($fotoPerfil, '/');
    }
    
    // Actualizar sesión si está autenticado por sesión
    if ($auth->isAuthenticated() && isset($_SESSION['usuario_id']) && $_SESSION['usuario_id'] == $usuarioId) {
        $_SESSION['usuario_foto_perfil'] = $fotoPerfil;
        $_SESSION['usuario_nombre'] = $usuario['nombre'];
        $_SESSION['usuario_email'] = $usuario['email'];
        $_SESSION['usuario_rol'] = $usuario['rol_nombre'];
    }
    
    // Preparar respuesta
    $response = [
        'success' => true,
        'message' => 'Datos del usuario obtenidos exitosamente',
        'usuario' => [
            'id' => intval($usuario['id']),
            'nombre' => $usuario['nombre'],
            'email' => $usuario['email'],
            'cedula' => $usuario['cedula'] ?? '',
            'telefono' => $usuario['telefono'] ?? '',
            'rol' => $usuario['rol_nombre'],
            'rol_id' => intval($usuario['rol_id']),
            'foto_perfil' => $fotoPerfilUrl, // URL completa
            'foto_perfil_ruta' => $fotoPerfil, // Ruta relativa
            'estado' => $usuario['estado'] ?? 'activo',
            'fecha_creacion' => $usuario['fecha_creacion'] ?? null,
            'fecha_actualizacion' => $usuario['fecha_actualizacion'] ?? null
        ]
    ];
    
    echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    
} catch (PDOException $e) {
    error_log("Error en get_user.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener datos del usuario. Intenta más tarde.'
    ], JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    error_log("Error en get_user.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error del servidor: ' . (defined('APP_DEBUG') && APP_DEBUG ? $e->getMessage() : 'Error interno del servidor')
    ], JSON_UNESCAPED_UNICODE);
}
?>

