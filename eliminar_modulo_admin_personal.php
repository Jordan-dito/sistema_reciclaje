<?php
require_once 'config/database.php';

$db = getDB();

try {
    // Buscar el módulo "Administración de Personal"
    $stmt = $db->prepare("SELECT * FROM modulos WHERE nombre = 'Administración de Personal'");
    $stmt->execute();
    $modulo = $stmt->fetch();
    
    if ($modulo) {
        echo "<h3>Módulo encontrado:</h3>";
        echo "<pre>";
        print_r($modulo);
        echo "</pre>";
        
        // Eliminar el módulo
        $stmt = $db->prepare("DELETE FROM modulos WHERE nombre = 'Administración de Personal'");
        $stmt->execute();
        
        echo "<p style='color: green;'>✓ Módulo 'Administración de Personal' eliminado exitosamente</p>";
        
        // También eliminar relaciones si existen
        $stmt = $db->prepare("DELETE FROM modulos_roles WHERE modulo_id = ?");
        $stmt->execute([$modulo['id']]);
        
        echo "<p style='color: green;'>✓ Relaciones del módulo eliminadas</p>";
    } else {
        echo "<p style='color: orange;'>El módulo 'Administración de Personal' no existe en la base de datos</p>";
    }
    
    echo "<hr>";
    echo "<h3>Módulos actuales:</h3>";
    $stmt = $db->query("SELECT id, nombre, icono, orden FROM modulos ORDER BY orden");
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Nombre</th><th>Icono</th><th>Orden</th></tr>";
    while ($m = $stmt->fetch()) {
        echo "<tr><td>{$m['id']}</td><td>{$m['nombre']}</td><td>{$m['icono']}</td><td>{$m['orden']}</td></tr>";
    }
    echo "</table>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>
