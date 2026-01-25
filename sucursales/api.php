<?php
/**
 * API para gestión de sucursales
 * Sistema de Gestión de Reciclaje
 */

error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');

header('Content-Type: application/json; charset=utf-8');
ob_start();

try {
    require_once __DIR__ . '/../config/auth.php';
    require_once __DIR__ . '/../config/validaciones.php';

    $auth = new Auth();
    $method = $_SERVER['REQUEST_METHOD'];
    $action = $_GET['action'] ?? $_POST['action'] ?? '';
    
    if (empty($action)) {
        throw new Exception('Acción no especificada');
    }

    // Acciones públicas (no requieren autenticación)
    $accionesPublicas = ['disponibles'];
    
    // Verificar autenticación solo para acciones que no son públicas
    if (!in_array($action, $accionesPublicas) && !$auth->isAuthenticated()) {
        ob_end_clean();
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'No autorizado']);
        exit;
    }

    $db = getDB();
    
    switch ($method) {
        case 'GET':
            if ($action === 'listar') {
                $stmt = $db->query("
                    SELECT s.*, u.nombre as responsable_nombre 
                    FROM sucursales s 
                    LEFT JOIN usuarios u ON s.responsable_id = u.id 
                    ORDER BY s.id ASC
                ");
                $sucursales = $stmt->fetchAll();
                
                ob_end_clean();
                echo json_encode(['success' => true, 'data' => $sucursales], JSON_UNESCAPED_UNICODE);
            } elseif ($action === 'obtener') {
                $id = $_GET['id'] ?? 0;
                $stmt = $db->prepare("
                    SELECT s.*, u.nombre as responsable_nombre 
                    FROM sucursales s 
                    LEFT JOIN usuarios u ON s.responsable_id = u.id 
                    WHERE s.id = ?
                ");
                $stmt->execute([$id]);
                $sucursal = $stmt->fetch();
                
                ob_end_clean();
                if ($sucursal) {
                    echo json_encode(['success' => true, 'data' => $sucursal]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Sucursal no encontrada']);
                }
            } elseif ($action === 'activas') {
                // Obtener solo sucursales activas
                // Filtrar por la sucursal del usuario logueado si tiene una asignada
                $usuario_id = $_SESSION['usuario_id'] ?? null;
                $sucursal_usuario = null;
                
                if ($usuario_id) {
                    // Buscar si es responsable de una sucursal
                    $stmt = $db->prepare("SELECT id FROM sucursales WHERE responsable_id = ? AND estado = 'activa' LIMIT 1");
                    $stmt->execute([$usuario_id]);
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($result) {
                        $sucursal_usuario = $result['id'];
                    } else {
                        // Buscar en perfil de usuario
                        $stmt = $db->prepare("SELECT sucursal_id FROM usuarios WHERE id = ?");
                        $stmt->execute([$usuario_id]);
                        $result = $stmt->fetch(PDO::FETCH_ASSOC);
                        if ($result && $result['sucursal_id']) {
                            $sucursal_usuario = $result['sucursal_id'];
                        }
                    }
                }
                
                // Si el usuario tiene sucursal asignada, solo mostrar esa
                if ($sucursal_usuario) {
                    $stmt = $db->prepare("
                        SELECT id, nombre 
                        FROM sucursales 
                        WHERE estado = 'activa' AND id = ?
                        ORDER BY nombre
                    ");
                    $stmt->execute([$sucursal_usuario]);
                } else {
                    // Si no tiene sucursal, mostrar todas las activas
                    $stmt = $db->query("
                        SELECT id, nombre 
                        FROM sucursales 
                        WHERE estado = 'activa' 
                        ORDER BY nombre
                    ");
                }
                
                $sucursales = $stmt->fetchAll();
                
                ob_end_clean();
                echo json_encode(['success' => true, 'data' => $sucursales]);
            } elseif ($action === 'disponibles') {
                // Endpoint para Flutter - obtener sucursales disponibles
                header('Access-Control-Allow-Origin: *');
                header('Access-Control-Allow-Methods: GET, OPTIONS');
                header('Access-Control-Allow-Headers: Content-Type, Authorization');
                
                if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
                    http_response_code(200);
                    exit;
                }
                
                $stmt = $db->query("
                    SELECT id, nombre, direccion, telefono, email, estado
                    FROM sucursales 
                    WHERE estado = 'activa' 
                    ORDER BY nombre ASC
                ");
                $sucursales = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                ob_end_clean();
                echo json_encode([
                    'success' => true, 
                    'message' => 'Sucursales disponibles obtenidas exitosamente',
                    'data' => $sucursales
                ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }
            break;
            
        case 'POST':
            if ($action === 'crear') {
                $nombre = trim($_POST['nombre'] ?? '');
                $direccion = trim($_POST['direccion'] ?? '');
                $telefono = trim($_POST['telefono'] ?? '');
                $email = trim($_POST['email'] ?? '');
                $responsable_id = !empty($_POST['responsable_id']) ? intval($_POST['responsable_id']) : null;
                $estado = $_POST['estado'] ?? 'activa';
                $saldo = floatval($_POST['saldo'] ?? 0);
                
                // Validar campos obligatorios
                if (empty($nombre)) {
                    throw new Exception('El nombre es obligatorio');
                }
                
                if (empty($direccion)) {
                    throw new Exception('La dirección es obligatoria');
                }
                
                if (empty($telefono)) {
                    throw new Exception('El teléfono es obligatorio');
                }
                
                if (empty($email)) {
                    throw new Exception('El email es obligatorio');
                }
                
                if (empty($responsable_id)) {
                    throw new Exception('El responsable es obligatorio');
                }
                
                // Validar formato de email
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    throw new Exception('Email inválido');
                }
                
                // Validar teléfono: debe tener 10 dígitos
                $telefono = preg_replace('/[^0-9]/', '', $telefono); // Solo números
                $validacionTelefono = validarTelefono10Digitos($telefono);
                if (!$validacionTelefono['valid']) {
                    throw new Exception($validacionTelefono['message']);
                }
                
                // Validar que no exista una sucursal con el mismo nombre
                $stmt = $db->prepare("SELECT id FROM sucursales WHERE LOWER(nombre) = LOWER(?)");
                $stmt->execute([$nombre]);
                if ($stmt->fetch()) {
                    throw new Exception('Ya existe una sucursal con este nombre');
                }
                
                if ($responsable_id) {
                    $stmt = $db->prepare("SELECT id, sucursal_id FROM usuarios WHERE id = ?");
                    $stmt->execute([$responsable_id]);
                    $usuario = $stmt->fetch();
                    if (!$usuario) {
                        throw new Exception('Responsable inválido');
                    }
                    if ($usuario['sucursal_id'] !== null) {
                        throw new Exception('Este usuario ya es responsable de otra sucursal');
                    }
                }
                
                $stmt = $db->prepare("
                    INSERT INTO sucursales (nombre, direccion, telefono, email, responsable_id, estado, saldo) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)
                ");
                
                // Log de depuración: datos recibidos
                error_log("sucursales/api.php - crear POST: " . print_r($_POST, true));

                $stmt->execute([
                    $nombre,
                    $direccion ?: null,
                    $telefono ?: null,
                    $email ?: null,
                    $responsable_id,
                    $estado,
                    $saldo
                ]);
                
                $nueva_sucursal_id = $db->lastInsertId();

                // Si se asignó un responsable, actualizar su sucursal_id en la tabla usuarios
                if ($responsable_id) {
                    $stmt = $db->prepare("UPDATE usuarios SET sucursal_id = ? WHERE id = ?");
                    $stmt->execute([$nueva_sucursal_id, $responsable_id]);
                    error_log("sucursales/api.php - crear: actualizado usuarios.sucursal_id para usuario {$responsable_id} => sucursal {$nueva_sucursal_id}. Affected: " . $stmt->rowCount());
                }

                // Preparar respuesta (añadir debug sólo si APP_DEBUG está activado)
                $response = [
                    'success' => true,
                    'message' => 'Sucursal creada exitosamente',
                    'id' => $nueva_sucursal_id
                ];

                if (defined('APP_DEBUG') && APP_DEBUG) {
                    $response['debug'] = [
                        'post' => $_POST,
                        'nueva_sucursal_id' => $nueva_sucursal_id,
                        'responsable_id' => $responsable_id
                    ];
                }

                ob_end_clean();
                echo json_encode($response);
            } elseif ($action === 'actualizar') {
                $id = intval($_POST['id'] ?? 0);
                $nombre = trim($_POST['nombre'] ?? '');
                $direccion = trim($_POST['direccion'] ?? '');
                $telefono = trim($_POST['telefono'] ?? '');
                $email = trim($_POST['email'] ?? '');
                $responsable_id = !empty($_POST['responsable_id']) ? intval($_POST['responsable_id']) : null;
                $estado = $_POST['estado'] ?? 'activa';
                
                // Validar campos obligatorios
                if (empty($nombre)) {
                    throw new Exception('El nombre es obligatorio');
                }
                
                if (empty($direccion)) {
                    throw new Exception('La dirección es obligatoria');
                }
                
                if (empty($telefono)) {
                    throw new Exception('El teléfono es obligatorio');
                }
                
                if (empty($email)) {
                    throw new Exception('El email es obligatorio');
                }
                
                if (empty($responsable_id)) {
                    throw new Exception('El responsable es obligatorio');
                }
                
                // Validar que no exista otra sucursal con el mismo nombre (excepto la actual)
                $stmt = $db->prepare("SELECT id FROM sucursales WHERE LOWER(nombre) = LOWER(?) AND id != ?");
                $stmt->execute([$nombre, $id]);
                if ($stmt->fetch()) {
                    throw new Exception('Ya existe otra sucursal con este nombre');
                }
                
                // Validar formato de email
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    throw new Exception('Email inválido');
                }
                
                // Validar teléfono: debe tener 10 dígitos
                $telefono = preg_replace('/[^0-9]/', '', $telefono); // Solo números
                $validacionTelefono = validarTelefono10Digitos($telefono);
                if (!$validacionTelefono['valid']) {
                    throw new Exception($validacionTelefono['message']);
                }
                
                if ($responsable_id) {
                    $stmt = $db->prepare("SELECT id, sucursal_id FROM usuarios WHERE id = ?");
                    $stmt->execute([$responsable_id]);
                    $usuario = $stmt->fetch();
                    if (!$usuario) {
                        throw new Exception('Responsable inválido');
                    }
                    // Si tiene sucursal_id y no es la actual sucursal que estamos editando
                    if ($usuario['sucursal_id'] !== null && $usuario['sucursal_id'] != $id) {
                        throw new Exception('Este usuario ya es responsable de otra sucursal');
                    }
                }

                // Obtener el responsable actual antes de actualizar para gestionar el cambio
                $stmt = $db->prepare("SELECT responsable_id FROM sucursales WHERE id = ?");
                $stmt->execute([$id]);
                $responsable_anterior = $stmt->fetchColumn();
                
                $stmt = $db->prepare("
                    UPDATE sucursales 
                    SET nombre = ?, direccion = ?, telefono = ?, email = ?, responsable_id = ?, estado = ?
                    WHERE id = ?
                ");
                
                $stmt->execute([
                    $nombre,
                    $direccion ?: null,
                    $telefono ?: null,
                    $email ?: null,
                    $responsable_id,
                    $estado,
                    $id
                ]);

                // Gestionar actualización de sucursal_id en la tabla usuarios
                if ($responsable_anterior != $responsable_id) {
                    // Si había un responsable anterior, quitarle la vinculación a esta sucursal
                    if ($responsable_anterior) {
                        $stmt = $db->prepare("UPDATE usuarios SET sucursal_id = NULL WHERE id = ? AND sucursal_id = ?");
                        $stmt->execute([$responsable_anterior, $id]);
                        error_log("sucursales/api.php - actualizar: desvinculado usuario {$responsable_anterior} de sucursal {$id}. Affected: " . $stmt->rowCount());
                    }
                    
                    // Si hay un nuevo responsable, vincularlo a esta sucursal
                    if ($responsable_id) {
                        $stmt = $db->prepare("UPDATE usuarios SET sucursal_id = ? WHERE id = ?");
                        $stmt->execute([$id, $responsable_id]);
                        error_log("sucursales/api.php - actualizar: vinculado usuario {$responsable_id} a sucursal {$id}. Affected: " . $stmt->rowCount());
                    }
                } else {
                    error_log("sucursales/api.php - actualizar: responsable no cambiado (anterior={$responsable_anterior} nuevo={$responsable_id})");
                }

                // Respuesta con posible información de depuración
                $response = [
                    'success' => true,
                    'message' => 'Sucursal actualizada exitosamente'
                ];
                if (defined('APP_DEBUG') && APP_DEBUG) {
                    $response['debug'] = [
                        'post' => $_POST,
                        'responsable_anterior' => $responsable_anterior,
                        'responsable_id' => $responsable_id
                    ];
                }

                ob_end_clean();
                echo json_encode($response);
            } elseif ($action === 'eliminar' || $action === 'desactivar') {
                $id = intval($_POST['id'] ?? 0);
                
                if ($id <= 0) {
                    throw new Exception('ID de sucursal inválido');
                }
                
                $stmt = $db->prepare("SELECT estado FROM sucursales WHERE id = ?");
                $stmt->execute([$id]);
                $sucursal = $stmt->fetch();
                
                if (!$sucursal) {
                    throw new Exception('Sucursal no encontrada');
                }
                
                if ($sucursal['estado'] === 'inactiva') {
                    throw new Exception('La sucursal ya está inactiva');
                }
                
                $stmt = $db->prepare("UPDATE sucursales SET estado = 'inactiva', fecha_actualizacion = NOW() WHERE id = ?");
                $stmt->execute([$id]);
                
                ob_end_clean();
                echo json_encode(['success' => true, 'message' => 'Sucursal desactivada exitosamente']);
            } elseif ($action === 'activar') {
                $id = intval($_POST['id'] ?? 0);
                
                if ($id <= 0) {
                    throw new Exception('ID de sucursal inválido');
                }
                
                $stmt = $db->prepare("SELECT estado FROM sucursales WHERE id = ?");
                $stmt->execute([$id]);
                $sucursal = $stmt->fetch();
                
                if (!$sucursal) {
                    throw new Exception('Sucursal no encontrada');
                }
                
                if ($sucursal['estado'] === 'activa') {
                    throw new Exception('La sucursal ya está activa');
                }
                
                $stmt = $db->prepare("UPDATE sucursales SET estado = 'activa', fecha_actualizacion = NOW() WHERE id = ?");
                $stmt->execute([$id]);
                
                ob_end_clean();
                echo json_encode(['success' => true, 'message' => 'Sucursal activada exitosamente']);
            }
            break;
            
        default:
            ob_end_clean();
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    }
    
} catch (PDOException $e) {
    ob_end_clean();
    $errorInfo = ErrorHandler::handleDatabaseError($e, 'sucursales/api.php');
    ErrorHandler::logError($e->getMessage(), ['exception' => $e->getMessage(), 'code' => $e->getCode()]);
    $debug = defined('APP_DEBUG') && APP_DEBUG;
    echo ErrorHandler::jsonResponse($errorInfo, 500, $debug);
} catch (Exception $e) {
    ob_end_clean();
    $errorInfo = ErrorHandler::handleException($e, 'sucursales/api.php');
    ErrorHandler::logError($e->getMessage(), ['exception' => $e->getMessage()]);
    $debug = defined('APP_DEBUG') && APP_DEBUG;
    echo ErrorHandler::jsonResponse($errorInfo, 400, $debug);
}
?>

