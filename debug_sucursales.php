<?php
require_once __DIR__ . '/config/database.php';
$db = getDB();

echo "--- USUARIOS ---\n";
$stmt = $db->query("SELECT id, nombre, email, rol_id FROM usuarios");
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($usuarios as $u) {
    echo "ID: {$u['id']} | Nombre: {$u['nombre']} | Rol: {$u['rol_id']}\n";
}

echo "\n--- SUCURSALES ---\n";
$stmt = $db->query("SELECT id, nombre, responsable_id, estado FROM sucursales");
$sucursales = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($sucursales as $s) {
    echo "ID: {$s['id']} | Nombre: {$s['nombre']} | Responsable ID: {$s['responsable_id']} | Estado: {$s['estado']}\n";
}
?>