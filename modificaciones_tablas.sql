-- ============================================
-- MODIFICACIONES A LA BASE DE DATOS
-- Tabla productos independiente y actualización automática de inventario
-- 
-- IMPORTANTE: Este script:
-- 1. Elimina triggers y tablas nuevas si existen
-- 2. Crea tablas nuevas: categorias, productos, precios
-- 3. Modifica tablas existentes: materiales, inventarios, compras_detalle, ventas_detalle
-- 4. Crea triggers para actualización automática de inventario y cálculo de subtotales
--
-- Ejecutar en una base de datos con las tablas originales ya creadas
-- ============================================

-- ============================================
-- ELIMINAR TRIGGERS Y TABLAS ANTERIORES
-- ============================================

-- Eliminar triggers existentes
DROP TRIGGER IF EXISTS `trg_actualizar_estado_inventario`;
DROP TRIGGER IF EXISTS `trg_registrar_movimiento_inventario`;
DROP TRIGGER IF EXISTS `trg_actualizar_inventario_compra`;
DROP TRIGGER IF EXISTS `trg_actualizar_inventario_venta`;
DROP TRIGGER IF EXISTS `trg_calcular_subtotal_compra`;
DROP TRIGGER IF EXISTS `trg_calcular_subtotal_compra_update`;
DROP TRIGGER IF EXISTS `trg_calcular_subtotal_venta`;
DROP TRIGGER IF EXISTS `trg_calcular_subtotal_venta_update`;

-- Eliminar foreign keys que pueden impedir eliminar tablas
-- Verificar y eliminar foreign keys solo si existen

-- Procedimiento para eliminar foreign key de materiales si existe
SET @fk_name = '';
SELECT CONSTRAINT_NAME INTO @fk_name
FROM information_schema.TABLE_CONSTRAINTS 
WHERE CONSTRAINT_SCHEMA = DATABASE() 
  AND TABLE_NAME = 'materiales' 
  AND CONSTRAINT_NAME = 'materiales_ibfk_categoria'
LIMIT 1;

