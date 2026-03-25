<?php
/**
 * Generador de PDF para Reportes
 * Sistema de Gestión de Reciclaje
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

try {
    $auth = new Auth();
    if (!$auth->isAuthenticated()) {
        header('Location: ../index.php');
        exit;
    }
    
    $db = getDB();
    $currentUser = $auth->getCurrentUser();
    
    // Detectar sucursal del usuario
    $sucursalId = null;
    $stmtSuc = $db->prepare("SELECT id FROM sucursales WHERE responsable_id = ? OR id = (SELECT sucursal_id FROM usuarios WHERE id = ?)");
    $stmtSuc->execute([$currentUser['id'], $currentUser['id']]);
    $resSuc = $stmtSuc->fetch();
    if ($resSuc) {
        $sucursalId = $resSuc['id'];
    }
    
    $tipo = $_GET['tipo'] ?? '';
    $fechaDesde = $_GET['fecha_desde'] ?? '';
    $fechaHasta = $_GET['fecha_hasta'] ?? '';
    $rolId = $_GET['rol_id'] ?? '';
    $sucursalIdFiltro = $_GET['sucursal_id'] ?? $sucursalId;
    $material = $_GET['material'] ?? '';
    $nombreEmpleado = $_GET['nombre_empleado'] ?? '';
    
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
    
    // Generar contenido según el tipo
    switch ($tipo) {
        case 'inventarios':
            generarPDFInventarios($db, $fechaDesde, $fechaHasta, $sucursalIdFiltro, $material);
            break;
        case 'compras':
            generarPDFCompras($db, $fechaDesde, $fechaHasta, $sucursalIdFiltro, $material);
            break;
        case 'ventas':
            generarPDFVentas($db, $fechaDesde, $fechaHasta, $sucursalIdFiltro, $material);
            break;
        case 'productos':
            generarPDFProductos($db, $sucursalIdFiltro, $material);
            break;
        case 'materiales':
            generarPDFMateriales($db);
            break;
        case 'asistencia':
            generarPDFAsistencia($db, $fechaDesde, $fechaHasta, $sucursalIdFiltro, $nombreEmpleado);
            break;
        case 'sucursales':
            generarPDFSucursales($db, $fechaDesde, $fechaHasta, $sucursalIdFiltro);
            break;
        case 'usuarios':
            generarPDFUsuarios($db, $fechaDesde, $fechaHasta, $rolId, $sucursalIdFiltro);
            break;
        default:
            throw new Exception('Tipo de reporte no válido');
    }
    
} catch (Exception $e) {
    echo '<html><body><h1>Error</h1><p>' . htmlspecialchars($e->getMessage()) . '</p></body></html>';
}

/**
 * Genera PDF para reporte de sucursales
 */
function generarPDFSucursales($db, $fechaDesde, $fechaHasta, $sucursalId = null) {
    $sql = "
        SELECT 
            s.*,
            u.nombre as responsable_nombre,
            COUNT(DISTINCT i.id) as total_productos,
            COUNT(DISTINCT v.id) as total_ventas,
            COUNT(DISTINCT c.id) as total_compras,
            COALESCE(SUM(v.total), 0) as total_ventas_monto,
            COALESCE(SUM(c.total), 0) as total_compras_monto
        FROM sucursales s
        LEFT JOIN usuarios u ON s.responsable_id = u.id
        LEFT JOIN inventarios i ON s.id = i.sucursal_id
        LEFT JOIN ventas v ON s.id = v.sucursal_id AND DATE(v.fecha_venta) BETWEEN ? AND ?
        LEFT JOIN compras c ON s.id = c.sucursal_id AND DATE(c.fecha_compra) BETWEEN ? AND ?
        WHERE DATE(s.fecha_creacion) BETWEEN ? AND ?";
    
    $params = [$fechaDesde, $fechaHasta, $fechaDesde, $fechaHasta, $fechaDesde, $fechaHasta];
    
    if ($sucursalId) {
        $sql .= " AND s.id = ?";
        $params[] = $sucursalId;
    }
    
    $sql .= " GROUP BY s.id ORDER BY s.nombre";
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $sucursales = $stmt->fetchAll();
    
    $titulo = "Reporte de Sucursales";
    $periodo = date('d/m/Y', strtotime($fechaDesde)) . ' - ' . date('d/m/Y', strtotime($fechaHasta));
    
    generarHTMLPDF($titulo, $periodo, function() use ($sucursales) {
        echo '<table style="width: 100%; border-collapse: collapse; margin-top: 20px;">';
        echo '<thead>';
        echo '<tr style="background-color: #f8f9fa; border-bottom: 2px solid #dee2e6;">';
        echo '<th style="padding: 10px; text-align: left; border: 1px solid #dee2e6;">ID</th>';
        echo '<th style="padding: 10px; text-align: left; border: 1px solid #dee2e6;">Nombre</th>';
        echo '<th style="padding: 10px; text-align: left; border: 1px solid #dee2e6;">Dirección</th>';
        echo '<th style="padding: 10px; text-align: left; border: 1px solid #dee2e6;">Teléfono</th>';
        echo '<th style="padding: 10px; text-align: left; border: 1px solid #dee2e6;">Email</th>';
        echo '<th style="padding: 10px; text-align: left; border: 1px solid #dee2e6;">Responsable</th>';
        echo '<th style="padding: 10px; text-align: left; border: 1px solid #dee2e6;">Estado</th>';
        echo '<th style="padding: 10px; text-align: right; border: 1px solid #dee2e6;">Productos</th>';
        echo '<th style="padding: 10px; text-align: right; border: 1px solid #dee2e6;">Ventas</th>';
        echo '<th style="padding: 10px; text-align: right; border: 1px solid #dee2e6;">Compras</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        
        foreach ($sucursales as $sucursal) {
            echo '<tr style="border-bottom: 1px solid #dee2e6;">';
            echo '<td style="padding: 8px; border: 1px solid #dee2e6;">' . htmlspecialchars($sucursal['id']) . '</td>';
            echo '<td style="padding: 8px; border: 1px solid #dee2e6;">' . htmlspecialchars($sucursal['nombre']) . '</td>';
            echo '<td style="padding: 8px; border: 1px solid #dee2e6;">' . htmlspecialchars($sucursal['direccion'] ?? '-') . '</td>';
            echo '<td style="padding: 8px; border: 1px solid #dee2e6;">' . htmlspecialchars($sucursal['telefono'] ?? '-') . '</td>';
            echo '<td style="padding: 8px; border: 1px solid #dee2e6;">' . htmlspecialchars($sucursal['email'] ?? '-') . '</td>';
            echo '<td style="padding: 8px; border: 1px solid #dee2e6;">' . htmlspecialchars($sucursal['responsable_nombre'] ?? '-') . '</td>';
            echo '<td style="padding: 8px; border: 1px solid #dee2e6;">' . ucfirst($sucursal['estado']) . '</td>';
            echo '<td style="padding: 8px; text-align: right; border: 1px solid #dee2e6;">' . $sucursal['total_productos'] . '</td>';
            echo '<td style="padding: 8px; text-align: right; border: 1px solid #dee2e6;">' . $sucursal['total_ventas'] . '</td>';
            echo '<td style="padding: 8px; text-align: right; border: 1px solid #dee2e6;">' . $sucursal['total_compras'] . '</td>';
            echo '</tr>';
        }
        
        echo '</tbody>';
        echo '</table>';
        echo '<p style="margin-top: 20px;"><strong>Total de sucursales:</strong> ' . count($sucursales) . '</p>';
    });
}

