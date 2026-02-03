-- Script para reorganizar módulos
-- 1. Mover "Gestión Operativa" después de "Gestión de Personal"
-- Los submódulos se configuran en el archivo includes/sidebar.php

-- Cambiar el orden de Gestión Operativa
-- De orden 0 a orden 6 (después de Gestión de Personal que tiene orden 5)
UPDATE modulos 
SET orden = 6, fecha_actualizacion = NOW()
WHERE nombre = 'Gestión Operativa';

-- Verificar el resultado
SELECT id, nombre, orden, estado 
FROM modulos 
WHERE estado = 'activo' 
ORDER BY orden ASC;
