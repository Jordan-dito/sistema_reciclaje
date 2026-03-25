<?php
/**
 * API de Reportes
 * Sistema de Gestión de Reciclaje
 * Soporta web (sesión) y Flutter (usuario_id / token)
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
ob_start();

// Preflight CORS (para Flutter/móvil)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    ob_end_clean();
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/ErrorHandler.php';

try {
    $auth = new Auth();
    $currentUser = null;
    
    // Web: autenticación por sesión
    if ($auth->isAuthenticated()) {
        $currentUser = $auth->getCurrentUser();
    }
    // Flutter: autenticación por usuario_id (GET, POST o JSON)
    if (!$currentUser) {
        $usuarioId = $_GET['usuario_id'] ?? $_GET['id'] ?? $_POST['usuario_id'] ?? $_POST['id'] ?? null;
        if (!$usuarioId && strpos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false) {
            $json = json_decode(file_get_contents('php://input'), true);
            $usuarioId = $json['usuario_id'] ?? $json['id'] ?? null;
        }
        if ($usuarioId) {
            $db = getDB();
            $stmt = $db->prepare("
                SELECT u.id, u.nombre, u.email, u.rol_id, u.sucursal_id 
                FROM usuarios u 
                WHERE u.id = ? AND u.estado = 'activo'
            ");
            $stmt->execute([$usuarioId]);
            $u = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($u) {
                $currentUser = [
                    'id' => $u['id'],
                    'nombre' => $u['nombre'],
                    'email' => $u['email'],
                    'rol' => ''
                ];
            }
        }
    }
    
    if (!$currentUser) {
        ob_end_clean();
        echo ErrorHandler::handleAuthError('No autenticado. Inicia sesión o proporciona usuario_id.');
        exit;
    }
    
    $db = getDB();
    
    // Detectar sucursal del usuario
    $sucursalId = null;
    $stmtSuc = $db->prepare("SELECT id FROM sucursales WHERE responsable_id = ? OR id = (SELECT sucursal_id FROM usuarios WHERE id = ?)");
    $stmtSuc->execute([$currentUser['id'], $currentUser['id']]);
    $resSuc = $stmtSuc->fetch();
    if ($resSuc) {
        $sucursalId = $resSuc['id'];
    }
    
    $action = $_GET['action'] ?? '';
    
    if ($action === 'vista_previa') {
        $tipo = $_GET['tipo'] ?? '';
        $fechaDesde = $_GET['fecha_desde'] ?? '';
        $fechaHasta = $_GET['fecha_hasta'] ?? '';
        $rolId = $_GET['rol_id'] ?? '';
        $sucursalIdFiltro = $_GET['sucursal_id'] ?? $sucursalId; // Si no viene filtro, usar la del usuario
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
        
        $html = '';
        
        $resultado = null;
        $tieneDatos = false;
        
        switch ($tipo) {
            case 'inventarios':
                $resultado = generarVistaPreviaInventarios($db, $fechaDesde, $fechaHasta, $sucursalIdFiltro, $material);
                break;
            case 'compras':
                $resultado = generarVistaPreviaCompras($db, $fechaDesde, $fechaHasta, $sucursalIdFiltro, $material);
                break;
            case 'ventas':
                $resultado = generarVistaPreviaVentas($db, $fechaDesde, $fechaHasta, $sucursalIdFiltro, $material);
                break;
            case 'productos':
                $resultado = generarVistaPreviaProductos($db, $sucursalIdFiltro, $material);
                break;
            case 'materiales':
                $resultado = generarVistaPreviaMateriales($db);
                break;
            case 'asistencia':
                $resultado = generarVistaPreviaAsistencia($db, $fechaDesde, $fechaHasta, $sucursalIdFiltro, $nombreEmpleado);
                break;
            case 'sucursales':
                $resultado = generarVistaPreviaSucursales($db, $fechaDesde, $fechaHasta, $sucursalIdFiltro);
                break;
            case 'usuarios':
                $resultado = generarVistaPreviaUsuarios($db, $fechaDesde, $fechaHasta, $rolId, $sucursalIdFiltro);
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
function generarVistaPreviaSucursales($db, $fechaDesde, $fechaHasta, $sucursalId = null) {
    // Verificar existencia de tablas
    $tablaSucursalesExiste = false;
    $tablaInventariosExiste = false;
    $tablaVentasExiste = false;
    $tablaComprasExiste = false;
    
    try {
        $db->query("SELECT 1 FROM sucursales LIMIT 1");
        $tablaSucursalesExiste = true;
    } catch (Exception $e) {
        $tablaSucursalesExiste = false;
    }
    
    try {
        $db->query("SELECT 1 FROM inventarios LIMIT 1");
        $tablaInventariosExiste = true;
    } catch (Exception $e) {
        $tablaInventariosExiste = false;
    }
    
    try {
        $db->query("SELECT 1 FROM ventas LIMIT 1");
        $tablaVentasExiste = true;
    } catch (Exception $e) {
        $tablaVentasExiste = false;
    }
    
    try {
        $db->query("SELECT 1 FROM compras LIMIT 1");
        $tablaComprasExiste = true;
    } catch (Exception $e) {
        $tablaComprasExiste = false;
    }
    
    if (!$tablaSucursalesExiste) {
        return ['html' => '<div class="alert alert-warning">La tabla de sucursales no existe.</div>', 'tieneDatos' => false, 'datos' => []];
    }
    
    if ($tablaInventariosExiste && $tablaVentasExiste && $tablaComprasExiste) {
        $sql = "
            SELECT 
                s.*,
                COUNT(DISTINCT i.id) as total_productos,
                COUNT(DISTINCT v.id) as total_ventas,
                COUNT(DISTINCT c.id) as total_compras
            FROM sucursales s
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
    } else {
        $sql = "
            SELECT 
                s.*,
                0 as total_productos,
                0 as total_ventas,
                0 as total_compras
            FROM sucursales s
            WHERE DATE(s.fecha_creacion) BETWEEN ? AND ?";
        
        $params = [$fechaDesde, $fechaHasta];
        
        if ($sucursalId) {
            $sql .= " AND s.id = ?";
            $params[] = $sucursalId;
        }
        
        $sql .= " ORDER BY s.nombre";
        
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
    }
    
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
function generarVistaPreviaUsuarios($db, $fechaDesde, $fechaHasta, $rolId = '', $sucursalId = null) {
    // Verificar existencia de tablas
    $tablaUsuariosExiste = false;
    $tablaRolesExiste = false;
    $tablaVentasExiste = false;
    $tablaComprasExiste = false;
    
    try {
        $db->query("SELECT 1 FROM usuarios LIMIT 1");
        $tablaUsuariosExiste = true;
    } catch (Exception $e) {
        $tablaUsuariosExiste = false;
    }
    
    try {
        $db->query("SELECT 1 FROM roles LIMIT 1");
        $tablaRolesExiste = true;
    } catch (Exception $e) {
        $tablaRolesExiste = false;
    }
    
    try {
        $db->query("SELECT 1 FROM ventas LIMIT 1");
        $tablaVentasExiste = true;
    } catch (Exception $e) {
        $tablaVentasExiste = false;
    }
    
    try {
        $db->query("SELECT 1 FROM compras LIMIT 1");
        $tablaComprasExiste = true;
    } catch (Exception $e) {
        $tablaComprasExiste = false;
    }
    
    if (!$tablaUsuariosExiste) {
        return ['html' => '<div class="alert alert-warning">La tabla de usuarios no existe.</div>', 'tieneDatos' => false, 'datos' => []];
    }
    
    if ($tablaRolesExiste && $tablaVentasExiste && $tablaComprasExiste) {
        $sql = "
            SELECT 
                u.*,
                COALESCE(r.nombre, 'Sin rol') as rol_nombre,
                r.descripcion as rol_descripcion,
                COUNT(DISTINCT v.id) as total_ventas,
                COUNT(DISTINCT c.id) as total_compras
            FROM usuarios u
            LEFT JOIN roles r ON u.rol_id = r.id
            LEFT JOIN ventas v ON u.id = v.creado_por AND DATE(v.fecha_venta) BETWEEN ? AND ?
            LEFT JOIN compras c ON u.id = c.creado_por AND DATE(c.fecha_compra) BETWEEN ? AND ?
            WHERE DATE(u.fecha_creacion) BETWEEN ? AND ?
        ";
    } else {
        $sql = "
            SELECT 
                u.*,
                'Sin rol' as rol_nombre,
                NULL as rol_descripcion,
                0 as total_ventas,
                0 as total_compras
            FROM usuarios u
            WHERE DATE(u.fecha_creacion) BETWEEN ? AND ?
        ";
    }
    
    $params = [$fechaDesde, $fechaHasta, $fechaDesde, $fechaHasta, $fechaDesde, $fechaHasta];
    
    if ($sucursalId) {
        $sql .= " AND u.sucursal_id = ?";
        $params[] = $sucursalId;
    }
    
    if (!empty($rolId)) {
        $sql .= " AND u.rol_id = ?";
        $params[] = $rolId;
    }
    
    $sql .= " GROUP BY u.id ORDER BY rol_nombre, u.nombre";
    
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
function generarVistaPreviaInventarios($db, $fechaDesde, $fechaHasta, $sucursalId = null, $material = '') {
    // Verificar existencia de tablas principales
    try {
        $db->query("SELECT 1 FROM inventarios LIMIT 1");
    } catch (Exception $e) {
        return ['html' => '<div class="alert alert-warning">La tabla de inventarios no existe.</div>', 'tieneDatos' => false, 'datos' => []];
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
function generarVistaPreviaCompras($db, $fechaDesde, $fechaHasta, $sucursalId = null, $material = '') {
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
        // Consulta simplificada
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
function generarVistaPreviaVentas($db, $fechaDesde, $fechaHasta, $sucursalId = null, $material = '') {
    // Verificar si la tabla ventas_detalle existe
    $tablaDetalleExiste = false;
    try {
        $db->query("SELECT 1 FROM ventas_detalle LIMIT 1");
        $tablaDetalleExiste = true;
    } catch (Exception $e) {
        $tablaDetalleExiste = false;
    }
    
    // Verificar si la tabla sucursales existe
    $tablaSucursalesExiste = false;
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
        // Consulta simplificada sin JOINs problemáticos
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
function generarVistaPreviaProductos($db, $sucursalId = null, $material = '') {
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
        INNER JOIN materiales m ON p.material_id = m.id
        LEFT JOIN categorias c ON m.categoria_id = c.id
        INNER JOIN unidades u ON p.unidad_id = u.id";
    
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

    $sql .= " ORDER BY c.nombre, m.nombre, p.nombre";
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    
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

/**
 * Genera vista previa HTML para reporte de asistencia
 */
