<?php
/**
 * Script para normalizar códigos de productos
 * Convierte todos los nombres a formato 0001, 0002, etc.
 */

require_once __DIR__ . '/config/database.php';

try {
    $db = getDB();
    
    echo "=== NORMALIZACIÓN DE CÓDIGOS DE PRODUCTOS ===\n\n";
    
    // Obtener todos los productos ordenados por ID
    $stmt = $db->query("SELECT id, nombre FROM productos ORDER BY id ASC");
    $productos = $stmt->fetchAll();
    
    echo "Total de productos a procesar: " . count($productos) . "\n\n";
    
    $db->beginTransaction();
    
    $contador = 1;
    $actualizados = 0;
    
    foreach ($productos as $prod) {
        $codigoActual = $prod['nombre'];
        $codigoNuevo = str_pad($contador, 4, '0', STR_PAD_LEFT);
        
        // Verificar si necesita actualización
        if ($codigoActual !== $codigoNuevo) {
            echo "ID {$prod['id']}: '{$codigoActual}' → '{$codigoNuevo}'\n";
            
            $stmt = $db->prepare("UPDATE productos SET nombre = ? WHERE id = ?");
            $stmt->execute([$codigoNuevo, $prod['id']]);
            
            $actualizados++;
        } else {
            echo "ID {$prod['id']}: '{$codigoActual}' (sin cambios)\n";
        }
        
        $contador++;
    }
    
    echo "\n=== RESUMEN ===\n";
    echo "Productos actualizados: $actualizados\n";
    echo "Productos sin cambios: " . (count($productos) - $actualizados) . "\n";
    echo "\n¿Confirmar cambios? (s/n): ";
    
    $handle = fopen("php://stdin", "r");
    $confirmacion = trim(fgets($handle));
    fclose($handle);
    
    if (strtolower($confirmacion) === 's') {
        $db->commit();
        echo "\n✓ Cambios guardados exitosamente.\n";
        echo "Próximo código a generar: " . str_pad($contador, 4, '0', STR_PAD_LEFT) . "\n";
    } else {
        $db->rollBack();
        echo "\n✗ Cambios descartados.\n";
    }
    
} catch (Exception $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    echo "ERROR: " . $e->getMessage() . "\n";
}
?>
