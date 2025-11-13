<?php
/**
 * Clase para manejo de errores y excepciones
 * Sistema de Gestión de Reciclaje
 * Proporciona información detallada y fácil de entender sobre errores
 */

class ErrorHandler {
    
    /**
     * Formatea y registra un error de base de datos
     */
    public static function handleDatabaseError(PDOException $e, $context = '') {
        $errorInfo = [
            'type' => 'Database Error',
            'message' => self::getFriendlyMessage($e->getCode()),
            'technical' => $e->getMessage(),
            'code' => $e->getCode(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'context' => $context,
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        // Registrar en log
        error_log("DB Error [{$e->getCode()}]: {$e->getMessage()} | Context: {$context} | File: {$e->getFile()}:{$e->getLine()}");
        
        return $errorInfo;
    }
    
    /**
     * Formatea y registra un error general
     */
    public static function handleException(Exception $e, $context = '') {
        $errorInfo = [
            'type' => 'Exception',
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'context' => $context,
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        // Registrar en log
        error_log("Exception: {$e->getMessage()} | Context: {$context} | File: {$e->getFile()}:{$e->getLine()}");
        
        return $errorInfo;
    }
    
    /**
     * Obtiene un mensaje amigable según el código de error de MySQL
     */
    private static function getFriendlyMessage($code) {
        $messages = [
            // Errores de conexión
            2002 => 'No se pudo conectar al servidor de base de datos. Verifica que el servidor esté activo.',
            2003 => 'No se pudo conectar al servidor de base de datos. Verifica la configuración de red.',
            1045 => 'Credenciales incorrectas. Verifica el usuario y contraseña de la base de datos.',
            
            // Errores de base de datos
            1049 => 'La base de datos especificada no existe. Verifica el nombre de la base de datos.',
            1146 => 'La tabla no existe en la base de datos. Verifica que las tablas estén creadas.',
            1054 => 'Columna desconocida. Verifica que la columna exista en la tabla.',
            1062 => 'Dato duplicado. Ya existe un registro con ese valor único.',
            1452 => 'Error de clave foránea. El registro referenciado no existe.',
            1451 => 'No se puede eliminar. Existen registros relacionados que dependen de este.',
            
            // Errores de sintaxis
            1064 => 'Error de sintaxis SQL. Verifica la consulta.',
            1055 => 'Error en la consulta GROUP BY. Verifica las columnas seleccionadas.',
            
            // Errores de permisos
            1044 => 'No tienes permisos para acceder a esta base de datos.',
            1142 => 'No tienes permisos para ejecutar esta operación.',
            
            // Errores de timeout
            2006 => 'El servidor MySQL se ha desconectado. Intenta nuevamente.',
        ];
        
        return $messages[$code] ?? 'Error de base de datos desconocido.';
    }
    
    /**
     * Genera una respuesta JSON con información del error
     */
    public static function jsonResponse($errorInfo, $httpCode = 500, $includeDetails = false) {
        $debug = defined('APP_DEBUG') && APP_DEBUG;
        
        $response = [
            'success' => false,
            'message' => $errorInfo['message'] ?? 'Ha ocurrido un error',
            'type' => $errorInfo['type'] ?? 'Error',
        ];
        
        // Incluir detalles técnicos solo si está en modo debug o si se solicita
        if ($debug || $includeDetails) {
            $response['error_details'] = [
                'technical' => $errorInfo['technical'] ?? $errorInfo['message'] ?? '',
                'code' => $errorInfo['code'] ?? null,
                'file' => $errorInfo['file'] ?? null,
                'line' => $errorInfo['line'] ?? null,
                'context' => $errorInfo['context'] ?? '',
                'timestamp' => $errorInfo['timestamp'] ?? date('Y-m-d H:i:s')
            ];
        }
        
        http_response_code($httpCode);
        header('Content-Type: application/json; charset=utf-8');
        
        return json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
    
    /**
     * Maneja errores de autenticación
     */
    public static function handleAuthError($message = 'No autorizado', $details = []) {
        $errorInfo = [
            'type' => 'Authentication Error',
            'message' => $message,
            'context' => 'Autenticación',
            'timestamp' => date('Y-m-d H:i:s'),
            'details' => $details
        ];
        
        error_log("Auth Error: {$message} | Details: " . json_encode($details));
        
        return self::jsonResponse($errorInfo, 401);
    }
    
    /**
     * Maneja errores de validación
     */
    public static function handleValidationError($message, $field = null) {
        $errorInfo = [
            'type' => 'Validation Error',
            'message' => $message,
            'field' => $field,
            'context' => 'Validación',
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        error_log("Validation Error: {$message} | Field: {$field}");
        
        return self::jsonResponse($errorInfo, 400);
    }
    
    /**
     * Maneja errores de recurso no encontrado
     */
    public static function handleNotFoundError($resource = 'Recurso') {
        $errorInfo = [
            'type' => 'Not Found',
            'message' => "{$resource} no encontrado",
            'context' => 'Búsqueda',
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        return self::jsonResponse($errorInfo, 404);
    }
    
    /**
     * Maneja errores de método no permitido
     */
    public static function handleMethodNotAllowed($method, $allowed = []) {
        $errorInfo = [
            'type' => 'Method Not Allowed',
            'message' => "Método {$method} no permitido",
            'allowed_methods' => $allowed,
            'context' => 'HTTP Method',
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        return self::jsonResponse($errorInfo, 405);
    }
    
    /**
     * Formatea un error para mostrar en consola del navegador
     */
    public static function formatForConsole($errorInfo) {
        $output = "\n";
        $output .= "╔═══════════════════════════════════════════════════════════╗\n";
        $output .= "║                    ERROR DETECTADO                        ║\n";
        $output .= "╠═══════════════════════════════════════════════════════════╣\n";
        $output .= "║ Tipo: " . str_pad($errorInfo['type'] ?? 'Desconocido', 51) . " ║\n";
        $output .= "║ Mensaje: " . str_pad(substr($errorInfo['message'] ?? '', 0, 48), 48) . " ║\n";
        
        if (isset($errorInfo['file'])) {
            $file = basename($errorInfo['file']);
            $output .= "║ Archivo: " . str_pad($file, 48) . " ║\n";
        }
        
        if (isset($errorInfo['line'])) {
            $output .= "║ Línea: " . str_pad($errorInfo['line'], 49) . " ║\n";
        }
        
        if (isset($errorInfo['code'])) {
            $output .= "║ Código: " . str_pad($errorInfo['code'], 49) . " ║\n";
        }
        
        $output .= "╚═══════════════════════════════════════════════════════════╝\n";
        
        return $output;
    }
    
    /**
     * Registra un error con contexto completo
     */
    public static function logError($message, $context = [], $level = 'ERROR') {
        $logEntry = [
            'level' => $level,
            'message' => $message,
            'context' => $context,
            'timestamp' => date('Y-m-d H:i:s'),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'request_uri' => $_SERVER['REQUEST_URI'] ?? 'unknown',
            'request_method' => $_SERVER['REQUEST_METHOD'] ?? 'unknown'
        ];
        
        error_log(json_encode($logEntry, JSON_PRETTY_PRINT));
    }
}

