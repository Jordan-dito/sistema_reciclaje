<?php
/**
 * Script para actualizar sucursal_id del usuario Clarizza
 */

require_once __DIR__ . '/config/database.php';

try {
    $db = getDB();
    
    echo "<h2>Actualizando sucursal del usuario Clarizza...</h2>";
    
    // Actualizar el usuario Clarizza (ID: 5) para que tenga la sucursal Montebello (ID: 4)
    $stmt = $db->prepare("UPDATE usuarios SET sucursal_id = 4 WHERE id = 5");
    
    if ($stmt->execute()) {
        echo "<div style='background: #d4edda; color: #155724; padding: 20px; border: 1px solid #c3e6cb; border-radius: 5px; margin: 20px 0;'>";
        echo "<h3>✅ ¡Sucursal actualizada correctamente!</h3>";
        echo "<p>El usuario <strong>Clarizza Suarez</strong> ahora tiene asignada la sucursal <strong>Montebello</strong>.</p>";
        echo "</div>";
        
        // Verificar el cambio
        $stmt = $db->prepare("
            SELECT u.id, u.nombre, u.email, u.sucursal_id, s.nombre as sucursal_nombre 
            FROM usuarios u 
            LEFT JOIN sucursales s ON u.sucursal_id = s.id 
            WHERE u.id = 5
        ");
        $stmt->execute();
        $usuario = $stmt->fetch();
        
        echo "<h3>Datos actualizados:</h3>";
        echo "<table border='1' style='border-collapse: collapse; margin: 20px 0;'>";
        echo "<tr><th style='padding: 10px; background: #1572e8; color: white;'>Campo</th><th style='padding: 10px; background: #1572e8; color: white;'>Valor</th></tr>";
        echo "<tr><td style='padding: 10px;'>ID Usuario</td><td style='padding: 10px;'>{$usuario['id']}</td></tr>";
        echo "<tr><td style='padding: 10px;'>Nombre</td><td style='padding: 10px;'>{$usuario['nombre']}</td></tr>";
        echo "<tr><td style='padding: 10px;'>Email</td><td style='padding: 10px;'>{$usuario['email']}</td></tr>";
        echo "<tr><td style='padding: 10px;'>Sucursal ID</td><td style='padding: 10px;'><strong>{$usuario['sucursal_id']}</strong></td></tr>";
        echo "<tr><td style='padding: 10px;'>Sucursal Nombre</td><td style='padding: 10px;'><strong style='color: #1572e8;'>{$usuario['sucursal_nombre']}</strong></td></tr>";
        echo "</table>";
        
        echo "<div style='background: #fff3cd; color: #856404; padding: 15px; border: 1px solid #ffeeba; border-radius: 5px; margin: 20px 0;'>";
        echo "<h4>📋 Próximos pasos:</h4>";
        echo "<ol>";
        echo "<li>El usuario <strong>Clarizza</strong> debe <strong>cerrar sesión</strong></li>";
        echo "<li>Volver a <strong>iniciar sesión</strong></li>";
        echo "<li>La sucursal <strong>Montebello</strong> aparecerá debajo del rol \"Gerente\" en el perfil</li>";
        echo "</ol>";
        echo "</div>";
        
        echo "<p style='text-align: center; margin-top: 30px;'>";
        echo "<a href='index.php' style='background: #1572e8; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; font-size: 16px;'>Ir al Sistema</a>";
        echo "</p>";
        
    } else {
        echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border: 1px solid #f5c6cb; border-radius: 5px;'>";
        echo "❌ Error al actualizar la sucursal";
        echo "</div>";
    }
    
    // Mostrar todos los usuarios con sus sucursales
    echo "<hr style='margin: 40px 0;'>";
    echo "<h3>Todos los usuarios y sus sucursales:</h3>";
    $stmt = $db->query("
        SELECT u.id, u.nombre, u.email, s.nombre as sucursal_nombre 
        FROM usuarios u 
        LEFT JOIN sucursales s ON u.sucursal_id = s.id 
        ORDER BY u.id
    ");
    $usuarios = $stmt->fetchAll();
    
    echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 20px 0;'>";
    echo "<tr>";
    echo "<th style='padding: 10px; background: #1572e8; color: white;'>ID</th>";
    echo "<th style='padding: 10px; background: #1572e8; color: white;'>Nombre</th>";
    echo "<th style='padding: 10px; background: #1572e8; color: white;'>Email</th>";
    echo "<th style='padding: 10px; background: #1572e8; color: white;'>Sucursal</th>";
    echo "</tr>";
    
    foreach ($usuarios as $usr) {
        $sucursal = $usr['sucursal_nombre'] ?? '<em style="color: #999;">Sin asignar</em>';
        echo "<tr>";
        echo "<td style='padding: 8px;'>{$usr['id']}</td>";
        echo "<td style='padding: 8px;'>{$usr['nombre']}</td>";
        echo "<td style='padding: 8px;'>{$usr['email']}</td>";
        echo "<td style='padding: 8px;'>{$sucursal}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border: 1px solid #f5c6cb; border-radius: 5px;'>";
    echo "<strong>Error:</strong> " . htmlspecialchars($e->getMessage());
    echo "</div>";
}
?>

<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        padding: 40px;
        background: #f5f5f5;
        max-width: 1000px;
        margin: 0 auto;
    }
    h2, h3, h4 {
        color: #333;
    }
    table {
        background: white;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    tr:nth-child(even) {
        background: #f8f9fa;
    }
</style>
