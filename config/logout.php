<?php
/**
 * Endpoint para el logout
 * Sistema de Gestión de Reciclaje
 */

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Limpiar todas las variables de sesión
$_SESSION = array();

// Destruir la cookie de sesión si existe
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Destruir la sesión
session_destroy();

// Limpiar cookies relacionadas con la sesión
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Redirigir al login (usar ruta absoluta desde la raíz del sitio)
// Calcular la ruta base del sitio
$scriptPath = $_SERVER['SCRIPT_NAME']; // Ej: /config/logout.php
$basePath = dirname(dirname($scriptPath)); // Subir dos niveles: /config -> /
$basePath = $basePath === '/' ? '' : $basePath; // Si es raíz, dejar vacío

// Construir URL absoluta
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$loginUrl = $protocol . '://' . $host . $basePath . '/index.php';

header('Location: ' . $loginUrl);
exit;
?>

