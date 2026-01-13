<?php
require_once 'config/database.php';

$db = getDB();

// Verificar usuario
$stmt = $db->prepare("
    SELECT u.id, u.nombre, u.email, u.sucursal_id, s.nombre as sucursal_nombre 
    FROM usuarios u 
    LEFT JOIN sucursales s ON u.sucursal_id = s.id 
    WHERE u.email = 'gerente@sistema.com'
");
$stmt->execute();
$usuario = $stmt->fetch();

echo "<h2>Usuario: " . $usuario['nombre'] . "</h2>";
echo "<p>Email: " . $usuario['email'] . "</p>";
echo "<p>Sucursal ID: " . ($usuario['sucursal_id'] ?? 'NULL') . "</p>";
echo "<p>Sucursal Nombre: " . ($usuario['sucursal_nombre'] ?? 'Sin sucursal asignada') . "</p>";

// Si no tiene sucursal, asignar Florida
if (empty($usuario['sucursal_id'])) {
    echo "<hr>";
    echo "<h3>Asignando sucursal...</h3>";
    
    // Obtener ID de Florida
    $stmt = $db->query("SELECT id, nombre FROM sucursales WHERE nombre = 'Florida' LIMIT 1");
    $sucursal = $stmt->fetch();
    
    if ($sucursal) {
        $stmt = $db->prepare("UPDATE usuarios SET sucursal_id = ? WHERE email = 'gerente@sistema.com'");
        $stmt->execute([$sucursal['id']]);
        echo "<p style='color: green;'>✓ Sucursal '{$sucursal['nombre']}' asignada correctamente</p>";
        echo "<p><strong>Ahora cierra sesión y vuelve a iniciar sesión para ver el cambio</strong></p>";
    } else {
        echo "<p style='color: red;'>No se encontró la sucursal 'Florida'</p>";
        
        // Listar sucursales disponibles
        echo "<h4>Sucursales disponibles:</h4>";
        $stmt = $db->query("SELECT id, nombre FROM sucursales");
        while($s = $stmt->fetch()) {
            echo "- ID: {$s['id']}, Nombre: {$s['nombre']}<br>";
        }
    }
}
?>
