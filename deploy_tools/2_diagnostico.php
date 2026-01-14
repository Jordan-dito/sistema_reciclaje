<?php
/**
 * HERRAMIENTA 2: DIAGNÓSTICO Y CONFIGURACIÓN SSH
 * Ejecuta esto DESPUÉS de haber agregado la llave en GitHub.
 */
header('Content-Type: text/plain; charset=utf-8');

$ssh_dir = $_SERVER['HOME'] . '/.ssh';
$key_file = $ssh_dir . '/id_rsa';
$config_file = $ssh_dir . '/config';

echo "--- 1. CONFIGURANDO PERMISOS Y ARCHIVOS ---\n";

// 1. Asegurar permisos estrictos (SSH los requiere)
if (!is_dir($ssh_dir)) mkdir($ssh_dir, 0700, true);
chmod($ssh_dir, 0700);
if (file_exists($key_file)) chmod($key_file, 0600);

// 2. Crear archivo 'config' para forzar el uso de la llave RSA
$config_content = "Host github.com\n  HostName github.com\n  User git\n  IdentityFile $ssh_dir/id_rsa\n  IdentitiesOnly yes\n";
file_put_contents($config_file, $config_content);
chmod($config_file, 0600);
echo "✅ Archivo de configuración SSH actualizado.\n";

// 3. Agregar GitHub a known_hosts (para evitar "Are you sure?")
$known_hosts = $ssh_dir . '/known_hosts';
exec("ssh-keyscan -t rsa,ed25519 github.com >> " . escapeshellarg($known_hosts) . " 2>&1");
echo "✅ GitHub añadido a hosts conocidos.\n";


echo "\n--- 2. PROBANDO CONEXIÓN CON GITHUB ---\n";
echo "Intentando conectar con GitHub...\n";

// Comando de prueba de conexión
$cmd = "ssh -v -i " . escapeshellarg($key_file) . " -o StrictHostKeyChecking=no -T git@github.com 2>&1";
exec($cmd, $output);

$auth_success = false;
foreach ($output as $line) {
    // Buscamos el mensaje de éxito
    if (strpos($line, 'successfully authenticated') !== false || strpos($line, 'Hi ') !== false) {
        $auth_success = true;
    }
}

echo implode("\n", $output);

echo "\n\n--- RESULTADO FINAL ---\n";
if ($auth_success) {
    echo "🎉 ¡CONEXIÓN EXITOSA!\n";
    echo "El servidor ya tiene permiso para descargar tu código.\n";
    echo "Pasa al siguiente script (3_vincular_git.php).";
} else {
    echo "❌ FALLÓ LA CONEXIÓN.\n";
    echo "Posibles causas:\n";
    echo "- No copiaste bien la llave en GitHub.\n";
    echo "- No marcaste 'Allow write access' en GitHub.\n";
    echo "- GitHub está bloqueando la IP (poco probable).";
}
?>
