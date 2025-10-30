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

// Cargar el archivo .env (si existe)
$envPath = __DIR__ . '/../.env';
loadEnv($envPath);

// Configuración de Base de Datos
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_PORT', getenv('DB_PORT') ?: '3306');
define('DB_NAME', getenv('DB_NAME') ?: 'sistema_reciclaje');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');

// Configuración de la Aplicación
define('APP_NAME', getenv('APP_NAME') ?: 'Sistema de Gestión de Reciclaje');
define('APP_ENV', getenv('APP_ENV') ?: 'development');
define('APP_DEBUG', getenv('APP_DEBUG') === 'true' ? true : false);

// Configuración de Sesión
define('SESSION_LIFETIME', getenv('SESSION_LIFETIME') ?: '120');

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

// Ejemplo de uso:
// $db = getDB();
// $stmt = $db->prepare("SELECT * FROM usuarios WHERE id = ?");
// $stmt->execute([$id]);
// $usuario = $stmt->fetch();
?>

