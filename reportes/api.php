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
        
        if (empty($tipo) || empty($fechaDesde) || empty($fechaHasta)) {
            throw new Exception('Parámetros incompletos');
        }
        
        // Validar fechas
        $fechaDesdeObj = new DateTime($fechaDesde);
        $fechaHastaObj = new DateTime($fechaHasta);
        
        if ($fechaDesdeObj > $fechaHastaObj) {
            throw new Exception('La fecha desde debe ser menor o igual a la fecha hasta');
        }
        
        $html = '';
        
        if ($tipo === 'sucursales') {
            $html = generarVistaPreviaSucursales($db, $fechaDesde, $fechaHasta);
        } elseif ($tipo === 'usuarios') {
            $html = generarVistaPreviaUsuarios($db, $fechaDesde, $fechaHasta, $rolId);
        } else {
            throw new Exception('Tipo de reporte no válido');
        }
        
        ob_end_clean();
        echo json_encode([
            'success' => true,
            'html' => $html
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
            u.nombre as responsable_nombre,
            COUNT(DISTINCT i.id) as total_productos,
            COUNT(DISTINCT v.id) as total_ventas,
            COUNT(DISTINCT c.id) as total_compras
        FROM sucursales s
        LEFT JOIN usuarios u ON s.responsable_id = u.id
        LEFT JOIN inventarios i ON s.id = i.sucursal_id
        LEFT JOIN ventas v ON s.id = v.sucursal_id AND DATE(v.fecha_venta) BETWEEN ? AND ?
        LEFT JOIN compras c ON s.id = c.sucursal_id AND DATE(c.fecha_compra) BETWEEN ? AND ?
        WHERE DATE(s.fecha_creacion) BETWEEN ? AND ?
        GROUP BY s.id
        ORDER BY s.nombre
    ");
    
    $stmt->execute([$fechaDesde, $fechaHasta, $fechaDesde, $fechaHasta, $fechaDesde, $fechaHasta]);
    $sucursales = $stmt->fetchAll();
    
    $html = '<div class="table-responsive">';
    $html .= '<h4>Reporte de Sucursales</h4>';
    $html .= '<p><strong>Período:</strong> ' . date('d/m/Y', strtotime($fechaDesde)) . ' - ' . date('d/m/Y', strtotime($fechaHasta)) . '</p>';
    $html .= '<table class="table table-bordered table-striped">';
    $html .= '<thead><tr>';
    $html .= '<th>ID</th>';
    $html .= '<th>Nombre</th>';
    $html .= '<th>Dirección</th>';
    $html .= '<th>Teléfono</th>';
    $html .= '<th>Email</th>';
    $html .= '<th>Responsable</th>';
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
        $html .= '<td>' . htmlspecialchars($sucursal['responsable_nombre'] ?? '-') . '</td>';
        $html .= '<td><span class="badge badge-' . ($sucursal['estado'] === 'activa' ? 'success' : 'danger') . '">' . ucfirst($sucursal['estado']) . '</span></td>';
        $html .= '<td>' . $sucursal['total_productos'] . '</td>';
        $html .= '<td>' . $sucursal['total_ventas'] . '</td>';
        $html .= '<td>' . $sucursal['total_compras'] . '</td>';
        $html .= '</tr>';
    }
    
    $html .= '</tbody></table>';
    $html .= '<p><strong>Total de sucursales:</strong> ' . count($sucursales) . '</p>';
    $html .= '</div>';
    
    return $html;
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
    
    return $html;
}