/**
 * Genera PDF para reporte de usuarios por rol
 */
function generarPDFUsuarios($db, $fechaDesde, $fechaHasta, $rolId = '', $sucursalId = null) {
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
    
    if ($sucursalId) {
        $sql .= " AND u.sucursal_id = ?";
        $params[] = $sucursalId;
    }
    
    if (!empty($rolId)) {
        $sql .= " AND u.rol_id = ?";
        $params[] = $rolId;
    }
    
    $sql .= " GROUP BY u.id ORDER BY r.nombre, u.nombre";
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $usuarios = $stmt->fetchAll();
    
    $titulo = "Reporte de Usuarios por Rol";
    $periodo = date('d/m/Y', strtotime($fechaDesde)) . ' - ' . date('d/m/Y', strtotime($fechaHasta));
    
    $filtroRol = '';
    if (!empty($rolId)) {
        $stmt = $db->prepare("SELECT nombre FROM roles WHERE id = ?");
        $stmt->execute([$rolId]);
        $rol = $stmt->fetch();
        if ($rol) {
            $filtroRol = '<p><strong>Rol filtrado:</strong> ' . htmlspecialchars($rol['nombre']) . '</p>';
        }
    }
    
    generarHTMLPDF($titulo, $periodo, function() use ($usuarios, $filtroRol) {
        if ($filtroRol) {
            echo $filtroRol;
        }
        
        echo '<table style="width: 100%; border-collapse: collapse; margin-top: 20px;">';
        echo '<thead>';
        echo '<tr style="background-color: #f8f9fa; border-bottom: 2px solid #dee2e6;">';
        echo '<th style="padding: 10px; text-align: left; border: 1px solid #dee2e6;">ID</th>';
        echo '<th style="padding: 10px; text-align: left; border: 1px solid #dee2e6;">Nombre</th>';
        echo '<th style="padding: 10px; text-align: left; border: 1px solid #dee2e6;">Cédula</th>';
        echo '<th style="padding: 10px; text-align: left; border: 1px solid #dee2e6;">Email</th>';
        echo '<th style="padding: 10px; text-align: left; border: 1px solid #dee2e6;">Teléfono</th>';
        echo '<th style="padding: 10px; text-align: left; border: 1px solid #dee2e6;">Rol</th>';
        echo '<th style="padding: 10px; text-align: left; border: 1px solid #dee2e6;">Estado</th>';
        echo '<th style="padding: 10px; text-align: right; border: 1px solid #dee2e6;">Ventas</th>';
        echo '<th style="padding: 10px; text-align: right; border: 1px solid #dee2e6;">Compras</th>';
        echo '<th style="padding: 10px; text-align: left; border: 1px solid #dee2e6;">Fecha Creación</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        
        foreach ($usuarios as $usuario) {
            echo '<tr style="border-bottom: 1px solid #dee2e6;">';
            echo '<td style="padding: 8px; border: 1px solid #dee2e6;">' . htmlspecialchars($usuario['id']) . '</td>';
            echo '<td style="padding: 8px; border: 1px solid #dee2e6;">' . htmlspecialchars($usuario['nombre']) . '</td>';
            echo '<td style="padding: 8px; border: 1px solid #dee2e6;">' . htmlspecialchars($usuario['cedula']) . '</td>';
            echo '<td style="padding: 8px; border: 1px solid #dee2e6;">' . htmlspecialchars($usuario['email']) . '</td>';
            echo '<td style="padding: 8px; border: 1px solid #dee2e6;">' . htmlspecialchars($usuario['telefono'] ?? '-') . '</td>';
            echo '<td style="padding: 8px; border: 1px solid #dee2e6;">' . htmlspecialchars($usuario['rol_nombre']) . '</td>';
            echo '<td style="padding: 8px; border: 1px solid #dee2e6;">' . ucfirst($usuario['estado']) . '</td>';
            echo '<td style="padding: 8px; text-align: right; border: 1px solid #dee2e6;">' . $usuario['total_ventas'] . '</td>';
            echo '<td style="padding: 8px; text-align: right; border: 1px solid #dee2e6;">' . $usuario['total_compras'] . '</td>';
            echo '<td style="padding: 8px; border: 1px solid #dee2e6;">' . date('d/m/Y', strtotime($usuario['fecha_creacion'])) . '</td>';
            echo '</tr>';
        }
        
        echo '</tbody>';
        echo '</table>';
        echo '<p style="margin-top: 20px;"><strong>Total de usuarios:</strong> ' . count($usuarios) . '</p>';
    });
}

