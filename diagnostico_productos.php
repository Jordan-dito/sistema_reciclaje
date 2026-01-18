<?php
/**
 * Diagnóstico de productos - Ver códigos actuales
 */

require_once __DIR__ . '/config/database.php';

try {
    $db = getDB();
    
    echo "=== DIAGNÓSTICO DE PRODUCTOS ===\n\n";
    
    // Listar todos los productos
    $stmt = $db->query("
        SELECT id, nombre, material_id, estado, fecha_creacion 
        FROM productos 
        ORDER BY id ASC
    ");
    $productos = $stmt->fetchAll();
    
    echo "Total de productos: " . count($productos) . "\n\n";
    
    echo "Lista de productos:\n";
    echo str_repeat("-", 80) . "\n";
    printf("%-5s | %-20s | %-12s | %-10s\n", "ID", "CÓDIGO/NOMBRE", "MATERIAL ID", "ESTADO");
    echo str_repeat("-", 80) . "\n";
    
    $codigosNumericos = [];
    $codigosNoNumericos = [];
    
    foreach ($productos as $prod) {
        printf("%-5s | %-20s | %-12s | %-10s\n", 
            $prod['id'], 
            $prod['nombre'], 
            $prod['material_id'], 
            $prod['estado']
        );
        
        // Verificar si es código numérico de 4 dígitos
        if (preg_match('/^[0-9]{4}$/', $prod['nombre'])) {
            $codigosNumericos[] = intval($prod['nombre']);
        } else {
            $codigosNoNumericos[] = $prod['nombre'];
        }
    }
    
    echo str_repeat("-", 80) . "\n\n";
    
    echo "=== ANÁLISIS ===\n\n";
    
    echo "Códigos numéricos (formato 0001):\n";
    if (!empty($codigosNumericos)) {
        sort($codigosNumericos);
        foreach ($codigosNumericos as $cod) {
            echo "  - " . str_pad($cod, 4, '0', STR_PAD_LEFT) . "\n";
        }
        echo "\nÚltimo código numérico: " . str_pad(max($codigosNumericos), 4, '0', STR_PAD_LEFT) . "\n";
        echo "Próximo código a generar: " . str_pad(max($codigosNumericos) + 1, 4, '0', STR_PAD_LEFT) . "\n";
    } else {
        echo "  No hay códigos numéricos. Próximo código: 0001\n";
    }
    
    echo "\nCódigos NO numéricos (nombres descriptivos):\n";
    if (!empty($codigosNoNumericos)) {
        foreach ($codigosNoNumericos as $cod) {
            echo "  - $cod\n";
        }
        echo "\nTOTAL: " . count($codigosNoNumericos) . " productos con nombres descriptivos\n";
        echo "⚠ ADVERTENCIA: Estos productos tienen nombres en lugar de códigos numéricos.\n";
    } else {
        echo "  Todos los productos tienen códigos numéricos ✓\n";
    }
    
    echo "\n=== RECOMENDACIONES ===\n\n";
    
    if (!empty($codigosNoNumericos)) {
        echo "Se encontraron productos con nombres descriptivos.\n";
        echo "Opciones:\n";
        echo "1. Actualizar estos productos con códigos numéricos secuenciales\n";
        echo "2. Eliminar estos productos si son de prueba\n";
        echo "3. Dejarlos como están (no afectará la generación de nuevos códigos)\n\n";
        
        echo "Para actualizar automáticamente, ejecutar:\n";
        echo "UPDATE productos SET nombre = LPAD(id, 4, '0') WHERE nombre NOT REGEXP '^[0-9]{4}$';\n";
    } else {
        echo "✓ Todos los productos tienen códigos numéricos correctos.\n";
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
?>
