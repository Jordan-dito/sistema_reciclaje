<?php
/**
 * API para obtener el HTML del sidebar dinámicamente
 * Permite actualizar el sidebar sin recargar la página completa
 */

session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/modulos_por_rol.php';

header('Content-Type: text/html; charset=utf-8');

$basePath = isset($_GET['basePath']) ? $_GET['basePath'] : '..';
$currentRoute = isset($_GET['currentRoute']) ? $_GET['currentRoute'] : '';

// Asegurar que el basePath termine con /
$basePath = rtrim($basePath, '/');
$basePath = $basePath !== '' ? $basePath . '/' : '';

// Incluir el sidebar
ob_start();
include __DIR__ . '/sidebar.php';
$sidebarHtml = ob_get_clean();

echo $sidebarHtml;

