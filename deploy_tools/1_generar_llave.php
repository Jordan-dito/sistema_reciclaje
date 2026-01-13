<?php
/**
 * HERRAMIENTA 1: GENERADOR DE LLAVE RSA
 * Sube este archivo a tu servidor y ejecútalo para obtener tu llave pública.
 */
header('Content-Type: text/html; charset=utf-8');

$ssh_dir = $_SERVER['HOME'] . '/.ssh';
$key_file = $ssh_dir . '/id_rsa';

if (!is_dir($ssh_dir)) {
    mkdir($ssh_dir, 0700, true);
}

// Generar llave si no existe o regenerar forzosamente
if (isset($_GET['regenerate']) || !file_exists($key_file)) {
    @unlink($key_file);
    @unlink($key_file . '.pub');
    // -N "" asegura que no tenga contraseña
    $cmd = "ssh-keygen -t rsa -b 4096 -f " . escapeshellarg($key_file) . " -N '' 2>&1";
    exec($cmd, $output, $return_var);
}

$pub_key = file_exists($key_file . '.pub') ? file_get_contents($key_file . '.pub') : "Error: No se pudo generar la llave.";
?>
<!DOCTYPE html>
<html>
<head>
    <title>1. Generador de Llave RSA</title>
    <style>
        body { font-family: sans-serif; padding: 20px; background: #f0f2f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        textarea { width: 100%; height: 150px; font-family: monospace; padding: 10px; border: 1px solid #ccc; border-radius: 4px; font-size: 14px; }
        .btn { display: inline-block; padding: 10px 20px; background: #2ea44f; color: white; text-decoration: none; border-radius: 6px; font-weight: bold; }
        .note { background: #fff8c5; padding: 15px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #d4a72c; }
        h2 { color: #333; }
    </style>
</head>
<body>
    <div class="container">
        <h2>🔑 Paso 1: Tu Llave Pública (RSA)</h2>
        
        <div class="note">
            <strong>Instrucciones:</strong><br>
            1. Haz clic en la caja de abajo.<br>
            2. Presiona <b>Ctrl + A</b> (seleccionar todo) y luego <b>Ctrl + C</b> (copiar).<br>
            3. Ve a tu repositorio en <b>GitHub > Settings > Deploy keys</b>.<br>
            4. Añade una nueva llave, pega el contenido y <b>MARCA "Allow write access"</b>.
        </div>

        <textarea readonly onclick="this.select()"><?php echo trim($pub_key); ?></textarea>
        
        <br><br>
        <p>
            <a href="?regenerate=true" style="color: red; font-size: 12px;">⚠️ Generar una nueva llave (Borrará la anterior)</a>
        </p>
    </div>
</body>
</html>
