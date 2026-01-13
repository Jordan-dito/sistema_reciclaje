<?php
/**
 * API para Gastos Varios de Empleados
 * Sistema de Gestión de Reciclaje
 */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/database.php';

$auth = new Auth();
if (!$auth->isAuthenticated()) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

$db = getDB();
$action = $_GET['action'] ?? $_POST['action'] ?? '';

try {
    switch ($action) {
        case 'list':
            // Listar todos los gastos
            $stmt = $db->query("
                SELECT g.*, 
                       CONCAT(e.nombre, ' ', e.apellido) as empleado_nombre
                FROM gastos_empleados g
                INNER JOIN empleados e ON g.empleado_id = e.id
                ORDER BY g.fecha DESC, g.id DESC
            ");
            $gastos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'data' => $gastos]);
            break;

        case 'get':
            // Obtener un gasto específico
            $id = $_GET['id'] ?? 0;
            $stmt = $db->prepare("SELECT * FROM gastos_empleados WHERE id = ?");
            $stmt->execute([$id]);
            $gasto = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($gasto) {
                echo json_encode(['success' => true, 'data' => $gasto]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Gasto no encontrado']);
            }
            break;

        case 'create':
            // Crear nuevo gasto
            $empleado_id = $_POST['empleado_id'] ?? 0;
            $concepto = $_POST['concepto'] ?? '';
            $descripcion = $_POST['descripcion'] ?? '';
            $monto = $_POST['monto'] ?? 0;
            $fecha = $_POST['fecha'] ?? date('Y-m-d');
            $estado = $_POST['estado'] ?? 'pendiente';

            // Validaciones
            if (empty($empleado_id) || empty($concepto) || empty($monto)) {
                echo json_encode(['success' => false, 'message' => 'Faltan datos requeridos']);
                break;
            }

            if ($monto <= 0) {
                echo json_encode(['success' => false, 'message' => 'El monto debe ser mayor a 0']);
                break;
            }

            $stmt = $db->prepare("
                INSERT INTO gastos_empleados (empleado_id, concepto, descripcion, monto, fecha, estado)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            
            if ($stmt->execute([$empleado_id, $concepto, $descripcion, $monto, $fecha, $estado])) {
                echo json_encode(['success' => true, 'message' => 'Gasto registrado exitosamente']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al registrar el gasto']);
            }
            break;

        case 'update':
            // Actualizar gasto
            $id = $_POST['id'] ?? 0;
            $empleado_id = $_POST['empleado_id'] ?? 0;
            $concepto = $_POST['concepto'] ?? '';
            $descripcion = $_POST['descripcion'] ?? '';
            $monto = $_POST['monto'] ?? 0;
            $fecha = $_POST['fecha'] ?? date('Y-m-d');
            $estado = $_POST['estado'] ?? 'pendiente';

            if (empty($id) || empty($empleado_id) || empty($concepto) || empty($monto)) {
                echo json_encode(['success' => false, 'message' => 'Faltan datos requeridos']);
                break;
            }

            if ($monto <= 0) {
                echo json_encode(['success' => false, 'message' => 'El monto debe ser mayor a 0']);
                break;
            }

            $stmt = $db->prepare("
                UPDATE gastos_empleados 
                SET empleado_id = ?, concepto = ?, descripcion = ?, monto = ?, fecha = ?, estado = ?
                WHERE id = ?
            ");
            
            if ($stmt->execute([$empleado_id, $concepto, $descripcion, $monto, $fecha, $estado, $id])) {
                echo json_encode(['success' => true, 'message' => 'Gasto actualizado exitosamente']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al actualizar el gasto']);
            }
            break;

        case 'delete':
            // Eliminar gasto
            $id = $_POST['id'] ?? 0;
            
            if (empty($id)) {
                echo json_encode(['success' => false, 'message' => 'ID no válido']);
                break;
            }

            $stmt = $db->prepare("DELETE FROM gastos_empleados WHERE id = ?");
            
            if ($stmt->execute([$id])) {
                echo json_encode(['success' => true, 'message' => 'Gasto eliminado exitosamente']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al eliminar el gasto']);
            }
            break;

        case 'por_empleado':
            // Obtener gastos de un empleado específico
            $empleado_id = $_GET['empleado_id'] ?? 0;
            $estado = $_GET['estado'] ?? null;
            
            $sql = "
                SELECT g.*, CONCAT(e.nombre, ' ', e.apellido) as empleado_nombre
                FROM gastos_empleados g
                INNER JOIN empleados e ON g.empleado_id = e.id
                WHERE g.empleado_id = ?
            ";
            
            $params = [$empleado_id];
            
            if ($estado) {
                $sql .= " AND g.estado = ?";
                $params[] = $estado;
            }
            
            $sql .= " ORDER BY g.fecha DESC";
            
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $gastos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Calcular total
            $total = array_sum(array_column($gastos, 'monto'));
            
            echo json_encode([
                'success' => true, 
                'data' => $gastos,
                'total' => $total
            ]);
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Acción no válida']);
            break;
    }
} catch (PDOException $e) {
    error_log("Error en api_gastos.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error en la base de datos']);
} catch (Exception $e) {
    error_log("Error en api_gastos.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error interno del servidor']);
}
?>