/**
 * Genera PDF para reporte de inventarios
 */
function generarPDFInventarios($db, $fechaDesde, $fechaHasta, $sucursalId = null, $material = '') {
    try {
        $db->query("SELECT 1 FROM inventarios LIMIT 1");
    } catch (Exception $e) {
        throw new Exception('La tabla de inventarios no existe');
    }
    
    $sql = "
        SELECT 
            i.*,
            COALESCE(p.nombre, 'Producto eliminado') as producto_nombre,
            COALESCE(m.nombre, 'Material eliminado') as material_nombre,
            c.nombre as categoria_nombre,
            COALESCE(u.nombre, 'Unidad eliminada') as unidad_nombre,
            u.simbolo as unidad_simbolo,
            COALESCE(s.nombre, 'Sucursal eliminada') as sucursal_nombre,
            pr.precio_unitario as precio_venta
        FROM inventarios i
        LEFT JOIN productos p ON i.producto_id = p.id
        LEFT JOIN materiales m ON p.material_id = m.id
        LEFT JOIN categorias c ON m.categoria_id = c.id
        LEFT JOIN unidades u ON p.unidad_id = u.id
        LEFT JOIN sucursales s ON i.sucursal_id = s.id
        LEFT JOIN precios pr ON p.id = pr.producto_id AND pr.tipo_precio = 'venta' AND pr.estado = 'activo'
        WHERE DATE(i.fecha_creacion) BETWEEN ? AND ?";
    
    $params = [$fechaDesde, $fechaHasta];
    
    if ($sucursalId) {
        $sql .= " AND i.sucursal_id = ?";
        $params[] = $sucursalId;
    }

    if ($material) {
        $sql .= " AND m.nombre = ?";
        $params[] = $material;
    }
    
    $sql .= " ORDER BY s.nombre, p.nombre";
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $inventarios = $stmt->fetchAll();
    
    $titulo = "Reporte de Inventarios";
    $periodo = date('d/m/Y', strtotime($fechaDesde)) . ' - ' . date('d/m/Y', strtotime($fechaHasta));
    
    generarHTMLPDF($titulo, $periodo, function() use ($inventarios) {
        if (empty($inventarios)) {
            echo '<p>No se encontraron inventarios en el período seleccionado.</p>';
            return;
        }
        
        echo '<table style="width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 10px;">';
        echo '<thead>';
        echo '<tr style="background-color: #f8f9fa; border-bottom: 2px solid #dee2e6;">';
        echo '<th style="padding: 8px; text-align: left; border: 1px solid #dee2e6;">Sucursal</th>';
        echo '<th style="padding: 8px; text-align: left; border: 1px solid #dee2e6;">Producto</th>';
        echo '<th style="padding: 8px; text-align: left; border: 1px solid #dee2e6;">Material</th>';
        echo '<th style="padding: 8px; text-align: left; border: 1px solid #dee2e6;">Categoría</th>';
        echo '<th style="padding: 8px; text-align: right; border: 1px solid #dee2e6;">Cantidad</th>';
        echo '<th style="padding: 8px; text-align: left; border: 1px solid #dee2e6;">Unidad</th>';
        echo '<th style="padding: 8px; text-align: right; border: 1px solid #dee2e6;">Precio Venta</th>';
        echo '<th style="padding: 8px; text-align: left; border: 1px solid #dee2e6;">Estado</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        
        $totalValor = 0;
        foreach ($inventarios as $inv) {
            $valor = floatval($inv['cantidad']) * floatval($inv['precio_venta'] ?? 0);
            $totalValor += $valor;
            
            echo '<tr style="border-bottom: 1px solid #dee2e6;">';
            echo '<td style="padding: 6px; border: 1px solid #dee2e6;">' . htmlspecialchars($inv['sucursal_nombre']) . '</td>';
            echo '<td style="padding: 6px; border: 1px solid #dee2e6;">' . htmlspecialchars($inv['producto_nombre']) . '</td>';
            echo '<td style="padding: 6px; border: 1px solid #dee2e6;">' . htmlspecialchars($inv['material_nombre']) . '</td>';
            echo '<td style="padding: 6px; border: 1px solid #dee2e6;">' . htmlspecialchars($inv['categoria_nombre'] ?? '-') . '</td>';
            echo '<td style="padding: 6px; text-align: right; border: 1px solid #dee2e6;">' . number_format($inv['cantidad'], 2) . '</td>';
            echo '<td style="padding: 6px; border: 1px solid #dee2e6;">' . htmlspecialchars($inv['unidad_simbolo'] ?? $inv['unidad_nombre']) . '</td>';
            echo '<td style="padding: 6px; text-align: right; border: 1px solid #dee2e6;">$' . number_format($inv['precio_venta'] ?? 0, 2) . '</td>';
            echo '<td style="padding: 6px; border: 1px solid #dee2e6;">' . ucfirst($inv['estado']) . '</td>';
            echo '</tr>';
        }
        
        echo '</tbody>';
        echo '</table>';
        echo '<p style="margin-top: 20px;"><strong>Total de registros:</strong> ' . count($inventarios) . '</p>';
        echo '<p><strong>Valor total del inventario:</strong> $' . number_format($totalValor, 2) . '</p>';
    });
}

