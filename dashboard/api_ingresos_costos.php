<?php
/**
 * API para Ingresos y Costos por Periodo
 * Sistema de Gestión de Reciclaje
 */

// Desactivar visualización de errores directos
error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');

header('Content-Type: application/json; charset=utf-8');

try {
    require_once __DIR__ . '/../config/database.php';
    require_once __DIR__ . '/../config/auth.php';

    // Verificar autenticación
    $auth = new Auth();
    if (!$auth->isAuthenticated()) {
        throw new Exception('No autorizado');
    }

    $db = getDB();

    // Filtros
    $sucursal_id = isset($_GET['sucursal_id']) && $_GET['sucursal_id'] !== '' ? intval($_GET['sucursal_id']) : null;
    $anio = isset($_GET['anio']) && $_GET['anio'] !== '' ? intval($_GET['anio']) : null;
    $mes = isset($_GET['mes']) && $_GET['mes'] !== '' ? intval($_GET['mes']) : null;

    // Determinar agrupación y rango
    // Si no hay año, agrupamos por Año (histórico)
    // Si hay año pero no mes, agrupamos por Mes (de ese año)
    // Si hay año y mes, agrupamos por Día (de ese mes)
    
    $groupBy = 'year';
    $dateFormat = '%Y'; // Formato para GROUP BY en MySQL
    
    if ($anio && !$mes) {
        $groupBy = 'month';
        $dateFormat = '%c'; // Mes numérico 1-12
    } elseif ($anio && $mes) {
        $groupBy = 'day';
        $dateFormat = '%e'; // Día del mes 1-31
    }

    // Construir condiciones
    $params = [];
    $where = "estado = 'completada'";
    
    if ($sucursal_id) {
        $where .= " AND sucursal_id = ?";
        $params[] = $sucursal_id;
    }
    
    if ($anio) {
        $where .= " AND YEAR(fecha_compra) = ?"; // Nota: para ventas se cambiará el campo dinámicamente
        $params[] = $anio;
    }
    
    if ($mes) {
        $where .= " AND MONTH(fecha_compra) = ?";
        $params[] = $mes;
    }

    // Función auxiliar para obtener datos
    function obtenerDatos($db, $tabla, $campoFecha, $where, $params, $dateFormat) {
        // Ajustar el WHERE para el campo de fecha correcto
        $whereLocal = str_replace('fecha_compra', $campoFecha, $where);
        
        $sql = "SELECT DATE_FORMAT($campoFecha, '$dateFormat') as periodo, SUM(total) as total 
                FROM $tabla 
                WHERE $whereLocal 
                GROUP BY periodo 
                ORDER BY MIN($campoFecha)"; // Ordenar cronológicamente
                
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $resultados = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $resultados[$row['periodo']] = floatval($row['total']);
        }
        return $resultados;
    }

    $datosCompras = obtenerDatos($db, 'compras', 'fecha_compra', $where, $params, $dateFormat);
    $datosVentas = obtenerDatos($db, 'ventas', 'fecha_venta', $where, $params, $dateFormat);

    // Unificar periodos
    $periodos = array_unique(array_merge(array_keys($datosCompras), array_keys($datosVentas)));
    
    // Ordenar periodos numéricamente
    usort($periodos, function($a, $b) {
        return intval($a) - intval($b);
    });

    $data = [];
    foreach ($periodos as $p) {
        $data[] = [
            'periodo' => intval($p),
            'ventas' => $datosVentas[$p] ?? 0,
            'compras' => $datosCompras[$p] ?? 0
        ];
    }

    echo json_encode([
        'success' => true, 
        'data' => $data,
        'labelType' => ($groupBy === 'year' ? 'anio' : ($groupBy === 'month' ? 'mes' : 'dia'))
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
