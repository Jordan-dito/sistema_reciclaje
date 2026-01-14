<?php
/**
 * Script temporal de depuración para verificar sucursales y usuario
 */

require_once __DIR__ . '/config/database.php';

try {
    $db = getDB();
    
    echo "<h2>Sucursales Disponibles:</h2>";
    $stmt = $db->query("SELECT id, nombre FROM sucursales ORDER BY id");
    $sucursales = $stmt->fetchAll();
    
    echo "<table border='1' style='border-collapse: collapse; margin: 10px;'>";
    echo "<tr><th style='padding: 8px;'>ID</th><th style='padding: 8px;'>Nombre</th></tr>";
    foreach ($sucursales as $suc) {
        echo "<tr><td style='padding: 8px;'>{$suc['id']}</td><td style='padding: 8px;'>{$suc['nombre']}</td></tr>";
    }
    echo "</table>";
    
    echo "<h2>Usuarios y sus Sucursales:</h2>";
    $stmt = $db->query("SELECT u.id, u.nombre, u.email, u.sucursal_id, s.nombre as sucursal_nombre 
                        FROM usuarios u 
                        LEFT JOIN sucursales s ON u.sucursal_id = s.id 
                        ORDER BY u.id");
    $usuarios = $stmt->fetchAll();
    
    echo "<table border='1' style='border-collapse: collapse; margin: 10px;'>";
    echo "<tr><th style='padding: 8px;'>ID Usuario</th><th style='padding: 8px;'>Nombre</th><th style='padding: 8px;'>Email</th><th style='padding: 8px;'>Sucursal ID</th><th style='padding: 8px;'>Sucursal Nombre</th></tr>";
    foreach ($usuarios as $usr) {
        $sucId = $usr['sucursal_id'] ?? 'NULL';
        $sucNombre = $usr['sucursal_nombre'] ?? 'Sin asignar';
        echo "<tr><td style='padding: 8px;'>{$usr['id']}</td><td style='padding: 8px;'>{$usr['nombre']}</td><td style='padding: 8px;'>{$usr['email']}</td><td style='padding: 8px;'>{$sucId}</td><td style='padding: 8px;'>{$sucNombre}</td></tr>";
    }
    echo "</table>";
    
    // Buscar específicamente el usuario Clarizza
    echo "<h2>Usuario Clarizza:</h2>";
    $stmt = $db->prepare("SELECT u.*, s.nombre as sucursal_nombre 
                          FROM usuarios u 
                          LEFT JOIN sucursales s ON u.sucursal_id = s.id 
                          WHERE u.email = 'clarizza_belen@hotmail.com'");
    $stmt->execute();
    $clarizza = $stmt->fetch();
    
    if ($clarizza) {
        echo "<pre>";
        print_r($clarizza);
        echo "</pre>";
        
        // Si no tiene sucursal, asignar la primera disponible
        if (empty($clarizza['sucursal_id']) && !empty($sucursales)) {
            echo "<h3>⚠️ Usuario sin sucursal asignada. ¿Desea asignar una?</h3>";
            echo "<form method='POST'>";
            echo "Seleccione sucursal: <select name='sucursal_id'>";
            foreach ($sucursales as $suc) {
                echo "<option value='{$suc['id']}'>{$suc['nombre']}</option>";
            }
            echo "</select>";
            echo "<input type='hidden' name='usuario_id' value='{$clarizza['id']}'>";
            echo " <button type='submit' name='asignar'>Asignar Sucursal</button>";
            echo "</form>";
        }
    }
    
    // Procesar asignación
    if (isset($_POST['asignar']) && isset($_POST['usuario_id']) && isset($_POST['sucursal_id'])) {
        $usuarioId = intval($_POST['usuario_id']);
        $sucursalId = intval($_POST['sucursal_id']);
        
        $stmt = $db->prepare("UPDATE usuarios SET sucursal_id = ? WHERE id = ?");
        if ($stmt->execute([$sucursalId, $usuarioId])) {
            echo "<p style='color: green; font-weight: bold;'>✅ Sucursal asignada correctamente. <a href='debug_sucursales_usuario.php'>Recargar</a></p>";
        } else {
            echo "<p style='color: red;'>❌ Error al asignar sucursal</p>";
        }
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>
<style>
    body { font-family: Arial, sans-serif; padding: 20px; }
    h2 { color: #333; margin-top: 30px; }
    table { margin: 20px 0; }
    th { background-color: #1572e8; color: white; }
    tr:nth-child(even) { background-color: #f8f9fa; }
</style>