/**
 * Genera PDF para reporte de compras
 */
function generarPDFCompras($db, $fechaDesde, $fechaHasta, $sucursalId = null, $material = '') {
    // Verificar existencia de tablas
    $tablaDetalleExiste = false;
    $tablaSucursalesExiste = false;
    $tablaProveedoresExiste = false;
    
    try {
        $db->query("SELECT 1 FROM compras_detalle LIMIT 1");
        $tablaDetalleExiste = true;
    } catch (Exception $e) {
        $tablaDetalleExiste = false;
    }
    
    try {
        $db->query("SELECT 1 FROM sucursales LIMIT 1");
        $tablaSucursalesExiste = true;
    } catch (Exception $e) {
        $tablaSucursalesExiste = false;
    }
    
    try {
        $db->query("SELECT 1 FROM proveedores LIMIT 1");
        $tablaProveedoresExiste = true;
    } catch (Exception $e) {
        $tablaProveedoresExiste = false;
    }
    
    if ($tablaDetalleExiste && $tablaSucursalesExiste && $tablaProveedoresExiste) {
        $sql = "
            SELECT 
                c.*,
                COALESCE(s.nombre, 'Sucursal eliminada') as sucursal_nombre,
                COALESCE(pr.nombre, 'Proveedor eliminado') as proveedor_nombre,
                u.nombre as creado_por_nombre,
                COUNT(cd.id) as total_items,
                COALESCE(SUM(cd.subtotal), 0) as total_compra
            FROM compras c
            LEFT JOIN sucursales s ON c.sucursal_id = s.id
            LEFT JOIN proveedores pr ON c.proveedor_id = pr.id
            LEFT JOIN usuarios u ON c.creado_por = u.id
            LEFT JOIN compras_detalle cd ON c.id = cd.compra_id";
        
        if ($material) {
            $sql .= " LEFT JOIN productos p ON cd.producto_id = p.id 
                      LEFT JOIN materiales m ON p.material_id = m.id";
        }

        $sql .= " WHERE DATE(c.fecha_compra) BETWEEN ? AND ?";
        
        $params = [$fechaDesde, $fechaHasta];
        
        if ($sucursalId) {
            $sql .= " AND c.sucursal_id = ?";
            $params[] = $sucursalId;
        }

        if ($material) {
            $sql .= " AND m.nombre = ?";
            $params[] = $material;
        }
        
        $sql .= " GROUP BY c.id ORDER BY c.fecha_compra DESC";
        
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
    } else {
        $sql = "
            SELECT 
                c.*,
                CAST(c.sucursal_id AS CHAR) as sucursal_nombre,
                CAST(c.proveedor_id AS CHAR) as proveedor_nombre,
                NULL as creado_por_nombre,
                0 as total_items,
                COALESCE(c.total, 0) as total_compra
            FROM compras c
            WHERE DATE(c.fecha_compra) BETWEEN ? AND ?";
        
        $params = [$fechaDesde, $fechaHasta];
        
        if ($sucursalId) {
            $sql .= " AND c.sucursal_id = ?";
            $params[] = $sucursalId;
        }
        
        $sql .= " ORDER BY c.fecha_compra DESC";
        
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
    }
    $compras = $stmt->fetchAll();
    
    $titulo = "Reporte de Compras";
    $periodo = date('d/m/Y', strtotime($fechaDesde)) . ' - ' . date('d/m/Y', strtotime($fechaHasta));
    
    generarHTMLPDF($titulo, $periodo, function() use ($compras) {
        if (empty($compras)) {
            echo '<p>No se encontraron compras en el período seleccionado.</p>';
            return;
        }
        
        echo '<table style="width: 100%; border-collapse: collapse; margin-top: 20px;">';
        echo '<thead>';
        echo '<tr style="background-color: #f8f9fa; border-bottom: 2px solid #dee2e6;">';
        echo '<th style="padding: 10px; text-align: left; border: 1px solid #dee2e6;">ID</th>';
        echo '<th style="padding: 10px; text-align: left; border: 1px solid #dee2e6;">Fecha</th>';
        echo '<th style="padding: 10px; text-align: left; border: 1px solid #dee2e6;">Sucursal</th>';
        echo '<th style="padding: 10px; text-align: left; border: 1px solid #dee2e6;">Proveedor</th>';
        echo '<th style="padding: 10px; text-align: right; border: 1px solid #dee2e6;">Total Items</th>';
        echo '<th style="padding: 10px; text-align: right; border: 1px solid #dee2e6;">Total Compra</th>';
        echo '<th style="padding: 10px; text-align: left; border: 1px solid #dee2e6;">Estado</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        
        $totalGeneral = 0;
        foreach ($compras as $compra) {
            $total = floatval($compra['total_compra'] ?? 0);
            $totalGeneral += $total;
            
            echo '<tr style="border-bottom: 1px solid #dee2e6;">';
            echo '<td style="padding: 8px; border: 1px solid #dee2e6;">' . htmlspecialchars($compra['id']) . '</td>';
            echo '<td style="padding: 8px; border: 1px solid #dee2e6;">' . date('d/m/Y', strtotime($compra['fecha_compra'])) . '</td>';
            echo '<td style="padding: 8px; border: 1px solid #dee2e6;">' . htmlspecialchars($compra['sucursal_nombre']) . '</td>';
            echo '<td style="padding: 8px; border: 1px solid #dee2e6;">' . htmlspecialchars($compra['proveedor_nombre']) . '</td>';
            echo '<td style="padding: 8px; text-align: right; border: 1px solid #dee2e6;">' . $compra['total_items'] . '</td>';
            echo '<td style="padding: 8px; text-align: right; border: 1px solid #dee2e6;">$' . number_format($total, 2) . '</td>';
            echo '<td style="padding: 8px; border: 1px solid #dee2e6;">' . ucfirst($compra['estado']) . '</td>';
            echo '</tr>';
        }
        
        echo '</tbody>';
        echo '</table>';
        echo '<p style="margin-top: 20px;"><strong>Total de compras:</strong> ' . count($compras) . '</p>';
        echo '<p><strong>Total general:</strong> $' . number_format($totalGeneral, 2) . '</p>';
    });
}

