-- ============================================
-- INSERT de Datos de Ejemplo
-- Sistema de Gestión de Reciclaje
-- ============================================
-- Este script inserta datos de ejemplo para:
-- - Categorías
-- - Materiales
-- - Productos
-- - Precios
-- - Sucursales
-- - Proveedores
-- ============================================

-- ============================================
-- CATEGORÍAS
-- ============================================

INSERT IGNORE INTO `categorias` (`id`, `nombre`, `descripcion`, `icono`, `estado`) VALUES
(1, 'Plásticos', 'Materiales plásticos reciclables', 'fa-recycle', 'activo'),
(2, 'Metales', 'Metales ferrosos y no ferrosos', 'fa-cog', 'activo'),
(3, 'Papel y Cartón', 'Materiales de papel y cartón', 'fa-file-alt', 'activo'),
(4, 'Vidrio', 'Envases y productos de vidrio', 'fa-wine-glass', 'activo'),
(5, 'Electrónicos', 'Desechos electrónicos', 'fa-microchip', 'activo');

-- ============================================
-- MATERIALES
-- ============================================

INSERT IGNORE INTO `materiales` (`id`, `nombre`, `categoria_id`, `descripcion`, `icono`, `estado`) VALUES
-- Plásticos
(1, 'PET', 1, 'Polietileno Tereftalato - Botellas de plástico', 'fa-bottle-water', 'activo'),
(2, 'PVC', 1, 'Policloruro de Vinilo', 'fa-flask', 'activo'),
(3, 'HDPE', 1, 'Polietileno de Alta Densidad', 'fa-cube', 'activo'),
(4, 'LDPE', 1, 'Polietileno de Baja Densidad', 'fa-box', 'activo'),

-- Metales
(5, 'Cobre', 2, 'Cobre puro y aleaciones', 'fa-coins', 'activo'),
(6, 'Aluminio', 2, 'Aluminio y latas de aluminio', 'fa-circle', 'activo'),
(7, 'Bronce', 2, 'Aleación de cobre y estaño', 'fa-medal', 'activo'),
(8, 'Hierro', 2, 'Chatarra de hierro', 'fa-hammer', 'activo'),

-- Papel y Cartón
(9, 'Papel Blanco', 3, 'Papel de oficina y documentos', 'fa-file', 'activo'),
(10, 'Cartón', 3, 'Cajas y embalajes de cartón', 'fa-archive', 'activo'),
(11, 'Periódico', 3, 'Periódicos y revistas', 'fa-newspaper', 'activo'),

-- Vidrio
(12, 'Vidrio Verde', 4, 'Botellas de vidrio verde', 'fa-wine-bottle', 'activo'),
(13, 'Vidrio Transparente', 4, 'Botellas de vidrio transparente', 'fa-wine-glass-alt', 'activo'),

-- Electrónicos
(14, 'Baterías', 5, 'Baterías usadas', 'fa-battery-half', 'activo'),
(15, 'Cables', 5, 'Cables eléctricos', 'fa-plug', 'activo');

-- ============================================
-- PRODUCTOS
-- ============================================
-- Nota: Las unidades ya están insertadas (id 1-5: kg, L, und, ton, m)

INSERT IGNORE INTO `productos` (`id`, `nombre`, `material_id`, `unidad_id`, `descripcion`, `estado`) VALUES
-- Productos de Plástico
(1, 'Botellas PET', 1, 1, 'Botellas de plástico PET recicladas', 'activo'),
(2, 'Envases PVC', 2, 1, 'Envases y productos de PVC', 'activo'),
(3, 'Contenedores HDPE', 3, 1, 'Contenedores de polietileno de alta densidad', 'activo'),

