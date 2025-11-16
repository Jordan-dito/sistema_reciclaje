-- ============================================
-- Script para actualizar la estructura de la base de datos
-- Sistema de Gestión de Reciclaje
-- ============================================
-- Este script agrega el campo 'permisos' a la tabla roles
-- si no existe, para mantener compatibilidad con el código PHP
-- ============================================

-- Verificar y agregar campo permisos a la tabla roles si no existe
SET @dbname = DATABASE();
SET @tablename = 'roles';
SET @columnname = 'permisos';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  'SELECT 1',
  CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN ', @columnname, ' longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT ''Permisos del rol en formato JSON'' CHECK (json_valid(`', @columnname, '`))')
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Actualizar roles con permisos por defecto
UPDATE `roles` 
SET `permisos` = '{"usuarios": ["crear", "leer", "actualizar", "eliminar"], "sucursales": ["crear", "leer", "actualizar", "eliminar"], "inventarios": ["crear", "leer", "actualizar", "eliminar"], "reportes": ["ver", "exportar"], "configuracion": ["modificar"], "categorias": ["crear", "leer", "actualizar", "eliminar"], "materiales": ["crear", "leer", "actualizar", "eliminar"], "productos": ["crear", "leer", "actualizar", "eliminar"], "compras": ["crear", "leer", "actualizar", "eliminar"], "ventas": ["crear", "leer", "actualizar", "eliminar"]}'
WHERE `id` = 1 AND (`permisos` IS NULL OR `permisos` = '');

UPDATE `roles` 
SET `permisos` = '{"usuarios": ["leer", "actualizar"], "sucursales": ["crear", "leer", "actualizar", "eliminar"], "inventarios": ["crear", "leer", "actualizar", "eliminar"], "reportes": ["ver", "exportar"], "configuracion": ["leer"], "categorias": ["leer"], "materiales": ["leer"], "productos": ["leer"], "compras": ["crear", "leer", "actualizar"], "ventas": ["crear", "leer", "actualizar"]}'
WHERE `id` = 2 AND (`permisos` IS NULL OR `permisos` = '');

-- ============================================
-- NOTAS:
-- ============================================
-- 1. Este script agrega el campo 'permisos' a la tabla roles si no existe
-- 2. Actualiza los roles existentes con permisos por defecto
-- 3. El campo permisos es opcional - el código PHP maneja el caso cuando es NULL
-- 4. Los administradores tienen todos los permisos automáticamente en el código
-- ============================================