/**
 * Genera PDF para reporte de ventas
 */
function generarPDFVentas($db, $fechaDesde, $fechaHasta, $sucursalId = null, $material = '') {
    // Verificar si las tablas existen
    $tablaDetalleExiste = false;
    $tablaSucursalesExiste = false;
    
    try {
        $db->query("SELECT 1 FROM ventas_detalle LIMIT 1");
        $tablaDetalleExiste = true;
    } catch (Exception $e) {
        $tablaDetalleExiste = false;
    }
    
    try {
        $db->query("SELECT 1 FROM sucursales LIMIT 1");
        $tablaSucursalesExiste = true;
    } catch (Exception $e) {
        $tablaSucursalesExiste = false;
    }
    
    if ($tablaDetalleExiste && $tablaSucursalesExiste) {
        $sql = "
            SELECT 
                v.*,
                COALESCE(s.nombre, 'Sucursal eliminada') as sucursal_nombre,
                u.nombre as creado_por_nombre,
                COUNT(vd.id) as total_items,
                COALESCE(SUM(vd.subtotal), 0) as total_venta
            FROM ventas v
            LEFT JOIN sucursales s ON v.sucursal_id = s.id
            LEFT JOIN usuarios u ON v.creado_por = u.id
            LEFT JOIN ventas_detalle vd ON v.id = vd.venta_id";

        if ($material) {
            $sql .= " LEFT JOIN productos p ON vd.producto_id = p.id 
                      LEFT JOIN materiales m ON p.material_id = m.id";
        }

        $sql .= " WHERE DATE(v.fecha_venta) BETWEEN ? AND ?";
        
        $params = [$fechaDesde, $fechaHasta];
        
        if ($sucursalId) {
            $sql .= " AND v.sucursal_id = ?";
            $params[] = $sucursalId;
        }

        if ($material) {
            $sql .= " AND m.nombre = ?";
            $params[] = $material;
        }
        
        $sql .= " GROUP BY v.id ORDER BY v.fecha_venta DESC";
        
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
    } else {
        $sql = "
            SELECT 
                v.*,
                CAST(v.sucursal_id AS CHAR) as sucursal_nombre,
                NULL as creado_por_nombre,
                0 as total_items,
                COALESCE(v.total, 0) as total_venta
            FROM ventas v
            WHERE DATE(v.fecha_venta) BETWEEN ? AND ?";
        
        $params = [$fechaDesde, $fechaHasta];
        
        if ($sucursalId) {
            $sql .= " AND v.sucursal_id = ?";
            $params[] = $sucursalId;
        }
        
        $sql .= " ORDER BY v.fecha_venta DESC";
        
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
    }
    $ventas = $stmt->fetchAll();
    
    $titulo = "Reporte de Ventas";
    $periodo = date('d/m/Y', strtotime($fechaDesde)) . ' - ' . date('d/m/Y', strtotime($fechaHasta));
    
    generarHTMLPDF($titulo, $periodo, function() use ($ventas) {
        if (empty($ventas)) {
            echo '<p>No se encontraron ventas en el período seleccionado.</p>';
            return;
        }
        
        echo '<table style="width: 100%; border-collapse: collapse; margin-top: 20px;">';
        echo '<thead>';
        echo '<tr style="background-color: #f8f9fa; border-bottom: 2px solid #dee2e6;">';
        echo '<th style="padding: 10px; text-align: left; border: 1px solid #dee2e6;">ID</th>';
        echo '<th style="padding: 10px; text-align: left; border: 1px solid #dee2e6;">Fecha</th>';
        echo '<th style="padding: 10px; text-align: left; border: 1px solid #dee2e6;">Sucursal</th>';
        echo '<th style="padding: 10px; text-align: left; border: 1px solid #dee2e6;">Cliente</th>';
        echo '<th style="padding: 10px; text-align: right; border: 1px solid #dee2e6;">Total Items</th>';
        echo '<th style="padding: 10px; text-align: right; border: 1px solid #dee2e6;">Total Venta</th>';
        echo '<th style="padding: 10px; text-align: left; border: 1px solid #dee2e6;">Estado</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        
        $totalGeneral = 0;
        foreach ($ventas as $venta) {
            $total = floatval($venta['total_venta'] ?? 0);
            $totalGeneral += $total;
            
            echo '<tr style="border-bottom: 1px solid #dee2e6;">';
            echo '<td style="padding: 8px; border: 1px solid #dee2e6;">' . htmlspecialchars($venta['id']) . '</td>';
            echo '<td style="padding: 8px; border: 1px solid #dee2e6;">' . date('d/m/Y', strtotime($venta['fecha_venta'])) . '</td>';
            echo '<td style="padding: 8px; border: 1px solid #dee2e6;">' . htmlspecialchars($venta['sucursal_nombre']) . '</td>';
            echo '<td style="padding: 8px; border: 1px solid #dee2e6;">' . htmlspecialchars($venta['cliente_nombre'] ?? 'Cliente General') . '</td>';
            echo '<td style="padding: 8px; text-align: right; border: 1px solid #dee2e6;">' . $venta['total_items'] . '</td>';
            echo '<td style="padding: 8px; text-align: right; border: 1px solid #dee2e6;">$' . number_format($total, 2) . '</td>';
            echo '<td style="padding: 8px; border: 1px solid #dee2e6;">' . ucfirst($venta['estado']) . '</td>';
            echo '</tr>';
        }
        
        echo '</tbody>';
        echo '</table>';
        echo '<p style="margin-top: 20px;"><strong>Total de ventas:</strong> ' . count($ventas) . '</p>';
        echo '<p><strong>Total general:</strong> $' . number_format($totalGeneral, 2) . '</p>';
    });
}

