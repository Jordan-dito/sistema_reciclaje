<?php
/**
 * API Backend para Gestión de Asistencia y Pagos Diarios
 * CON CONTROL DE CAJA (Resta automática de saldo)
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
$currentUser = $auth->getCurrentUser();

try {
    switch ($action) {
        case 'get_semana':
            obtenerDatosSemana($db);
            break;
            
        case 'toggle_asistencia':
            guardarAsistencia($db, $currentUser['id']);
            break;
            
        case 'save_config_dias':
            guardarConfigDias($db);
            break;
            
        case 'pagar_dia':
            pagarDia($db, $currentUser['id']);
            break;
        
        case 'eliminar_pago_dia':
            eliminarPagoDia($db);
            break;
            
        default:
            throw new Exception("Acción no válida");
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

function obtenerDatosSemana($db) {
    $fechaRef = $_GET['fecha'] ?? date('Y-m-d');
    $timestamp = strtotime($fechaRef);
    
    if (date('w', $timestamp) == 0) {
        $lunes = date('Y-m-d', strtotime('monday last week', $timestamp));
    } else {
        $lunes = date('Y-m-d', strtotime('monday this week', $timestamp));
    }
    $domingo = date('Y-m-d', strtotime($lunes . ' +6 days'));
    
    // Configuración Días
    $stmtConfig = $db->prepare("SELECT dias_laborables FROM configuracion_jornada WHERE semana_inicio = ?");
    $stmtConfig->execute([$lunes]);
    $configRow = $stmtConfig->fetch();
    $diasLaborables = $configRow ? json_decode($configRow['dias_laborables'], true) : ['lun','mar','mie','jue','vie','sab'];

    // Empleados
    $stmtEmp = $db->query("
        SELECT e.id, e.nombres, e.apellidos, e.tarifa_diaria as tarifa, s.nombre as sucursal, s.saldo as saldo_sucursal
        FROM empleados e 
        LEFT JOIN sucursales s ON e.sucursal_id = s.id 
        WHERE e.estado = 'ACTIVO'
        ORDER BY s.nombre, e.apellidos
    ");
    $empleados = $stmtEmp->fetchAll();

    // Asistencias
    $stmtAsist = $db->prepare("SELECT empleado_id, fecha FROM asistencias WHERE fecha BETWEEN ? AND ? AND estado='asistio'");
    $stmtAsist->execute([$lunes, $domingo]);
    $asistenciasMap = [];
    foreach ($stmtAsist->fetchAll() as $row) {
        $asistenciasMap[$row['empleado_id']][$row['fecha']] = true;
    }

    // Pagos
    $pagosMap = [];
    try {
        $stmtPagos = $db->prepare("SELECT empleado_id, fecha_laborada, monto FROM pagos_diarios WHERE fecha_laborada BETWEEN ? AND ?");
        $stmtPagos->execute([$lunes, $domingo]);
        foreach($stmtPagos->fetchAll() as $p) {
            $pagosMap[$p['empleado_id']][$p['fecha_laborada']] = $p['monto'];
        }
    } catch (Exception $e) { }

    // Estructurar
    $dataEmpleados = [];
    $fechasCalculadas = [];
    for($i=0; $i<7; $i++) {
        $fechasCalculadas[] = date('Y-m-d', strtotime($lunes . " +$i days"));
    }

    foreach ($empleados as $emp) {
        $diasData = [];
        $totalPagadoSemana = 0;
        
        foreach ($fechasCalculadas as $fecha) {
            $montoPago = $pagosMap[$emp['id']][$fecha] ?? null;
            if ($montoPago !== null) $totalPagadoSemana += $montoPago;

            $diasData[] = [
                'asistio' => isset($asistenciasMap[$emp['id']][$fecha]),
                'pagado' => ($montoPago !== null),
                'monto' => $montoPago
            ];
        }

        $dataEmpleados[] = [
            'id' => $emp['id'],
            'n' => $emp['nombres'] . ' ' . $emp['apellidos'],
            's' => $emp['sucursal'] ?? 'Sin Sucursal',
            'saldo_sucursal' => $emp['saldo_sucursal'], // Dato extra para mostrar si hay fondos
            'tarifa' => $emp['tarifa'] ?? 18.00,
            'dias' => $diasData,
            'total_pagado' => $totalPagadoSemana
        ];
    }

    echo json_encode([
        'success' => true,
        'lunes' => $lunes,
        'dias_laborables' => $diasLaborables,
        'empleados' => $dataEmpleados
    ]);
}

function guardarAsistencia($db, $userId) {
    $empleadoId = $_POST['empleado_id'];
    $fecha = $_POST['fecha'];
    $estado = $_POST['estado'] == 1 ? 'asistio' : 'falta';

    if ($estado === 'asistio') {
        $stmt = $db->prepare("INSERT INTO asistencias (empleado_id, fecha, estado, registrado_por) VALUES (?, ?, 'asistio', ?) ON DUPLICATE KEY UPDATE estado = 'asistio'");
        $stmt->execute([$empleadoId, $fecha, $userId]);
    } else {
        $stmtCheck = $db->prepare("SELECT id FROM pagos_diarios WHERE empleado_id = ? AND fecha_laborada = ?");
        $stmtCheck->execute([$empleadoId, $fecha]);
        if ($stmtCheck->fetch()) {
            throw new Exception("No puedes quitar la asistencia de un día ya pagado. Elimina el pago primero.");
        }
        $stmt = $db->prepare("DELETE FROM asistencias WHERE empleado_id = ? AND fecha = ?");
        $stmt->execute([$empleadoId, $fecha]);
    }
    echo json_encode(['success' => true]);
}

function guardarConfigDias($db) {
    $lunes = $_POST['semana_inicio'];
    $dias = $_POST['dias'] ?? [];
    $stmt = $db->prepare("INSERT INTO configuracion_jornada (semana_inicio, dias_laborables) VALUES (?, ?) ON DUPLICATE KEY UPDATE dias_laborables = VALUES(dias_laborables)");
    $stmt->execute([$lunes, json_encode($dias)]);
    echo json_encode(['success' => true]);
}

/**
 * PAGA EL DÍA Y RESTA DE LA CAJA DE LA SUCURSAL
 */
