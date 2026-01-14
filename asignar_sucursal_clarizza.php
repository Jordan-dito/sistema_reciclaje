<?php
/**
 * Script para asignar sucursal al usuario Clarizza
 */

require_once __DIR__ . '/config/database.php';

try {
    $db = getDB();
    
    // Obtener el usuario Clarizza
    $stmt = $db->prepare("SELECT id, nombre, email, sucursal_id FROM usuarios WHERE email = 'clarizza_belen@hotmail.com'");
    $stmt->execute();
    $usuario = $stmt->fetch();
    
    if (!$usuario) {
        die("Usuario Clarizza no encontrado");
    }
    
    echo "<h2>Usuario encontrado:</h2>";
    echo "<p><strong>ID:</strong> {$usuario['id']}</p>";
    echo "<p><strong>Nombre:</strong> {$usuario['nombre']}</p>";
    echo "<p><strong>Email:</strong> {$usuario['email']}</p>";
    echo "<p><strong>Sucursal ID actual:</strong> " . ($usuario['sucursal_id'] ?? 'NULL') . "</p>";
    
    // Obtener todas las sucursales
    $stmt = $db->query("SELECT id, nombre FROM sucursales ORDER BY id");
    $sucursales = $stmt->fetchAll();
    
    if (empty($sucursales)) {
        die("<p style='color: red;'>No hay sucursales disponibles en el sistema</p>");
    }
    
    echo "<h3>Sucursales disponibles:</h3>";
    echo "<ul>";
    foreach ($sucursales as $suc) {
        echo "<li>ID: {$suc['id']} - {$suc['nombre']}</li>";
    }
    echo "</ul>";
    
    // Si ya se envió el formulario
    if (isset($_POST['asignar'])) {
        $sucursalId = intval($_POST['sucursal_id']);
        
        $stmt = $db->prepare("UPDATE usuarios SET sucursal_id = ? WHERE id = ?");
        if ($stmt->execute([$sucursalId, $usuario['id']])) {
            echo "<div style='background: #d4edda; color: #155724; padding: 15px; border: 1px solid #c3e6cb; border-radius: 5px; margin: 20px 0;'>";
            echo "<strong>✅ ¡Sucursal asignada correctamente!</strong><br>";
            echo "El usuario {$usuario['nombre']} ahora tiene asignada la sucursal.<br><br>";
            echo "<a href='../index.php' style='background: #1572e8; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Ir al sistema</a> ";
            echo "<a href='asignar_sucursal_clarizza.php' style='background: #6c757d; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-left: 10px;'>Recargar</a>";
            echo "</div>";
            echo "<p><strong>Instrucciones:</strong> Cierra la sesión del usuario Clarizza y vuelve a iniciar sesión para ver la sucursal en el perfil.</p>";
        } else {
            echo "<p style='color: red;'>❌ Error al asignar la sucursal</p>";
        }
    }
    
    // Formulario
    if (!isset($_POST['asignar'])) {
        echo "<hr>";
        echo "<h3>Asignar sucursal a {$usuario['nombre']}</h3>";
        echo "<form method='POST' style='background: #f8f9fa; padding: 20px; border-radius: 5px;'>";
        echo "<label style='display: block; margin-bottom: 10px;'><strong>Seleccione la sucursal:</strong></label>";
        echo "<select name='sucursal_id' style='padding: 8px; font-size: 14px; margin-bottom: 15px; width: 300px;' required>";
        foreach ($sucursales as $suc) {
            echo "<option value='{$suc['id']}'>{$suc['nombre']}</option>";
        }
        echo "</select><br>";
        echo "<button type='submit' name='asignar' style='background: #1572e8; color: white; padding: 10px 30px; border: none; border-radius: 5px; cursor: pointer; font-size: 14px;'>Asignar Sucursal</button>";
        echo "</form>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>

<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        padding: 40px;
        background: #f5f5f5;
        max-width: 800px;
        margin: 0 auto;
    }
    h2, h3 {
        color: #333;
    }
    ul {
        background: white;
        padding: 20px;
        border-radius: 5px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
</style>
