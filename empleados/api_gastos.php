<?php
/**
 * API para Gastos Operativos de Sucursales (Sin empleados)
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
$currentUser = $auth->getCurrentUser();
$action = $_GET['action'] ?? $_POST['action'] ?? '';

// DETECCIÓN AUTOMÁTICA DE SUCURSAL DEL USUARIO - DESHABILITADO PARA VER TODOS
$sucursalId = null; // Mostrar todos los gastos
// $stmtSuc = $db->prepare("SELECT id FROM sucursales WHERE responsable_id = ? OR id = (SELECT sucursal_id FROM usuarios WHERE id = ?)");
// $stmtSuc->execute([$currentUser['id'], $currentUser['id']]);
// $resSuc = $stmtSuc->fetch();
// if ($resSuc) {
//     $sucursalId = $resSuc['id'];
// }

try {
    switch ($action) {
        case 'list':
            $mes = $_GET['mes'] ?? date('m');
            $anio = $_GET['anio'] ?? date('Y');
            $filtroSucursal = $_GET['sucursal_id'] ?? null;
            
            $sql = "
                SELECT g.*, s.nombre as sucursal_nombre
                FROM gastos_varios g
                INNER JOIN sucursales s ON g.sucursal_id = s.id
                WHERE 1=1
            ";
            
            $params = [];
            
            // Si el usuario tiene sucursal asignada, se fuerza el filtro
            if ($userSucursalId) {
                $sql .= " AND g.sucursal_id = ?";
                $params[] = $userSucursalId;
            } elseif ($filtroSucursal) {
                // Si es admin y seleccionó una sucursal
                $sql .= " AND g.sucursal_id = ?";
                $params[] = $filtroSucursal;
            }
            
            if ($mes && $anio) {
                $sql .= " AND MONTH(g.fecha) = ? AND YEAR(g.fecha) = ?";
                $params[] = $mes;
                $params[] = $anio;
            }
            
            $sql .= " ORDER BY g.fecha DESC, g.id DESC";
            
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $gastos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $saldoSucursal = 0;
            // Calcular saldo de la sucursal visible
            $sucursalParaSaldo = $userSucursalId ?: $filtroSucursal;
            
            if ($sucursalParaSaldo) {
                $stmtSaldo = $db->prepare("SELECT saldo FROM sucursales WHERE id = ?");
                $stmtSaldo->execute([$sucursalParaSaldo]);
                $saldoSucursal = $stmtSaldo->fetchColumn();
            }
            
            echo json_encode([
                'success' => true, 
                'data' => $gastos, 
                'saldo_sucursal' => $saldoSucursal
            ]);
            break;

        case 'create':
            $concepto = $_POST['concepto'] ?? '';
            $descripcion = $_POST['descripcion'] ?? '';
            $monto = floatval($_POST['monto'] ?? 0);
            $fecha = $_POST['fecha'] ?? date('Y-m-d');
            $sucursalPost = $_POST['sucursal_id'] ?? null;
            
            $finalSucursalId = null;

            if ($userSucursalId) {
                $finalSucursalId = $userSucursalId;
            } else {
                if (empty($sucursalPost)) throw new Exception('Debe seleccionar una sucursal');
                $finalSucursalId = $sucursalPost;
            }
            
            if (empty($concepto) || $monto <= 0) throw new Exception('Concepto y monto válido son requeridos');

            $db->beginTransaction();
            
            $stmt = $db->prepare("
                INSERT INTO gastos_varios (sucursal_id, concepto, descripcion, monto, fecha, creado_por)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$finalSucursalId, $concepto, $descripcion, $monto, $fecha, $currentUser['id']]);
            
            $stmtUpdate = $db->prepare("UPDATE sucursales SET saldo = saldo - ? WHERE id = ?");
            $stmtUpdate->execute([$monto, $finalSucursalId]);
            
            $db->commit();
            echo json_encode(['success' => true, 'message' => 'Gasto registrado y descontado de caja']);
            break;

        case 'delete':
            $id = $_POST['id'] ?? 0;
            $db->beginTransaction();
            
            $stmt = $db->prepare("SELECT monto, sucursal_id, estado FROM gastos_varios WHERE id = ?");
            $stmt->execute([$id]);
            $gasto = $stmt->fetch();
            
            if (!$gasto) throw new Exception('Gasto no encontrado');
            
            // Verificar permisos para borrar
            if ($userSucursalId && $gasto['sucursal_id'] != $userSucursalId) {
                throw new Exception('No tiene permiso para cancelar gastos de otra sucursal');
            }

            if ($gasto['estado'] === 'cancelado') throw new Exception('El gasto ya está cancelado');

            $stmtUpdate = $db->prepare("UPDATE sucursales SET saldo = saldo + ? WHERE id = ?");
            $stmtUpdate->execute([$gasto['monto'], $gasto['sucursal_id']]);
            
            $stmtDelete = $db->prepare("UPDATE gastos_varios SET estado = 'cancelado' WHERE id = ?");
            $stmtDelete->execute([$id]);
            
            $db->commit();
            echo json_encode(['success' => true, 'message' => 'Gasto cancelado y monto devuelto a caja']);
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Acción no válida']);
    }
} catch (Exception $e) {
    if ($db->inTransaction()) $db->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
