-- ============================================
-- ELIMINAR CAMPO ORDEN DE TODAS LAS TABLAS
-- ============================================

-- Eliminar campo orden de CATEGORIAS
-- Si el campo no existe, puedes ignorar el error
ALTER TABLE `categorias`
  DROP COLUMN `orden`,
  DROP KEY `idx_orden`;

-- Eliminar campo orden de MATERIALES
-- Si el campo no existe, puedes ignorar el error
ALTER TABLE `materiales`
  DROP COLUMN `orden`,
  DROP KEY `idx_orden`;

-- Eliminar campo orden de UNIDADES
-- Si el campo no existe, puedes ignorar el error
ALTER TABLE `unidades`
  DROP COLUMN `orden`,
  DROP KEY `idx_orden`;

