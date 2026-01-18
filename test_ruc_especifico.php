<?php
/**
 * Prueba específica para el RUC de la imagen
 */

require_once __DIR__ . '/config/validaciones.php';

echo "=== PRUEBA DEL RUC: 0930792767001 ===\n\n";

$ruc = '0930792767001';

echo "Analizando RUC: $ruc\n\n";

// Desglose del RUC
$provincia = substr($ruc, 0, 2);
$tercerDigito = substr($ruc, 2, 1);
$cedula = substr($ruc, 0, 10);
$establecimiento = substr($ruc, 10, 3);

echo "Desglose:\n";
echo "- Provincia: $provincia (Guayas = 09)\n";
echo "- Tercer dígito: $tercerDigito (Persona Natural: 0-5)\n";
echo "- Cédula parte: $cedula\n";
echo "- Establecimiento: $establecimiento\n\n";

// Validar primero la cédula
echo "1. Validando la cédula (primeros 10 dígitos):\n";
$resultadoCedula = validarCedulaEcuatoriana($cedula);
echo "   Resultado: " . ($resultadoCedula['valid'] ? 'VÁLIDA ✓' : 'INVÁLIDA ✗') . "\n";
echo "   Mensaje: " . $resultadoCedula['message'] . "\n\n";

// Validar el RUC completo
echo "2. Validando RUC completo:\n";
$resultadoRuc = validarRucEcuatoriano($ruc);
echo "   Resultado: " . ($resultadoRuc['valid'] ? 'VÁLIDO ✓' : 'INVÁLIDO ✗') . "\n";
echo "   Mensaje: " . $resultadoRuc['message'] . "\n\n";

if ($resultadoRuc['valid']) {
    echo "✓ EL RUC ES VÁLIDO - La validación está funcionando correctamente!\n";
} else {
    echo "✗ EL RUC ES INVÁLIDO - Necesita corrección\n";
    echo "\nDetalles del error:\n";
    echo $resultadoRuc['message'] . "\n";
}

// Validar usando la función principal
echo "\n3. Validando con validarDocumentoEcuatoriano:\n";
$resultadoDoc = validarDocumentoEcuatoriano($ruc, 'ruc');
echo "   Resultado: " . ($resultadoDoc['valid'] ? 'VÁLIDO ✓' : 'INVÁLIDO ✗') . "\n";
echo "   Mensaje: " . $resultadoDoc['message'] . "\n";
