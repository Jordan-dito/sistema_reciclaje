<?php
/**
 * API Configuración del Sistema
 */
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json');

$auth = new Auth();
if (!$auth->isAuthenticated()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

$db = getDB();
$action = $_REQUEST['action'] ?? '';

try {
    switch ($action) {
        case 'get':
            $rows = $db->query("SELECT clave, valor, descripcion, tipo FROM configuracion_sistema ORDER BY id")->fetchAll();
            echo json_encode(['success' => true, 'datos' => $rows]);
            break;

        case 'update':
            $clave = trim($_POST['clave'] ?? '');
            $valor = trim($_POST['valor'] ?? '');
            if (empty($clave)) throw new Exception('Clave requerida');
            $stmt = $db->prepare("UPDATE configuracion_sistema SET valor = ? WHERE clave = ?");
            $stmt->execute([$valor, $clave]);
            echo json_encode(['success' => true]);
            break;

        default:
            throw new Exception('Acción no válida');
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
