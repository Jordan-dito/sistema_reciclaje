-- ============================================
-- TABLA UNIDADES Y MODIFICACIÓN DE PRODUCTOS
-- ============================================

-- 1. Crear tabla UNIDADES (independiente)
CREATE TABLE IF NOT EXISTS `unidades` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL COMMENT 'Nombre de la unidad (kilogramos, litros, etc.)',
  `simbolo` varchar(10) DEFAULT NULL COMMENT 'Símbolo de la unidad (kg, L, und, etc.)',
  `tipo` enum('peso','volumen','longitud','cantidad') DEFAULT 'peso' COMMENT 'Tipo de unidad',
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `fecha_creacion` timestamp NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`),
  KEY `idx_tipo` (`tipo`),
  KEY `idx_estado` (`estado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabla de unidades de medida';

-- 2. Insertar unidades básicas
INSERT INTO `unidades` (`id`, `nombre`, `simbolo`, `tipo`, `estado`) VALUES
(1, 'Kilogramos', 'kg', 'peso', 'activo'),
(2, 'Litros', 'L', 'volumen', 'activo'),
(3, 'Unidades', 'und', 'cantidad', 'activo'),
(4, 'Toneladas', 'ton', 'peso', 'activo'),
(5, 'Metros', 'm', 'longitud', 'activo');

-- 3. Modificar tabla PRODUCTOS para usar unidad_id
-- PASO 1: Agregar columna unidad_id temporalmente
ALTER TABLE `productos`
  ADD COLUMN `unidad_id` int(11) DEFAULT NULL COMMENT 'ID de la unidad de medida' AFTER `material_id`;

-- PASO 2: Migrar datos existentes (si hay productos con unidad como ENUM)
-- Actualizar productos según su unidad actual
UPDATE `productos` SET `unidad_id` = 1 WHERE `unidad` = 'kg';
UPDATE `productos` SET `unidad_id` = 2 WHERE `unidad` = 'litros';
UPDATE `productos` SET `unidad_id` = 3 WHERE `unidad` = 'unidades';
UPDATE `productos` SET `unidad_id` = 4 WHERE `unidad` = 'toneladas';
UPDATE `productos` SET `unidad_id` = 5 WHERE `unidad` = 'metros';

-- PASO 3: Hacer unidad_id NOT NULL y agregar restricciones
ALTER TABLE `productos`
  MODIFY COLUMN `unidad_id` int(11) NOT NULL COMMENT 'ID de la unidad de medida',
  DROP COLUMN `unidad`,
  ADD KEY `idx_unidad` (`unidad_id`),
  ADD CONSTRAINT `productos_ibfk_unidad` FOREIGN KEY (`unidad_id`) REFERENCES `unidades` (`id`) ON UPDATE CASCADE;

