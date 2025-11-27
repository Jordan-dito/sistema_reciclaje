<?php
/**
 * Configuración de Email con PHPMailer
 * Sistema de Gestión de Reciclaje
 */

require_once __DIR__ . '/../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Cargar variables de entorno
$envPath = __DIR__ . '/../.env';
if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0 || strpos($line, '=') === false) {
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
}

// Configuración de email desde .env o valores por defecto
define('SMTP_HOST', getenv('SMTP_HOST') ?: 'smtp-hermanosyanez.alwaysdata.net');
define('SMTP_PORT', getenv('SMTP_PORT') ?: 587);
define('SMTP_USER', getenv('SMTP_USER') ?: 'hermanosyanez@alwaysdata.net');
define('SMTP_PASS', getenv('SMTP_PASS') ?: '');
define('SMTP_FROM_EMAIL', getenv('SMTP_FROM_EMAIL') ?: 'hermanosyanez@alwaysdata.net');
define('SMTP_FROM_NAME', getenv('SMTP_FROM_NAME') ?: 'Sistema de Gestión de Reciclaje');
define('SMTP_SECURE', getenv('SMTP_SECURE') ?: 'tls'); // 'tls' o 'ssl'

/**
 * Enviar email usando PHPMailer
 * 
 * @param string $to Email del destinatario
 * @param string $subject Asunto del email
 * @param string $body Cuerpo del email (HTML)
 * @param string $altBody Texto alternativo (opcional)
 * @return array Resultado del envío ['success' => bool, 'message' => string]
 */
function enviarEmail($to, $subject, $body, $altBody = '') {
    $mail = new PHPMailer(true);
    
    try {
        // Configuración del servidor SMTP
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USER;
        $mail->Password = SMTP_PASS;
        $mail->SMTPSecure = SMTP_SECURE;
        $mail->Port = SMTP_PORT;
        $mail->CharSet = 'UTF-8';
        
        // Remitente
        $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
        
        // Destinatario
        $mail->addAddress($to);
        
        // Contenido
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->AltBody = $altBody ?: strip_tags($body);
        
        // Enviar
        $mail->send();
        
        return [
            'success' => true,
            'message' => 'Email enviado correctamente'
        ];
        
    } catch (Exception $e) {
        error_log("Error al enviar email: " . $mail->ErrorInfo);
        return [
            'success' => false,
            'message' => 'Error al enviar el email: ' . $mail->ErrorInfo
        ];
    }
}

/**
 * Generar URL para restablecer contraseña
 * 
 * @param string $token Token de recuperación
 * @return string URL completa
 */
function generarUrlResetPassword($token) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || 
                 (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')) 
                 ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    
    // Obtener el directorio base del proyecto (raíz)
    // Si estamos en /config/auth.php, necesitamos subir un nivel
    $scriptPath = $_SERVER['SCRIPT_NAME'] ?? $_SERVER['PHP_SELF'] ?? '';
    $scriptDir = dirname($scriptPath);
    
    // Si estamos en /config, subir un nivel para llegar a la raíz
    if (basename($scriptDir) === 'config' || strpos($scriptDir, '/config') !== false) {
        $basePath = dirname($scriptDir);
    } else {
        $basePath = $scriptDir;
    }
    
    // Normalizar la ruta base
    if ($basePath === '/' || $basePath === '\\' || $basePath === '.' || empty($basePath)) {
        $basePath = '';
    } else {
        // Asegurar que comience con /
        $basePath = '/' . trim($basePath, '/');
    }
    
    return $protocol . '://' . $host . $basePath . '/reset-password.php?token=' . urlencode($token);
}

?>

