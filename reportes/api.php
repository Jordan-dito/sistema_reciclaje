<?php
/**
 * API de Reportes
 * Sistema de Gestión de Reciclaje
 */

header('Content-Type: application/json; charset=utf-8');
ob_start();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

use ErrorHandler;

try {
    $auth = new Auth();
    if (!$auth->isAuthenticated()) {
        ob_end_clean();
        ErrorHandler::handleAuthError('No autenticado');
        exit;
    }
    
    $db = getDB();
    $action = $_GET['action'] ?? '';
    
    if ($action === 'vista_previa') {
        $tipo = $_GET['tipo'] ?? '';
        $fechaDesde = $_GET['fecha_desde'] ?? '';
        $fechaHasta = $_GET['fecha_hasta'] ?? '';
        $rolId = $_GET['rol_id'] ?? '';
        
        if (empty($tipo)) {
            throw new Exception('Tipo de reporte no especificado');
        }
        
        // Reportes que no requieren fechas
        $reportesSinFechas = ['productos', 'materiales'];
        
        // Validar fechas solo si son requeridas
        if (!in_array($tipo, $reportesSinFechas)) {
            if (empty($fechaDesde) || empty($fechaHasta)) {
                throw new Exception('Las fechas son obligatorias para este tipo de reporte');
            }
            
            $fechaDesdeObj = new DateTime($fechaDesde);
            $fechaHastaObj = new DateTime($fechaHasta);
            
            if ($fechaDesdeObj > $fechaHastaObj) {
                throw new Exception('La fecha desde debe ser menor o igual a la fecha hasta');
            }
        }
        
        $html = '';
        
        $resultado = null;
        $tieneDatos = false;
        
        switch ($tipo) {
            case 'inventarios':
                $resultado = generarVistaPreviaInventarios($db, $fechaDesde, $fechaHasta);
                break;
            case 'compras':
                $resultado = generarVistaPreviaCompras($db, $fechaDesde, $fechaHasta);
                break;
            case 'ventas':
                $resultado = generarVistaPreviaVentas($db, $fechaDesde, $fechaHasta);
                break;
            case 'productos':
                $resultado = generarVistaPreviaProductos($db);
                break;
            case 'materiales':
                $resultado = generarVistaPreviaMateriales($db);
                break;
            case 'sucursales':
                $resultado = generarVistaPreviaSucursales($db, $fechaDesde, $fechaHasta);
                break;
            case 'usuarios':
                $resultado = generarVistaPreviaUsuarios($db, $fechaDesde, $fechaHasta, $rolId);
                break;
            default:
                throw new Exception('Tipo de reporte no válido');
        }
        
        $tieneDatos = $resultado['tieneDatos'];
        $html = $resultado['html'];
        
        ob_end_clean();
        echo json_encode([
            'success' => true,
            'html' => $html,
            'tieneDatos' => $tieneDatos,
            'datos' => $resultado['datos'] ?? []
        ], JSON_UNESCAPED_UNICODE);
    } else {
        throw new Exception('Acción no válida');
    }
    
} catch (Exception $e) {
    ob_end_clean();
    ErrorHandler::handleException($e);
}

/**
 * Genera vista previa HTML para reporte de sucursales
 */
