<?php
/**
 * API para cambiar contraseña del usuario autenticado
 * Sistema de Gestión de Reciclaje
 */

// Desactivar mostrar errores directamente (solo log)
error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');

// Configurar headers JSON
header('Content-Type: application/json; charset=utf-8');

// Capturar cualquier salida no deseada
ob_start();

try {
    // Cargar configuración
    require_once __DIR__ . '/database.php';
    require_once __DIR__ . '/auth.php';
    require_once __DIR__ . '/email.php';

    $auth = new Auth();
    
    // Verificar autenticación
    if (!$auth->isAuthenticated()) {
        ob_end_clean();
        echo json_encode([
            'success' => false,
            'message' => 'No autorizado. Por favor, inicia sesión primero.'
        ]);
        exit;
    }

    // Solo aceptar POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        ob_end_clean();
        http_response_code(405);
        echo json_encode([
            'success' => false,
            'message' => 'Método no permitido'
        ]);
        exit;
    }

    // Obtener datos del formulario
    $password_actual = $_POST['password_actual'] ?? '';
    $password_nueva = $_POST['password_nueva'] ?? '';
    $password_confirmar = $_POST['password_confirmar'] ?? '';

    // Validaciones
    if (empty($password_actual) || empty($password_nueva) || empty($password_confirmar)) {
        throw new Exception('Todos los campos son obligatorios');
    }

    if ($password_nueva !== $password_confirmar) {
        throw new Exception('La nueva contraseña y su confirmación no coinciden');
    }

    if (strlen($password_nueva) < 8) {
        throw new Exception('La nueva contraseña debe tener al menos 8 caracteres');
    }

    // Validar que la nueva contraseña sea diferente a la actual
    if ($password_actual === $password_nueva) {
        throw new Exception('La nueva contraseña debe ser diferente a la actual');
    }

    // Obtener usuario actual
    $usuario_id = $_SESSION['usuario_id'];
    $db = getDB();

    // Verificar contraseña actual
    $stmt = $db->prepare("SELECT password, nombre, email FROM usuarios WHERE id = ? AND estado = 'activo'");
    $stmt->execute([$usuario_id]);
    $usuario = $stmt->fetch();

    if (!$usuario) {
        throw new Exception('Usuario no encontrado');
    }

    if (!password_verify($password_actual, $usuario['password'])) {
        throw new Exception('La contraseña actual es incorrecta');
    }

    // Hash de la nueva contraseña
    $password_hash = password_hash($password_nueva, PASSWORD_DEFAULT);

    // Actualizar contraseña
    $stmt = $db->prepare("UPDATE usuarios SET password = ?, fecha_actualizacion = NOW() WHERE id = ?");
    $stmt->execute([$password_hash, $usuario_id]);

    // Enviar notificación por email
    $nombreUsuario = $usuario['nombre'];
    $emailUsuario = $usuario['email'];
    $fechaCambio = date('d/m/Y H:i:s');
    
    $subject = 'Contraseña actualizada - Sistema de Gestión de Reciclaje';
    $body = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(135deg, #2c9f5f 0%, #1e7e4a 100%); color: white; padding: 25px; text-align: center; border-radius: 10px 10px 0 0; }
            .header h2 { margin: 0; font-size: 24px; }
            .content { background: #ffffff; padding: 30px; border: 1px solid #e0e0e0; border-top: none; }
            .alert-box { background: #e8f5e9; border-left: 4px solid #2c9f5f; padding: 15px; margin: 20px 0; border-radius: 0 5px 5px 0; }
            .alert-box.warning { background: #fff3e0; border-left-color: #ff9800; }
            .info-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
            .info-table td { padding: 12px; border-bottom: 1px solid #e0e0e0; }
            .info-table td:first-child { font-weight: bold; color: #555; width: 40%; }
            .footer { background: #f5f5f5; padding: 20px; text-align: center; border-radius: 0 0 10px 10px; border: 1px solid #e0e0e0; border-top: none; }
            .footer p { margin: 5px 0; color: #666; font-size: 12px; }
            .icon { font-size: 48px; margin-bottom: 10px; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <div class="icon">🔐</div>
                <h2>Contraseña Actualizada</h2>
            </div>
            <div class="content">
                <p>Hola <strong>' . htmlspecialchars($nombreUsuario) . '</strong>,</p>
                
                <div class="alert-box">
                    <p style="margin: 0;"><strong>✅ Tu contraseña ha sido cambiada exitosamente.</strong></p>
                </div>
                
                <p>Te informamos que la contraseña de tu cuenta en el Sistema de Gestión de Reciclaje ha sido actualizada.</p>
                
                <table class="info-table">
                    <tr>
                        <td>📅 Fecha y hora:</td>
                        <td>' . $fechaCambio . '</td>
                    </tr>
                    <tr>
                        <td>📧 Cuenta:</td>
                        <td>' . htmlspecialchars($emailUsuario) . '</td>
                    </tr>
                </table>
                
                <div class="alert-box warning">
                    <p style="margin: 0;"><strong>⚠️ ¿No realizaste este cambio?</strong></p>
                    <p style="margin: 10px 0 0 0;">Si no fuiste tú quien cambió la contraseña, por favor contacta inmediatamente al administrador del sistema para proteger tu cuenta.</p>
                </div>
                
                <p>Recuerda mantener tu contraseña segura y no compartirla con nadie.</p>
            </div>
            <div class="footer">
                <p>Este es un correo automático de seguridad, por favor no respondas.</p>
                <p>🌿 Sistema de Gestión de Reciclaje &copy; ' . date('Y') . '</p>
            </div>
        </div>
    </body>
    </html>
    ';
    
    $altBody = "Hola {$nombreUsuario},\n\n";
    $altBody .= "Tu contraseña ha sido cambiada exitosamente.\n\n";
    $altBody .= "Fecha y hora: {$fechaCambio}\n";
    $altBody .= "Cuenta: {$emailUsuario}\n\n";
    $altBody .= "Si no realizaste este cambio, por favor contacta inmediatamente al administrador del sistema.\n\n";
    $altBody .= "Este es un correo automático, por favor no respondas.\n";
    $altBody .= "Sistema de Gestión de Reciclaje";
    
    // Enviar email (no bloqueamos si falla)
    $resultadoEmail = enviarEmail($emailUsuario, $subject, $body, $altBody);
    
    if (!$resultadoEmail['success']) {
        error_log("Error al enviar email de notificación de cambio de contraseña: " . $resultadoEmail['message']);
    }

    // Limpiar buffer y enviar respuesta
    ob_end_clean();
    
    echo json_encode([
        'success' => true,
        'message' => 'Contraseña actualizada correctamente. Se ha enviado una notificación a tu correo electrónico.'
    ]);

} catch (PDOException $e) {
    ob_end_clean();
    error_log("Error de BD al cambiar contraseña: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error al procesar la solicitud. Intenta más tarde.'
    ]);
} catch (Exception $e) {
    ob_end_clean();
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
