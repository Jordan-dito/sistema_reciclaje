<?php
/**
 * Script para forzar actualización y limpieza de sesión
 */

require_once __DIR__ . '/config/database.php';

// Iniciar sesión para poder destruirla
session_start();

try {
    $db = getDB();
    
    echo "<h2>🔧 Proceso de actualización completo</h2>";
    
    // PASO 1: Actualizar directamente en la base de datos
    echo "<h3>Paso 1: Actualizando base de datos...</h3>";
    
    $updateQuery = "UPDATE usuarios SET sucursal_id = 4 WHERE email = 'clarizza_belen@hotmail.com'";
    $stmt = $db->prepare($updateQuery);
    $result = $stmt->execute();
    
    echo "<p>Query ejecutado: <code>$updateQuery</code></p>";
    echo "<p>Resultado: " . ($result ? "✅ Éxito" : "❌ Error") . "</p>";
    echo "<p>Filas afectadas: " . $stmt->rowCount() . "</p>";
    
    // PASO 2: Verificar el cambio
    echo "<h3>Paso 2: Verificando cambio en BD...</h3>";
    
    $verifyQuery = "
        SELECT u.id, u.nombre, u.email, u.sucursal_id, s.id as suc_id, s.nombre as sucursal_nombre 
        FROM usuarios u 
        LEFT JOIN sucursales s ON u.sucursal_id = s.id 
        WHERE u.email = 'clarizza_belen@hotmail.com'
    ";
    
    $stmt = $db->prepare($verifyQuery);
    $stmt->execute();
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr><th style='padding: 8px; background: #333; color: white;'>Campo</th><th style='padding: 8px; background: #333; color: white;'>Valor</th></tr>";
    echo "<tr><td style='padding: 8px;'>ID Usuario</td><td style='padding: 8px;'><strong>{$usuario['id']}</strong></td></tr>";
    echo "<tr><td style='padding: 8px;'>Nombre</td><td style='padding: 8px;'><strong>{$usuario['nombre']}</strong></td></tr>";
    echo "<tr><td style='padding: 8px;'>Email</td><td style='padding: 8px;'><strong>{$usuario['email']}</strong></td></tr>";
    echo "<tr><td style='padding: 8px;'>sucursal_id (en usuarios)</td><td style='padding: 8px;'><strong style='color: " . ($usuario['sucursal_id'] ? 'green' : 'red') . ";'>" . ($usuario['sucursal_id'] ?? 'NULL') . "</strong></td></tr>";
    echo "<tr><td style='padding: 8px;'>suc_id (de JOIN)</td><td style='padding: 8px;'><strong style='color: " . ($usuario['suc_id'] ? 'green' : 'red') . ";'>" . ($usuario['suc_id'] ?? 'NULL') . "</strong></td></tr>";
    echo "<tr><td style='padding: 8px;'>sucursal_nombre</td><td style='padding: 8px;'><strong style='color: " . ($usuario['sucursal_nombre'] ? 'green' : 'red') . "; font-size: 16px;'>" . ($usuario['sucursal_nombre'] ?? 'NULL') . "</strong></td></tr>";
    echo "</table>";
    
    if ($usuario['sucursal_nombre']) {
        echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
        echo "<strong>✅ La sucursal está correctamente asignada en la BD: {$usuario['sucursal_nombre']}</strong>";
        echo "</div>";
    } else {
        echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
        echo "<strong>❌ ERROR: La sucursal NO está asignada o no existe</strong>";
        echo "</div>";
        
        // Mostrar sucursales disponibles
        echo "<h4>Sucursales en la tabla:</h4>";
        $stmt = $db->query("SELECT id, nombre FROM sucursales");
        $sucursales = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "<ul>";
        foreach ($sucursales as $suc) {
            echo "<li>ID: {$suc['id']} - {$suc['nombre']}</li>";
        }
        echo "</ul>";
    }
    
    // PASO 3: Destruir la sesión actual
    echo "<h3>Paso 3: Limpiando sesión actual...</h3>";
    
    echo "<p>Sesión actual antes de limpiar:</p>";
    echo "<pre style='background: #f8f9fa; padding: 10px; border-radius: 5px;'>";
    print_r($_SESSION);
    echo "</pre>";
    
    // Destruir completamente la sesión
    session_unset();
    session_destroy();
    
    // Crear una nueva sesión limpia
    session_start();
    session_regenerate_id(true);
    
    echo "<div style='background: #fff3cd; color: #856404; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<strong>✅ Sesión destruida y regenerada</strong>";
    echo "</div>";
    
    // PASO 4: Instrucciones finales
    echo "<div style='background: #d1ecf1; color: #0c5460; padding: 20px; border-radius: 5px; margin: 30px 0;'>";
    echo "<h3>📋 Instrucciones Finales - IMPORTANTE:</h3>";
    echo "<ol style='line-height: 2;'>";
    echo "<li><strong>Haz clic en el botón \"CERRAR SESIÓN Y LIMPIAR\" abajo</strong></li>";
    echo "<li><strong>Cierra COMPLETAMENTE el navegador</strong> (todas las pestañas)</li>";
    echo "<li>Abre el navegador de nuevo</li>";
    echo "<li>Ingresa a: <strong>http://localhost/sistema_reciclaje</strong></li>";
    echo "<li>Inicia sesión con: <strong>clarizza_belen@hotmail.com</strong></li>";
    echo "<li>Verifica el perfil - debería aparecer la sucursal</li>";
    echo "</ol>";
    echo "</div>";
    
    echo "<div style='text-align: center; margin: 30px 0;'>";
    echo "<a href='config/logout.php' style='background: #dc3545; color: white; padding: 15px 40px; text-decoration: none; border-radius: 5px; font-size: 18px; font-weight: bold; display: inline-block;'>🔐 CERRAR SESIÓN Y LIMPIAR</a>";
    echo "</div>";
    
    echo "<hr style='margin: 40px 0;'>";
    
    // Mostrar todos los usuarios
    echo "<h3>Todos los usuarios con sus sucursales:</h3>";
    $stmt = $db->query("
        SELECT u.id, u.nombre, u.email, s.nombre as sucursal_nombre 
        FROM usuarios u 
        LEFT JOIN sucursales s ON u.sucursal_id = s.id 
        ORDER BY u.id
    ");
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr>";
    echo "<th style='padding: 10px; background: #1572e8; color: white;'>ID</th>";
    echo "<th style='padding: 10px; background: #1572e8; color: white;'>Nombre</th>";
    echo "<th style='padding: 10px; background: #1572e8; color: white;'>Email</th>";
    echo "<th style='padding: 10px; background: #1572e8; color: white;'>Sucursal</th>";
    echo "</tr>";
    
    foreach ($usuarios as $usr) {
        $sucursal = $usr['sucursal_nombre'] ?? '<em style="color: red;">Sin asignar</em>';
        $bgColor = $usr['sucursal_nombre'] ? '#d4edda' : '#f8d7da';
        echo "<tr style='background: $bgColor;'>";
        echo "<td style='padding: 8px;'>{$usr['id']}</td>";
        echo "<td style='padding: 8px;'><strong>{$usr['nombre']}</strong></td>";
        echo "<td style='padding: 8px;'>{$usr['email']}</td>";
        echo "<td style='padding: 8px;'><strong>{$sucursal}</strong></td>";
        echo "</tr>";
    }
    echo "</table>";
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 20px; border-radius: 5px;'>";
    echo "<h3>❌ Error:</h3>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<pre style='background: white; padding: 10px; border-radius: 3px; overflow-x: auto;'>";
    echo htmlspecialchars($e->getTraceAsString());
    echo "</pre>";
    echo "</div>";
}
?>

<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        padding: 40px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
    }
    h2, h3 {
        color: #333;
    }
    h2 {
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    h3 {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 5px;
        border-left: 5px solid #1572e8;
        margin-top: 30px;
    }
    table {
        background: white;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    code {
        background: #f8f9fa;
        padding: 2px 6px;
        border-radius: 3px;
        font-family: 'Courier New', monospace;
        color: #e83e8c;
    }
</style>
