<?php
/**
 * Configuración de Base de Datos
 * Sistema de Gestión de Reciclaje
 * Tesis de Grado
 */

// Cargar variables de entorno
function loadEnv($filePath) {
    if (!file_exists($filePath)) {
        // En lugar de lanzar excepción, solo registrar en el log
        error_log("Advertencia: El archivo .env no existe en: " . $filePath);
        error_log("Usando valores por defecto para la conexión de base de datos.");
        return false;
    }

    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }

        // Validar que la línea tenga el formato correcto
        if (strpos($line, '=') === false) {
            continue;
        }

        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);

        if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
            putenv(sprintf('%s=%s', $name, $value));
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
    return true;
}

// Configuración de Base de Datos - Valores directos (sin .env)
// Si necesitas usar .env, descomenta las líneas siguientes y comenta esta sección

// =====================================================
// CONFIGURACIÓN DIRECTA DE BASE DE DATOS
// =====================================================
$dbHost = 'mysql-hermanosyanez.alwaysdata.net';
$dbPort = '3306';
$dbName = 'hermanosyanez_base';
$dbUser = '438328';
$dbPass = 'belen.jayron.tesis';

// =====================================================
// OPCIONAL: Cargar desde .env si existe (comentado por defecto)
// =====================================================
/*
$envPath = __DIR__ . '/../.env';
if (file_exists($envPath)) {
    $envLoaded = loadEnv($envPath);
    if ($envLoaded) {
        // Sobrescribir con valores de .env si existen
        $dbHost = getenv('DB_HOST') ?: $dbHost;
        $dbPort = getenv('DB_PORT') ?: $dbPort;
        $dbName = getenv('DB_NAME') ?: $dbName;
        $dbUser = getenv('DB_USER') ?: $dbUser;
        $dbPass = getenv('DB_PASS') ?: $dbPass;
    }
}
*/

if (empty($dbHost) || empty($dbName) || empty($dbUser)) {
    throw new Exception('Las variables de conexión DB_HOST, DB_NAME y DB_USER son requeridas');
}

define('DB_HOST', $dbHost);
define('DB_PORT', $dbPort ?: '3306');
define('DB_NAME', $dbName);
define('DB_USER', $dbUser);
define('DB_PASS', $dbPass ?: '');

// Configuración de la Aplicación (valores directos)
define('APP_NAME', 'Sistema de Gestión de Reciclaje');
define('APP_ENV', 'production');
define('APP_DEBUG', false); // Cambiar a true para ver errores detallados

// Configuración de Sesión
define('SESSION_LIFETIME', '120'); // minutos

/**
 * Clase de Conexión a Base de Datos
 */
class Database {
    private static $instance = null;
    private $conn;
    
    private $host;
    private $port;
    private $dbname;
    private $username;
    private $password;

    private function __construct() {
        $this->host = DB_HOST;
        $this->port = DB_PORT;
        $this->dbname = DB_NAME;
        $this->username = DB_USER;
        $this->password = DB_PASS;

        try {
            $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->dbname};charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
            ];

            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
            
            if (APP_DEBUG) {
                error_log("Conexión a base de datos establecida correctamente");
            }
        } catch (PDOException $e) {
            error_log("Error de conexión a la base de datos: " . $e->getMessage());
            // En lugar de die(), lanzar excepción para que pueda ser capturada
            throw new Exception("Error de conexión a la base de datos: " . (APP_DEBUG ? $e->getMessage() : "Error de conexión"));
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->conn;
    }

    // Prevenir clonación
    private function __clone() {}

    // Prevenir deserialización
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}

// Función helper para obtener conexión
function getDB() {
    return Database::getInstance()->getConnection();
}

// Cargar ErrorHandler automáticamente
if (file_exists(__DIR__ . '/ErrorHandler.php')) {
    require_once __DIR__ . '/ErrorHandler.php';
}

// Ejemplo de uso:
// $db = getDB();
// $stmt = $db->prepare("SELECT * FROM usuarios WHERE id = ?");
// $stmt->execute([$id]);
// $usuario = $stmt->fetch();
?>

