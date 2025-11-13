-- =====================================================
-- MIGRACIÓN SIMPLE: Cambio de ENUM categoria a tabla materiales
-- =====================================================
-- IMPORTANTE: Hacer backup antes de ejecutar
-- =====================================================

-- PASO 1: Crear tabla materiales
CREATE TABLE IF NOT EXISTS materiales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    categoria_padre_id INT NULL,
    descripcion TEXT,
    icono VARCHAR(100),
    orden INT DEFAULT 0,
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (categoria_padre_id) REFERENCES materiales(id) ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX idx_categoria_padre (categoria_padre_id),
    INDEX idx_estado (estado),
    INDEX idx_orden (orden),
    INDEX idx_nombre (nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- PASO 2: Insertar materiales
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

INSERT IGNORE INTO materiales (nombre, categoria_padre_id, descripcion, orden, estado) VALUES
('PET', 1, 'Polietileno Tereftalato', 1, 'activo'),
('Hogar', 1, 'Plásticos de uso doméstico', 2, 'activo'),
('Soplado', 1, 'Plásticos soplados', 3, 'activo'),
('PVC', 1, 'Policloruro de Vinilo', 4, 'activo'),
('Chatarra', 2, 'Chatarra metálica', 1, 'activo'),
('Cobre', 2, 'Cobre reciclable', 2, 'activo'),
('Aluminio', 2, 'Aluminio reciclable', 3, 'activo'),
('Perfil', 2, 'Perfiles metálicos', 4, 'activo'),
('Rayador Cobre Aluminio', 2, 'Mezcla de cobre y aluminio rayado', 5, 'activo'),
('Rayador Aluminio', 2, 'Aluminio rayado', 6, 'activo'),
('Papel', 3, 'Papel reciclable', 1, 'activo'),
('Cartón', 3, 'Cartón reciclable', 2, 'activo'),
('Periódico', 3, 'Periódicos', 3, 'activo'),
('Químico', 3, 'Papel químico', 4, 'activo'),
('Dúplex', 3, 'Papel dúplex', 5, 'activo'),
('Seca', 4, 'Baterías secas', 1, 'activo'),
('Húmeda', 4, 'Baterías húmedas', 2, 'activo');

-- =====================================================
-- OPCIONAL: Borrar registros de inventario
-- =====================================================
-- Descomenta las siguientes líneas si quieres borrar todo:
-- DELETE FROM movimientos_inventario;
-- DELETE FROM ventas_detalle;
-- DELETE FROM compras_detalle;
-- DELETE FROM inventarios;

-- =====================================================
-- PASO 3: Agregar columna material_id a inventarios
-- =====================================================
ALTER TABLE inventarios 
ADD COLUMN material_id INT NULL AFTER nombre_producto;

-- Migrar datos de categoria a material_id
UPDATE inventarios i
SET i.material_id = CASE 
    WHEN i.categoria = 'papel' THEN (SELECT id FROM materiales WHERE nombre = 'Papel' AND categoria_padre_id = 3 LIMIT 1)
    WHEN i.categoria = 'plastico' THEN (SELECT id FROM materiales WHERE nombre = 'PET' AND categoria_padre_id = 1 LIMIT 1)
    WHEN i.categoria = 'vidrio' THEN 5
    WHEN i.categoria = 'metal' THEN (SELECT id FROM materiales WHERE nombre = 'Aluminio' AND categoria_padre_id = 2 LIMIT 1)
    WHEN i.categoria = 'organico' THEN 6
    WHEN i.categoria = 'electronico' THEN 7
    WHEN i.categoria = 'textil' THEN 8
    WHEN i.categoria = 'otro' THEN 9
    ELSE 9
END
WHERE i.material_id IS NULL;

-- Hacer NOT NULL y agregar foreign key
ALTER TABLE inventarios 
MODIFY material_id INT NOT NULL,
ADD INDEX idx_material (material_id),
ADD CONSTRAINT inventarios_ibfk_material 
FOREIGN KEY (material_id) REFERENCES materiales(id) ON DELETE RESTRICT ON UPDATE CASCADE;

-- Eliminar columna categoria
ALTER TABLE inventarios DROP COLUMN categoria;

-- =====================================================
-- PASO 4: Agregar columna material_id a compras_detalle
-- =====================================================
ALTER TABLE compras_detalle 
ADD COLUMN material_id INT NULL AFTER nombre_producto;

UPDATE compras_detalle cd
SET cd.material_id = CASE 
    WHEN cd.categoria = 'papel' THEN (SELECT id FROM materiales WHERE nombre = 'Papel' AND categoria_padre_id = 3 LIMIT 1)
    WHEN cd.categoria = 'plastico' THEN (SELECT id FROM materiales WHERE nombre = 'PET' AND categoria_padre_id = 1 LIMIT 1)
    WHEN cd.categoria = 'vidrio' THEN 5
    WHEN cd.categoria = 'metal' THEN (SELECT id FROM materiales WHERE nombre = 'Aluminio' AND categoria_padre_id = 2 LIMIT 1)
    WHEN cd.categoria = 'organico' THEN 6
    WHEN cd.categoria = 'electronico' THEN 7
    WHEN cd.categoria = 'textil' THEN 8
    WHEN cd.categoria = 'otro' THEN 9
    ELSE 9
END
WHERE cd.material_id IS NULL;

ALTER TABLE compras_detalle 
MODIFY material_id INT NOT NULL,
ADD INDEX idx_material (material_id),
ADD CONSTRAINT compras_detalle_ibfk_material 
FOREIGN KEY (material_id) REFERENCES materiales(id) ON DELETE RESTRICT ON UPDATE CASCADE,
DROP COLUMN categoria;

-- =====================================================
-- PASO 5: Agregar columna material_id a ventas_detalle
-- =====================================================
ALTER TABLE ventas_detalle 
ADD COLUMN material_id INT NULL AFTER nombre_producto;

UPDATE ventas_detalle vd
SET vd.material_id = CASE 
    WHEN vd.categoria = 'papel' THEN (SELECT id FROM materiales WHERE nombre = 'Papel' AND categoria_padre_id = 3 LIMIT 1)
    WHEN vd.categoria = 'plastico' THEN (SELECT id FROM materiales WHERE nombre = 'PET' AND categoria_padre_id = 1 LIMIT 1)
    WHEN vd.categoria = 'vidrio' THEN 5
    WHEN vd.categoria = 'metal' THEN (SELECT id FROM materiales WHERE nombre = 'Aluminio' AND categoria_padre_id = 2 LIMIT 1)
    WHEN vd.categoria = 'organico' THEN 6
    WHEN vd.categoria = 'electronico' THEN 7
    WHEN vd.categoria = 'textil' THEN 8
    WHEN vd.categoria = 'otro' THEN 9
    ELSE 9
END
WHERE vd.material_id IS NULL;

ALTER TABLE ventas_detalle 
MODIFY material_id INT NOT NULL,
ADD INDEX idx_material (material_id),
ADD CONSTRAINT ventas_detalle_ibfk_material 
FOREIGN KEY (material_id) REFERENCES materiales(id) ON DELETE RESTRICT ON UPDATE CASCADE,
DROP COLUMN categoria;

-- =====================================================
-- PASO 6: Actualizar vistas
-- =====================================================
DROP VIEW IF EXISTS v_inventarios_completos;
CREATE OR REPLACE VIEW v_inventarios_completos AS
SELECT 
    i.id, i.nombre_producto, i.material_id,
    m.nombre AS material_nombre, m.categoria_padre_id,
    mp.nombre AS categoria_padre_nombre,
    i.cantidad, i.unidad, i.precio_unitario,
    i.stock_minimo, i.stock_maximo, i.estado AS estado_inventario,
    s.nombre AS sucursal_nombre, s.direccion AS sucursal_direccion,
    s.responsable_id, ur.nombre AS responsable_nombre,
    uc.nombre AS creado_por_nombre,
    i.fecha_creacion, i.fecha_actualizacion
FROM inventarios i
INNER JOIN sucursales s ON i.sucursal_id = s.id
INNER JOIN materiales m ON i.material_id = m.id
LEFT JOIN materiales mp ON m.categoria_padre_id = mp.id
LEFT JOIN usuarios ur ON s.responsable_id = ur.id
LEFT JOIN usuarios uc ON i.creado_por = uc.id;

DROP VIEW IF EXISTS v_inventario_por_categoria;
CREATE OR REPLACE VIEW v_inventario_por_categoria AS
SELECT 
    m.id AS material_id, m.nombre AS material_nombre,
    mp.nombre AS categoria_padre_nombre, s.nombre AS sucursal_nombre,
    COUNT(i.id) AS total_items, SUM(i.cantidad) AS cantidad_total,
    i.unidad, SUM(i.cantidad * i.precio_unitario) AS valor_total
FROM inventarios i
INNER JOIN sucursales s ON i.sucursal_id = s.id
INNER JOIN materiales m ON i.material_id = m.id
LEFT JOIN materiales mp ON m.categoria_padre_id = mp.id
WHERE i.estado = 'disponible' AND s.estado = 'activa'
GROUP BY m.id, m.nombre, mp.nombre, s.id, s.nombre, i.unidad;

-- =====================================================
-- PASO 7: Actualizar procedimientos almacenados
-- =====================================================
DROP PROCEDURE IF EXISTS sp_inventario_por_categoria;
DROP PROCEDURE IF EXISTS sp_inventario_por_material;

DELIMITER $$
CREATE PROCEDURE sp_inventario_por_material(IN p_material_id INT)
BEGIN
    SELECT 
        i.nombre_producto, m.nombre AS material_nombre,
        mp.nombre AS categoria_padre, s.nombre AS sucursal,
        i.cantidad, i.unidad, i.precio_unitario,
        (i.cantidad * i.precio_unitario) AS valor_total
    FROM inventarios i
    INNER JOIN sucursales s ON i.sucursal_id = s.id
    INNER JOIN materiales m ON i.material_id = m.id
    LEFT JOIN materiales mp ON m.categoria_padre_id = mp.id
    WHERE i.material_id = p_material_id 
    AND i.estado = 'disponible' AND s.estado = 'activa'
    ORDER BY s.nombre, i.nombre_producto;
END$$
DELIMITER ;

SELECT 'Migración completada exitosamente' AS resultado;