/**
 * Genera PDF para reporte de productos
 */
function generarPDFProductos($db, $sucursalId = null, $material = '') {
    $sql = "
        SELECT 
            p.*,
            m.nombre as material_nombre,
            c.nombre as categoria_nombre,
            u.nombre as unidad_nombre,
            u.simbolo as unidad_simbolo,
            (SELECT precio_unitario FROM precios WHERE producto_id = p.id AND tipo_precio = 'venta' AND estado = 'activo' LIMIT 1) as precio_venta,
            (SELECT precio_unitario FROM precios WHERE producto_id = p.id AND tipo_precio = 'compra' AND estado = 'activo' LIMIT 1) as precio_compra
        FROM productos p
        LEFT JOIN materiales m ON p.material_id = m.id
        LEFT JOIN categorias c ON m.categoria_id = c.id
        LEFT JOIN unidades u ON p.unidad_id = u.id";
    
    $params = [];
    $where = ["p.estado = 'activo'"];

    if ($sucursalId) {
        $sql .= " INNER JOIN inventarios i ON p.id = i.producto_id";
        $where[] = "i.sucursal_id = ?";
        $params[] = $sucursalId;
    }

    if ($material) {
        $where[] = "m.nombre = ?";
        $params[] = $material;
    }
    
    if (!empty($where)) {
        $sql .= " WHERE " . implode(" AND ", $where);
    }
    
    if ($sucursalId) {
        $sql .= " GROUP BY p.id";
    }
    
    $sql .= " ORDER BY c.nombre, m.nombre, p.nombre";
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    
    $productos = $stmt->fetchAll();
    
    $titulo = "Reporte de Productos";
    $periodo = "Todos los productos activos";
    
    generarHTMLPDF($titulo, $periodo, function() use ($productos) {
        if (empty($productos)) {
            echo '<p>No se encontraron productos activos.</p>';
            return;
        }
        
        echo '<table style="width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 10px;">';
        echo '<thead>';
        echo '<tr style="background-color: #f8f9fa; border-bottom: 2px solid #dee2e6;">';
        echo '<th style="padding: 8px; text-align: left; border: 1px solid #dee2e6;">ID</th>';
        echo '<th style="padding: 8px; text-align: left; border: 1px solid #dee2e6;">Producto</th>';
        echo '<th style="padding: 8px; text-align: left; border: 1px solid #dee2e6;">Material</th>';
        echo '<th style="padding: 8px; text-align: left; border: 1px solid #dee2e6;">Categoría</th>';
        echo '<th style="padding: 8px; text-align: left; border: 1px solid #dee2e6;">Unidad</th>';
        echo '<th style="padding: 8px; text-align: right; border: 1px solid #dee2e6;">Precio Compra</th>';
        echo '<th style="padding: 8px; text-align: right; border: 1px solid #dee2e6;">Precio Venta</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        
        foreach ($productos as $producto) {
            echo '<tr style="border-bottom: 1px solid #dee2e6;">';
            echo '<td style="padding: 6px; border: 1px solid #dee2e6;">' . htmlspecialchars($producto['id']) . '</td>';
            echo '<td style="padding: 6px; border: 1px solid #dee2e6;"><strong>' . htmlspecialchars($producto['nombre']) . '</strong></td>';
            echo '<td style="padding: 6px; border: 1px solid #dee2e6;">' . htmlspecialchars($producto['material_nombre'] ?? '-') . '</td>';
            echo '<td style="padding: 6px; border: 1px solid #dee2e6;">' . htmlspecialchars($producto['categoria_nombre'] ?? '-') . '</td>';
            echo '<td style="padding: 6px; border: 1px solid #dee2e6;">' . htmlspecialchars($producto['unidad_simbolo'] ?? $producto['unidad_nombre']) . '</td>';
            echo '<td style="padding: 6px; text-align: right; border: 1px solid #dee2e6;">$' . number_format($producto['precio_compra'] ?? 0, 2) . '</td>';
            echo '<td style="padding: 6px; text-align: right; border: 1px solid #dee2e6;">$' . number_format($producto['precio_venta'] ?? 0, 2) . '</td>';
            echo '</tr>';
        }
        
        echo '</tbody>';
        echo '</table>';
        echo '<p style="margin-top: 20px;"><strong>Total de productos:</strong> ' . count($productos) . '</p>';
    });
}

