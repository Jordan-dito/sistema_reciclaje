<?php
/**
 * API Backend para Gestión de Asistencia y Pagos Diarios
 * CON CONTROL DE CAJA Y FILTRADO POR SUCURSAL
 */
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json');

// Configurar zona horaria
date_default_timezone_set('America/Guayaquil');

$auth = new Auth();
if (!$auth->isAuthenticated()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

$db = getDB();
$action = $_REQUEST['action'] ?? '';
$currentUser = $auth->getCurrentUser();

// DETECCIÓN AUTOMÁTICA DE SUCURSAL DEL USUARIO
// Buscamos si el usuario es responsable de alguna sucursal
$stmtSuc = $db->prepare("SELECT id, nombre FROM sucursales WHERE responsable_id = ?");
$stmtSuc->execute([$currentUser['id']]);
$miSucursal = $stmtSuc->fetch();
$sucursalId = $miSucursal ? $miSucursal['id'] : null;

try {
    switch ($action) {
        case 'get_semana':
            obtenerDatosSemana($db, $sucursalId);
            break;
            
        case 'toggle_asistencia':
            guardarAsistencia($db, $currentUser['id']);
            break;
            
        case 'save_config_dias':
            guardarConfigDias($db, $sucursalId);
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

function obtenerDatosSemana($db, $filtroSucursalId) {
    $fechaRef = $_GET['fecha'] ?? date('Y-m-d');
    $timestamp = strtotime($fechaRef);
    
    // Calcular Lunes
    $diaSemana = date('w', $timestamp);
    if ($diaSemana == 0) {
        $lunes = date('Y-m-d', strtotime('monday last week', $timestamp));
    } else {
        $lunes = date('Y-m-d', strtotime('monday this week', $timestamp));
    }
    $domingo = date('Y-m-d', strtotime($lunes . ' +6 days'));
    
    // 1. Configuración de Días (Prioridad: Configuración de Sucursal > Configuración Global)
    $diasLaborables = ['lun','mar','mie','jue','vie','sab']; // Default
    
    $sqlConfig = "SELECT dias_laborables FROM configuracion_jornada WHERE semana_inicio = ?";
    $paramsConfig = [$lunes];
    
    if ($filtroSucursalId) {
        // Si hay sucursal, buscar configuración específica
        $sqlConfig .= " AND sucursal_id = ?";
        $paramsConfig[] = $filtroSucursalId;
    } else {
        // Si es admin global, buscar la global (NULL)
        $sqlConfig .= " AND sucursal_id IS NULL";
    }
    
    $stmtConfig = $db->prepare($sqlConfig);
    $stmtConfig->execute($paramsConfig);
    $configRow = $stmtConfig->fetch();
    
    if ($configRow) {
        $diasLaborables = json_decode($configRow['dias_laborables'], true);
    } else if ($filtroSucursalId) {
        // Fallback: Si la sucursal no tiene config propia, intentar buscar la global
        $stmtGlobal = $db->prepare("SELECT dias_laborables FROM configuracion_jornada WHERE semana_inicio = ? AND sucursal_id IS NULL");
        $stmtGlobal->execute([$lunes]);
        if ($rowGlobal = $stmtGlobal->fetch()) {
            $diasLaborables = json_decode($rowGlobal['dias_laborables'], true);
        }
    }

    // 2. Obtener Empleados (Filtrado por sucursal si corresponde)
    $sqlEmp = "
        SELECT e.id, e.nombres, e.apellidos, e.tarifa_diaria as tarifa, s.nombre as sucursal, s.saldo as saldo_sucursal
        FROM empleados e 
        LEFT JOIN sucursales s ON e.sucursal_id = s.id 
        WHERE e.estado = 'ACTIVO'
    ";
    
    if ($filtroSucursalId) {
        $sqlEmp .= " AND e.sucursal_id = " . intval($filtroSucursalId);
    }
    
    $sqlEmp .= " ORDER BY s.nombre, e.apellidos";
    
    $stmtEmp = $db->query($sqlEmp);
    $empleados = $stmtEmp->fetchAll();

    // 3. Obtener Asistencias
    $stmtAsist = $db->prepare("SELECT empleado_id, fecha FROM asistencias WHERE fecha BETWEEN ? AND ? AND estado='asistio'");
    $stmtAsist->execute([$lunes, $domingo]);
    $asistenciasMap = [];
    foreach ($stmtAsist->fetchAll() as $row) {
        $asistenciasMap[$row['empleado_id']][$row['fecha']] = true;
    }

    // 4. Obtener Pagos
    $pagosMap = [];
    try {
        $stmtPagos = $db->prepare("SELECT empleado_id, fecha_laborada, monto FROM pagos_diarios WHERE fecha_laborada BETWEEN ? AND ?");
        $stmtPagos->execute([$lunes, $domingo]);
        foreach($stmtPagos->fetchAll() as $p) {
            $pagosMap[$p['empleado_id']][$p['fecha_laborada']] = $p['monto'];
        }
    } catch (Exception $e) { }

    // Estructurar respuesta
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
            'saldo_sucursal' => $emp['saldo_sucursal'],
            'tarifa' => $emp['tarifa'] ?? 18.00,
            'dias' => $diasData,
            'total_pagado' => $totalPagadoSemana
        ];
    }

    echo json_encode([
        'success' => true,
        'lunes' => $lunes,
        'dias_laborables' => $diasLaborables,
        'empleados' => $dataEmpleados,
        'sucursal_id' => $filtroSucursalId // Para debug
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

function guardarConfigDias($db, $sucursalId) {
    $lunes = $_POST['semana_inicio'];
    $dias = $_POST['dias'] ?? [];
    
    // Si no hay sucursalId (es Admin global), guardamos como NULL (global)
    // OJO: La tabla tiene Unique Key (semana_inicio, sucursal_id). 
    // Si sucursal_id es NULL, MySQL permite multiples NULL en UNIQUE KEY a menos que sea MySQL 5.7+ configurado estricto.
    // Para evitar líos, usamos la consulta con ON DUPLICATE KEY UPDATE.
    
    if ($sucursalId) {
        $stmt = $db->prepare("INSERT INTO configuracion_jornada (semana_inicio, dias_laborables, sucursal_id) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE dias_laborables = VALUES(dias_laborables)");
        $stmt->execute([$lunes, json_encode($dias), $sucursalId]);
    } else {
        // Configuración Global (Solo Admin sin sucursal)
        // Revisar si ya existe para NULL (el ON DUPLICATE no siempre funciona bien con NULL en UNIQUE indexes dependiendo del motor)
        $stmtCheck = $db->prepare("SELECT id FROM configuracion_jornada WHERE semana_inicio = ? AND sucursal_id IS NULL");
        $stmtCheck->execute([$lunes]);
        $exist = $stmtCheck->fetch();
        
        if ($exist) {
            $stmt = $db->prepare("UPDATE configuracion_jornada SET dias_laborables = ? WHERE id = ?");
            $stmt->execute([json_encode($dias), $exist['id']]);
        } else {
            $stmt = $db->prepare("INSERT INTO configuracion_jornada (semana_inicio, dias_laborables, sucursal_id) VALUES (?, ?, NULL)");
            $stmt->execute([$lunes, json_encode($dias)]);
        }
    }
    
    echo json_encode(['success' => true]);
}

function pagarDia($db, $userId) {
    $empleadoId = $_POST['empleado_id'];
    $fecha = $_POST['fecha'];
    $monto = $_POST['monto'];

    if (!is_numeric($monto) || $monto < 0) throw new Exception("Monto inválido");

    $db->beginTransaction();

    try {
        $stmtCheck = $db->prepare("SELECT monto FROM pagos_diarios WHERE empleado_id = ? AND fecha_laborada = ?");
        $stmtCheck->execute([$empleadoId, $fecha]);
        $pagoExistente = $stmtCheck->fetch();
        
        $montoAnterior = 0;
        if ($pagoExistente) {
            $montoAnterior = $pagoExistente['monto'];
        }

        $stmtSuc = $db->prepare("SELECT sucursal_id FROM empleados WHERE id = ?");
        $stmtSuc->execute([$empleadoId]);
        $emp = $stmtSuc->fetch();
        if (!$emp || !$emp['sucursal_id']) {
            throw new Exception("El empleado no tiene sucursal asignada. No se puede descontar de caja.");
        }
        $sucursalId = $emp['sucursal_id'];

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

        $diferencia = $monto - $montoAnterior;
        $stmtUpdSuc = $db->prepare("UPDATE sucursales SET saldo = saldo - ? WHERE id = ?");
        $stmtUpdSuc->execute([$diferencia, $sucursalId]);

        $db->commit();
        echo json_encode(['success' => true]);

    } catch (Exception $e) {
        $db->rollBack();
        throw $e;
    }
}

function eliminarPagoDia($db) {
    $empleadoId = $_POST['empleado_id'];
    $fecha = $_POST['fecha'];

    $db->beginTransaction();
    try {
        $stmtGet = $db->prepare("SELECT monto FROM pagos_diarios WHERE empleado_id = ? AND fecha_laborada = ?");
        $stmtGet->execute([$empleadoId, $fecha]);
        $pago = $stmtGet->fetch();

        if (!$pago) throw new Exception("El pago no existe");
        $monto = $pago['monto'];

        $stmtSuc = $db->prepare("SELECT sucursal_id FROM empleados WHERE id = ?");
        $stmtSuc->execute([$empleadoId]);
        $sucursalId = $stmtSuc->fetchColumn();

        $stmtDel = $db->prepare("DELETE FROM pagos_diarios WHERE empleado_id = ? AND fecha_laborada = ?");
        $stmtDel->execute([$empleadoId, $fecha]);

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
