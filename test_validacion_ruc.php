<?php
/**
 * Script de prueba para validación de RUC ecuatoriano
 */

require_once __DIR__ . '/config/validaciones.php';

echo "=== PRUEBA DE VALIDACIÓN DE RUC ECUATORIANO ===\n\n";

// Casos de prueba
$casos = [
    // Persona Natural (tercer dígito 0-5)
    ['ruc' => '1234567890001', 'desc' => 'Persona natural válida (cédula válida + 001)', 'esperado' => true],
    ['ruc' => '0918234567001', 'desc' => 'Persona natural provincia válida (09)', 'esperado' => true],
    ['ruc' => '1710034065001', 'desc' => 'Persona natural real válida', 'esperado' => true],
    
    // Sociedad Privada (tercer dígito 9)
    ['ruc' => '1790016919001', 'desc' => 'Sociedad privada válida', 'esperado' => true],
    ['ruc' => '0992398032001', 'desc' => 'Sociedad privada provincia 09', 'esperado' => true],
    
    // Entidad Pública (tercer dígito 6)
    ['ruc' => '1768013940001', 'desc' => 'Entidad pública válida', 'esperado' => true],
    
    // Casos especiales con código 30
    ['ruc' => '3050012345001', 'desc' => 'Extranjero con código 30', 'esperado' => true],
    
    // Casos inválidos
    ['ruc' => '123456789012', 'desc' => 'RUC con 12 dígitos (debe ser 13)', 'esperado' => false],
    ['ruc' => '12345678901234', 'desc' => 'RUC con 14 dígitos (debe ser 13)', 'esperado' => false],
    ['ruc' => '00123456789001', 'desc' => 'Provincia inválida (00)', 'esperado' => false],
    ['ruc' => '25123456789001', 'desc' => 'Provincia inválida (25)', 'esperado' => false],
    ['ruc' => '1712345678001', 'desc' => 'Tercer dígito inválido (7)', 'esperado' => false],
    ['ruc' => '1718345678001', 'desc' => 'Tercer dígito inválido (8)', 'esperado' => false],
    ['ruc' => '1790016919000', 'desc' => 'Establecimiento inválido (000)', 'esperado' => false],
    ['ruc' => '179001691A001', 'desc' => 'RUC con letra (A)', 'esperado' => false],
    ['ruc' => '1790016918001', 'desc' => 'Sociedad privada con dígito verificador incorrecto', 'esperado' => false],
];

$total = count($casos);
$pasados = 0;
$fallados = 0;

foreach ($casos as $i => $caso) {
    $resultado = validarRucEcuatoriano($caso['ruc']);
    $paso = ($resultado['valid'] === $caso['esperado']);
    
    if ($paso) {
        $pasados++;
        $marca = '✓ PASÓ';
    } else {
        $fallados++;
        $marca = '✗ FALLÓ';
    }
    
    echo sprintf(
        "%d. [%s] %s\n   RUC: %s\n   Esperado: %s, Obtenido: %s\n   Mensaje: %s\n\n",
        $i + 1,
        $marca,
        $caso['desc'],
        $caso['ruc'],
        $caso['esperado'] ? 'válido' : 'inválido',
        $resultado['valid'] ? 'válido' : 'inválido',
        $resultado['message']
    );
}

echo "=== RESUMEN ===\n";
echo "Total de pruebas: $total\n";
echo "Pruebas pasadas: $pasados\n";
echo "Pruebas falladas: $fallados\n";
echo "Porcentaje de éxito: " . round(($pasados / $total) * 100, 2) . "%\n";

if ($fallados === 0) {
    echo "\n¡TODAS LAS PRUEBAS PASARON! ✓\n";
} else {
    echo "\nSe encontraron $fallados errores. Revisar casos fallidos.\n";
}