/**
 * Genera PDF para reporte de materiales por categoría
 */
function generarPDFMateriales($db) {
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
    
    $titulo = "Reporte de Materiales por Categoría";
    $periodo = "Todos los materiales activos";
    
    generarHTMLPDF($titulo, $periodo, function() use ($materiales) {
        if (empty($materiales)) {
            echo '<p>No se encontraron materiales activos.</p>';
            return;
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
            echo '<h3 style="margin-top: 30px; color: #007bff; border-bottom: 2px solid #007bff; padding-bottom: 5px;">';
            echo htmlspecialchars($categoria) . ' (' . count($mats) . ' materiales)';
            echo '</h3>';
            
            echo '<table style="width: 100%; border-collapse: collapse; margin-top: 10px; margin-bottom: 20px;">';
            echo '<thead>';
            echo '<tr style="background-color: #f8f9fa; border-bottom: 2px solid #dee2e6;">';
            echo '<th style="padding: 10px; text-align: left; border: 1px solid #dee2e6;">ID</th>';
            echo '<th style="padding: 10px; text-align: left; border: 1px solid #dee2e6;">Material</th>';
            echo '<th style="padding: 10px; text-align: left; border: 1px solid #dee2e6;">Descripción</th>';
            echo '<th style="padding: 10px; text-align: right; border: 1px solid #dee2e6;">Total Productos</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
            
            foreach ($mats as $mat) {
                echo '<tr style="border-bottom: 1px solid #dee2e6;">';
                echo '<td style="padding: 8px; border: 1px solid #dee2e6;">' . htmlspecialchars($mat['id']) . '</td>';
                echo '<td style="padding: 8px; border: 1px solid #dee2e6;"><strong>' . htmlspecialchars($mat['nombre']) . '</strong></td>';
                echo '<td style="padding: 8px; border: 1px solid #dee2e6;">' . htmlspecialchars($mat['descripcion'] ?? '-') . '</td>';
                echo '<td style="padding: 8px; text-align: right; border: 1px solid #dee2e6;">' . $mat['total_productos'] . '</td>';
                echo '</tr>';
            }
            
            echo '</tbody>';
            echo '</table>';
        }
        
        echo '<p style="margin-top: 30px;"><strong>Total de materiales:</strong> ' . count($materiales) . '</p>';
        echo '<p><strong>Total de categorías:</strong> ' . count($porCategoria) . '</p>';
    });
}

/**
 * Genera PDF para reporte de asistencia
 */
