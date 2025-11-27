<?php
/**
 * Redirección para restablecer contraseña
 * Este archivo redirige a reset-password.php en la raíz del proyecto
 * para mantener compatibilidad con URLs antiguas
 */

// Obtener el token de la URL
$token = $_GET['token'] ?? '';

// Construir la URL de redirección hacia la raíz
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || 
             (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')) 
             ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';

// Obtener el directorio base del proyecto
// Si estamos en /config/reset-password.php, necesitamos subir un nivel
$scriptPath = $_SERVER['SCRIPT_NAME'] ?? $_SERVER['PHP_SELF'] ?? '';
$scriptDir = dirname($scriptPath);

// Si estamos en /config, subir un nivel
if (basename($scriptDir) === 'config' || strpos($scriptDir, '/config') !== false) {
    $basePath = dirname($scriptDir);
} else {
    $basePath = $scriptDir;
}

// Normalizar la ruta base
if ($basePath === '/' || $basePath === '\\' || $basePath === '.' || empty($basePath)) {
    $basePath = '';
} else {
    // Asegurar que comience con / y no termine con /
    $basePath = '/' . trim($basePath, '/');
}

// Construir la URL completa
$redirectUrl = $protocol . '://' . $host . $basePath . '/reset-password.php';
if ($token) {
    $redirectUrl .= '?token=' . urlencode($token);
}

// Redirigir con código 301 (permanente) para mejor SEO
header('Location: ' . $redirectUrl, true, 301);
exit;

