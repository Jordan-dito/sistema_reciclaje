<?php
/**
 * Script para generar contraseñas hash
 * Ejecutar una vez para generar los hashes de las contraseñas
 * Sistema de Gestión de Reciclaje - Tesis
 */

echo "==========================================\n";
echo "Generador de Hashes de Contraseñas\n";
echo "Sistema de Gestión de Reciclaje\n";
echo "==========================================\n\n";

// Contraseñas por defecto
$passwords = [
    'admin' => 'Admin123!',
    'usuario' => 'Usuario123!'
];

echo "Generando hashes para las contraseñas por defecto:\n\n";

foreach ($passwords as $tipo => $password) {
    $hash = password_hash($password, PASSWORD_DEFAULT);
    echo "Tipo: {$tipo}\n";
    echo "Contraseña: {$password}\n";
    echo "Hash: {$hash}\n";
    echo "-------------------\n\n";
}

echo "==========================================\n";
echo "INSTRUCCIONES:\n";
echo "1. Copia los hashes generados\n";
echo "2. Actualiza el archivo database.sql con estos valores\n";
echo "3. O ejecuta este script en línea de comando para obtener los hashes\n";
echo "==========================================\n";

// Generar SQL actualizado
echo "\nSQL para actualizar contraseñas:\n\n";
echo "-- Actualizar contraseña de administrador\n";
echo "UPDATE usuarios SET password = '" . password_hash($passwords['admin'], PASSWORD_DEFAULT) . "' WHERE email = 'admin@sistema.com';\n\n";
echo "-- Actualizar contraseña de usuario\n";
echo "UPDATE usuarios SET password = '" . password_hash($passwords['usuario'], PASSWORD_DEFAULT) . "' WHERE email = 'usuario@sistema.com';\n\n";
?>

