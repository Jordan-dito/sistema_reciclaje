<?php
/**
 * Endpoint AJAX para validar RUC
 */
header('Content-Type: application/json');

require_once __DIR__ . '/config/validaciones.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ruc = $_POST['ruc'] ?? '';
    
    if (empty($ruc)) {
        echo json_encode([
            'valid' => false,
            'message' => 'Por favor ingrese un RUC'
        ]);
        exit;
    }
    
    $resultado = validarRucEcuatoriano($ruc);
    
    // Agregar información adicional
    if ($resultado['valid']) {
        $tercerDigito = intval(substr($ruc, 2, 1));
        $provincia = intval(substr($ruc, 0, 2));
        $establecimiento = substr($ruc, 10, 3);
        
        $tipo = '';
        if ($tercerDigito >= 0 && $tercerDigito <= 5) {
            $tipo = 'Persona Natural';
        } elseif ($tercerDigito === 6) {
            $tipo = 'Entidad Pública';
        } elseif ($tercerDigito === 9) {
            $tipo = 'Sociedad Privada';
        }
        
        $resultado['message'] = '<br><br>' .
            '<strong>Detalles:</strong><br>' .
            '• Tipo: ' . $tipo . '<br>' .
            '• Provincia: ' . str_pad($provincia, 2, '0', STR_PAD_LEFT) . '<br>' .
            '• Establecimiento: ' . $establecimiento;
    }
    
    echo json_encode($resultado);
} else {
    echo json_encode([
        'valid' => false,
        'message' => 'Método no permitido'
    ]);
}
