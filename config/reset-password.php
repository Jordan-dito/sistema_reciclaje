<?php
/**
 * Redirección para restablecer contraseña
 * Este archivo redirige a reset-password.php en la raíz del proyecto
 * para mantener compatibilidad con URLs antiguas
 */

// Obtener el token de la URL
$token = $_GET['token'] ?? '';

// Construir la URL de redirección hacia la raíz
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';

// Obtener el directorio base (subir un nivel desde /config)
$scriptDir = dirname($_SERVER['PHP_SELF']);
$basePath = dirname($scriptDir);

// Normalizar la ruta base
if ($basePath === '/' || $basePath === '\\' || $basePath === '.') {
    $basePath = '';
} else {
    $basePath = '/' . ltrim($basePath, '/');
}

// Redirigir al archivo correcto en la raíz
$redirectUrl = $protocol . '://' . $host . $basePath . '/reset-password.php';
if ($token) {
    $redirectUrl .= '?token=' . urlencode($token);
}

header('Location: ' . $redirectUrl);
exit;

