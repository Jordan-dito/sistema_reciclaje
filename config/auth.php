<?php
/**
 * Sistema de Autenticación
 * Sistema de Gestión de Reciclaje
 * Tesis de Grado
 */

require_once __DIR__ . '/database.php';

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    // Configurar parámetros de sesión para cookies
    ini_set('session.cookie_httponly', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.cookie_samesite', 'Lax');
    
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
            $_SESSION['usuario_cedula'] = $usuario['cedula'] ?? '';
            $_SESSION['usuario_rol'] = $usuario['rol_nombre'];
            
            // Cargar foto de perfil si existe
            $fotoPerfil = isset($usuario['foto_perfil']) && !empty($usuario['foto_perfil']) 
                ? $usuario['foto_perfil'] 
                : null;
            $_SESSION['usuario_foto_perfil'] = $fotoPerfil;
            
            // Manejar permisos (puede ser NULL si la tabla no tiene el campo)
            $permisos = null;
            if (isset($usuario['rol_permisos']) && !empty($usuario['rol_permisos'])) {
                $permisos = json_decode($usuario['rol_permisos'], true);
            }
            $_SESSION['usuario_permisos'] = $permisos;
            $_SESSION['logged_in'] = true;
            $_SESSION['login_time'] = time();

            return [
                'success' => true,
                'message' => 'Inicio de sesión exitoso',
                'usuario' => [
                    'id' => $usuario['id'],
                    'nombre' => $usuario['nombre'],
                    'email' => $usuario['email'],
                    'cedula' => $usuario['cedula'] ?? '',
                    'telefono' => $usuario['telefono'] ?? '',
                    'rol' => $usuario['rol_nombre'],
                    'rol_id' => $usuario['rol_id'],
                    'foto_perfil_ruta' => $fotoPerfil // Ruta relativa, se convertirá a URL completa en login.php
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
        if (strtolower($_SESSION['usuario_rol']) === 'administrador') {
            return true;
        }

        return false;
    }

    /**
     * Solicitar recuperación de contraseña
     */
    public function solicitarRecuperacion($email) {
        try {
            // Verificar que el usuario existe y está activo
            $stmt = $this->db->prepare("SELECT id, nombre FROM usuarios WHERE email = ? AND estado = 'activo'");
            $stmt->execute([$email]);
            $usuario = $stmt->fetch();

            if (!$usuario) {
                // Por seguridad, no revelar si el email existe o no
                return [
                    'success' => true,
                    'message' => 'Si el correo existe, recibirás un enlace para restablecer tu contraseña'
                ];
            }

            // Generar token único
            $token = bin2hex(random_bytes(32));
            
            // Token expira en 1 hora
            $expiraEn = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Invalidar tokens anteriores del usuario
            $stmt = $this->db->prepare("UPDATE password_reset_tokens SET usado = 1 WHERE usuario_id = ? AND usado = 0");
            $stmt->execute([$usuario['id']]);
            
            // Guardar nuevo token
            $stmt = $this->db->prepare("
                INSERT INTO password_reset_tokens (usuario_id, token, email, expira_en, usado) 
                VALUES (?, ?, ?, ?, 0)
            ");
            $stmt->execute([$usuario['id'], $token, $email, $expiraEn]);
            
            // Cargar funciones de email
            require_once __DIR__ . '/email.php';
            
            // Generar URL de restablecimiento
            $resetUrl = generarUrlResetPassword($token);
            
            // Preparar email
            $subject = 'Restablecer tu contraseña - Sistema de Gestión de Reciclaje';
            $body = '
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background: #2c9f5f; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
                    .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 5px 5px; }
                    .button { display: inline-block; padding: 12px 30px; background: #2c9f5f; color: white; text-decoration: none; border-radius: 5px; margin: 20px 0; }
                    .button:hover { background: #1e7e4a; }
                    .footer { text-align: center; margin-top: 20px; color: #666; font-size: 12px; }
                    .warning { color: #d32f2f; font-size: 14px; margin-top: 20px; }
                </style>
            </head>
            <body>
                <div class="container">
                    <div class="header">
                        <h2>Sistema de Gestión de Reciclaje</h2>
                    </div>
                    <div class="content">
                        <h3>Hola ' . htmlspecialchars($usuario['nombre']) . ',</h3>
                        <p>Recibimos una solicitud para restablecer la contraseña de tu cuenta.</p>
                        <p>Haz clic en el siguiente botón para restablecer tu contraseña:</p>
                        <div style="text-align: center;">
                            <a href="' . htmlspecialchars($resetUrl) . '" class="button">Restablecer Contraseña</a>
                        </div>
                        <p>O copia y pega este enlace en tu navegador:</p>
                        <p style="word-break: break-all; color: #2c9f5f;">' . htmlspecialchars($resetUrl) . '</p>
                        <div class="warning">
                            <p><strong>⚠️ Importante:</strong></p>
                            <ul>
                                <li>Este enlace expirará en 1 hora</li>
                                <li>Si no solicitaste este cambio, ignora este email</li>
                                <li>Por seguridad, no compartas este enlace con nadie</li>
                            </ul>
                        </div>
                    </div>
                    <div class="footer">
                        <p>Este es un email automático, por favor no respondas.</p>
                        <p>&copy; ' . date('Y') . ' Sistema de Gestión de Reciclaje</p>
                    </div>
                </div>
            </body>
            </html>
            ';
            
            $altBody = "Hola {$usuario['nombre']},\n\n";
            $altBody .= "Recibimos una solicitud para restablecer la contraseña de tu cuenta.\n\n";
            $altBody .= "Haz clic en el siguiente enlace para restablecer tu contraseña:\n";
            $altBody .= $resetUrl . "\n\n";
            $altBody .= "Este enlace expirará en 1 hora.\n\n";
            $altBody .= "Si no solicitaste este cambio, ignora este email.\n\n";
            $altBody .= "Este es un email automático, por favor no respondas.";
            
            // Enviar email
            $resultadoEmail = enviarEmail($email, $subject, $body, $altBody);
            
            if (!$resultadoEmail['success']) {
                error_log("Error al enviar email de recuperación: " . $resultadoEmail['message']);
                return [
                    'success' => false,
                    'message' => 'Error al enviar el email. Por favor, intenta más tarde.'
                ];
            }
            
            return [
                'success' => true,
                'message' => 'Si el correo existe, recibirás un enlace para restablecer tu contraseña'
            ];

        } catch (PDOException $e) {
            error_log("Error en recuperación: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error al procesar la solicitud'
            ];
        }
    }
    
    /**
     * Verificar token de recuperación
     */
    public function verificarToken($token) {
        try {
            $stmt = $this->db->prepare("
                SELECT prt.*, u.id as usuario_id, u.email as usuario_email 
                FROM password_reset_tokens prt
                INNER JOIN usuarios u ON prt.usuario_id = u.id
                WHERE prt.token = ? 
                AND prt.usado = 0 
                AND prt.expira_en > NOW()
                AND u.estado = 'activo'
            ");
            $stmt->execute([$token]);
            $tokenData = $stmt->fetch();
            
            if (!$tokenData) {
                return [
                    'success' => false,
                    'message' => 'Token inválido o expirado'
                ];
            }
            
            return [
                'success' => true,
                'message' => 'Token válido',
                'usuario_id' => $tokenData['usuario_id'],
                'email' => $tokenData['usuario_email']
            ];
            
        } catch (PDOException $e) {
            error_log("Error al verificar token: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error al verificar el token'
            ];
        }
    }
    
    /**
     * Restablecer contraseña con token
     */
    public function restablecerPassword($token, $nuevaPassword) {
        try {
            // Verificar token
            $verificacion = $this->verificarToken($token);
            if (!$verificacion['success']) {
                return $verificacion;
            }
            
            // Validar contraseña
            if (strlen($nuevaPassword) < 8) {
                return [
                    'success' => false,
                    'message' => 'La contraseña debe tener al menos 8 caracteres'
                ];
            }
            
            // Hash de la nueva contraseña
            $passwordHash = password_hash($nuevaPassword, PASSWORD_DEFAULT);
            
            // Actualizar contraseña del usuario
            $stmt = $this->db->prepare("UPDATE usuarios SET password = ? WHERE id = ?");
            $stmt->execute([$passwordHash, $verificacion['usuario_id']]);
            
            // Marcar token como usado
            $stmt = $this->db->prepare("UPDATE password_reset_tokens SET usado = 1 WHERE token = ?");
            $stmt->execute([$token]);
            
            return [
                'success' => true,
                'message' => 'Contraseña restablecida correctamente'
            ];
            
        } catch (PDOException $e) {
            error_log("Error al restablecer contraseña: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error al restablecer la contraseña'
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
