<?php
/**
 * Script de Despliegue Automático
 * Sistema de Gestión de Reciclaje
 * 
 * Este script se ejecuta automáticamente cuando GitHub envía un webhook
 * después de un push al repositorio.
 * 
 * CONFIGURACIÓN:
 * 1. Sube este archivo a tu servidor AlwaysData
 * 2. Configura el webhook en GitHub: Settings > Webhooks > Add webhook
 * 3. URL del webhook: https://hermanosyanez.alwaysdata.net/deploy.php
 * 4. Content type: application/json
 * 5. Secret: (configura un secreto y actualízalo en $webhook_secret)
 */

// Configuración
$webhook_secret = 'TU_SECRETO_AQUI'; // Cambia esto por un secreto seguro
$repo_path = __DIR__; // Ruta donde está el proyecto
$log_file = __DIR__ . '/deploy.log';

// Función para escribir en el log
function writeLog($message) {
    global $log_file;
    $timestamp = date('Y-m-d H:i:s');
    $log_message = "[$timestamp] $message\n";
    file_put_contents($log_file, $log_message, FILE_APPEND);
    error_log($log_message);
}

// Función para ejecutar comandos
function executeCommand($command) {
    writeLog("Ejecutando: $command");
    $output = [];
    $return_var = 0;
    exec($command . ' 2>&1', $output, $return_var);
    $result = implode("\n", $output);
    writeLog("Resultado: $result");
    return ['output' => $result, 'return_code' => $return_var];
}

// Verificar que es una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die('Método no permitido');
}

// Obtener el payload
$payload = file_get_contents('php://input');
$headers = getallheaders();

// Verificar la firma del webhook (opcional pero recomendado)
if (!empty($webhook_secret) && $webhook_secret !== 'TU_SECRETO_AQUI') {
    $signature = $headers['X-Hub-Signature-256'] ?? '';
    $expected_signature = 'sha256=' . hash_hmac('sha256', $payload, $webhook_secret);
    
    if (!hash_equals($expected_signature, $signature)) {
        writeLog("ERROR: Firma del webhook inválida");
        http_response_code(403);
        die('Firma inválida');
    }
}

// Decodificar el JSON
$data = json_decode($payload, true);

// Verificar que es un push
if (!isset($data['ref']) || $data['ref'] !== 'refs/heads/main') {
    writeLog("INFO: Push ignorado (no es main branch)");
    http_response_code(200);
    die('Push ignorado');
}

writeLog("=== INICIO DE DESPLIEGUE ===");
writeLog("Commit: " . ($data['head_commit']['id'] ?? 'N/A'));
writeLog("Mensaje: " . ($data['head_commit']['message'] ?? 'N/A'));
writeLog("Autor: " . ($data['head_commit']['author']['name'] ?? 'N/A'));

// Cambiar al directorio del repositorio
chdir($repo_path);

// Opción 1: Si tienes git en el servidor (recomendado)
if (is_dir('.git')) {
    writeLog("Usando Git Pull...");
    
    // Hacer pull del repositorio
    $result = executeCommand('git pull origin main');
    
    if ($result['return_code'] === 0) {
        writeLog("✓ Git pull exitoso");
        
        // Opcional: Limpiar cache si usas algún sistema de cache
        // executeCommand('php artisan cache:clear'); // Laravel
        // executeCommand('php bin/console cache:clear'); // Symfony
        
        writeLog("=== DESPLIEGUE COMPLETADO ===");
        http_response_code(200);
        echo json_encode(['status' => 'success', 'message' => 'Despliegue completado']);
    } else {
        writeLog("ERROR: Git pull falló");
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Error en git pull']);
    }
} else {
    // Opción 2: Si no tienes git, usar wget/curl para descargar desde GitHub
    writeLog("Git no disponible, usando descarga directa...");
    
    // Esta opción requiere que el repositorio sea público o uses un token
    // Por ahora, solo logueamos que se necesita configuración manual
    writeLog("ADVERTENCIA: Git no está disponible. Configuración manual requerida.");
    writeLog("=== DESPLIEGUE REQUIERE ACCIÓN MANUAL ===");
    
    http_response_code(200);
    echo json_encode([
        'status' => 'warning',
        'message' => 'Git no disponible. Se requiere configuración manual.'
    ]);
}

?>

