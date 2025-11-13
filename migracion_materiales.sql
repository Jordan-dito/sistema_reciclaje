-- =====================================================
-- SCRIPT DE MIGRACIÓN: Cambio de ENUM categoria a tabla materiales
-- =====================================================
-- Este script actualiza la base de datos existente para usar la tabla materiales
-- en lugar del campo ENUM categoria
-- 
-- IMPORTANTE: Hacer backup de la base de datos antes de ejecutar este script
-- =====================================================

-- =====================================================
-- PASO 1: Crear tabla materiales si no existe
-- =====================================================
CREATE TABLE IF NOT EXISTS materiales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL COMMENT 'Nombre del material o categoría',
    categoria_padre_id INT NULL COMMENT 'ID de la categoría padre (NULL si es categoría principal)',
    descripcion TEXT COMMENT 'Descripción del material',
    icono VARCHAR(100) COMMENT 'Icono del material (clase de Font Awesome)',
    orden INT DEFAULT 0 COMMENT 'Orden de aparición',
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (categoria_padre_id) REFERENCES materiales(id) ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX idx_categoria_padre (categoria_padre_id),
    INDEX idx_estado (estado),
    INDEX idx_orden (orden),
    INDEX idx_nombre (nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabla de materiales y categorías';

-- =====================================================
-- PASO 2: Insertar materiales (categorías y subcategorías)
-- =====================================================
-- Categorías principales
INSERT IGNORE INTO materiales (id, nombre, categoria_padre_id, descripcion, icono, orden, estado) VALUES
(1, 'Plástico', NULL, 'Materiales plásticos reciclables', 'fas fa-recycle', 1, 'activo'),
(2, 'Metales', NULL, 'Metales reciclables', 'fas fa-cog', 2, 'activo'),
(3, 'Fibroso', NULL, 'Materiales fibrosos (papel, cartón)', 'fas fa-file-alt', 3, 'activo'),
(4, 'Batería', NULL, 'Baterías reciclables', 'fas fa-battery-half', 4, 'activo'),
(5, 'Vidrio', NULL, 'Vidrio reciclable', 'fas fa-wine-bottle', 5, 'activo'),
(6, 'Orgánico', NULL, 'Material orgánico compostable', 'fas fa-leaf', 6, 'activo'),
(7, 'Electrónico', NULL, 'Residuos electrónicos', 'fas fa-microchip', 7, 'activo'),
(8, 'Textil', NULL, 'Ropa y textiles', 'fas fa-tshirt', 8, 'activo'),
(9, 'Otro', NULL, 'Otros materiales', 'fas fa-box', 9, 'activo');

-- Subcategorías de Plástico
INSERT IGNORE INTO materiales (nombre, categoria_padre_id, descripcion, orden, estado) VALUES
('PET', 1, 'Polietileno Tereftalato', 1, 'activo'),
('Hogar', 1, 'Plásticos de uso doméstico', 2, 'activo'),
('Soplado', 1, 'Plásticos soplados', 3, 'activo'),
('PVC', 1, 'Policloruro de Vinilo', 4, 'activo');

-- Subcategorías de Metales
INSERT IGNORE INTO materiales (nombre, categoria_padre_id, descripcion, orden, estado) VALUES
('Chatarra', 2, 'Chatarra metálica', 1, 'activo'),
('Cobre', 2, 'Cobre reciclable', 2, 'activo'),
('Aluminio', 2, 'Aluminio reciclable', 3, 'activo'),
('Perfil', 2, 'Perfiles metálicos', 4, 'activo'),
('Rayador Cobre Aluminio', 2, 'Mezcla de cobre y aluminio rayado', 5, 'activo'),
('Rayador Aluminio', 2, 'Aluminio rayado', 6, 'activo');

-- Subcategorías de Fibroso
INSERT IGNORE INTO materiales (nombre, categoria_padre_id, descripcion, orden, estado) VALUES
('Papel', 3, 'Papel reciclable', 1, 'activo'),
('Cartón', 3, 'Cartón reciclable', 2, 'activo'),
('Periódico', 3, 'Periódicos', 3, 'activo'),
('Químico', 3, 'Papel químico', 4, 'activo'),
('Dúplex', 3, 'Papel dúplex', 5, 'activo');

-- Subcategorías de Batería
INSERT IGNORE INTO materiales (nombre, categoria_padre_id, descripcion, orden, estado) VALUES
('Seca', 4, 'Baterías secas', 1, 'activo'),
('Húmeda', 4, 'Baterías húmedas', 2, 'activo');

-- =====================================================
-- PASO 3: OPCIONAL - Borrar registros de inventario existentes
-- =====================================================
-- Descomenta las siguientes líneas si quieres borrar todos los inventarios:
-- DELETE FROM inventarios;
-- DELETE FROM compras_detalle;
-- DELETE FROM ventas_detalle;
-- DELETE FROM movimientos_inventario;

-- =====================================================
-- PASO 4: Agregar columna material_id a inventarios
-- =====================================================
-- Verificar si la columna ya existe antes de agregarla
SET @col_exists = (
    SELECT COUNT(*) 
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'inventarios' 
    AND COLUMN_NAME = 'material_id'
);

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE inventarios ADD COLUMN material_id INT NULL COMMENT ''ID del material/categoría'' AFTER nombre_producto',
    'SELECT ''Columna material_id ya existe en inventarios'' AS mensaje'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Agregar índice y foreign key
SET @fk_exists = (
    SELECT COUNT(*) 
    FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'inventarios' 
    AND CONSTRAINT_NAME = 'inventarios_ibfk_material'
);

SET @sql = IF(@fk_exists = 0,
    'ALTER TABLE inventarios ADD INDEX idx_material (material_id), ADD CONSTRAINT inventarios_ibfk_material FOREIGN KEY (material_id) REFERENCES materiales(id) ON DELETE RESTRICT ON UPDATE CASCADE',
    'SELECT ''Foreign key ya existe en inventarios'' AS mensaje'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- =====================================================
-- PASO 5: Migrar datos de categoria a material_id en inventarios
-- =====================================================
-- Mapeo de categorías antiguas a nuevos materiales
UPDATE inventarios i
SET i.material_id = CASE 
    WHEN i.categoria = 'papel' THEN (SELECT id FROM materiales WHERE nombre = 'Papel' AND categoria_padre_id = 3 LIMIT 1)
    WHEN i.categoria = 'plastico' THEN (SELECT id FROM materiales WHERE nombre = 'PET' AND categoria_padre_id = 1 LIMIT 1)
    WHEN i.categoria = 'vidrio' THEN (SELECT id FROM materiales WHERE nombre = 'Vidrio' AND categoria_padre_id IS NULL LIMIT 1)
    WHEN i.categoria = 'metal' THEN (SELECT id FROM materiales WHERE nombre = 'Aluminio' AND categoria_padre_id = 2 LIMIT 1)
    WHEN i.categoria = 'organico' THEN (SELECT id FROM materiales WHERE nombre = 'Orgánico' AND categoria_padre_id IS NULL LIMIT 1)
    WHEN i.categoria = 'electronico' THEN (SELECT id FROM materiales WHERE nombre = 'Electrónico' AND categoria_padre_id IS NULL LIMIT 1)
    WHEN i.categoria = 'textil' THEN (SELECT id FROM materiales WHERE nombre = 'Textil' AND categoria_padre_id IS NULL LIMIT 1)
    WHEN i.categoria = 'otro' THEN (SELECT id FROM materiales WHERE nombre = 'Otro' AND categoria_padre_id IS NULL LIMIT 1)
    ELSE (SELECT id FROM materiales WHERE nombre = 'Otro' AND categoria_padre_id IS NULL LIMIT 1)
END
WHERE i.material_id IS NULL;

-- Hacer material_id NOT NULL después de migrar
ALTER TABLE inventarios MODIFY material_id INT NOT NULL COMMENT 'ID del material/categoría';

-- =====================================================
-- PASO 6: Agregar columna material_id a compras_detalle
-- =====================================================
SET @col_exists = (
    SELECT COUNT(*) 
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'compras_detalle' 
    AND COLUMN_NAME = 'material_id'
);

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE compras_detalle ADD COLUMN material_id INT NULL COMMENT ''ID del material/categoría'' AFTER nombre_producto',
    'SELECT ''Columna material_id ya existe en compras_detalle'' AS mensaje'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Migrar datos
UPDATE compras_detalle cd
SET cd.material_id = CASE 
    WHEN cd.categoria = 'papel' THEN (SELECT id FROM materiales WHERE nombre = 'Papel' AND categoria_padre_id = 3 LIMIT 1)
    WHEN cd.categoria = 'plastico' THEN (SELECT id FROM materiales WHERE nombre = 'PET' AND categoria_padre_id = 1 LIMIT 1)
    WHEN cd.categoria = 'vidrio' THEN (SELECT id FROM materiales WHERE nombre = 'Vidrio' AND categoria_padre_id IS NULL LIMIT 1)
    WHEN cd.categoria = 'metal' THEN (SELECT id FROM materiales WHERE nombre = 'Aluminio' AND categoria_padre_id = 2 LIMIT 1)
    WHEN cd.categoria = 'organico' THEN (SELECT id FROM materiales WHERE nombre = 'Orgánico' AND categoria_padre_id IS NULL LIMIT 1)
    WHEN cd.categoria = 'electronico' THEN (SELECT id FROM materiales WHERE nombre = 'Electrónico' AND categoria_padre_id IS NULL LIMIT 1)
    WHEN cd.categoria = 'textil' THEN (SELECT id FROM materiales WHERE nombre = 'Textil' AND categoria_padre_id IS NULL LIMIT 1)
    WHEN cd.categoria = 'otro' THEN (SELECT id FROM materiales WHERE nombre = 'Otro' AND categoria_padre_id IS NULL LIMIT 1)
    ELSE (SELECT id FROM materiales WHERE nombre = 'Otro' AND categoria_padre_id IS NULL LIMIT 1)
END
WHERE cd.material_id IS NULL;

-- Agregar foreign key
SET @fk_exists = (
    SELECT COUNT(*) 
    FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'compras_detalle' 
    AND CONSTRAINT_NAME = 'compras_detalle_ibfk_material'
);

SET @sql = IF(@fk_exists = 0,
    'ALTER TABLE compras_detalle ADD INDEX idx_material (material_id), ADD CONSTRAINT compras_detalle_ibfk_material FOREIGN KEY (material_id) REFERENCES materiales(id) ON DELETE RESTRICT ON UPDATE CASCADE, MODIFY material_id INT NOT NULL',
    'SELECT ''Foreign key ya existe en compras_detalle'' AS mensaje'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- =====================================================
-- PASO 7: Agregar columna material_id a ventas_detalle
-- =====================================================
SET @col_exists = (
    SELECT COUNT(*) 
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'ventas_detalle' 
    AND COLUMN_NAME = 'material_id'
);

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE ventas_detalle ADD COLUMN material_id INT NULL COMMENT ''ID del material/categoría'' AFTER nombre_producto',
    'SELECT ''Columna material_id ya existe en ventas_detalle'' AS mensaje'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Migrar datos
UPDATE ventas_detalle vd
SET vd.material_id = CASE 
    WHEN vd.categoria = 'papel' THEN (SELECT id FROM materiales WHERE nombre = 'Papel' AND categoria_padre_id = 3 LIMIT 1)
    WHEN vd.categoria = 'plastico' THEN (SELECT id FROM materiales WHERE nombre = 'PET' AND categoria_padre_id = 1 LIMIT 1)
    WHEN vd.categoria = 'vidrio' THEN (SELECT id FROM materiales WHERE nombre = 'Vidrio' AND categoria_padre_id IS NULL LIMIT 1)
    WHEN vd.categoria = 'metal' THEN (SELECT id FROM materiales WHERE nombre = 'Aluminio' AND categoria_padre_id = 2 LIMIT 1)
    WHEN vd.categoria = 'organico' THEN (SELECT id FROM materiales WHERE nombre = 'Orgánico' AND categoria_padre_id IS NULL LIMIT 1)
    WHEN vd.categoria = 'electronico' THEN (SELECT id FROM materiales WHERE nombre = 'Electrónico' AND categoria_padre_id IS NULL LIMIT 1)
    WHEN vd.categoria = 'textil' THEN (SELECT id FROM materiales WHERE nombre = 'Textil' AND categoria_padre_id IS NULL LIMIT 1)
    WHEN vd.categoria = 'otro' THEN (SELECT id FROM materiales WHERE nombre = 'Otro' AND categoria_padre_id IS NULL LIMIT 1)
    ELSE (SELECT id FROM materiales WHERE nombre = 'Otro' AND categoria_padre_id IS NULL LIMIT 1)
END
WHERE vd.material_id IS NULL;

-- Agregar foreign key
SET @fk_exists = (
    SELECT COUNT(*) 
    FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'ventas_detalle' 
    AND CONSTRAINT_NAME = 'ventas_detalle_ibfk_material'
);

SET @sql = IF(@fk_exists = 0,
    'ALTER TABLE ventas_detalle ADD INDEX idx_material (material_id), ADD CONSTRAINT ventas_detalle_ibfk_material FOREIGN KEY (material_id) REFERENCES materiales(id) ON DELETE RESTRICT ON UPDATE CASCADE, MODIFY material_id INT NOT NULL',
    'SELECT ''Foreign key ya existe en ventas_detalle'' AS mensaje'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- =====================================================
-- PASO 8: Eliminar columna categoria de las tablas
-- =====================================================
-- Eliminar índice de categoria si existe
ALTER TABLE inventarios DROP INDEX IF EXISTS idx_categoria;
ALTER TABLE compras_detalle DROP INDEX IF EXISTS idx_categoria;
ALTER TABLE ventas_detalle DROP INDEX IF EXISTS idx_categoria;

-- Eliminar columna categoria
SET @col_exists = (
    SELECT COUNT(*) 
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'inventarios' 
    AND COLUMN_NAME = 'categoria'
);

SET @sql = IF(@col_exists > 0,
    'ALTER TABLE inventarios DROP COLUMN categoria',
    'SELECT ''Columna categoria no existe en inventarios'' AS mensaje'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @col_exists = (
    SELECT COUNT(*) 
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'compras_detalle' 
    AND COLUMN_NAME = 'categoria'
);

SET @sql = IF(@col_exists > 0,
    'ALTER TABLE compras_detalle DROP COLUMN categoria',
    'SELECT ''Columna categoria no existe en compras_detalle'' AS mensaje'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @col_exists = (
    SELECT COUNT(*) 
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'ventas_detalle' 
    AND COLUMN_NAME = 'categoria'
);

SET @sql = IF(@col_exists > 0,
    'ALTER TABLE ventas_detalle DROP COLUMN categoria',
    'SELECT ''Columna categoria no existe en ventas_detalle'' AS mensaje'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- =====================================================
-- PASO 9: Actualizar vistas
-- =====================================================
DROP VIEW IF EXISTS v_inventarios_completos;
CREATE OR REPLACE VIEW v_inventarios_completos AS
SELECT 
    i.id,
    i.nombre_producto,
    i.material_id,
    m.nombre AS material_nombre,
    m.categoria_padre_id,
    mp.nombre AS categoria_padre_nombre,
    i.cantidad,
    i.unidad,
    i.precio_unitario,
    i.stock_minimo,
    i.stock_maximo,
    i.estado AS estado_inventario,
    s.nombre AS sucursal_nombre,
    s.direccion AS sucursal_direccion,
    s.responsable_id,
    ur.nombre AS responsable_nombre,
    uc.nombre AS creado_por_nombre,
    i.fecha_creacion,
    i.fecha_actualizacion
FROM inventarios i
INNER JOIN sucursales s ON i.sucursal_id = s.id
INNER JOIN materiales m ON i.material_id = m.id
LEFT JOIN materiales mp ON m.categoria_padre_id = mp.id
LEFT JOIN usuarios ur ON s.responsable_id = ur.id
LEFT JOIN usuarios uc ON i.creado_por = uc.id;

DROP VIEW IF EXISTS v_inventario_por_categoria;
CREATE OR REPLACE VIEW v_inventario_por_categoria AS
SELECT 
    m.id AS material_id,
    m.nombre AS material_nombre,
    mp.nombre AS categoria_padre_nombre,
    s.nombre AS sucursal_nombre,
    COUNT(i.id) AS total_items,
    SUM(i.cantidad) AS cantidad_total,
    i.unidad,
    SUM(i.cantidad * i.precio_unitario) AS valor_total
FROM inventarios i
INNER JOIN sucursales s ON i.sucursal_id = s.id
INNER JOIN materiales m ON i.material_id = m.id
LEFT JOIN materiales mp ON m.categoria_padre_id = mp.id
WHERE i.estado = 'disponible' AND s.estado = 'activa'
GROUP BY m.id, m.nombre, mp.nombre, s.id, s.nombre, i.unidad;

-- =====================================================
-- PASO 10: Actualizar procedimientos almacenados
-- =====================================================
DROP PROCEDURE IF EXISTS sp_inventario_por_categoria;
DROP PROCEDURE IF EXISTS sp_inventario_por_material;

DELIMITER $$
CREATE PROCEDURE sp_inventario_por_material(IN p_material_id INT)
BEGIN
    SELECT 
        i.nombre_producto,
        m.nombre AS material_nombre,
        mp.nombre AS categoria_padre,
        s.nombre AS sucursal,
        i.cantidad,
        i.unidad,
        i.precio_unitario,
        (i.cantidad * i.precio_unitario) AS valor_total
    FROM inventarios i
    INNER JOIN sucursales s ON i.sucursal_id = s.id
    INNER JOIN materiales m ON i.material_id = m.id
    LEFT JOIN materiales mp ON m.categoria_padre_id = mp.id
    WHERE i.material_id = p_material_id AND i.estado = 'disponible' AND s.estado = 'activa'
    ORDER BY s.nombre, i.nombre_producto;
END$$
DELIMITER ;

-- =====================================================
-- FIN DE LA MIGRACIÓN
-- =====================================================
SELECT 'Migración completada exitosamente' AS resultado;