function generarVistaPreviaAsistencia($db, $fechaDesde, $fechaHasta, $sucursalId = null, $nombreEmpleado = '') {
    $sql = "
        SELECT
            e.id,
            e.nombres,
            e.apellidos,
            e.cedula,
            e.cargo,
            s.nombre as sucursal,
            COUNT(CASE WHEN a.estado = 'asistio' THEN 1 END) as dias_asistidos,
            COUNT(CASE WHEN a.estado = 'falta' THEN 1 END) as dias_falta,
            COUNT(a.id) as total_registros
        FROM empleados e
        LEFT JOIN sucursales s ON e.sucursal_id = s.id
        LEFT JOIN asistencias a ON a.empleado_id = e.id AND a.fecha BETWEEN ? AND ?
        WHERE e.estado = 'ACTIVO'
    ";
    $params = [$fechaDesde, $fechaHasta];

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

    $tieneDatos = count($empleados) > 0;

    $html = '<div class="table-responsive">';
    $html .= '<h4>Reporte de Asistencia</h4>';
    $html .= '<p><strong>Período:</strong> ' . date('d/m/Y', strtotime($fechaDesde)) . ' - ' . date('d/m/Y', strtotime($fechaHasta)) . '</p>';

    if (!$tieneDatos) {
        $html .= '<div class="alert alert-warning">No se encontraron empleados con registros en el período seleccionado.</div>';
        return ['html' => $html, 'tieneDatos' => false, 'datos' => []];
    }

    $html .= '<table class="table table-bordered table-striped">';
    $html .= '<thead><tr>';
    $html .= '<th>Nombres</th>';
    $html .= '<th>Apellidos</th>';
    $html .= '<th>Cédula</th>';
    $html .= '<th>Cargo</th>';
    $html .= '<th>Sucursal</th>';
    $html .= '<th>Días Asistidos</th>';
    $html .= '<th>Días Falta</th>';
    $html .= '<th>Total Registros</th>';
    $html .= '</tr></thead><tbody>';

    foreach ($empleados as $emp) {
        $html .= '<tr>';
        $html .= '<td>' . htmlspecialchars($emp['nombres']) . '</td>';
        $html .= '<td>' . htmlspecialchars($emp['apellidos']) . '</td>';
        $html .= '<td>' . htmlspecialchars($emp['cedula'] ?? '-') . '</td>';
        $html .= '<td>' . htmlspecialchars($emp['cargo'] ?? '-') . '</td>';
        $html .= '<td>' . htmlspecialchars($emp['sucursal'] ?? '-') . '</td>';
        $html .= '<td><span class="badge badge-success">' . $emp['dias_asistidos'] . '</span></td>';
        $html .= '<td><span class="badge badge-danger">' . $emp['dias_falta'] . '</span></td>';
        $html .= '<td>' . $emp['total_registros'] . '</td>';
        $html .= '</tr>';
    }

    $html .= '</tbody></table>';
    $html .= '<p><strong>Total de empleados:</strong> ' . count($empleados) . '</p>';
    $html .= '</div>';

    return ['html' => $html, 'tieneDatos' => true, 'datos' => $empleados];
}