-- Productos de Metal
(4, 'Cobre Cable', 5, 1, 'Cable de cobre reciclado', 'activo'),
(5, 'Cobre Chatarra', 5, 1, 'Chatarra de cobre', 'activo'),
(6, 'Latas de Aluminio', 6, 1, 'Latas de aluminio comprimidas', 'activo'),
(7, 'Aluminio Chatarra', 6, 1, 'Chatarra de aluminio', 'activo'),
(8, 'Bronce Reciclado', 7, 1, 'Bronce reciclado', 'activo'),
(9, 'Hierro Chatarra', 8, 1, 'Chatarra de hierro', 'activo'),

-- Productos de Papel
(10, 'Papel Reciclado', 9, 1, 'Papel blanco reciclado', 'activo'),
(11, 'Cartón Corrugado', 10, 1, 'Cartón corrugado reciclado', 'activo'),
(12, 'Periódico Reciclado', 11, 1, 'Periódicos y revistas recicladas', 'activo'),

-- Productos de Vidrio
(13, 'Vidrio Verde Reciclado', 12, 1, 'Botellas de vidrio verde', 'activo'),
(14, 'Vidrio Transparente Reciclado', 13, 1, 'Botellas de vidrio transparente', 'activo'),

-- Productos Electrónicos
(15, 'Baterías Usadas', 14, 3, 'Baterías recicladas', 'activo'),
(16, 'Cables Eléctricos', 15, 1, 'Cables eléctricos reciclados', 'activo');

-- ============================================
-- PRECIOS
-- ============================================
-- Precios de compra y venta para cada producto

-- Botellas PET
INSERT IGNORE INTO `precios` (`producto_id`, `precio_unitario`, `tipo_precio`, `estado`) VALUES
(1, 0.50, 'compra', 'activo'),
(1, 0.80, 'venta', 'activo'),

-- Envases PVC
(2, 0.40, 'compra', 'activo'),
(2, 0.65, 'venta', 'activo'),

-- Contenedores HDPE
(3, 0.45, 'compra', 'activo'),
(3, 0.70, 'venta', 'activo'),

-- Cobre Cable
(4, 6.50, 'compra', 'activo'),
(4, 8.00, 'venta', 'activo'),

-- Cobre Chatarra
(5, 5.80, 'compra', 'activo'),
(5, 7.20, 'venta', 'activo'),

-- Latas de Aluminio
(6, 1.20, 'compra', 'activo'),
(6, 1.50, 'venta', 'activo'),

-- Aluminio Chatarra
(7, 1.00, 'compra', 'activo'),
(7, 1.30, 'venta', 'activo'),

-- Bronce Reciclado
(8, 4.50, 'compra', 'activo'),
(8, 5.80, 'venta', 'activo'),

-- Hierro Chatarra
(9, 0.30, 'compra', 'activo'),
(9, 0.45, 'venta', 'activo'),

-- Papel Reciclado
(10, 0.25, 'compra', 'activo'),
(10, 0.40, 'venta', 'activo'),

-- Cartón Corrugado
(11, 0.20, 'compra', 'activo'),
(11, 0.35, 'venta', 'activo'),

-- Periódico Reciclado
(12, 0.15, 'compra', 'activo'),
(12, 0.25, 'venta', 'activo'),

-- Vidrio Verde
(13, 0.10, 'compra', 'activo'),
(13, 0.18, 'venta', 'activo'),

-- Vidrio Transparente
(14, 0.12, 'compra', 'activo'),
(14, 0.20, 'venta', 'activo'),

-- Baterías Usadas
(15, 2.50, 'compra', 'activo'),
(15, 3.50, 'venta', 'activo'),

-- Cables Eléctricos
(16, 3.00, 'compra', 'activo'),
(16, 4.00, 'venta', 'activo');

-- ============================================
-- SUCURSALES
-- ============================================

INSERT IGNORE INTO `sucursales` (`id`, `nombre`, `direccion`, `telefono`, `email`, `estado`) VALUES
(1, 'Sucursal Central', 'Av. Principal 123, Quito', '02-2345678', 'central@reciclaje.com', 'activa'),
(2, 'Sucursal Norte', 'Av. Amazonas 456, Quito', '02-2345679', 'norte@reciclaje.com', 'activa'),
(3, 'Sucursal Sur', 'Av. Maldonado 789, Quito', '02-2345680', 'sur@reciclaje.com', 'activa');

