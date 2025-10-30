<?php
/**
 * Endpoint para el logout
 * Sistema de Gestión de Reciclaje
 */

require_once __DIR__ . '/auth.php';

$auth = new Auth();
$auth->logout();

header('Location: index.php');
exit;
?>

