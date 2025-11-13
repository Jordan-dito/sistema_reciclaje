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
    
    // Validar que hay datos antes de generar PDF
    $tieneDatos = false;
    if ($tipo === 'sucursales') {
        $stmt = $db->prepare("SELECT COUNT(*) as total FROM sucursales WHERE DATE(fecha_creacion) BETWEEN ? AND ?");
        $stmt->execute([$fechaDesde, $fechaHasta]);
        $result = $stmt->fetch();
        $tieneDatos = $result['total'] > 0;
    } elseif ($tipo === 'usuarios') {
        $sql = "SELECT COUNT(*) as total FROM usuarios WHERE DATE(fecha_creacion) BETWEEN ? AND ?";
        $params = [$fechaDesde, $fechaHasta];
        if (!empty($rolId)) {
            $sql .= " AND rol_id = ?";
            $params[] = $rolId;
        }
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        $tieneDatos = $result['total'] > 0;
    } else {
        throw new Exception('Tipo de reporte no válido');
    }
    
    if (!$tieneDatos) {
        echo '<html><body><h1>Sin datos</h1><p>No hay datos para generar el reporte en el período seleccionado.</p></body></html>';
        exit;
    }
    
    // Generar contenido según el tipo
    if ($tipo === 'sucursales') {
        generarPDFSucursales($db, $fechaDesde, $fechaHasta);
    } elseif ($tipo === 'usuarios') {
        generarPDFUsuarios($db, $fechaDesde, $fechaHasta, $rolId);
    }
    
} catch (Exception $e) {
    echo '<html><body><h1>Error</h1><p>' . htmlspecialchars($e->getMessage()) . '</p></body></html>';
}

/**
 * Genera PDF para reporte de sucursales
 */
function generarPDFSucursales($db, $fechaDesde, $fechaHasta) {
    $stmt = $db->prepare("
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
        WHERE DATE(s.fecha_creacion) BETWEEN ? AND ?
        GROUP BY s.id
        ORDER BY s.nombre
    ");
    
    $stmt->execute([$fechaDesde, $fechaHasta, $fechaDesde, $fechaHasta, $fechaDesde, $fechaHasta]);
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
function generarPDFUsuarios($db, $fechaDesde, $fechaHasta, $rolId = '') {
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