function pagarDia($db, $userId) {
    $empleadoId = $_POST['empleado_id'];
    $fecha = $_POST['fecha'];
    $monto = $_POST['monto'];

    if (!is_numeric($monto) || $monto < 0) throw new Exception("Monto inválido");

    // Iniciar Transacción (Todo o nada)
    $db->beginTransaction();

    try {
        // 1. Verificar si ya estaba pagado (para ajustar la resta si es una edición de monto)
        $stmtCheck = $db->prepare("SELECT monto FROM pagos_diarios WHERE empleado_id = ? AND fecha_laborada = ?");
        $stmtCheck->execute([$empleadoId, $fecha]);
        $pagoExistente = $stmtCheck->fetch();
        
        $montoAnterior = 0;
        if ($pagoExistente) {
            $montoAnterior = $pagoExistente['monto'];
        }

        // 2. Obtener Sucursal del Empleado
        $stmtSuc = $db->prepare("SELECT sucursal_id FROM empleados WHERE id = ?");
        $stmtSuc->execute([$empleadoId]);
        $emp = $stmtSuc->fetch();
        if (!$emp || !$emp['sucursal_id']) {
            throw new Exception("El empleado no tiene sucursal asignada. No se puede descontar de caja.");
        }
        $sucursalId = $emp['sucursal_id'];

        // 3. Insertar/Actualizar Pago
        // Asegurar asistencia
        $stmtAsist = $db->prepare("SELECT id FROM asistencias WHERE empleado_id = ? AND fecha = ?");
        $stmtAsist->execute([$empleadoId, $fecha]);
        if (!$stmtAsist->fetch()) {
            $stmtIns = $db->prepare("INSERT INTO asistencias (empleado_id, fecha, estado, registrado_por) VALUES (?, ?, 'asistio', ?)");
            $stmtIns->execute([$empleadoId, $fecha, $userId]);
        }

        $sqlPago = "INSERT INTO pagos_diarios (empleado_id, fecha_laborada, monto, registrado_por) 
                    VALUES (?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE monto = VALUES(monto), registrado_por = VALUES(registrado_por)";
        $stmtPago = $db->prepare($sqlPago);
        $stmtPago->execute([$empleadoId, $fecha, $monto, $userId]);

        // 4. Actualizar Saldo Sucursal
        // La diferencia es: NuevoMonto - MontoAnterior
        // Si es pago nuevo: Monto - 0 = Restamos Monto
        // Si edito de 18 a 20: 20 - 18 = 2. Restamos 2 adicionales.
        // Si edito de 20 a 15: 15 - 20 = -5. Restamos -5 (Sumamos 5).
        $diferencia = $monto - $montoAnterior;
        
        // Verificar saldo suficiente (Opcional: permitir negativo si es caja chica flexible)
        /*
        $stmtSaldo = $db->prepare("SELECT saldo FROM sucursales WHERE id = ?");
        $stmtSaldo->execute([$sucursalId]);
        $saldoActual = $stmtSaldo->fetchColumn();
        if ($saldoActual < $diferencia) {
             throw new Exception("Saldo insuficiente en la sucursal.");
        }
        */

        $stmtUpdSuc = $db->prepare("UPDATE sucursales SET saldo = saldo - ? WHERE id = ?");
        $stmtUpdSuc->execute([$diferencia, $sucursalId]);

        $db->commit();
        echo json_encode(['success' => true]);

    } catch (Exception $e) {
        $db->rollBack();
        throw $e;
    }
}

/**
 * ELIMINA PAGO Y DEVUELVE DINERO A LA SUCURSAL
 */
function eliminarPagoDia($db) {
    $empleadoId = $_POST['empleado_id'];
    $fecha = $_POST['fecha'];

    $db->beginTransaction();
    try {
        // 1. Obtener datos del pago antes de borrar
        $stmtGet = $db->prepare("SELECT monto FROM pagos_diarios WHERE empleado_id = ? AND fecha_laborada = ?");
        $stmtGet->execute([$empleadoId, $fecha]);
        $pago = $stmtGet->fetch();

        if (!$pago) throw new Exception("El pago no existe");
        $monto = $pago['monto'];

        // 2. Obtener Sucursal
        $stmtSuc = $db->prepare("SELECT sucursal_id FROM empleados WHERE id = ?");
        $stmtSuc->execute([$empleadoId]);
        $sucursalId = $stmtSuc->fetchColumn();

        // 3. Borrar Pago
        $stmtDel = $db->prepare("DELETE FROM pagos_diarios WHERE empleado_id = ? AND fecha_laborada = ?");
        $stmtDel->execute([$empleadoId, $fecha]);

        // 4. Devolver dinero a Sucursal (UPDATE saldo = saldo + monto)
        if ($sucursalId) {
            $stmtUpd = $db->prepare("UPDATE sucursales SET saldo = saldo + ? WHERE id = ?");
            $stmtUpd->execute([$monto, $sucursalId]);
        }

        $db->commit();
        echo json_encode(['success' => true]);

    } catch (Exception $e) {
        $db->rollBack();
        throw $e;
    }
}
