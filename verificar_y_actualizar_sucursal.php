<?php
/**
 * Script de actualización forzada de sucursal
 */

require_once __DIR__ . '/config/database.php';

try {
    $db = getDB();
    
    echo "<h2>Verificando y actualizando sucursales...</h2>";
    
    // 1. Verificar estado actual
    echo "<h3>1. Estado actual de Clarizza:</h3>";
    $stmt = $db->prepare("SELECT id, nombre, email, sucursal_id FROM usuarios WHERE email = 'clarizza_belen@hotmail.com'");
    $stmt->execute();
    $usuario = $stmt->fetch();
    
    echo "<pre>";
    print_r($usuario);
    echo "</pre>";
    
    // 2. Actualizar con sucursal Montebello (ID: 4)
    echo "<h3>2. Actualizando sucursal a Montebello (ID: 4)...</h3>";
    $stmt = $db->prepare("UPDATE usuarios SET sucursal_id = 4 WHERE email = 'clarizza_belen@hotmail.com'");
    $result = $stmt->execute();
    
    if ($result) {
        echo "<p style='color: green; font-weight: bold;'>✅ UPDATE ejecutado correctamente</p>";
        echo "<p>Filas afectadas: " . $stmt->rowCount() . "</p>";
    } else {
        echo "<p style='color: red;'>❌ Error en UPDATE</p>";
    }
    
    // 3. Verificar después del UPDATE
    echo "<h3>3. Verificando después del UPDATE:</h3>";
    $stmt = $db->prepare("SELECT id, nombre, email, sucursal_id FROM usuarios WHERE email = 'clarizza_belen@hotmail.com'");
    $stmt->execute();
    $usuario = $stmt->fetch();
    
    echo "<pre>";
    print_r($usuario);
    echo "</pre>";
    
    // 4. Verificar con JOIN (igual que en auth.php)
    echo "<h3>4. Verificando con JOIN (como en login):</h3>";
    $stmt = $db->prepare("
        SELECT u.*, r.nombre as rol_nombre, r.permisos as rol_permisos, s.nombre as sucursal_nombre, s.id as suc_id
        FROM usuarios u 
        INNER JOIN roles r ON u.rol_id = r.id 
        LEFT JOIN sucursales s ON u.sucursal_id = s.id
        WHERE u.email = 'clarizza_belen@hotmail.com'
    ");
    $stmt->execute();
    $usuario = $stmt->fetch();
    
    echo "<pre>";
    print_r($usuario);
    echo "</pre>";
    
    if (!empty($usuario['sucursal_nombre'])) {
        echo "<div style='background: #d4edda; color: #155724; padding: 20px; border-radius: 5px; margin: 20px 0;'>";
        echo "<h3>✅ ¡Sucursal encontrada correctamente!</h3>";
        echo "<p><strong>Sucursal:</strong> {$usuario['sucursal_nombre']}</p>";
        echo "<p><strong>ID Sucursal:</strong> {$usuario['suc_id']}</p>";
        echo "</div>";
    } else {
        echo "<div style='background: #f8d7da; color: #721c24; padding: 20px; border-radius: 5px; margin: 20px 0;'>";
        echo "<h3>⚠️ No se encontró sucursal</h3>";
        echo "<p>El usuario tiene sucursal_id = {$usuario['sucursal_id']}, pero no se encontró en la tabla sucursales</p>";
        echo "</div>";
    }
    
    // 5. Verificar que la sucursal existe
    echo "<h3>5. Verificando que la sucursal ID 4 existe:</h3>";
    $stmt = $db->prepare("SELECT * FROM sucursales WHERE id = 4");
    $stmt->execute();
    $sucursal = $stmt->fetch();
    
    if ($sucursal) {
        echo "<p style='color: green;'>✅ La sucursal existe:</p>";
        echo "<pre>";
        print_r($sucursal);
        echo "</pre>";
    } else {
        echo "<p style='color: red;'>❌ La sucursal ID 4 NO existe</p>";
        
        // Mostrar todas las sucursales
        echo "<h4>Sucursales disponibles:</h4>";
        $stmt = $db->query("SELECT * FROM sucursales");
        $sucursales = $stmt->fetchAll();
        echo "<pre>";
        print_r($sucursales);
        echo "</pre>";
    }
    
    echo "<hr style='margin: 30px 0;'>";
    echo "<div style='background: #fff3cd; color: #856404; padding: 20px; border-radius: 5px;'>";
    echo "<h3>📋 Instrucciones finales:</h3>";
    echo "<ol>";
    echo "<li><strong>Cierra completamente el navegador</strong> (no solo la pestaña)</li>";
    echo "<li>Abre el navegador nuevamente</li>";
    echo "<li>Ve a: <a href='config/logout.php'>http://localhost/sistema_reciclaje/config/logout.php</a></li>";
    echo "<li>Inicia sesión nuevamente con Clarizza</li>";
    echo "<li>Verifica el perfil de usuario</li>";
    echo "</ol>";
    echo "</div>";
    
    echo "<p style='text-align: center; margin-top: 30px;'>";
    echo "<a href='config/logout.php' style='background: #dc3545; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 10px;'>Cerrar Sesión</a>";
    echo "<a href='index.php' style='background: #1572e8; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 10px;'>Ir al Sistema</a>";
    echo "</p>";
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 20px; border-radius: 5px;'>";
    echo "<strong>Error:</strong> " . htmlspecialchars($e->getMessage());
    echo "<br><br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
    echo "</div>";
}
?>

<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        padding: 40px;
        background: #f5f5f5;
        max-width: 1200px;
        margin: 0 auto;
    }
    h2, h3, h4 {
        color: #333;
    }
    pre {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 5px;
        border-left: 4px solid #1572e8;
        overflow-x: auto;
    }
</style>
