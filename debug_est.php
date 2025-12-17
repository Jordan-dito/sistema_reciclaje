<?php
require_once __DIR__ . '/config/database.php';
$db = getDB();

echo "<h1>Estructura de la tabla usuarios</h1>";
$stmt = $db->query("DESCRIBE usuarios");
$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "<pre>";
print_r($columns);
echo "</pre>";

echo "<h1>Usuario ID: " . ($_SESSION['usuario_id'] ?? 'No logueado') . "</h1>";

if (isset($_SESSION['usuario_id'])) {
    echo "<h1>Sucursales donde es responsable</h1>";
    $stmt = $db->prepare("SELECT * FROM sucursales WHERE responsable_id = ?");
    $stmt->execute([$_SESSION['usuario_id']]);
    $sucursales = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<pre>";
    print_r($sucursales);
    echo "</pre>";
}
?>