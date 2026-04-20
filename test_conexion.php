<?php
// ARCHIVO TEMPORAL DE DIAGNÓSTICO - ELIMINAR DESPUÉS DE VERIFICAR
header('Content-Type: text/html; charset=utf-8');

$results = [];

// 1. PHP Version
$results['php_version'] = ['label' => 'PHP Version', 'value' => PHP_VERSION, 'ok' => version_compare(PHP_VERSION, '7.4', '>=')];

// 2. .env file
$envPath = __DIR__ . '/.env';
$envExists = file_exists($envPath);
$results['env_file'] = ['label' => 'Archivo .env', 'value' => $envExists ? 'Encontrado en ' . $envPath : 'NO ENCONTRADO en ' . $envPath, 'ok' => $envExists];

// 3. Cargar .env manualmente
$envVars = [];
if ($envExists) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if (str_starts_with($line, '#') || strpos($line, '=') === false) continue;
        [$k, $v] = explode('=', $line, 2);
        $envVars[trim($k)] = trim($v);
    }
}

$dbHost = $envVars['DB_HOST'] ?? 'localhost';
$dbPort = $envVars['DB_PORT'] ?? '3306';
$dbName = $envVars['DB_NAME'] ?? '';
$dbUser = $envVars['DB_USER'] ?? '';
$dbPass = $envVars['DB_PASS'] ?? '';

$results['db_host']  = ['label' => 'DB_HOST', 'value' => $dbHost, 'ok' => !empty($dbHost)];
$results['db_name']  = ['label' => 'DB_NAME', 'value' => $dbName, 'ok' => !empty($dbName)];
$results['db_user']  = ['label' => 'DB_USER', 'value' => $dbUser, 'ok' => !empty($dbUser)];
$results['db_pass']  = ['label' => 'DB_PASS', 'value' => empty($dbPass) ? '(vacío)' : str_repeat('*', strlen($dbPass)), 'ok' => !empty($dbPass)];

// 4. Conexión a BD
try {
    $dsn = "mysql:host={$dbHost};port={$dbPort};dbname={$dbName};charset=utf8mb4";
    $pdo = new PDO($dsn, $dbUser, $dbPass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $results['db_connect'] = ['label' => 'Conexión MySQL', 'value' => 'CONECTADO correctamente', 'ok' => true];

    // 5. Contar tablas
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $results['db_tables'] = ['label' => 'Tablas encontradas', 'value' => count($tables) . ' tablas: ' . implode(', ', $tables), 'ok' => count($tables) > 0];

    // 6. Probar tabla usuarios
    if (in_array('usuarios', $tables)) {
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM usuarios");
        $row = $stmt->fetch();
        $results['usuarios'] = ['label' => 'Tabla usuarios', 'value' => $row['total'] . ' registros', 'ok' => true];
    } else {
        $results['usuarios'] = ['label' => 'Tabla usuarios', 'value' => 'NO EXISTE - BD posiblemente vacía', 'ok' => false];
    }

} catch (PDOException $e) {
    $results['db_connect'] = ['label' => 'Conexión MySQL', 'value' => 'ERROR: ' . $e->getMessage(), 'ok' => false];
}

// 7. URL actual
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$currentUrl = $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$results['url'] = ['label' => 'URL actual', 'value' => $currentUrl, 'ok' => true];

// 8. Extensions PHP
foreach (['pdo', 'pdo_mysql', 'mbstring', 'json', 'openssl'] as $ext) {
    $results['ext_' . $ext] = ['label' => "Extensión $ext", 'value' => extension_loaded($ext) ? 'Habilitada' : 'FALTANTE', 'ok' => extension_loaded($ext)];
}

$pass = array_filter($results, fn($r) => $r['ok']);
$fail = array_filter($results, fn($r) => !$r['ok']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Diagnóstico del Sistema</title>
<style>
  body { font-family: monospace; background: #1a1a2e; color: #eee; padding: 20px; }
  h1 { color: #4ecca3; }
  .result { padding: 8px 12px; margin: 4px 0; border-radius: 4px; display: flex; gap: 12px; }
  .ok   { background: #1a3a2a; border-left: 4px solid #4ecca3; }
  .fail { background: #3a1a1a; border-left: 4px solid #e74c3c; }
  .label { min-width: 200px; font-weight: bold; }
  .summary { margin: 20px 0; padding: 10px; background: #16213e; border-radius: 6px; }
  .warn { color: #f39c12; margin-top: 20px; font-size: 12px; }
</style>
</head>
<body>
<h1>Diagnostico del Sistema - Reciclaje</h1>
<div class="summary">
  <span style="color:#4ecca3">✓ <?= count($pass) ?> OK</span> &nbsp;|&nbsp;
  <span style="color:#e74c3c">✗ <?= count($fail) ?> FALLIDOS</span>
</div>
<?php foreach ($results as $r): ?>
<div class="result <?= $r['ok'] ? 'ok' : 'fail' ?>">
  <span><?= $r['ok'] ? '✓' : '✗' ?></span>
  <span class="label"><?= htmlspecialchars($r['label']) ?></span>
  <span><?= htmlspecialchars($r['value']) ?></span>
</div>
<?php endforeach; ?>
<p class="warn">⚠ ELIMINAR este archivo (test_conexion.php) una vez verificado el funcionamiento.</p>
</body>
</html>
