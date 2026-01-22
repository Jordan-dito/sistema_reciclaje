<?php
/**
 * API para Gastos Operativos de Sucursales (Sin empleados)
 * Sistema de Gestión de Reciclaje
 */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/database.php';

try {
    $auth = new Auth();
    if (!$auth->isAuthenticated()) {
        echo json_encode(['success' => false, 'message' => 'No autorizado']);
        exit;
    }

    $db = getDB();
    $currentUser = $auth->getCurrentUser();
    $action = $_GET['action'] ?? $_POST['action'] ?? '';

    // DETECCIÓN AUTOMÁTICA DE SUCURSAL DEL USUARIO (Si no es administrador)
    $userSucursalId = null;
    $esAdmin = (strtolower($currentUser['rol']) === 'administrador');

    if (!$esAdmin) {
        try {
            $stmtSuc = $db->prepare("SELECT id FROM sucursales WHERE responsable_id = ? OR id = (SELECT sucursal_id FROM usuarios WHERE id = ?)");
            $stmtSuc->execute([$currentUser['id'], $currentUser['id']]);
            $resSuc = $stmtSuc->fetch();
            if ($resSuc) {
                $userSucursalId = $resSuc['id'];
            }
        } catch (Exception $e) {
            error_log("Error detectando sucursal: " . $e->getMessage());
        }
    }

    // Borrar el encabezado si ya se ha enviado para evitar errores de "headers already sent"
    if (headers_sent()) {
        ob_end_clean();
    } else {
        header('Content-Type: application/json; charset=utf-8');
    }

    error_log("DEBUG GASTOS: user_id=" . $currentUser['id'] . " rol=" . $currentUser['rol'] . " userSucursalId=" . ($userSucursalId ?? 'NULL') . " filtroSucursal=" . ($filtroSucursal ?? 'NULL'));

    switch ($action) {
        case 'list':
            $mes = $_GET['mes'] ?? date('m');
            $anio = $_GET['anio'] ?? date('Y');
            $filtroSucursal = $_GET['sucursal_id'] ?? null;
            
            // Re-log after getting params
            error_log("DEBUG GASTOS LIST: mes=$mes anio=$anio filtroSucursal=$filtroSucursal");
            
            $sql = "
                SELECT g.*, s.nombre as sucursal_nombre,
                       DATE_FORMAT(g.fecha, '%M') as mes_nombre
                FROM gastos_varios g
                INNER JOIN sucursales s ON g.sucursal_id = s.id
                WHERE 1=1
            ";
            
            $params = [];
            
            // Si el usuario tiene sucursal asignada (y no es admin), se fuerza el filtro
            if ($userSucursalId) {
                $sql .= " AND g.sucursal_id = ?";
                $params[] = $userSucursalId;
            } elseif (!empty($filtroSucursal)) {
                // Si es admin y seleccionó una sucursal específica
                $sql .= " AND g.sucursal_id = ?";
                $params[] = $filtroSucursal;
            }
            
            if (!empty($mes) && !empty($anio)) {
                $sql .= " AND MONTH(g.fecha) = ? AND YEAR(g.fecha) = ?";
                $params[] = $mes;
                $params[] = $anio;
            }
            
            $sql .= " ORDER BY g.fecha DESC, g.id DESC";
            
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $gastos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Mapeo de meses en español
            $mesesES = [
                'January' => 'Enero', 'February' => 'Febrero', 'March' => 'Marzo',
                'April' => 'Abril', 'May' => 'Mayo', 'June' => 'Junio',
                'July' => 'Julio', 'August' => 'Agosto', 'September' => 'Septiembre',
                'October' => 'Octubre', 'November' => 'Noviembre', 'December' => 'Diciembre'
            ];

            foreach ($gastos as &$g) {
                $g['mes_nombre_es'] = $mesesES[$g['mes_nombre']] ?? $g['mes_nombre'];
            }
            
            $saldoSucursal = 0;
            // Calcular saldo: Prioridad sucursal asignada, luego filtro seleccionado
            $sucursalParaSaldo = $userSucursalId ?: $filtroSucursal;
            
            error_log("DEBUG GASTOS SALDO: sucursalParaSaldo=$sucursalParaSaldo");

            if (!empty($sucursalParaSaldo)) {
                $stmtSaldo = $db->prepare("SELECT saldo FROM sucursales WHERE id = ?");
                $stmtSaldo->execute([$sucursalParaSaldo]);
                $saldoSucursal = $stmtSaldo->fetchColumn();
                // Asegurar que sea número
                $saldoSucursal = $saldoSucursal !== false ? floatval($saldoSucursal) : 0;
            } else if ($esAdmin && empty($filtroSucursal)) {
                // Si es admin y ve "Todas", mostrar suma total (opcional, por ahora 0)
                $saldoSucursal = 0;
            }
            
            error_log("DEBUG GASTOS RESULT: saldo=$saldoSucursal count=" . count($gastos));
            
            $response = [
                'success' => true, 
                'data' => $gastos, 
                'saldo_sucursal' => $saldoSucursal
            ];

            if (defined('APP_DEBUG') && APP_DEBUG) {
                $response['debug'] = [
                    'user_id' => $currentUser['id'],
                    'rol' => $currentUser['rol'],
                    'user_sucursal_id' => $userSucursalId,
                    'filtro_sucursal' => $filtroSucursal,
                    'sucursal_para_saldo' => $sucursalParaSaldo,
                    'params' => $params
                ];
            }
            
            echo json_encode($response);
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

            // Validar saldo suficiente
            $stmtSaldo = $db->prepare("SELECT saldo, nombre FROM sucursales WHERE id = ?");
            $stmtSaldo->execute([$finalSucursalId]);
            $sucursalData = $stmtSaldo->fetch();
            
            if (!$sucursalData) throw new Exception("Sucursal no encontrada.");
            
            if ($sucursalData['saldo'] < $monto) {
                throw new Exception("Saldo insuficiente en la caja de '{$sucursalData['nombre']}'. Saldo disponible: $" . number_format($sucursalData['saldo'], 2));
            }

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
