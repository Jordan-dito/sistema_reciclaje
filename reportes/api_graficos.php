<?php
/**
 * API para obtener datos de gráficos consolidados por sucursal
 * para la aplicación móvil.
 */

error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *'); // Permitir acceso desde la app Flutter
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
ob_start();

// Manejar pre-vuelos OPTIONS de CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

try {
    require_once __DIR__ . '/../config/database.php';
    require_once __DIR__ . '/../config/ErrorHandler.php';

    $db = getDB();
    $action = $_GET['action'] ?? '';

    if (empty($action)) {
        throw new Exception('Acción no especificada');
    }

    switch ($action) {
        case 'gastos_compras_por_sucursal':
            // Obtener el mes y año para filtrar (opcional)
            $mes = $_GET['mes'] ?? null;
            $anio = $_GET['anio'] ?? null;

            // Construir las cláusulas WHERE para el filtro de fecha
            $whereClause = "WHERE s.estado = 'activa'";
            $params = [];

            if ($mes !== null && $anio !== null) {
                $whereClause .= " AND MONTH(g.fecha) = ? AND YEAR(g.fecha) = ?";
                $params[] = $mes;
                $params[] = $anio;

                $whereClauseCompras = "WHERE s.estado = 'activa' AND MONTH(c.fecha) = ? AND YEAR(c.fecha) = ?";
                $paramsCompras = [$mes, $anio];
            } else {
                $whereClauseCompras = "WHERE s.estado = 'activa'";
                $paramsCompras = [];
            }


            // Consulta para obtener el total de gastos por sucursal
            $stmtGastos = $db->prepare("
                SELECT 
                    s.id AS sucursal_id,
                    s.nombre AS sucursal_nombre,
                    COALESCE(SUM(g.monto), 0) AS total_gastos
                FROM sucursales s
                LEFT JOIN gastos_varios g ON s.id = g.sucursal_id
                " . $whereClause . "
                GROUP BY s.id, s.nombre
                ORDER BY s.nombre ASC
            ");
            $stmtGastos->execute($params);
            $gastosPorSucursal = $stmtGastos->fetchAll(PDO::FETCH_ASSOC);

            // Consulta para obtener el total de compras por sucursal
            $stmtCompras = $db->prepare("
                SELECT
                    s.id AS sucursal_id,
                    s.nombre AS sucursal_nombre,
                    COALESCE(SUM(c.monto_total), 0) AS total_compras
                FROM sucursales s
                LEFT JOIN compras c ON s.id = c.sucursal_id
                " . $whereClauseCompras . "
                GROUP BY s.id, s.nombre
                ORDER BY s.nombre ASC
            ");
            $stmtCompras->execute($paramsCompras);
            $comprasPorSucursal = $stmtCompras->fetchAll(PDO::FETCH_ASSOC);

            // Combinar los resultados
            $datosGrafico = [];
            $sucursales = [];

            // Inicializar datos con todas las sucursales activas
            $stmtSucursales = $db->query("SELECT id, nombre FROM sucursales WHERE estado = 'activa' ORDER BY nombre ASC");
            $sucursalesActivas = $stmtSucursales->fetchAll(PDO::FETCH_ASSOC);
            foreach ($sucursalesActivas as $s) {
                $datosGrafico[$s['id']] = [
                    'sucursal_id' => $s['id'],
                    'sucursal_nombre' => $s['nombre'],
                    'total_gastos' => 0,
                    'total_compras' => 0
                ];
            }

            foreach ($gastosPorSucursal as $gasto) {
                $datosGrafico[$gasto['sucursal_id']]['total_gastos'] = $gasto['total_gastos'];
            }

            foreach ($comprasPorSucursal as $compra) {
                $datosGrafico[$compra['sucursal_id']]['total_compras'] = $compra['total_compras'];
            }
            
            // Convertir a array indexado para la respuesta JSON
            $response = array_values($datosGrafico);

            ob_end_clean();
            echo json_encode([
                'success' => true,
                'message' => 'Datos de gastos y compras por sucursal obtenidos exitosamente',
                'data' => $response
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            break;

        default:
            ob_end_clean();
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Acción no válida']);
            break;
    }

} catch (PDOException $e) {
    ob_end_clean();
    $errorInfo = ErrorHandler::handleDatabaseError($e, 'reportes/api_graficos.php');
    ErrorHandler::logError($e->getMessage(), ['exception' => $e->getMessage(), 'code' => $e->getCode()]);
    $debug = defined('APP_DEBUG') && APP_DEBUG;
    echo ErrorHandler::jsonResponse($errorInfo, 500, $debug);
} catch (Exception $e) {
    ob_end_clean();
    $errorInfo = ErrorHandler::handleException($e, 'reportes/api_graficos.php');
    ErrorHandler::logError($e->getMessage(), ['exception' => $e->getMessage()]);
    $debug = defined('APP_DEBUG') && APP_DEBUG;
    echo ErrorHandler::jsonResponse($errorInfo, 400, $debug);
}
?>