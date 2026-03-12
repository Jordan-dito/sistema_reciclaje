<?php
/**
 * API para Flujo FIFO de Inventario
 * Sistema de Gestión de Reciclaje
 */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

try {
    $auth = new Auth();
    if (!$auth->isAuthenticated()) {
        echo json_encode(['success' => false, 'message' => 'No autenticado']);
        exit;
    }
    
    $db = getDB();
    $action = $_GET['action'] ?? '';
    
    if ($action === 'promedio') {
        $fechaDesde = $_GET['fecha_desde'] ?? '';
        $fechaHasta = $_GET['fecha_hasta'] ?? '';
        $sucursalId = $_GET['sucursal_id'] ?? '';
        $material = $_GET['material'] ?? '';
        
        if (empty($fechaDesde) || empty($fechaHasta)) {
            throw new Exception('Las fechas son obligatorias');
        }
        
        $resultado = generarPromedioPonderado($db, $fechaDesde, $fechaHasta, $sucursalId, $material);
        echo json_encode($resultado);
    } else {
        throw new Exception('Acción no válida');
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}


/**
 * Genera el reporte de inventario usando el método del promedio ponderado
 */
function generarPromedioPonderado($db, $fechaDesde, $fechaHasta, $sucursalId, $material) {
    // Obtener todos los movimientos (compras y ventas) ordenados cronológicamente
    $sql = "
        SELECT 'COMPRA' as tipo_movimiento, c.fecha_compra as fecha, cd.producto_id, p.nombre as producto_nombre, m.nombre as material_nombre, cd.cantidad, cd.subtotal as monto, s.nombre as sucursal_nombre, prov.nombre as tercero, c.numero_factura
        FROM compras c
        INNER JOIN compras_detalle cd ON c.id = cd.compra_id
        INNER JOIN productos p ON cd.producto_id = p.id
        LEFT JOIN materiales m ON p.material_id = m.id
        INNER JOIN sucursales s ON c.sucursal_id = s.id
        LEFT JOIN proveedores prov ON c.proveedor_id = prov.id
        WHERE c.estado = 'completada' AND c.fecha_compra BETWEEN ? AND ?
    ";
    $params = [$fechaDesde, $fechaHasta];
    if (!empty($sucursalId)) {
        $sql .= " AND c.sucursal_id = ?";
        $params[] = $sucursalId;
    }
    if (!empty($material)) {
        $sql .= " AND m.nombre = ?";
        $params[] = $material;
    }
    $sql .= "
        UNION ALL
        SELECT 'VENTA' as tipo_movimiento, v.fecha_venta as fecha, vd.producto_id, p.nombre as producto_nombre, m.nombre as material_nombre, vd.cantidad, vd.subtotal as monto, s.nombre as sucursal_nombre, v.cliente_nombre as tercero, v.numero_factura
        FROM ventas v
        INNER JOIN ventas_detalle vd ON v.id = vd.venta_id
        INNER JOIN productos p ON vd.producto_id = p.id
        LEFT JOIN materiales m ON p.material_id = m.id
        INNER JOIN sucursales s ON v.sucursal_id = s.id
        WHERE v.estado = 'completada' AND v.fecha_venta BETWEEN ? AND ?
    ";
    $params = array_merge($params, [$fechaDesde, $fechaHasta]);
    if (!empty($sucursalId)) {
        $sql .= " AND v.sucursal_id = ?";
        $params[] = $sucursalId;
    }
    if (!empty($material)) {
        $sql .= " AND m.nombre = ?";
        $params[] = $material;
    }
    $sql .= " ORDER BY producto_nombre, fecha ASC, tipo_movimiento DESC";
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $movimientos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (empty($movimientos)) {
        return [
            'success' => true,
            'html' => '<div class="alert alert-warning"><i class="fa fa-exclamation-triangle"></i> No se encontraron movimientos en el período seleccionado.</div>',
            'datos' => []
        ];
    }
    // Agrupar por producto
    $porProducto = [];
    foreach ($movimientos as $mov) {
        $prodId = $mov['producto_id'];
        if (!isset($porProducto[$prodId])) {
            $porProducto[$prodId] = [
                'nombre' => $mov['producto_nombre'],
                'material' => $mov['material_nombre'],
                'movimientos' => []
            ];
        }
        $porProducto[$prodId]['movimientos'][] = $mov;
    }
    $html = generarHTMLPromedioPonderado($porProducto, $fechaDesde, $fechaHasta, $sucursalId, $material, $db);
    return [
        'success' => true,
        'html' => $html,
        'datos' => $movimientos
    ];
}

function generarHTMLPromedioPonderado($porProducto, $fechaDesde, $fechaHasta, $sucursalId, $material, $db) {
    $html = '<div class="mb-3">';
    $html .= '<p><strong>Período:</strong> ' . date('d/m/Y', strtotime($fechaDesde)) . ' al ' . date('d/m/Y', strtotime($fechaHasta)) . '</p>';
    if (!empty($sucursalId)) {
        $stmt = $db->prepare("SELECT nombre FROM sucursales WHERE id = ?");
        $stmt->execute([$sucursalId]);
        $suc = $stmt->fetch();
        if ($suc) {
            $html .= '<p><strong>Sucursal:</strong> ' . htmlspecialchars($suc['nombre']) . '</p>';
        }
    }
    if (!empty($material)) {
        $html .= '<p><strong>Material:</strong> ' . htmlspecialchars($material) . '</p>';
    }
    $html .= '</div>';
    foreach ($porProducto as $prodId => $producto) {
        $html .= '<div class="mb-4">';
        $html .= '<h5 style="background-color: #f8f9fa; padding: 10px; border-left: 4px solid #1572e8;">';
        $html .= '<i class="fa fa-box"></i> ' . htmlspecialchars($producto['nombre']);
        if ($producto['material']) {
            $html .= ' <small class="text-muted">(' . htmlspecialchars($producto['material']) . ')</small>';
        }
        $html .= '</h5>';
        $html .= '<div class="table-responsive">';
        $html .= '<table class="table table-bordered table-hover table-sm">';
        $html .= '<thead class="thead-light"><tr>';
        $html .= '<th style="width: 80px;">Tipo</th>';
        $html .= '<th style="width: 100px;">Fecha</th>';
        $html .= '<th>Sucursal</th>';
        $html .= '<th>Tercero</th>';
        $html .= '<th>Factura</th>';
        $html .= '<th style="width: 100px;" class="text-right">Cantidad</th>';
        $html .= '<th style="width: 100px;" class="text-right">Monto</th>';
        $html .= '<th style="width: 120px;" class="text-right">Costo Promedio</th>';
        $html .= '<th style="width: 110px;" class="text-right">Saldo (Stock)</th>';
        $html .= '</tr></thead><tbody>';
        $stock = 0;
        $costoTotal = 0;
        $costoPromedio = 0;
        foreach ($producto['movimientos'] as $mov) {
            $esCompra = $mov['tipo_movimiento'] === 'COMPRA';
            $colorFila = $esCompra ? 'table-success' : 'table-danger';
            $iconoTipo = $esCompra ? '<i class="fa fa-arrow-down text-success"></i>' : '<i class="fa fa-arrow-up text-danger"></i>';
            if ($esCompra) {
                $stock += $mov['cantidad'];
                $costoTotal += $mov['monto'];
                $costoPromedio = $stock > 0 ? $costoTotal / $stock : 0;
            } else {
                $stock -= $mov['cantidad'];
                // El costo promedio se mantiene igual para la salida
            }
            $html .= '<tr class="' . $colorFila . '">';
            $html .= '<td><strong>' . $iconoTipo . ' ' . $mov['tipo_movimiento'] . '</strong></td>';
            $html .= '<td>' . date('d/m/Y', strtotime($mov['fecha'])) . '</td>';
            $html .= '<td>' . htmlspecialchars($mov['sucursal_nombre']) . '</td>';
            $html .= '<td>' . htmlspecialchars($mov['tercero'] ?? '-') . '</td>';
            $html .= '<td>' . htmlspecialchars($mov['numero_factura'] ?? '-') . '</td>';
            $html .= '<td class="text-right"><strong>' . number_format($mov['cantidad'], 2) . '</strong></td>';
            $html .= '<td class="text-right">$' . number_format($mov['monto'], 2) . '</td>';
            $html .= '<td class="text-right">$' . number_format($costoPromedio, 2) . '</td>';
            $html .= '<td class="text-right"><strong>' . number_format($stock, 2) . '</strong></td>';
            $html .= '</tr>';
        }
        $html .= '</tbody></table>';
        $html .= '</div>';
        // Apartado: Stock disponible cuando quedó material en inventario
        if ($stock > 0) {
            $valorStock = $stock * $costoPromedio;
            $html .= '<div class="alert alert-info mt-2 mb-0" style="border-left: 4px solid #17a2b8;">';
            $html .= '<h6 class="alert-heading mb-2"><i class="fa fa-box-open"></i> Material en stock (no se vendió todo)</h6>';
            $html .= '<p class="mb-1"><strong>Stock disponible al cierre del período:</strong> ' . number_format($stock, 2) . ' unidades</p>';
            $html .= '<p class="mb-1"><strong>Costo promedio ponderado:</strong> $' . number_format($costoPromedio, 2) . '</p>';
            $html .= '<p class="mb-0"><strong>Valor en inventario:</strong> $' . number_format($valorStock, 2) . '</p>';
            $html .= '</div>';
        } else {
            $html .= '<div class="alert alert-secondary mt-2 mb-0"><i class="fa fa-check"></i> Todo el material del período fue vendido. No hay stock disponible.</div>';
        }
        $html .= '</div>';
    }
    $html .= '</div>';
    return $html;
}

/**
 * Genera el HTML para mostrar el flujo FIFO
 */
function generarHTMLFlujoFIFO($porProducto, $fechaDesde, $fechaHasta, $sucursalId, $material, $db) {
    $html = '<div class="mb-3">';
    $html .= '<p><strong>Período:</strong> ' . date('d/m/Y', strtotime($fechaDesde)) . ' al ' . date('d/m/Y', strtotime($fechaHasta)) . '</p>';
    
    if (!empty($sucursalId)) {
        $stmt = $db->prepare("SELECT nombre FROM sucursales WHERE id = ?");
        $stmt->execute([$sucursalId]);
        $suc = $stmt->fetch();
        if ($suc) {
            $html .= '<p><strong>Sucursal:</strong> ' . htmlspecialchars($suc['nombre']) . '</p>';
        }
    }
    
    if (!empty($material)) {
        $html .= '<p><strong>Material:</strong> ' . htmlspecialchars($material) . '</p>';
    }
    $html .= '</div>';
    
    $totalCompras = 0;
    $totalVentas = 0;
    $cantidadComprada = 0;
    $cantidadVendida = 0;
    
    foreach ($porProducto as $prodId => $producto) {
        $html .= '<div class="mb-4">';
        $html .= '<h5 style="background-color: #f8f9fa; padding: 10px; border-left: 4px solid #1572e8;">';
        $html .= '<i class="fa fa-box"></i> ' . htmlspecialchars($producto['nombre']);
        if ($producto['material']) {
            $html .= ' <small class="text-muted">(' . htmlspecialchars($producto['material']) . ')</small>';
        }
        $html .= '</h5>';
        
        $html .= '<div class="table-responsive">';
        $html .= '<table class="table table-bordered table-hover table-sm">';
        $html .= '<thead class="thead-light"><tr>';
        $html .= '<th style="width: 80px;">Tipo</th>';
        $html .= '<th style="width: 100px;">Fecha</th>';
        $html .= '<th>Sucursal</th>';
        $html .= '<th>Tercero</th>';
        $html .= '<th>Factura</th>';
        $html .= '<th style="width: 100px;" class="text-right">Cantidad</th>';
        $html .= '<th style="width: 100px;" class="text-right">Monto</th>';
        $html .= '</tr></thead><tbody>';
        
        foreach ($producto['movimientos'] as $mov) {
            $esCompra = $mov['tipo_movimiento'] === 'COMPRA';
            $colorFila = $esCompra ? 'table-success' : 'table-danger';
            $iconoTipo = $esCompra ? '<i class="fa fa-arrow-down text-success"></i>' : '<i class="fa fa-arrow-up text-danger"></i>';
            
            $html .= '<tr class="' . $colorFila . '">';
            $html .= '<td><strong>' . $iconoTipo . ' ' . $mov['tipo_movimiento'] . '</strong></td>';
            $html .= '<td>' . date('d/m/Y', strtotime($mov['fecha'])) . '</td>';
            $html .= '<td>' . htmlspecialchars($mov['sucursal_nombre']) . '</td>';
            $html .= '<td>' . htmlspecialchars($mov['tercero'] ?? '-') . '</td>';
            $html .= '<td>' . htmlspecialchars($mov['numero_factura'] ?? '-') . '</td>';
            $html .= '<td class="text-right"><strong>' . number_format($mov['cantidad'], 2) . '</strong></td>';
            $html .= '<td class="text-right">$' . number_format($mov['monto'], 2) . '</td>';
            $html .= '</tr>';
            
            if ($esCompra) {
                $totalCompras += $mov['monto'];
                $cantidadComprada += $mov['cantidad'];
            } else {
                $totalVentas += $mov['monto'];
                $cantidadVendida += $mov['cantidad'];
            }
        }
        
        $html .= '</tbody></table>';
        $html .= '</div>';
        $html .= '</div>';
    }
    
    // Resumen
    $html .= '<div class="row mt-4">';
    $html .= '<div class="col-md-3">';
    $html .= '<div class="card card-stats card-round" style="border-left: 4px solid #28a745;">';
    $html .= '<div class="card-body">';
    $html .= '<div class="row">';
    $html .= '<div class="col-3"><div class="icon-big text-center"><i class="fa fa-shopping-cart text-success"></i></div></div>';
    $html .= '<div class="col-9 col-stats">';
    $html .= '<div class="numbers">';
    $html .= '<p class="card-category">Total Compras</p>';
    $html .= '<h4 class="card-title">$' . number_format($totalCompras, 2) . '</h4>';
    $html .= '<small>' . number_format($cantidadComprada, 2) . ' unidades</small>';
    $html .= '</div></div></div></div></div></div>';
    
    $html .= '<div class="col-md-3">';
    $html .= '<div class="card card-stats card-round" style="border-left: 4px solid #dc3545;">';
    $html .= '<div class="card-body">';
    $html .= '<div class="row">';
    $html .= '<div class="col-3"><div class="icon-big text-center"><i class="fa fa-dollar-sign text-danger"></i></div></div>';
    $html .= '<div class="col-9 col-stats">';
    $html .= '<div class="numbers">';
    $html .= '<p class="card-category">Total Ventas</p>';
    $html .= '<h4 class="card-title">$' . number_format($totalVentas, 2) . '</h4>';
    $html .= '<small>' . number_format($cantidadVendida, 2) . ' unidades</small>';
    $html .= '</div></div></div></div></div></div>';
    
    $balance = $totalVentas - $totalCompras;
    $colorBalance = $balance >= 0 ? '#17a2b8' : '#ffc107';
    
    $html .= '<div class="col-md-3">';
    $html .= '<div class="card card-stats card-round" style="border-left: 4px solid ' . $colorBalance . ';">';
    $html .= '<div class="card-body">';
    $html .= '<div class="row">';
    $html .= '<div class="col-3"><div class="icon-big text-center"><i class="fa fa-balance-scale text-info"></i></div></div>';
    $html .= '<div class="col-9 col-stats">';
    $html .= '<div class="numbers">';
    $html .= '<p class="card-category">Balance</p>';
    $html .= '<h4 class="card-title">$' . number_format($balance, 2) . '</h4>';
    $html .= '<small>' . number_format($cantidadVendida - $cantidadComprada, 2) . ' unidades</small>';
    $html .= '</div></div></div></div></div></div>';
    
    $html .= '<div class="col-md-3">';
    $html .= '<div class="card card-stats card-round" style="border-left: 4px solid #6c757d;">';
    $html .= '<div class="card-body">';
    $html .= '<div class="row">';
    $html .= '<div class="col-3"><div class="icon-big text-center"><i class="fa fa-box text-secondary"></i></div></div>';
    $html .= '<div class="col-9 col-stats">';
    $html .= '<div class="numbers">';
    $html .= '<p class="card-category">Productos</p>';
    $html .= '<h4 class="card-title">' . count($porProducto) . '</h4>';
    $html .= '<small>productos diferentes</small>';
    $html .= '</div></div></div></div></div></div>';
    
    $html .= '</div>';
    
    return $html;
}
