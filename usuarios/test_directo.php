<?php
/**
 * Script de prueba directo sin autenticación
 * Para verificar que el API funciona
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');
header('Content-Type: text/html; charset=utf-8');

echo "<h2>Prueba Directa del API de Usuarios</h2>";

// Simular sesión para prueba
session_start();
$_SESSION['logged_in'] = true;
$_SESSION['usuario_id'] = 1;
$_SESSION['login_time'] = time();

echo "<h3>1. Estado de Sesión:</h3>";
echo "Session ID: " . session_id() . "<br>";
echo "logged_in: " . (isset($_SESSION['logged_in']) ? 'true' : 'false') . "<br>";
echo "usuario_id: " . (isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : 'no definido') . "<br>";

echo "<h3>2. Probando API directamente:</h3>";

// Capturar la salida del API
ob_start();
$_GET['action'] = 'listar';
$_SERVER['REQUEST_METHOD'] = 'GET';

try {
    include __DIR__ . '/api.php';
    $output = ob_get_clean();
    
    echo "<h4>Respuesta del API:</h4>";
    echo "<pre style='background: #f5f5f5; padding: 10px; border: 1px solid #ddd;'>";
    echo htmlspecialchars($output);
    echo "</pre>";
    
    // Intentar decodificar JSON
    $json = json_decode($output, true);
    if ($json) {
        echo "<h4>JSON Decodificado:</h4>";
        echo "<pre style='background: #e8f5e9; padding: 10px; border: 1px solid #4caf50;'>";
        print_r($json);
        echo "</pre>";
    } else {
        echo "<p style='color: red;'>⚠ No se pudo decodificar como JSON. Puede haber un error en la salida.</p>";
    }
} catch (Exception $e) {
    ob_end_clean();
    echo "<p style='color: red;'>✗ Error: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<hr>";
echo "<h3>3. Prueba sin sesión (debería dar 401):</h3>";

// Limpiar sesión
session_unset();
$_SESSION = [];

ob_start();
$_GET['action'] = 'listar';
$_SERVER['REQUEST_METHOD'] = 'GET';

try {
    include __DIR__ . '/api.php';
    $output = ob_get_clean();
    
    echo "<pre style='background: #fff3cd; padding: 10px; border: 1px solid #ffc107;'>";
    echo htmlspecialchars($output);
    echo "</pre>";
    
    $json = json_decode($output, true);
    if ($json) {
        echo "<pre>";
        print_r($json);
        echo "</pre>";
    }
} catch (Exception $e) {
    ob_end_clean();
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>

