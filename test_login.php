<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

echo "<h2>Test de Conexión y Login</h2>";

// --- 1. Test .env ---
$envPath = __DIR__ . '/.env';
echo "<h3>1. Archivo .env</h3>";
if (file_exists($envPath)) {
    echo "<p style='color:green'>✓ .env encontrado en: $envPath</p>";
} else {
    echo "<p style='color:red'>✗ .env NO encontrado (buscado en: $envPath)</p>";
    echo "<p style='color:orange'>Se usarán credenciales por defecto del código</p>";
}

// --- 2. Cargar database.php ---
echo "<h3>2. Carga de database.php</h3>";
try {
    require_once __DIR__ . '/config/database.php';
    echo "<p style='color:green'>✓ database.php cargado correctamente</p>";
    echo "<p>DB_HOST: " . DB_HOST . "</p>";
    echo "<p>DB_NAME: " . DB_NAME . "</p>";
    echo "<p>DB_USER: " . DB_USER . "</p>";
} catch (Exception $e) {
    echo "<p style='color:red'>✗ Error cargando database.php: " . $e->getMessage() . "</p>";
    exit;
}

// --- 3. Test conexión BD (raw PDO para ver error real) ---
echo "<h3>3. Conexión a Base de Datos</h3>";
try {
    $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    echo "<p style='color:green'>✓ Conexión exitosa a la BD</p>";
    $db = $pdo;
} catch (PDOException $e) {
    echo "<p style='color:red'>✗ Error PDO real: " . $e->getMessage() . "</p>";
    echo "<p>DSN usado: mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . "</p>";
    exit;
}

// --- 4. Test tabla usuarios ---
echo "<h3>4. Tabla usuarios</h3>";
try {
    $stmt = $db->query("SELECT COUNT(*) as total FROM usuarios");
    $result = $stmt->fetch();
    echo "<p style='color:green'>✓ Tabla usuarios existe — Total registros: " . $result['total'] . "</p>";
} catch (Exception $e) {
    echo "<p style='color:red'>✗ Error en tabla usuarios: " . $e->getMessage() . "</p>";
    exit;
}

// --- 5. Test usuario gerente ---
echo "<h3>5. Usuario gerente@sistema.com</h3>";
try {
    $stmt = $db->prepare("SELECT id, nombre, email, estado FROM usuarios WHERE email = ?");
    $stmt->execute(['gerente@sistema.com']);
    $usuario = $stmt->fetch();
    if ($usuario) {
        echo "<p style='color:green'>✓ Usuario encontrado: " . $usuario['nombre'] . " (estado: " . $usuario['estado'] . ")</p>";
    } else {
        echo "<p style='color:red'>✗ Usuario gerente@sistema.com NO existe en la BD</p>";
    }
} catch (Exception $e) {
    echo "<p style='color:red'>✗ Error: " . $e->getMessage() . "</p>";
}

// --- 6. Test login completo ---
echo "<h3>6. Login con gerente@sistema.com</h3>";
try {
    require_once __DIR__ . '/config/auth.php';
    $auth = new Auth();
    $resultado = $auth->login('gerente@sistema.com', 'Gerente123!');
    if ($resultado['success']) {
        echo "<p style='color:green'>✓ Login exitoso — Rol: " . $resultado['usuario']['rol'] . "</p>";
    } else {
        echo "<p style='color:red'>✗ Login fallido: " . $resultado['message'] . "</p>";
    }
} catch (Exception $e) {
    echo "<p style='color:red'>✗ Error en Auth: " . $e->getMessage() . "</p>";
}

echo "<hr><p><small>Test ejecutado: " . date('Y-m-d H:i:s') . "</small></p>";
?>
