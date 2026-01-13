<?php
/**
 * Endpoint para obtener porcentajes de categorías por sucursal
 * Sistema de Gestión de Reciclaje - API para Flutter
 * 
 * Filtros: año, mes, sucursal_id
 * Retorna: porcentaje de cada categoría (1-100%) basado en cantidad de productos/materiales
 */

error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');

// Headers CORS para Flutter
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json; charset=utf-8');

// Manejar preflight OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

ob_start();

try {
    require_once __DIR__ . '/database.php';
    require_once __DIR__ . '/auth.php';
} catch (Exception $e) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error del servidor: ' . (defined('APP_DEBUG') && APP_DEBUG ? $e->getMessage() : 'No se pudo conectar al servidor')
    ]);
    exit;
}

// Autenticación opcional (puedes requerirla si es necesario)
$auth = new Auth();
$usuarioId = null;

// Intentar obtener usuario_id de la sesión (para web) o de POST/GET (para Flutter)
if ($auth->isAuthenticated()) {
    $usuarioId = $_SESSION['usuario_id'];
} else {
    // Para Flutter, permitir acceso sin autenticación o con token
    // Puedes ajustar esto según tu sistema de autenticación
}

// Obtener parámetros de filtro
$data = [];
$contentType = $_SERVER['CONTENT_TYPE'] ?? '';
if (strpos($contentType, 'application/json') !== false) {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true) ?: [];
} else {
    $data = array_merge($_GET, $_POST);
}

$anio = isset($data['anio']) ? intval($data['anio']) : date('Y');
$mes = isset($data['mes']) ? intval($data['mes']) : date('m');
$sucursalId = isset($data['sucursal_id']) ? intval($data['sucursal_id']) : null;

// Validar parámetros
if ($anio < 2000 || $anio > 2100) {
    ob_end_clean();
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Año inválido. Debe estar entre 2000 y 2100'
    ]);
    exit;
}

if ($mes < 1 || $mes > 12) {
    ob_end_clean();
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Mes inválido. Debe estar entre 1 y 12'
    ]);
    exit;
}

if ($sucursalId && $sucursalId <= 0) {
    ob_end_clean();
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'ID de sucursal inválido'
    ]);
    exit;
}

try {
    $db = getDB();
    
    // Construir fecha de inicio y fin del mes
    $fechaInicio = sprintf('%04d-%02d-01', $anio, $mes);
    $fechaFin = date('Y-m-t', strtotime($fechaInicio)); // Último día del mes
    
    // Query simplificado: calcular porcentajes basado en inventarios actuales
    // Filtrado por sucursal y considerando productos que fueron actualizados en el mes
    $sqlBase = "
        SELECT 
            c.id as categoria_id,
            c.nombre as categoria_nombre,
            COALESCE(SUM(inv.cantidad), 0) as cantidad_total
        FROM categorias c
        INNER JOIN materiales m ON m.categoria_id = c.id AND m.estado = 'activo'
        INNER JOIN productos p ON p.material_id = m.id AND p.estado = 'activo'
        INNER JOIN inventarios inv ON inv.producto_id = p.id 
            AND inv.estado = 'disponible'
            AND YEAR(inv.fecha_actualizacion) = ?
            AND MONTH(inv.fecha_actualizacion) = ?
    ";
    
    $params = [$anio, $mes];
    
    if ($sucursalId) {
        $sqlBase .= " AND inv.sucursal_id = ?";
        $params[] = $sucursalId;
    }
    
    $sqlBase .= "
        WHERE c.estado = 'activo'
        GROUP BY c.id, c.nombre
        HAVING cantidad_total > 0
        ORDER BY cantidad_total DESC
    ";
    
    $stmt = $db->prepare($sqlBase);
    $stmt->execute($params);
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Calcular total y porcentajes
    $totalCantidad = 0;
    foreach ($resultados as $row) {
        $totalCantidad += floatval($row['cantidad_total']);
    }
    
    $categorias = [];
    foreach ($resultados as $row) {
        $cantidad = floatval($row['cantidad_total']);
        $porcentaje = $totalCantidad > 0 ? ($cantidad / $totalCantidad) * 100 : 0;
        
        // Redondear a 2 decimales y asegurar que esté entre 1 y 100
        $porcentaje = round($porcentaje, 2);
        if ($porcentaje < 1 && $cantidad > 0) {
            $porcentaje = 1; // Mínimo 1% si hay cantidad
        }
        if ($porcentaje > 100) {
            $porcentaje = 100; // Máximo 100%
        }
        
        $categorias[] = [
            'categoria_id' => intval($row['categoria_id']),
            'categoria_nombre' => $row['categoria_nombre'],
            'cantidad' => $cantidad,
            'porcentaje' => $porcentaje
        ];
    }
    
    // Si no hay resultados, retornar todas las categorías con 0%
    if (empty($categorias)) {
        $stmt = $db->query("
            SELECT id as categoria_id, nombre as categoria_nombre
            FROM categorias 
            WHERE estado = 'activo'
            ORDER BY nombre
        ");
        $todasCategorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($todasCategorias as $cat) {
            $categorias[] = [
                'categoria_id' => intval($cat['categoria_id']),
                'categoria_nombre' => $cat['categoria_nombre'],
                'cantidad' => 0,
                'porcentaje' => 0
            ];
        }
    }
    
    ob_end_clean();
    echo json_encode([
        'success' => true,
        'message' => 'Porcentajes de categorías obtenidos exitosamente',
        'filtros' => [
            'anio' => $anio,
            'mes' => $mes,
            'sucursal_id' => $sucursalId,
            'fecha_inicio' => $fechaInicio,
            'fecha_fin' => $fechaFin
        ],
        'total_cantidad' => $totalCantidad,
        'categorias' => $categorias
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    
} catch (PDOException $e) {
    ob_end_clean();
    error_log("Error en porcentajes_categorias.php (PDO): " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error interno del servidor al calcular porcentajes.' . 
                     (defined('APP_DEBUG') && APP_DEBUG ? ' Detalle: ' . $e->getMessage() : '')
    ]);
} catch (Exception $e) {
    ob_end_clean();
    error_log("Error en porcentajes_categorias.php (General): " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error interno del servidor.' . 
                     (defined('APP_DEBUG') && APP_DEBUG ? ' Detalle: ' . $e->getMessage() : '')
    ]);
}
?>

