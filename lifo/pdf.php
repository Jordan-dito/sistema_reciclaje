<?php
/**
 * Generador de PDF para Flujo LIFO
 * Sistema de Gestión de Reciclaje
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/api.php';

try {
    $auth = new Auth();
    if (!$auth->isAuthenticated()) {
        header('Location: ../index.php');
        exit;
    }
    
    $db = getDB();
    
    $fechaDesde = $_GET['fecha_desde'] ?? '';
    $fechaHasta = $_GET['fecha_hasta'] ?? '';
    $sucursalId = $_GET['sucursal_id'] ?? '';
    $material = $_GET['material'] ?? '';
    
    if (empty($fechaDesde) || empty($fechaHasta)) {
        throw new Exception('Las fechas son obligatorias');
    }
    
    $resultado = generarFlujoLIFO($db, $fechaDesde, $fechaHasta, $sucursalId, $material);
    
    if (!$resultado['success']) {
        echo '<html><body><h1>Error</h1><p>' . htmlspecialchars($resultado['message'] ?? 'Error desconocido') . '</p></body></html>';
        exit;
    }
    
    // Generar PDF
    $titulo = 'Reporte de Flujo LIFO de Inventario';
    $periodo = date('d/m/Y', strtotime($fechaDesde)) . ' al ' . date('d/m/Y', strtotime($fechaHasta));
    
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title><?php echo $titulo; ?></title>
        <style>
            body {
                font-family: Arial, sans-serif;
                font-size: 12px;
                margin: 20px;
            }
            h1 {
                color: #1572e8;
                border-bottom: 2px solid #1572e8;
                padding-bottom: 10px;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin: 20px 0;
            }
            th, td {
                border: 1px solid #ddd;
                padding: 8px;
                text-align: left;
            }
            th {
                background-color: #f8f9fa;
                font-weight: bold;
            }
            .table-success {
                background-color: #d4edda;
            }
            .table-danger {
                background-color: #f8d7da;
            }
            .text-right {
                text-align: right;
            }
            .alert {
                padding: 15px;
                margin: 20px 0;
                border: 1px solid transparent;
                border-radius: 4px;
            }
            .alert-info {
                background-color: #d1ecf1;
                border-color: #bee5eb;
                color: #0c5460;
            }
            .resumen {
                display: flex;
                justify-content: space-around;
                margin: 20px 0;
            }
            .resumen-item {
                text-align: center;
                padding: 15px;
                border: 2px solid #ddd;
                border-radius: 5px;
                flex: 1;
                margin: 0 10px;
            }
            .producto-header {
                background-color: #f8f9fa;
                padding: 10px;
                border-left: 4px solid #1572e8;
                margin: 20px 0 10px 0;
                font-weight: bold;
            }
        </style>
    </head>
    <body>
        <h1><?php echo htmlspecialchars($titulo); ?></h1>
        <p><strong>Período:</strong> <?php echo htmlspecialchars($periodo); ?></p>
        <p><strong>Fecha de generación:</strong> <?php echo date('d/m/Y H:i:s'); ?></p>
        
        <div class="alert alert-info">
            <strong>Método LIFO:</strong> Last In, First Out (Último en Entrar, Primero en Salir). 
            Este reporte muestra cómo los productos más recientes en inventario son los primeros en venderse.
        </div>
        
        <?php echo $resultado['html']; ?>
        
        <script>
            window.onload = function() {
                window.print();
            }
        </script>
    </body>
    </html>
    <?php
    
} catch (Exception $e) {
    echo '<html><body><h1>Error</h1><p>' . htmlspecialchars($e->getMessage()) . '</p></body></html>';
}
?>