function generarPDFAsistencia($db, $fechaDesde, $fechaHasta, $sucursalId = null, $nombreEmpleado = '') {
    $sql = "
        SELECT
            e.nombres,
            e.apellidos,
            e.cedula,
            e.cargo,
            s.nombre as sucursal,
            COALESCE(a.dias_asistidos, 0) as dias_asistidos,
            COALESCE(a.dias_falta, 0) as dias_falta,
            COALESCE(pd.sueldo_acumulado, 0) as sueldo_acumulado
        FROM empleados e
        LEFT JOIN sucursales s ON e.sucursal_id = s.id
        LEFT JOIN (
            SELECT empleado_id,
                   COUNT(CASE WHEN estado = 'asistio' THEN 1 END) as dias_asistidos,
                   COUNT(CASE WHEN estado = 'falta' THEN 1 END) as dias_falta
            FROM asistencias
            WHERE fecha BETWEEN ? AND ?
            GROUP BY empleado_id
        ) a ON a.empleado_id = e.id
        LEFT JOIN (
            SELECT empleado_id, SUM(monto) as sueldo_acumulado
            FROM pagos_diarios
            WHERE fecha_laborada BETWEEN ? AND ?
            GROUP BY empleado_id
        ) pd ON pd.empleado_id = e.id
        WHERE e.estado = 'ACTIVO'
    ";
    $params = [$fechaDesde, $fechaHasta, $fechaDesde, $fechaHasta];

    if ($sucursalId) {
        $sql .= " AND e.sucursal_id = ?";
        $params[] = $sucursalId;
    }

    if (!empty($nombreEmpleado)) {
        $sql .= " AND CONCAT(e.nombres, ' ', e.apellidos) LIKE ?";
        $params[] = '%' . $nombreEmpleado . '%';
    }

    $sql .= " GROUP BY e.id ORDER BY s.nombre, e.apellidos, e.nombres";

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $empleados = $stmt->fetchAll();

    $titulo = "Reporte de Asistencia";
    $periodo = date('d/m/Y', strtotime($fechaDesde)) . ' - ' . date('d/m/Y', strtotime($fechaHasta));

    generarHTMLPDF($titulo, $periodo, function() use ($empleados, $nombreEmpleado) {
        if (!empty($nombreEmpleado)) {
            echo '<p><strong>Empleado filtrado:</strong> ' . htmlspecialchars($nombreEmpleado) . '</p>';
        }

        if (empty($empleados)) {
            echo '<p>No se encontraron empleados con registros en el período seleccionado.</p>';
            return;
        }

        echo '<table style="width: 100%; border-collapse: collapse; margin-top: 20px;">';
        echo '<thead>';
        echo '<tr style="background-color: #f8f9fa; border-bottom: 2px solid #dee2e6;">';
        echo '<th style="padding: 10px; text-align: left; border: 1px solid #dee2e6;">Nombres</th>';
        echo '<th style="padding: 10px; text-align: left; border: 1px solid #dee2e6;">Apellidos</th>';
        echo '<th style="padding: 10px; text-align: left; border: 1px solid #dee2e6;">Cédula</th>';
        echo '<th style="padding: 10px; text-align: left; border: 1px solid #dee2e6;">Cargo</th>';
        echo '<th style="padding: 10px; text-align: left; border: 1px solid #dee2e6;">Sucursal</th>';
        echo '<th style="padding: 10px; text-align: right; border: 1px solid #dee2e6;">Días Asistidos</th>';
        echo '<th style="padding: 10px; text-align: right; border: 1px solid #dee2e6;">Días Falta</th>';
        echo '<th style="padding: 10px; text-align: right; border: 1px solid #dee2e6;">Sueldo Acumulado</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';

        foreach ($empleados as $emp) {
            echo '<tr style="border-bottom: 1px solid #dee2e6;">';
            echo '<td style="padding: 8px; border: 1px solid #dee2e6;">' . htmlspecialchars($emp['nombres']) . '</td>';
            echo '<td style="padding: 8px; border: 1px solid #dee2e6;">' . htmlspecialchars($emp['apellidos']) . '</td>';
            echo '<td style="padding: 8px; border: 1px solid #dee2e6;">' . htmlspecialchars($emp['cedula'] ?? '-') . '</td>';
            echo '<td style="padding: 8px; border: 1px solid #dee2e6;">' . htmlspecialchars($emp['cargo'] ?? '-') . '</td>';
            echo '<td style="padding: 8px; border: 1px solid #dee2e6;">' . htmlspecialchars($emp['sucursal'] ?? '-') . '</td>';
            echo '<td style="padding: 8px; text-align: right; border: 1px solid #dee2e6;">' . $emp['dias_asistidos'] . '</td>';
            echo '<td style="padding: 8px; text-align: right; border: 1px solid #dee2e6;">' . $emp['dias_falta'] . '</td>';
            echo '<td style="padding: 8px; text-align: right; border: 1px solid #dee2e6;">$' . number_format($emp['sueldo_acumulado'], 2) . '</td>';
            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';
        echo '<p style="margin-top: 20px;"><strong>Total de empleados:</strong> ' . count($empleados) . '</p>';
    });
}

/**
 * Genera HTML formateado para PDF
 */
function generarHTMLPDF($titulo, $periodo, $callback) {
    header('Content-Type: text/html; charset=utf-8');
    
    echo '<!DOCTYPE html>';
    echo '<html lang="es">';
    echo '<head>';
    echo '<meta charset="UTF-8">';
    echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
    echo '<title>' . htmlspecialchars($titulo) . '</title>';
    echo '<style>';
    echo 'body { font-family: Arial, sans-serif; margin: 20px; }';
    echo 'h1 { color: #333; border-bottom: 3px solid #007bff; padding-bottom: 10px; }';
    echo 'p { margin: 10px 0; }';
    echo '@media print {';
    echo '  body { margin: 0; }';
    echo '  .no-print { display: none; }';
    echo '}';
    echo '</style>';
    echo '<script>';
    echo 'window.onload = function() { window.print(); }';
    echo '</script>';
    echo '</head>';
    echo '<body>';
    echo '<div class="no-print" style="margin-bottom: 20px; padding: 10px; background-color: #f8f9fa; border-radius: 5px;">';
    echo '<button onclick="window.print()" style="padding: 10px 20px; background-color: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer;">Imprimir / Guardar como PDF</button>';
    echo '</div>';
    echo '<h1>' . htmlspecialchars($titulo) . '</h1>';
    echo '<p><strong>Período:</strong> ' . htmlspecialchars($periodo) . '</p>';
    echo '<p><strong>Fecha de generación:</strong> ' . date('d/m/Y H:i:s') . '</p>';
    
    call_user_func($callback);
    
    echo '</body>';
    echo '</html>';
}