SET @sql = IF(@fk_name != '', 
    CONCAT('ALTER TABLE `materiales` DROP FOREIGN KEY `', @fk_name, '`'), 
    'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @fk_name = '';
SELECT CONSTRAINT_NAME INTO @fk_name
FROM information_schema.TABLE_CONSTRAINTS 
WHERE CONSTRAINT_SCHEMA = DATABASE() 
  AND TABLE_NAME = 'materiales' 
  AND CONSTRAINT_NAME = 'materiales_ibfk_1'
LIMIT 1;

SET @sql = IF(@fk_name != '', 
    CONCAT('ALTER TABLE `materiales` DROP FOREIGN KEY `', @fk_name, '`'), 
    'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Eliminar tablas nuevas si existen (se recrearán después)
-- IMPORTANTE: Orden inverso a las dependencias
DROP TABLE IF EXISTS `precios`;
DROP TABLE IF EXISTS `productos`;
DROP TABLE IF EXISTS `categorias`;

-- ============================================
-- CREAR NUEVAS TABLAS
-- ============================================

-- 1. Crear tabla PRODUCTOS (solo datos generales del producto, sin precio)
CREATE TABLE `productos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) NOT NULL COMMENT 'Nombre del producto',
  `material_id` int(11) NOT NULL COMMENT 'ID del material/categoría',
  `unidad` enum('kg','litros','unidades','toneladas','metros') NOT NULL DEFAULT 'kg' COMMENT 'Unidad de medida',
  `descripcion` text DEFAULT NULL COMMENT 'Descripción adicional',
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `fecha_creacion` timestamp NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_material` (`material_id`),
  KEY `idx_estado` (`estado`),
  KEY `idx_nombre` (`nombre`),
  CONSTRAINT `productos_ibfk_material` FOREIGN KEY (`material_id`) REFERENCES `materiales` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabla de productos del sistema';

-- 2. Crear tabla CATEGORIAS (categorías relacionadas con materiales)
CREATE TABLE `categorias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) NOT NULL COMMENT 'Nombre de la categoría',
  `descripcion` text DEFAULT NULL COMMENT 'Descripción de la categoría',
  `icono` varchar(100) DEFAULT NULL COMMENT 'Icono de la categoría',
  `orden` int(11) DEFAULT 0 COMMENT 'Orden de visualización',
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `fecha_creacion` timestamp NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_estado` (`estado`),
  KEY `idx_orden` (`orden`),
  KEY `idx_nombre` (`nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabla de categorías';

-- 3. Modificar tabla MATERIALES para relacionarla con categorias
-- Cambiar categoria_padre_id por categoria_id
ALTER TABLE `materiales`
  DROP FOREIGN KEY `materiales_ibfk_1`,
  DROP COLUMN `categoria_padre_id`,
  ADD COLUMN `categoria_id` int(11) DEFAULT NULL COMMENT 'ID de la categoría' AFTER `nombre`,
  ADD KEY `idx_categoria` (`categoria_id`),
  ADD CONSTRAINT `materiales_ibfk_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- 4. Crear tabla PRECIOS (precios independientes y automatizados, sin fechas)
CREATE TABLE `precios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `producto_id` int(11) NOT NULL COMMENT 'ID del producto',
  `precio_unitario` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Precio por unidad',
  `tipo_precio` enum('compra','venta','referencia') DEFAULT 'venta' COMMENT 'Tipo de precio',
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `fecha_creacion` timestamp NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_producto` (`producto_id`),
  KEY `idx_tipo_precio` (`tipo_precio`),
  KEY `idx_estado` (`estado`),
  CONSTRAINT `precios_ibfk_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabla de precios de productos';

-- ============================================
-- MODIFICAR TABLAS EXISTENTES
-- ============================================

-- 5. Modificar tabla INVENTARIOS (mantiene stock_minimo y stock_maximo porque son por sucursal)
-- PASO 1: Agregar columna producto_id temporalmente como NULL para poder migrar datos
ALTER TABLE `inventarios`
  ADD COLUMN `producto_id` int(11) DEFAULT NULL COMMENT 'ID del producto (temporal para migración)' AFTER `id`;

-- PASO 2: Migrar datos existentes de inventarios a productos y actualizar producto_id
-- Crear productos únicos desde inventarios (solo si no existen)
INSERT IGNORE INTO productos (nombre, material_id, unidad, descripcion)
SELECT DISTINCT 
    nombre_producto, 
    material_id, 
    unidad, 
    descripcion
FROM inventarios
WHERE nombre_producto IS NOT NULL;

-- Actualizar producto_id en inventarios haciendo match con productos
UPDATE inventarios i
INNER JOIN productos p ON 
    i.nombre_producto = p.nombre 
    AND i.material_id = p.material_id 
    AND i.unidad = p.unidad
SET i.producto_id = p.id
WHERE i.producto_id IS NULL;

-- PASO 3: Verificar que todos los inventarios tengan producto_id antes de hacer NOT NULL
-- Si hay inventarios sin producto_id, se eliminan (o puedes ajustarlos manualmente)
DELETE FROM inventarios WHERE producto_id IS NULL;

-- Ahora hacer producto_id NOT NULL y agregar restricciones
ALTER TABLE `inventarios`
  MODIFY COLUMN `producto_id` int(11) NOT NULL COMMENT 'ID del producto',
  ADD KEY `idx_producto` (`producto_id`),
  ADD UNIQUE KEY `uk_producto_sucursal` (`producto_id`,`sucursal_id`),
  ADD CONSTRAINT `inventarios_ibfk_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- PASO 4: Eliminar columnas que ahora están en productos
ALTER TABLE `inventarios`
  DROP COLUMN `nombre_producto`,
  DROP COLUMN `material_id`,
  DROP COLUMN `precio_unitario`,
  DROP COLUMN `unidad`,
  DROP COLUMN `descripcion`,
  DROP COLUMN `creado_por`,
  DROP FOREIGN KEY `inventarios_ibfk_material`;

-- 6. Modificar tabla COMPRAS_DETALLE
-- Eliminar columnas innecesarias
ALTER TABLE `compras_detalle`
  DROP COLUMN `inventario_id`,
  DROP COLUMN `nombre_producto`,
  DROP COLUMN `unidad`,
  DROP FOREIGN KEY `compras_detalle_ibfk_2`,
  DROP FOREIGN KEY `compras_detalle_ibfk_material`;

-- Agregar producto_id y precio_id (precio se obtiene automáticamente de la tabla precios)
ALTER TABLE `compras_detalle`
  ADD COLUMN `producto_id` int(11) NOT NULL COMMENT 'ID del producto' AFTER `compra_id`,
  ADD COLUMN `precio_id` int(11) NOT NULL COMMENT 'ID del precio usado (tipo compra)' AFTER `producto_id`,
  DROP COLUMN `precio_unitario`,
  ADD KEY `idx_producto` (`producto_id`),
  ADD KEY `idx_precio` (`precio_id`),
  ADD CONSTRAINT `compras_detalle_ibfk_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `compras_detalle_ibfk_precio` FOREIGN KEY (`precio_id`) REFERENCES `precios` (`id`) ON UPDATE CASCADE;

-- 7. Modificar tabla VENTAS_DETALLE
-- Agregar producto_id y precio_id (precio se obtiene automáticamente de la tabla precios)
ALTER TABLE `ventas_detalle`
  ADD COLUMN `producto_id` int(11) NOT NULL COMMENT 'ID del producto' AFTER `inventario_id`,
  ADD COLUMN `precio_id` int(11) NOT NULL COMMENT 'ID del precio usado (tipo venta)' AFTER `producto_id`,
  DROP COLUMN `precio_unitario`,
  ADD KEY `idx_producto` (`producto_id`),
  ADD KEY `idx_precio` (`precio_id`),
  ADD CONSTRAINT `ventas_detalle_ibfk_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `ventas_detalle_ibfk_precio` FOREIGN KEY (`precio_id`) REFERENCES `precios` (`id`) ON UPDATE CASCADE;

-- ============================================
-- TRIGGERS PARA ACTUALIZAR INVENTARIO AUTOMÁTICAMENTE
-- ============================================

-- Trigger: Actualizar estado de inventario basado en stock mínimo (que está en la misma tabla inventarios)
DELIMITER $$
CREATE TRIGGER `trg_actualizar_estado_inventario` BEFORE UPDATE ON `inventarios` FOR EACH ROW BEGIN
    -- Actualizar estado basado en cantidad y stock mínimo (que está en la misma tabla)
    IF NEW.cantidad < NEW.stock_minimo AND NEW.stock_minimo > 0 THEN
        SET NEW.estado = 'agotado';
    ELSEIF NEW.cantidad >= NEW.stock_minimo THEN
        SET NEW.estado = 'disponible';
    END IF;
END$$
DELIMITER ;

-- Trigger: Registrar movimiento de inventario
DELIMITER $$
CREATE TRIGGER `trg_registrar_movimiento_inventario` AFTER UPDATE ON `inventarios` FOR EACH ROW BEGIN
    DECLARE v_tipo_movimiento VARCHAR(20);
    DECLARE v_cantidad_movida DECIMAL(10,2);
    
    -- Solo registrar si cambió la cantidad
    IF OLD.cantidad != NEW.cantidad THEN
        -- Determinar el tipo de movimiento
        IF NEW.cantidad > OLD.cantidad THEN
            SET v_tipo_movimiento = 'entrada';
            SET v_cantidad_movida = NEW.cantidad - OLD.cantidad;
        ELSEIF NEW.cantidad < OLD.cantidad THEN
            SET v_tipo_movimiento = 'salida';
            SET v_cantidad_movida = OLD.cantidad - NEW.cantidad;
        ELSE
            SET v_tipo_movimiento = 'ajuste';
            SET v_cantidad_movida = ABS(NEW.cantidad - OLD.cantidad);
        END IF;
        
        -- Insertar en el historial de movimientos
        INSERT INTO movimientos_inventario (
            inventario_id,
            tipo_movimiento,
            cantidad,
            cantidad_anterior,
            cantidad_nueva,
            usuario_id,
            motivo
        ) VALUES (
            NEW.id,
            v_tipo_movimiento,
            v_cantidad_movida,
            OLD.cantidad,
            NEW.cantidad,
            NULL,
            CONCAT('Actualización automática de inventario')
        );
    END IF;
END$$
DELIMITER ;

-- Trigger: SUMAR al inventario cuando se completa una COMPRA
DELIMITER $$
CREATE TRIGGER `trg_actualizar_inventario_compra` AFTER UPDATE ON `compras` FOR EACH ROW BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE v_producto_id INT;
    DECLARE v_cantidad DECIMAL(10,2);
    DECLARE v_sucursal_id INT;
    DECLARE v_inventario_id INT;
    DECLARE v_cantidad_actual DECIMAL(10,2);
    
    DECLARE cur_detalle CURSOR FOR 
        SELECT producto_id, cantidad 
        FROM compras_detalle 
        WHERE compra_id = NEW.id;
    
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
    
    -- Solo procesar si la compra cambió a estado 'completada'
    IF NEW.estado = 'completada' AND (OLD.estado IS NULL OR OLD.estado != 'completada') THEN
        SET v_sucursal_id = NEW.sucursal_id;
        
        OPEN cur_detalle;
        
        read_loop: LOOP
            FETCH cur_detalle INTO v_producto_id, v_cantidad;
            IF done THEN
                LEAVE read_loop;
            END IF;
            
            -- Buscar si ya existe inventario para este producto en esta sucursal
            SELECT id, cantidad INTO v_inventario_id, v_cantidad_actual
            FROM inventarios
            WHERE producto_id = v_producto_id AND sucursal_id = v_sucursal_id
            LIMIT 1;
            
            IF v_inventario_id IS NOT NULL THEN
                -- Actualizar inventario existente (SUMAR)
                UPDATE inventarios
                SET cantidad = cantidad + v_cantidad,
                    fecha_actualizacion = CURRENT_TIMESTAMP
                WHERE id = v_inventario_id;
            ELSE
                -- Crear nuevo registro de inventario
                INSERT INTO inventarios (producto_id, sucursal_id, cantidad, estado)
                VALUES (v_producto_id, v_sucursal_id, v_cantidad, 'disponible');
            END IF;
        END LOOP;
        
        CLOSE cur_detalle;
    END IF;
END$$
DELIMITER ;

-- Trigger: RESTAR del inventario cuando se completa una VENTA
DELIMITER $$
CREATE TRIGGER `trg_actualizar_inventario_venta` AFTER UPDATE ON `ventas` FOR EACH ROW BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE v_inventario_id INT;
    DECLARE v_cantidad DECIMAL(10,2);
    
    DECLARE cur_detalle CURSOR FOR 
        SELECT inventario_id, cantidad 
        FROM ventas_detalle 
        WHERE venta_id = NEW.id;
    
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
    
    -- Solo procesar si la venta cambió a estado 'completada'
    IF NEW.estado = 'completada' AND (OLD.estado IS NULL OR OLD.estado != 'completada') THEN
        OPEN cur_detalle;
        
        read_loop: LOOP
            FETCH cur_detalle INTO v_inventario_id, v_cantidad;
            IF done THEN
                LEAVE read_loop;
            END IF;
            
            -- Restar cantidad del inventario
            UPDATE inventarios
            SET cantidad = cantidad - v_cantidad,
                fecha_actualizacion = CURRENT_TIMESTAMP
            WHERE id = v_inventario_id;
        END LOOP;
        
        CLOSE cur_detalle;
    END IF;
END$$
DELIMITER ;

-- ============================================
-- TRIGGERS PARA CALCULAR SUBTOTAL AUTOMÁTICAMENTE
-- ============================================

-- Trigger: Calcular subtotal automáticamente en COMPRAS_DETALLE usando precio desde tabla precios
DELIMITER $$
CREATE TRIGGER `trg_calcular_subtotal_compra` BEFORE INSERT ON `compras_detalle` FOR EACH ROW BEGIN
    DECLARE v_precio_unitario DECIMAL(10,2);
    
    -- Obtener el precio desde la tabla precios
    SELECT precio_unitario INTO v_precio_unitario
    FROM precios
    WHERE id = NEW.precio_id AND estado = 'activo';
    
    -- Calcular subtotal automáticamente
    SET NEW.subtotal = NEW.cantidad * IFNULL(v_precio_unitario, 0);
END$$
DELIMITER ;

DELIMITER $$
CREATE TRIGGER `trg_calcular_subtotal_compra_update` BEFORE UPDATE ON `compras_detalle` FOR EACH ROW BEGIN
    DECLARE v_precio_unitario DECIMAL(10,2);
    
    -- Obtener el precio desde la tabla precios
    SELECT precio_unitario INTO v_precio_unitario
    FROM precios
    WHERE id = NEW.precio_id AND estado = 'activo';
    
    -- Calcular subtotal automáticamente
    SET NEW.subtotal = NEW.cantidad * IFNULL(v_precio_unitario, 0);
END$$
DELIMITER ;

-- Trigger: Calcular subtotal automáticamente en VENTAS_DETALLE usando precio desde tabla precios
DELIMITER $$
CREATE TRIGGER `trg_calcular_subtotal_venta` BEFORE INSERT ON `ventas_detalle` FOR EACH ROW BEGIN
    DECLARE v_precio_unitario DECIMAL(10,2);
    
    -- Obtener el precio desde la tabla precios
    SELECT precio_unitario INTO v_precio_unitario
    FROM precios
    WHERE id = NEW.precio_id AND estado = 'activo';
    
    -- Calcular subtotal automáticamente
    SET NEW.subtotal = NEW.cantidad * IFNULL(v_precio_unitario, 0);
END$$
DELIMITER ;

DELIMITER $$
CREATE TRIGGER `trg_calcular_subtotal_venta_update` BEFORE UPDATE ON `ventas_detalle` FOR EACH ROW BEGIN
    DECLARE v_precio_unitario DECIMAL(10,2);
    
    -- Obtener el precio desde la tabla precios
    SELECT precio_unitario INTO v_precio_unitario
    FROM precios
    WHERE id = NEW.precio_id AND estado = 'activo';
    
    -- Calcular subtotal automáticamente
    SET NEW.subtotal = NEW.cantidad * IFNULL(v_precio_unitario, 0);
END$$
DELIMITER ;

