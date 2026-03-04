<?php
// API para Ingresos y Costos por Año (con filtro de sucursal)
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'error' => 'Error de conexión a la base de datos']);
    exit;
}

// Filtro de sucursal
$sucursal_id = isset($_GET['sucursal_id']) && $_GET['sucursal_id'] !== '' ? intval($_GET['sucursal_id']) : null;

// Ventas por año
$condVentas = ["estado != 'cancelada'"];
if ($sucursal_id) $condVentas[] = "sucursal_id = $sucursal_id";
$whereVentas = count($condVentas) ? 'WHERE ' . implode(' AND ', $condVentas) : '';
$sqlVentas = "SELECT YEAR(fecha_venta) as anio, SUM(total) as ventas FROM ventas $whereVentas GROUP BY anio ORDER BY anio";
$resVentas = $conn->query($sqlVentas);
$ventas = [];
while ($row = $resVentas->fetch_assoc()) {
    $ventas[$row['anio']] = floatval($row['ventas']);
}

// Compras por año
$condCompras = ["estado != 'cancelada'"];
if ($sucursal_id) $condCompras[] = "sucursal_id = $sucursal_id";
$whereCompras = count($condCompras) ? 'WHERE ' . implode(' AND ', $condCompras) : '';
$sqlCompras = "SELECT YEAR(fecha_compra) as anio, SUM(total) as compras FROM compras $whereCompras GROUP BY anio ORDER BY anio";
$resCompras = $conn->query($sqlCompras);
$compras = [];
while ($row = $resCompras->fetch_assoc()) {
    $compras[$row['anio']] = floatval($row['compras']);
}

// Unir años
$anios = array_unique(array_merge(array_keys($ventas), array_keys($compras)));
sort($anios);

$data = [];
foreach ($anios as $anio) {
    $data[] = [
        'periodo' => $anio,
        'ventas' => isset($ventas[$anio]) ? $ventas[$anio] : 0,
        'compras' => isset($compras[$anio]) ? $compras[$anio] : 0
    ];
}

echo json_encode(['success' => true, 'data' => $data]);
$conn->close();
