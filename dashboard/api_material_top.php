<?php
// API: Material más comprado / vendido por sucursal
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'error' => 'Error de conexión']);
    exit;
}

$tipo       = isset($_GET['tipo']) ? $_GET['tipo'] : 'compras';
$sucursal   = isset($_GET['sucursal_id']) && $_GET['sucursal_id'] !== '' ? intval($_GET['sucursal_id']) : null;
$mes        = isset($_GET['mes'])  && $_GET['mes']  !== '' ? intval($_GET['mes'])  : null;
$anio       = isset($_GET['anio']) && $_GET['anio'] !== '' ? intval($_GET['anio']) : null;

if ($tipo === 'ventas') {
    $where = ["v.estado != 'cancelada'"];
    if ($sucursal) $where[] = "v.sucursal_id = $sucursal";
    if ($anio)     $where[] = "YEAR(v.fecha_venta) = $anio";
    if ($mes)      $where[] = "MONTH(v.fecha_venta) = $mes";
    $whereSQL = 'WHERE ' . implode(' AND ', $where);

    $sql = "
        SELECT m.nombre as material,
               SUM(vd.subtotal) as total_monto,
               SUM(vd.cantidad) as total_cantidad
        FROM ventas v
        JOIN ventas_detalle vd ON vd.venta_id = v.id
        JOIN productos p ON p.id = vd.producto_id
        JOIN materiales m ON m.id = p.material_id
        $whereSQL
        GROUP BY m.id, m.nombre
        ORDER BY total_monto DESC
        LIMIT 10
    ";
} else {
    $where = ["c.estado != 'cancelada'"];
    if ($sucursal) $where[] = "c.sucursal_id = $sucursal";
    if ($anio)     $where[] = "YEAR(c.fecha_compra) = $anio";
    if ($mes)      $where[] = "MONTH(c.fecha_compra) = $mes";
    $whereSQL = 'WHERE ' . implode(' AND ', $where);

    $sql = "
        SELECT m.nombre as material,
               SUM(cd.subtotal) as total_monto,
               SUM(cd.cantidad) as total_cantidad
        FROM compras c
        JOIN compras_detalle cd ON cd.compra_id = c.id
        JOIN productos p ON p.id = cd.producto_id
        JOIN materiales m ON m.id = p.material_id
        $whereSQL
        GROUP BY m.id, m.nombre
        ORDER BY total_monto DESC
        LIMIT 10
    ";
}

$res = $conn->query($sql);
$data = [];
while ($row = $res->fetch_assoc()) {
    $data[] = [
        'material'        => $row['material'],
        'total_monto'     => floatval($row['total_monto']),
        'total_cantidad'  => intval($row['total_cantidad'])
    ];
}

echo json_encode(['success' => true, 'data' => $data, 'tipo' => $tipo]);
$conn->close();
