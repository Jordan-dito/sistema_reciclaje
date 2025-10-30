<?php
/**
 * Sistema de Autenticación
 * Sistema de Gestión de Reciclaje
 * Tesis de Grado
 */

require_once __DIR__ . '/database.php';

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class Auth {
    private $db;

    public function __construct() {
        try {
            $this->db = getDB();
        } catch (Exception $e) {
            error_log("Error al conectar a la base de datos en Auth: " . $e->getMessage());
            throw new Exception("Error de conexión: No se pudo conectar a la base de datos");
        }
    }

    /**
     * Iniciar sesión
     */
    public function login($email, $password) {
        try {
            $stmt = $this->db->prepare("
                SELECT u.*, r.nombre as rol_nombre, r.permisos as rol_permisos 
                FROM usuarios u 
                INNER JOIN roles r ON u.rol_id = r.id 
                WHERE u.email = ? AND u.estado = 'activo'
            ");
            $stmt->execute([$email]);
            $usuario = $stmt->fetch();

            if (!$usuario) {
                return [
                    'success' => false,
                    'message' => 'Credenciales incorrectas'
                ];
            }

            // Verificar contraseña
            if (!password_verify($password, $usuario['password'])) {
                return [
                    'success' => false,
                    'message' => 'Credenciales incorrectas'
                ];
            }

            // Crear sesión
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nombre'] = $usuario['nombre'];
            $_SESSION['usuario_email'] = $usuario['email'];
            $_SESSION['usuario_rol'] = $usuario['rol_nombre'];
            $_SESSION['usuario_permisos'] = json_decode($usuario['rol_permisos'], true);
            $_SESSION['logged_in'] = true;
            $_SESSION['login_time'] = time();

            return [
                'success' => true,
                'message' => 'Inicio de sesión exitoso',
                'usuario' => [
                    'id' => $usuario['id'],
                    'nombre' => $usuario['nombre'],
                    'email' => $usuario['email'],
                    'rol' => $usuario['rol_nombre']
                ]
            ];

        } catch (PDOException $e) {
            error_log("Error en login: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error al iniciar sesión. Intenta más tarde.'
            ];
        }
    }

    /**
     * Cerrar sesión
     */
    public function logout() {
        // Destruir sesión
        session_unset();
        session_destroy();

        return true;
    }

    /**
     * Verificar si el usuario está autenticado
     */
    public function isAuthenticated() {
        // Verificar sesión
        if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
            // Verificar tiempo de sesión
            if (isset($_SESSION['login_time'])) {
                $session_lifetime = SESSION_LIFETIME * 60; // Convertir a segundos
                if (time() - $_SESSION['login_time'] > $session_lifetime) {
                    $this->logout();
                    return false;
                }
            }
            return true;
        }

        return false;
    }

    /**
     * Obtener usuario actual
     */
    public function getCurrentUser() {
        if (!$this->isAuthenticated()) {
            return null;
        }

        return [
            'id' => $_SESSION['usuario_id'],
            'nombre' => $_SESSION['usuario_nombre'],
            'email' => $_SESSION['usuario_email'],
            'rol' => $_SESSION['usuario_rol'],
            'permisos' => $_SESSION['usuario_permisos']
        ];
    }

    /**
     * Verificar si el usuario tiene un permiso específico
     */
    public function hasPermission($modulo, $accion) {
        if (!$this->isAuthenticated()) {
            return false;
        }

        $permisos = $_SESSION['usuario_permisos'] ?? [];
        
        if (isset($permisos[$modulo])) {
            if (in_array($accion, $permisos[$modulo]) || in_array('all', $permisos[$modulo])) {
                return true;
            }
        }

        // Los administradores tienen todos los permisos
        if ($_SESSION['usuario_rol'] === 'administrador') {
            return true;
        }

        return false;
    }

    /**
     * Solicitar recuperación de contraseña
     */
    public function solicitarRecuperacion($email) {
        try {
            $stmt = $this->db->prepare("SELECT id FROM usuarios WHERE email = ? AND estado = 'activo'");
            $stmt->execute([$email]);
            $usuario = $stmt->fetch();

            if (!$usuario) {
                return [
                    'success' => false,
                    'message' => 'No se encontró una cuenta con ese correo'
                ];
            }

            // Aquí se implementaría la lógica de recuperación de contraseña
            // Por ahora solo retornamos éxito

            return [
                'success' => true,
                'message' => 'Funcionalidad de recuperación próximamente'
            ];

        } catch (PDOException $e) {
            error_log("Error en recuperación: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error al procesar la solicitud'
            ];
        }
    }
}

// Función helper
function requireAuth() {
    $auth = new Auth();
    if (!$auth->isAuthenticated()) {
        header('Location: index.php');
        exit;
    }
    return $auth;
}

// Función helper para verificar permisos
function requirePermission($modulo, $accion) {
    $auth = requireAuth();
    if (!$auth->hasPermission($modulo, $accion)) {
        http_response_code(403);
        die('No tienes permisos para realizar esta acción');
    }
}

?>