function generarVistaPreviaSucursales($db, $fechaDesde, $fechaHasta) {
    $stmt = $db->prepare("
        SELECT 
            s.*,
            COUNT(DISTINCT i.id) as total_productos,
            COUNT(DISTINCT v.id) as total_ventas,
            COUNT(DISTINCT c.id) as total_compras
        FROM sucursales s
        LEFT JOIN inventarios i ON s.id = i.sucursal_id
        LEFT JOIN ventas v ON s.id = v.sucursal_id AND DATE(v.fecha_venta) BETWEEN ? AND ?
        LEFT JOIN compras c ON s.id = c.sucursal_id AND DATE(c.fecha_compra) BETWEEN ? AND ?
        WHERE DATE(s.fecha_creacion) BETWEEN ? AND ?
        GROUP BY s.id
        ORDER BY s.nombre
    ");
    
    $stmt->execute([$fechaDesde, $fechaHasta, $fechaDesde, $fechaHasta, $fechaDesde, $fechaHasta]);
    $sucursales = $stmt->fetchAll();
    
    $tieneDatos = count($sucursales) > 0;
    
    $html = '<div class="table-responsive">';
    $html .= '<h4>Reporte de Sucursales</h4>';
    $html .= '<p><strong>Período:</strong> ' . date('d/m/Y', strtotime($fechaDesde)) . ' - ' . date('d/m/Y', strtotime($fechaHasta)) . '</p>';
    
    if (!$tieneDatos) {
        $html .= '<div class="alert alert-warning">No se encontraron sucursales en el período seleccionado.</div>';
        return ['html' => $html, 'tieneDatos' => false, 'datos' => []];
    }
    
    $html .= '<table class="table table-bordered table-striped">';
    $html .= '<thead><tr>';
    $html .= '<th>ID</th>';
    $html .= '<th>Nombre</th>';
    $html .= '<th>Dirección</th>';
    $html .= '<th>Teléfono</th>';
    $html .= '<th>Email</th>';
    $html .= '<th>Estado</th>';
    $html .= '<th>Total Productos</th>';
    $html .= '<th>Ventas (período)</th>';
    $html .= '<th>Compras (período)</th>';
    $html .= '</tr></thead><tbody>';
    
    foreach ($sucursales as $sucursal) {
        $html .= '<tr>';
        $html .= '<td>' . htmlspecialchars($sucursal['id']) . '</td>';
        $html .= '<td>' . htmlspecialchars($sucursal['nombre']) . '</td>';
        $html .= '<td>' . htmlspecialchars($sucursal['direccion'] ?? '-') . '</td>';
        $html .= '<td>' . htmlspecialchars($sucursal['telefono'] ?? '-') . '</td>';
        $html .= '<td>' . htmlspecialchars($sucursal['email'] ?? '-') . '</td>';
        $html .= '<td><span class="badge badge-' . ($sucursal['estado'] === 'activa' ? 'success' : 'danger') . '">' . ucfirst($sucursal['estado']) . '</span></td>';
        $html .= '<td>' . $sucursal['total_productos'] . '</td>';
        $html .= '<td>' . $sucursal['total_ventas'] . '</td>';
        $html .= '<td>' . $sucursal['total_compras'] . '</td>';
        $html .= '</tr>';
    }
    
    $html .= '</tbody></table>';
    $html .= '<p><strong>Total de sucursales:</strong> ' . count($sucursales) . '</p>';
    $html .= '</div>';
    
    return ['html' => $html, 'tieneDatos' => true, 'datos' => $sucursales];
}

/**
 * Genera vista previa HTML para reporte de usuarios por rol
 */
function generarVistaPreviaUsuarios($db, $fechaDesde, $fechaHasta, $rolId = '') {
    $sql = "
        SELECT 
            u.*,
            r.nombre as rol_nombre,
            r.descripcion as rol_descripcion,
            COUNT(DISTINCT v.id) as total_ventas,
            COUNT(DISTINCT c.id) as total_compras
        FROM usuarios u
        INNER JOIN roles r ON u.rol_id = r.id
        LEFT JOIN ventas v ON u.id = v.creado_por AND DATE(v.fecha_venta) BETWEEN ? AND ?
        LEFT JOIN compras c ON u.id = c.creado_por AND DATE(c.fecha_compra) BETWEEN ? AND ?
        WHERE DATE(u.fecha_creacion) BETWEEN ? AND ?
    ";
    
    $params = [$fechaDesde, $fechaHasta, $fechaDesde, $fechaHasta, $fechaDesde, $fechaHasta];
    
    if (!empty($rolId)) {
        $sql .= " AND u.rol_id = ?";
        $params[] = $rolId;
    }
    
    $sql .= " GROUP BY u.id ORDER BY r.nombre, u.nombre";
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $usuarios = $stmt->fetchAll();
    
    $tieneDatos = count($usuarios) > 0;
    
    $html = '<div class="table-responsive">';
    $html .= '<h4>Reporte de Usuarios por Rol</h4>';
    $html .= '<p><strong>Período:</strong> ' . date('d/m/Y', strtotime($fechaDesde)) . ' - ' . date('d/m/Y', strtotime($fechaHasta)) . '</p>';
    
    if (!empty($rolId)) {
        $stmt = $db->prepare("SELECT nombre FROM roles WHERE id = ?");
        $stmt->execute([$rolId]);
        $rol = $stmt->fetch();
        if ($rol) {
            $html .= '<p><strong>Rol filtrado:</strong> ' . htmlspecialchars($rol['nombre']) . '</p>';
        }
    }
    
    if (!$tieneDatos) {
        $html .= '<div class="alert alert-warning">No se encontraron usuarios en el período seleccionado.</div>';
        return ['html' => $html, 'tieneDatos' => false, 'datos' => []];
    }
    
    $html .= '<table class="table table-bordered table-striped">';
    $html .= '<thead><tr>';
    $html .= '<th>ID</th>';
    $html .= '<th>Nombre</th>';
    $html .= '<th>Cédula</th>';
    $html .= '<th>Email</th>';
    $html .= '<th>Teléfono</th>';
    $html .= '<th>Rol</th>';
    $html .= '<th>Estado</th>';
    $html .= '<th>Ventas (período)</th>';
    $html .= '<th>Compras (período)</th>';
    $html .= '<th>Fecha Creación</th>';
    $html .= '</tr></thead><tbody>';
    
    foreach ($usuarios as $usuario) {
        $html .= '<tr>';
        $html .= '<td>' . htmlspecialchars($usuario['id']) . '</td>';
        $html .= '<td>' . htmlspecialchars($usuario['nombre']) . '</td>';
        $html .= '<td>' . htmlspecialchars($usuario['cedula']) . '</td>';
        $html .= '<td>' . htmlspecialchars($usuario['email']) . '</td>';
        $html .= '<td>' . htmlspecialchars($usuario['telefono'] ?? '-') . '</td>';
        $html .= '<td>' . htmlspecialchars($usuario['rol_nombre']) . '</td>';
        $html .= '<td><span class="badge badge-' . ($usuario['estado'] === 'activo' ? 'success' : 'danger') . '">' . ucfirst($usuario['estado']) . '</span></td>';
        $html .= '<td>' . $usuario['total_ventas'] . '</td>';
        $html .= '<td>' . $usuario['total_compras'] . '</td>';
        $html .= '<td>' . date('d/m/Y', strtotime($usuario['fecha_creacion'])) . '</td>';
        $html .= '</tr>';
    }
    
    $html .= '</tbody></table>';
    $html .= '<p><strong>Total de usuarios:</strong> ' . count($usuarios) . '</p>';
    $html .= '</div>';
    
    return ['html' => $html, 'tieneDatos' => true, 'datos' => $usuarios];
}

/**
 * Genera vista previa HTML para reporte de inventarios
 */
function generarVistaPreviaInventarios($db, $fechaDesde, $fechaHasta) {
    $stmt = $db->prepare("
        SELECT 
            i.*,
            p.nombre as producto_nombre,
            m.nombre as material_nombre,
            c.nombre as categoria_nombre,
            u.nombre as unidad_nombre,
            u.simbolo as unidad_simbolo,
            s.nombre as sucursal_nombre,
            pr.precio_unitario as precio_venta
        FROM inventarios i
        INNER JOIN productos p ON i.producto_id = p.id
        INNER JOIN materiales m ON p.material_id = m.id
        LEFT JOIN categorias c ON m.categoria_id = c.id
        INNER JOIN unidades u ON p.unidad_id = u.id
        INNER JOIN sucursales s ON i.sucursal_id = s.id
        LEFT JOIN precios pr ON p.id = pr.producto_id AND pr.tipo_precio = 'venta' AND pr.estado = 'activo'
        WHERE DATE(i.fecha_creacion) BETWEEN ? AND ?
        ORDER BY s.nombre, p.nombre
    ");
    
    $stmt->execute([$fechaDesde, $fechaHasta]);
    $inventarios = $stmt->fetchAll();
    
    $tieneDatos = count($inventarios) > 0;
    
    $html = '<div class="table-responsive">';
    $html .= '<h4>Reporte de Inventarios</h4>';
    $html .= '<p><strong>Período:</strong> ' . date('d/m/Y', strtotime($fechaDesde)) . ' - ' . date('d/m/Y', strtotime($fechaHasta)) . '</p>';
    
    if (!$tieneDatos) {
        $html .= '<div class="alert alert-warning">No se encontraron inventarios en el período seleccionado.</div>';
        return ['html' => $html, 'tieneDatos' => false, 'datos' => []];
    }
    
    $html .= '<table class="table table-bordered table-striped">';
    $html .= '<thead><tr>';
    $html .= '<th>Sucursal</th>';
    $html .= '<th>Producto</th>';
    $html .= '<th>Material</th>';
    $html .= '<th>Categoría</th>';
    $html .= '<th>Cantidad</th>';
    $html .= '<th>Unidad</th>';
    $html .= '<th>Stock Mínimo</th>';
    $html .= '<th>Stock Máximo</th>';
    $html .= '<th>Precio Venta</th>';
    $html .= '<th>Estado</th>';
    $html .= '</tr></thead><tbody>';
    
    $totalValor = 0;
    foreach ($inventarios as $inv) {
        $valor = floatval($inv['cantidad']) * floatval($inv['precio_venta'] ?? 0);
        $totalValor += $valor;
        
        $html .= '<tr>';
        $html .= '<td>' . htmlspecialchars($inv['sucursal_nombre']) . '</td>';
        $html .= '<td>' . htmlspecialchars($inv['producto_nombre']) . '</td>';
        $html .= '<td>' . htmlspecialchars($inv['material_nombre']) . '</td>';
        $html .= '<td>' . htmlspecialchars($inv['categoria_nombre'] ?? '-') . '</td>';
        $html .= '<td>' . number_format($inv['cantidad'], 2) . '</td>';
        $html .= '<td>' . htmlspecialchars($inv['unidad_simbolo'] ?? $inv['unidad_nombre']) . '</td>';
        $html .= '<td>' . number_format($inv['stock_minimo'] ?? 0, 2) . '</td>';
        $html .= '<td>' . number_format($inv['stock_maximo'] ?? 0, 2) . '</td>';
        $html .= '<td>$' . number_format($inv['precio_venta'] ?? 0, 2) . '</td>';
        $html .= '<td><span class="badge badge-' . ($inv['estado'] === 'disponible' ? 'success' : 'warning') . '">' . ucfirst($inv['estado']) . '</span></td>';
        $html .= '</tr>';
    }
    
    $html .= '</tbody></table>';
    $html .= '<p><strong>Total de registros:</strong> ' . count($inventarios) . '</p>';
    $html .= '<p><strong>Valor total del inventario:</strong> $' . number_format($totalValor, 2) . '</p>';
    $html .= '</div>';
    
    return ['html' => $html, 'tieneDatos' => true, 'datos' => $inventarios];
}

/**
 * Genera vista previa HTML para reporte de compras
 */
function generarVistaPreviaCompras($db, $fechaDesde, $fechaHasta) {
    $stmt = $db->prepare("
        SELECT 
            c.*,
            s.nombre as sucursal_nombre,
            pr.nombre as proveedor_nombre,
            u.nombre as creado_por_nombre,
            COUNT(cd.id) as total_items,
            COALESCE(SUM(cd.subtotal), 0) as total_compra
        FROM compras c
        INNER JOIN sucursales s ON c.sucursal_id = s.id
        INNER JOIN proveedores pr ON c.proveedor_id = pr.id
        LEFT JOIN usuarios u ON c.creado_por = u.id
        LEFT JOIN compras_detalle cd ON c.id = cd.compra_id
        WHERE DATE(c.fecha_compra) BETWEEN ? AND ?
        GROUP BY c.id
        ORDER BY c.fecha_compra DESC
    ");
    
    $stmt->execute([$fechaDesde, $fechaHasta]);
    $compras = $stmt->fetchAll();
    
    $tieneDatos = count($compras) > 0;
    
    $html = '<div class="table-responsive">';
    $html .= '<h4>Reporte de Compras</h4>';
    $html .= '<p><strong>Período:</strong> ' . date('d/m/Y', strtotime($fechaDesde)) . ' - ' . date('d/m/Y', strtotime($fechaHasta)) . '</p>';
    
    if (!$tieneDatos) {
        $html .= '<div class="alert alert-warning">No se encontraron compras en el período seleccionado.</div>';
        return ['html' => $html, 'tieneDatos' => false, 'datos' => []];
    }
    
    $html .= '<table class="table table-bordered table-striped">';
    $html .= '<thead><tr>';
    $html .= '<th>ID</th>';
    $html .= '<th>Fecha</th>';
    $html .= '<th>Sucursal</th>';
    $html .= '<th>Proveedor</th>';
    $html .= '<th>Total Items</th>';
    $html .= '<th>Total Compra</th>';
    $html .= '<th>Estado</th>';
    $html .= '<th>Creado por</th>';
    $html .= '</tr></thead><tbody>';
    
    $totalGeneral = 0;
    foreach ($compras as $compra) {
        $total = floatval($compra['total_compra'] ?? 0);
        $totalGeneral += $total;
        
        $html .= '<tr>';
        $html .= '<td>' . htmlspecialchars($compra['id']) . '</td>';
        $html .= '<td>' . date('d/m/Y', strtotime($compra['fecha_compra'])) . '</td>';
        $html .= '<td>' . htmlspecialchars($compra['sucursal_nombre']) . '</td>';
        $html .= '<td>' . htmlspecialchars($compra['proveedor_nombre']) . '</td>';
        $html .= '<td>' . $compra['total_items'] . '</td>';
        $html .= '<td>$' . number_format($total, 2) . '</td>';
        $html .= '<td><span class="badge badge-' . ($compra['estado'] === 'completada' ? 'success' : 'warning') . '">' . ucfirst($compra['estado']) . '</span></td>';
        $html .= '<td>' . htmlspecialchars($compra['creado_por_nombre'] ?? '-') . '</td>';
        $html .= '</tr>';
    }
    
    $html .= '</tbody></table>';
    $html .= '<p><strong>Total de compras:</strong> ' . count($compras) . '</p>';
    $html .= '<p><strong>Total general:</strong> $' . number_format($totalGeneral, 2) . '</p>';
    $html .= '</div>';
    
    return ['html' => $html, 'tieneDatos' => true, 'datos' => $compras];
}

/**
 * Genera vista previa HTML para reporte de ventas
 */
function generarVistaPreviaVentas($db, $fechaDesde, $fechaHasta) {
    $stmt = $db->prepare("
        SELECT 
            v.*,
            s.nombre as sucursal_nombre,
            u.nombre as creado_por_nombre,
            COUNT(vd.id) as total_items,
            COALESCE(SUM(vd.subtotal), 0) as total_venta
        FROM ventas v
        INNER JOIN sucursales s ON v.sucursal_id = s.id
        LEFT JOIN usuarios u ON v.creado_por = u.id
        LEFT JOIN ventas_detalle vd ON v.id = vd.venta_id
        WHERE DATE(v.fecha_venta) BETWEEN ? AND ?
        GROUP BY v.id
        ORDER BY v.fecha_venta DESC
    ");
    
    $stmt->execute([$fechaDesde, $fechaHasta]);
    $ventas = $stmt->fetchAll();
    
    $tieneDatos = count($ventas) > 0;
    
    $html = '<div class="table-responsive">';
    $html .= '<h4>Reporte de Ventas</h4>';
    $html .= '<p><strong>Período:</strong> ' . date('d/m/Y', strtotime($fechaDesde)) . ' - ' . date('d/m/Y', strtotime($fechaHasta)) . '</p>';
    
    if (!$tieneDatos) {
        $html .= '<div class="alert alert-warning">No se encontraron ventas en el período seleccionado.</div>';
        return ['html' => $html, 'tieneDatos' => false, 'datos' => []];
    }
    
    $html .= '<table class="table table-bordered table-striped">';
    $html .= '<thead><tr>';
    $html .= '<th>ID</th>';
    $html .= '<th>Fecha</th>';
    $html .= '<th>Sucursal</th>';
    $html .= '<th>Cliente</th>';
    $html .= '<th>Total Items</th>';
    $html .= '<th>Total Venta</th>';
    $html .= '<th>Estado</th>';
    $html .= '<th>Creado por</th>';
    $html .= '</tr></thead><tbody>';
    
    $totalGeneral = 0;
    foreach ($ventas as $venta) {
        $total = floatval($venta['total_venta'] ?? 0);
        $totalGeneral += $total;
        
        $html .= '<tr>';
        $html .= '<td>' . htmlspecialchars($venta['id']) . '</td>';
        $html .= '<td>' . date('d/m/Y', strtotime($venta['fecha_venta'])) . '</td>';
        $html .= '<td>' . htmlspecialchars($venta['sucursal_nombre']) . '</td>';
        $html .= '<td>' . htmlspecialchars($venta['cliente_nombre'] ?? 'Cliente General') . '</td>';
        $html .= '<td>' . $venta['total_items'] . '</td>';
        $html .= '<td>$' . number_format($total, 2) . '</td>';
        $html .= '<td><span class="badge badge-' . ($venta['estado'] === 'completada' ? 'success' : 'warning') . '">' . ucfirst($venta['estado']) . '</span></td>';
        $html .= '<td>' . htmlspecialchars($venta['creado_por_nombre'] ?? '-') . '</td>';
        $html .= '</tr>';
    }
    
    $html .= '</tbody></table>';
    $html .= '<p><strong>Total de ventas:</strong> ' . count($ventas) . '</p>';
    $html .= '<p><strong>Total general:</strong> $' . number_format($totalGeneral, 2) . '</p>';
    $html .= '</div>';
    
    return ['html' => $html, 'tieneDatos' => true, 'datos' => $ventas];
}

/**
 * Genera vista previa HTML para reporte de productos
 */
function generarVistaPreviaProductos($db) {
    $stmt = $db->query("
        SELECT 
            p.*,
            m.nombre as material_nombre,
            c.nombre as categoria_nombre,
            u.nombre as unidad_nombre,
            u.simbolo as unidad_simbolo,
            (SELECT precio_unitario FROM precios WHERE producto_id = p.id AND tipo_precio = 'venta' AND estado = 'activo' LIMIT 1) as precio_venta,
            (SELECT precio_unitario FROM precios WHERE producto_id = p.id AND tipo_precio = 'compra' AND estado = 'activo' LIMIT 1) as precio_compra
        FROM productos p
        INNER JOIN materiales m ON p.material_id = m.id
        LEFT JOIN categorias c ON m.categoria_id = c.id
        INNER JOIN unidades u ON p.unidad_id = u.id
        WHERE p.estado = 'activo'
        ORDER BY c.nombre, m.nombre, p.nombre
    ");
    
    $productos = $stmt->fetchAll();
    
    $tieneDatos = count($productos) > 0;
    
    $html = '<div class="table-responsive">';
    $html .= '<h4>Reporte de Productos</h4>';
    $html .= '<p><strong>Fecha de generación:</strong> ' . date('d/m/Y H:i:s') . '</p>';
    
    if (!$tieneDatos) {
        $html .= '<div class="alert alert-warning">No se encontraron productos activos.</div>';
        return ['html' => $html, 'tieneDatos' => false, 'datos' => []];
    }
    
    $html .= '<table class="table table-bordered table-striped">';
    $html .= '<thead><tr>';
    $html .= '<th>ID</th>';
    $html .= '<th>Producto</th>';
    $html .= '<th>Material</th>';
    $html .= '<th>Categoría</th>';
    $html .= '<th>Unidad</th>';
    $html .= '<th>Precio Compra</th>';
    $html .= '<th>Precio Venta</th>';
    $html .= '<th>Descripción</th>';
    $html .= '</tr></thead><tbody>';
    
    foreach ($productos as $producto) {
        $html .= '<tr>';
        $html .= '<td>' . htmlspecialchars($producto['id']) . '</td>';
        $html .= '<td><strong>' . htmlspecialchars($producto['nombre']) . '</strong></td>';
        $html .= '<td>' . htmlspecialchars($producto['material_nombre']) . '</td>';
        $html .= '<td>' . htmlspecialchars($producto['categoria_nombre'] ?? '-') . '</td>';
        $html .= '<td>' . htmlspecialchars($producto['unidad_simbolo'] ?? $producto['unidad_nombre']) . '</td>';
        $html .= '<td>$' . number_format($producto['precio_compra'] ?? 0, 2) . '</td>';
        $html .= '<td>$' . number_format($producto['precio_venta'] ?? 0, 2) . '</td>';
        $html .= '<td>' . htmlspecialchars($producto['descripcion'] ?? '-') . '</td>';
        $html .= '</tr>';
    }
    
    $html .= '</tbody></table>';
    $html .= '<p><strong>Total de productos:</strong> ' . count($productos) . '</p>';
    $html .= '</div>';
    
    return ['html' => $html, 'tieneDatos' => true, 'datos' => $productos];
}

/**
 * Genera vista previa HTML para reporte de materiales por categoría
 */
function generarVistaPreviaMateriales($db) {
    $stmt = $db->query("
        SELECT 
            m.*,
            c.nombre as categoria_nombre,
            COUNT(DISTINCT p.id) as total_productos
        FROM materiales m
        LEFT JOIN categorias c ON m.categoria_id = c.id
        LEFT JOIN productos p ON m.id = p.material_id
        WHERE m.estado = 'activo'
        GROUP BY m.id
        ORDER BY c.nombre, m.nombre
    ");
    
    $materiales = $stmt->fetchAll();
    
    $tieneDatos = count($materiales) > 0;
    
    $html = '<div class="table-responsive">';
    $html .= '<h4>Reporte de Materiales por Categoría</h4>';
    $html .= '<p><strong>Fecha de generación:</strong> ' . date('d/m/Y H:i:s') . '</p>';
    
    if (!$tieneDatos) {
        $html .= '<div class="alert alert-warning">No se encontraron materiales activos.</div>';
        return ['html' => $html, 'tieneDatos' => false, 'datos' => []];
    }
    
    // Agrupar por categoría
    $porCategoria = [];
    foreach ($materiales as $material) {
        $catNombre = $material['categoria_nombre'] ?? 'Sin Categoría';
        if (!isset($porCategoria[$catNombre])) {
            $porCategoria[$catNombre] = [];
        }
        $porCategoria[$catNombre][] = $material;
    }
    
    foreach ($porCategoria as $categoria => $mats) {
        $html .= '<h5 class="mt-4 mb-3">';
        $html .= htmlspecialchars($categoria) . ' (' . count($mats) . ' materiales)</h5>';
        
        $html .= '<table class="table table-bordered table-striped mb-4">';
        $html .= '<thead><tr>';
        $html .= '<th>ID</th>';
        $html .= '<th>Material</th>';
        $html .= '<th>Descripción</th>';
        $html .= '<th>Total Productos</th>';
        $html .= '<th>Estado</th>';
        $html .= '</tr></thead><tbody>';
        
        foreach ($mats as $mat) {
            $html .= '<tr>';
            $html .= '<td>' . htmlspecialchars($mat['id']) . '</td>';
            $html .= '<td><strong>' . htmlspecialchars($mat['nombre']) . '</strong></td>';
            $html .= '<td>' . htmlspecialchars($mat['descripcion'] ?? '-') . '</td>';
            $html .= '<td>' . $mat['total_productos'] . '</td>';
            $html .= '<td><span class="badge badge-success">Activo</span></td>';
            $html .= '</tr>';
        }
        
        $html .= '</tbody></table>';
    }
    
    $html .= '<p><strong>Total de materiales:</strong> ' . count($materiales) . '</p>';
    $html .= '<p><strong>Total de categorías:</strong> ' . count($porCategoria) . '</p>';
    $html .= '</div>';
    
    return ['html' => $html, 'tieneDatos' => true, 'datos' => $materiales];
}