-- ============================================
-- PROVEEDORES
-- ============================================

INSERT IGNORE INTO `proveedores` (`id`, `nombre`, `cedula_ruc`, `tipo_documento`, `direccion`, `telefono`, `email`, `contacto`, `tipo_proveedor`, `materiales_suministra`, `estado`) VALUES
(1, 'Reciclajes del Norte S.A.', '0998765432001', 'ruc', 'Av. 10 de Agosto 234, Quito', '02-2345001', 'contacto@reciclajesnorte.com', 'Juan Pérez', 'recolector', 'PET, Aluminio, Papel', 'activo'),
(2, 'EcoMateriales Ecuador', '0998765432002', 'ruc', 'Av. Colón 567, Quito', '02-2345002', 'ventas@ecomateriales.com', 'María González', 'procesador', 'Cobre, Hierro, Bronce', 'activo'),
(3, 'Reciclaje Express', '0998765432003', 'ruc', 'Av. América 890, Quito', '02-2345003', 'info@reciclajeexpress.com', 'Carlos Ramírez', 'mayorista', 'Cartón, Vidrio, Plásticos', 'activo'),
(4, 'Recolectores Unidos', '0998765432004', 'ruc', 'Calle 24 de Mayo 123, Quito', '02-2345004', 'unidos@recolectores.com', 'Ana Martínez', 'recolector', 'Baterías, Cables, Electrónicos', 'activo');

-- ============================================
-- INVENTARIOS INICIALES (Opcional)
-- ============================================
-- Puedes comentar esta sección si no quieres inventarios iniciales

-- Inventario en Sucursal Central
INSERT IGNORE INTO `inventarios` (`producto_id`, `sucursal_id`, `cantidad`, `stock_minimo`, `stock_maximo`, `estado`) VALUES
(1, 1, 500.00, 100.00, 1000.00, 'disponible'),
(4, 1, 200.00, 50.00, 500.00, 'disponible'),
(6, 1, 300.00, 100.00, 800.00, 'disponible'),
(10, 1, 1000.00, 200.00, 2000.00, 'disponible'),
(11, 1, 800.00, 150.00, 1500.00, 'disponible');

-- Inventario en Sucursal Norte
INSERT IGNORE INTO `inventarios` (`producto_id`, `sucursal_id`, `cantidad`, `stock_minimo`, `stock_maximo`, `estado`) VALUES
(1, 2, 300.00, 100.00, 1000.00, 'disponible'),
(5, 2, 150.00, 50.00, 500.00, 'disponible'),
(7, 2, 250.00, 100.00, 800.00, 'disponible'),
(13, 2, 400.00, 100.00, 1000.00, 'disponible');

-- Inventario en Sucursal Sur
INSERT IGNORE INTO `inventarios` (`producto_id`, `sucursal_id`, `cantidad`, `stock_minimo`, `stock_maximo`, `estado`) VALUES
(2, 3, 200.00, 50.00, 500.00, 'disponible'),
(3, 3, 180.00, 50.00, 500.00, 'disponible'),
(8, 3, 100.00, 30.00, 300.00, 'disponible'),
(14, 3, 350.00, 100.00, 1000.00, 'disponible');

-- ============================================
-- NOTAS:
-- ============================================
-- 1. Todos los INSERT usan IGNORE para evitar errores si los datos ya existen
-- 2. Los precios están en dólares por unidad (kg, L, und, etc.)
-- 3. Los inventarios iniciales son opcionales
-- 4. Puedes ajustar las cantidades y precios según tus necesidades
-- 5. Los IDs se asignan automáticamente si usas AUTO_INCREMENT
-- ============================================

