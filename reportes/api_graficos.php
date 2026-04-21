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
            $mes        = $_GET['mes']        ?? null;
            $anio       = $_GET['anio']       ?? null;
            $sucursalId = $_GET['sucursal_id'] ?? null;

            // Parámetros de fecha para agregación condicional
            $filtroMes  = ($mes  !== null && $anio !== null);

            // Construir filtro de sucursal
            $whereSucursal = "";
            $paramsSuc = [];
            if ($sucursalId !== null && $sucursalId !== '' && $sucursalId !== '0') {
                $whereSucursal = " AND s.id = ?";
                $paramsSuc[] = (int)$sucursalId;
            }

            // Una sola consulta con agregación condicional para evitar problemas de LEFT JOIN + WHERE
            $sql = "
                SELECT
                    s.id AS sucursal_id,
                    s.nombre AS sucursal_nombre,
                    COALESCE(SUM(CASE WHEN " . ($filtroMes ? "MONTH(c.fecha_compra) = ? AND YEAR(c.fecha_compra) = ?" : "1=1") . " THEN c.total ELSE 0 END), 0) AS total_compras,
                    COALESCE(SUM(CASE WHEN " . ($filtroMes ? "MONTH(v.fecha_venta) = ? AND YEAR(v.fecha_venta) = ?"   : "1=1") . " THEN v.total ELSE 0 END), 0) AS total_ventas,
                    COALESCE(SUM(CASE WHEN " . ($filtroMes ? "MONTH(g.fecha) = ? AND YEAR(g.fecha) = ?"              : "1=1") . " THEN g.monto ELSE 0 END), 0) AS total_gastos
                FROM sucursales s
                LEFT JOIN compras c     ON s.id = c.sucursal_id
                LEFT JOIN ventas v      ON s.id = v.sucursal_id
                LEFT JOIN gastos_varios g ON s.id = g.sucursal_id
                WHERE s.estado = 'activa'" . $whereSucursal . "
                GROUP BY s.id, s.nombre
                ORDER BY s.nombre ASC
            ";

            $params = [];
            if ($filtroMes) {
                $params[] = (int)$mes; $params[] = (int)$anio; // compras
                $params[] = (int)$mes; $params[] = (int)$anio; // ventas
                $params[] = (int)$mes; $params[] = (int)$anio; // gastos
            }
            $params = array_merge($params, $paramsSuc);

            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Calcular ganancia
            foreach ($rows as &$row) {
                $row['total_compras'] = floatval($row['total_compras']);
                $row['total_ventas']  = floatval($row['total_ventas']);
                $row['total_gastos']  = floatval($row['total_gastos']);
                $row['ganancia']      = $row['total_ventas'] - $row['total_compras'] - $row['total_gastos'];
            }
            unset($row);

            ob_end_clean();
            echo json_encode([
                'success' => true,
                'message' => 'Datos obtenidos exitosamente',
                'data'    => $rows
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