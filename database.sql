-- =====================================================
-- Base de Datos: Sistema de Gestión de Reciclaje
-- Tesis de Grado
-- =====================================================
-- INSTRUCCIONES PARA HOSTING COMPARTIDO:
-- 
-- 1. Crea la base de datos desde el panel de control de tu hosting
--    (no desde phpMyAdmin, ya que no tienes permisos)
-- 
-- 2. Una vez creada, accede a phpMyAdmin
-- 
-- 3. Selecciona la base de datos que creaste (haz clic en ella en el menú lateral)
-- 
-- 4. Luego importa este archivo SQL
-- 
-- IMPORTANTE: El nombre de la base de datos debe coincidir con DB_NAME en tu .env
-- 
-- =====================================================
-- TABLA: roles
-- =====================================================
CREATE TABLE IF NOT EXISTS roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE COMMENT 'Nombre del rol (administrador, usuario)',
    descripcion TEXT COMMENT 'Descripción del rol',
    permisos JSON COMMENT 'Permisos del rol en formato JSON',
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabla de roles del sistema';

-- =====================================================
-- TABLA: modulos
-- =====================================================
CREATE TABLE IF NOT EXISTS modulos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL COMMENT 'Nombre del módulo',
    descripcion TEXT COMMENT 'Descripción del módulo',
    ruta VARCHAR(200) COMMENT 'Ruta del módulo en el sistema',
    icono VARCHAR(100) COMMENT 'Icono del módulo (clase de Font Awesome)',
    orden INT DEFAULT 0 COMMENT 'Orden de aparición en el menú',
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_estado (estado),
    INDEX idx_orden (orden)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabla de módulos del sistema';

-- =====================================================
-- TABLA INTERMEDIA: usuario_modulos (Permisos de módulos por rol)
-- =====================================================
CREATE TABLE IF NOT EXISTS rol_modulos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    rol_id INT NOT NULL COMMENT 'ID del rol',
    modulo_id INT NOT NULL COMMENT 'ID del módulo',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (rol_id) REFERENCES roles(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (modulo_id) REFERENCES modulos(id) ON DELETE CASCADE ON UPDATE CASCADE,
    UNIQUE KEY uk_rol_modulo (rol_id, modulo_id),
    INDEX idx_rol (rol_id),
    INDEX idx_modulo (modulo_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Relación entre roles y módulos accesibles';

-- =====================================================
-- TABLA: usuarios
-- =====================================================
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL COMMENT 'Nombre completo del usuario',
    email VARCHAR(150) NOT NULL UNIQUE COMMENT 'Correo electrónico',
    cedula VARCHAR(20) NOT NULL UNIQUE COMMENT 'Cédula de identidad',
    password VARCHAR(255) NOT NULL COMMENT 'Contraseña hasheada',
    telefono VARCHAR(20) COMMENT 'Teléfono de contacto',
    rol_id INT NOT NULL COMMENT 'ID del rol del usuario',
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (rol_id) REFERENCES roles(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    INDEX idx_email (email),
    INDEX idx_cedula (cedula),
    INDEX idx_rol (rol_id),
    INDEX idx_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabla de usuarios del sistema';

-- =====================================================
-- TABLA: sucursales
-- =====================================================
CREATE TABLE IF NOT EXISTS sucursales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL COMMENT 'Nombre de la sucursal',
    direccion TEXT COMMENT 'Dirección completa',
    telefono VARCHAR(20) COMMENT 'Teléfono de contacto',
    email VARCHAR(150) COMMENT 'Correo electrónico',
    responsable_id INT COMMENT 'ID del usuario responsable',
    estado ENUM('activa', 'inactiva') DEFAULT 'activa',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (responsable_id) REFERENCES usuarios(id) ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX idx_estado (estado),
    INDEX idx_responsable (responsable_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabla de sucursales';

-- =====================================================
-- TABLA: materiales (Categorías y subcategorías de materiales)
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
-- TABLA: inventarios
-- =====================================================
CREATE TABLE IF NOT EXISTS inventarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sucursal_id INT NOT NULL COMMENT 'ID de la sucursal',
    nombre_producto VARCHAR(150) NOT NULL COMMENT 'Nombre del producto/material',
    material_id INT NOT NULL COMMENT 'ID del material/categoría',
    cantidad DECIMAL(10,2) NOT NULL DEFAULT 0 COMMENT 'Cantidad disponible',
    unidad ENUM('kg', 'litros', 'unidades', 'toneladas', 'metros') NOT NULL DEFAULT 'kg' COMMENT 'Unidad de medida',
    precio_unitario DECIMAL(10,2) DEFAULT 0 COMMENT 'Precio por unidad',
    stock_minimo DECIMAL(10,2) DEFAULT 0 COMMENT 'Stock mínimo para alerta',
    stock_maximo DECIMAL(10,2) DEFAULT 0 COMMENT 'Stock máximo recomendado',
    descripcion TEXT COMMENT 'Descripción adicional',
    estado ENUM('disponible', 'agotado', 'reservado', 'inactivo') DEFAULT 'disponible',
    creado_por INT COMMENT 'ID del usuario que creó el registro',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (sucursal_id) REFERENCES sucursales(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (material_id) REFERENCES materiales(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (creado_por) REFERENCES usuarios(id) ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX idx_sucursal (sucursal_id),
    INDEX idx_material (material_id),
    INDEX idx_estado (estado),
    INDEX idx_creado_por (creado_por),
    INDEX idx_fecha_actualizacion (fecha_actualizacion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabla de inventarios por sucursal';

-- =====================================================
-- TABLA: movimientos_inventario (Historial de movimientos)
-- =====================================================
CREATE TABLE IF NOT EXISTS movimientos_inventario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    inventario_id INT NOT NULL COMMENT 'ID del inventario afectado',
    tipo_movimiento ENUM('entrada', 'salida', 'ajuste') NOT NULL COMMENT 'Tipo de movimiento',
    cantidad DECIMAL(10,2) NOT NULL COMMENT 'Cantidad movida',
    cantidad_anterior DECIMAL(10,2) COMMENT 'Cantidad antes del movimiento',
    cantidad_nueva DECIMAL(10,2) COMMENT 'Cantidad después del movimiento',
    motivo TEXT COMMENT 'Motivo del movimiento',
    usuario_id INT COMMENT 'Usuario que realizó el movimiento',
    fecha_movimiento TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (inventario_id) REFERENCES inventarios(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX idx_inventario (inventario_id),
    INDEX idx_usuario (usuario_id),
    INDEX idx_tipo (tipo_movimiento),
    INDEX idx_fecha (fecha_movimiento)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Historial de movimientos de inventario';

-- =====================================================
-- TABLA: clientes
-- =====================================================
CREATE TABLE IF NOT EXISTS clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL COMMENT 'Nombre o razón social del cliente',
    cedula_ruc VARCHAR(20) COMMENT 'Cédula o RUC del cliente',
    tipo_documento ENUM('cedula', 'ruc', 'pasaporte', 'otro') DEFAULT 'cedula' COMMENT 'Tipo de documento',
    direccion TEXT COMMENT 'Dirección completa',
    telefono VARCHAR(20) COMMENT 'Teléfono de contacto',
    email VARCHAR(150) COMMENT 'Correo electrónico',
    contacto VARCHAR(100) COMMENT 'Nombre de persona de contacto',
    tipo_cliente ENUM('minorista', 'mayorista', 'empresa', 'institucion') DEFAULT 'minorista' COMMENT 'Tipo de cliente',
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    notas TEXT COMMENT 'Notas adicionales sobre el cliente',
    creado_por INT COMMENT 'ID del usuario que creó el registro',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (creado_por) REFERENCES usuarios(id) ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX idx_nombre (nombre),
    INDEX idx_cedula_ruc (cedula_ruc),
    INDEX idx_estado (estado),
    INDEX idx_tipo_cliente (tipo_cliente),
    INDEX idx_creado_por (creado_por)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabla de clientes del sistema';

-- =====================================================
-- TABLA: proveedores
-- =====================================================
CREATE TABLE IF NOT EXISTS proveedores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL COMMENT 'Nombre o razón social del proveedor',
    cedula_ruc VARCHAR(20) COMMENT 'Cédula o RUC del proveedor',
    tipo_documento ENUM('cedula', 'ruc', 'pasaporte', 'otro') DEFAULT 'ruc' COMMENT 'Tipo de documento',
    direccion TEXT COMMENT 'Dirección completa',
    telefono VARCHAR(20) COMMENT 'Teléfono de contacto',
    email VARCHAR(150) COMMENT 'Correo electrónico',
    contacto VARCHAR(100) COMMENT 'Nombre de persona de contacto',
    tipo_proveedor ENUM('recolector', 'procesador', 'mayorista', 'otro') DEFAULT 'recolector' COMMENT 'Tipo de proveedor',
    materiales_suministra TEXT COMMENT 'Materiales que suministra el proveedor',
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    notas TEXT COMMENT 'Notas adicionales sobre el proveedor',
    creado_por INT COMMENT 'ID del usuario que creó el registro',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (creado_por) REFERENCES usuarios(id) ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX idx_nombre (nombre),
    INDEX idx_cedula_ruc (cedula_ruc),
    INDEX idx_estado (estado),
    INDEX idx_tipo_proveedor (tipo_proveedor),
    INDEX idx_creado_por (creado_por)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabla de proveedores del sistema';

-- =====================================================
-- TABLA: compras (Registro de compras de materiales)
-- =====================================================
CREATE TABLE IF NOT EXISTS compras (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero_factura VARCHAR(50) COMMENT 'Número de factura o comprobante',
    proveedor_id INT NOT NULL COMMENT 'ID del proveedor',
    sucursal_id INT NOT NULL COMMENT 'ID de la sucursal donde se recibe',
    fecha_compra DATE NOT NULL COMMENT 'Fecha de la compra',
    tipo_comprobante ENUM('factura', 'boleta', 'recibo', 'nota_credito', 'otro') DEFAULT 'factura' COMMENT 'Tipo de comprobante',
    subtotal DECIMAL(10,2) DEFAULT 0 COMMENT 'Subtotal de la compra',
    iva DECIMAL(10,2) DEFAULT 0 COMMENT 'IVA de la compra',
    descuento DECIMAL(10,2) DEFAULT 0 COMMENT 'Descuento aplicado',
    total DECIMAL(10,2) NOT NULL DEFAULT 0 COMMENT 'Total de la compra',
    estado ENUM('pendiente', 'completada', 'cancelada') DEFAULT 'pendiente' COMMENT 'Estado de la compra',
    notas TEXT COMMENT 'Notas adicionales',
    creado_por INT COMMENT 'ID del usuario que creó el registro',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (proveedor_id) REFERENCES proveedores(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (sucursal_id) REFERENCES sucursales(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (creado_por) REFERENCES usuarios(id) ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX idx_proveedor (proveedor_id),
    INDEX idx_sucursal (sucursal_id),
    INDEX idx_fecha_compra (fecha_compra),
    INDEX idx_estado (estado),
    INDEX idx_numero_factura (numero_factura),
    INDEX idx_creado_por (creado_por)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabla de compras de materiales';

-- =====================================================
-- TABLA: compras_detalle (Detalle de productos por compra)
-- =====================================================
CREATE TABLE IF NOT EXISTS compras_detalle (
    id INT AUTO_INCREMENT PRIMARY KEY,
    compra_id INT NOT NULL COMMENT 'ID de la compra',
    inventario_id INT COMMENT 'ID del inventario (si existe)',
    nombre_producto VARCHAR(150) NOT NULL COMMENT 'Nombre del producto/material',
    material_id INT NOT NULL COMMENT 'ID del material/categoría',
    cantidad DECIMAL(10,2) NOT NULL COMMENT 'Cantidad comprada',
    unidad ENUM('kg', 'litros', 'unidades', 'toneladas', 'metros') NOT NULL DEFAULT 'kg' COMMENT 'Unidad de medida',
    precio_unitario DECIMAL(10,2) NOT NULL COMMENT 'Precio por unidad',
    subtotal DECIMAL(10,2) NOT NULL COMMENT 'Subtotal (cantidad * precio_unitario)',
    descripcion TEXT COMMENT 'Descripción adicional',
    FOREIGN KEY (compra_id) REFERENCES compras(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (inventario_id) REFERENCES inventarios(id) ON DELETE SET NULL ON UPDATE CASCADE,
    FOREIGN KEY (material_id) REFERENCES materiales(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    INDEX idx_compra (compra_id),
    INDEX idx_inventario (inventario_id),
    INDEX idx_material (material_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Detalle de productos por compra';

-- =====================================================
-- TABLA: ventas (Registro de ventas de materiales)
-- =====================================================
CREATE TABLE IF NOT EXISTS ventas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero_factura VARCHAR(50) COMMENT 'Número de factura o comprobante',
    cliente_id INT NOT NULL COMMENT 'ID del cliente',
    sucursal_id INT NOT NULL COMMENT 'ID de la sucursal donde se realiza la venta',
    fecha_venta DATE NOT NULL COMMENT 'Fecha de la venta',
    tipo_comprobante ENUM('factura', 'boleta', 'recibo', 'nota_credito', 'otro') DEFAULT 'factura' COMMENT 'Tipo de comprobante',
    subtotal DECIMAL(10,2) DEFAULT 0 COMMENT 'Subtotal de la venta',
    iva DECIMAL(10,2) DEFAULT 0 COMMENT 'IVA de la venta',
    descuento DECIMAL(10,2) DEFAULT 0 COMMENT 'Descuento aplicado',
    total DECIMAL(10,2) NOT NULL DEFAULT 0 COMMENT 'Total de la venta',
    metodo_pago ENUM('efectivo', 'transferencia', 'cheque', 'tarjeta', 'credito', 'otro') DEFAULT 'efectivo' COMMENT 'Método de pago',
    estado ENUM('pendiente', 'completada', 'cancelada', 'devuelta') DEFAULT 'pendiente' COMMENT 'Estado de la venta',
    notas TEXT COMMENT 'Notas adicionales',
    creado_por INT COMMENT 'ID del usuario que creó el registro',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (sucursal_id) REFERENCES sucursales(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (creado_por) REFERENCES usuarios(id) ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX idx_cliente (cliente_id),
    INDEX idx_sucursal (sucursal_id),
    INDEX idx_fecha_venta (fecha_venta),
    INDEX idx_estado (estado),
    INDEX idx_numero_factura (numero_factura),
    INDEX idx_metodo_pago (metodo_pago),
    INDEX idx_creado_por (creado_por)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabla de ventas de materiales';

-- =====================================================
-- TABLA: ventas_detalle (Detalle de productos por venta)
-- =====================================================
CREATE TABLE IF NOT EXISTS ventas_detalle (
    id INT AUTO_INCREMENT PRIMARY KEY,
    venta_id INT NOT NULL COMMENT 'ID de la venta',
    inventario_id INT NOT NULL COMMENT 'ID del inventario vendido',
    nombre_producto VARCHAR(150) NOT NULL COMMENT 'Nombre del producto/material',
    material_id INT NOT NULL COMMENT 'ID del material/categoría',
    cantidad DECIMAL(10,2) NOT NULL COMMENT 'Cantidad vendida',
    unidad ENUM('kg', 'litros', 'unidades', 'toneladas', 'metros') NOT NULL DEFAULT 'kg' COMMENT 'Unidad de medida',
    precio_unitario DECIMAL(10,2) NOT NULL COMMENT 'Precio por unidad',
    subtotal DECIMAL(10,2) NOT NULL COMMENT 'Subtotal (cantidad * precio_unitario)',
    descripcion TEXT COMMENT 'Descripción adicional',
    FOREIGN KEY (venta_id) REFERENCES ventas(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (inventario_id) REFERENCES inventarios(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (material_id) REFERENCES materiales(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    INDEX idx_venta (venta_id),
    INDEX idx_inventario (inventario_id),
    INDEX idx_material (material_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Detalle de productos por venta';

-- =====================================================
-- INSERTAR ROLES INICIALES
-- =====================================================
INSERT INTO roles (nombre, descripcion, permisos, estado) VALUES
('Administrador', 'Administrador del sistema con acceso completo', 
 '{"usuarios": ["crear", "leer", "actualizar", "eliminar"], "sucursales": ["crear", "leer", "actualizar", "eliminar"], "inventarios": ["crear", "leer", "actualizar", "eliminar"], "reportes": ["ver", "exportar"], "configuracion": ["modificar"]}', 
 'activo'),
('Gerente', 'Gerente con permisos de gestión y reportes', 
 '{"usuarios": ["leer", "actualizar"], "sucursales": ["crear", "leer", "actualizar", "eliminar"], "inventarios": ["crear", "leer", "actualizar", "eliminar"], "reportes": ["ver", "exportar"], "configuracion": ["leer"]}', 
 'activo'),
('usuario', 'Usuario normal del sistema con acceso limitado', 
 '{"perfil": ["leer", "actualizar"], "inventarios": ["leer", "actualizar"], "reportes": ["ver"]}', 
 'activo')
ON DUPLICATE KEY UPDATE nombre=nombre;

-- =====================================================
-- INSERTAR MÓDULOS INICIALES
-- =====================================================
INSERT INTO modulos (nombre, descripcion, ruta, icono, orden, estado) VALUES
('Dashboard', 'Panel principal del sistema', 'Dashboard.php', 'fas fa-home', 1, 'activo'),
('Usuarios', 'Gestión de usuarios del sistema', 'usuarios/index.php', 'fas fa-users', 2, 'activo'),
('Sucursales', 'Gestión de sucursales', 'sucursales/index.php', 'fas fa-building', 3, 'activo'),
('Inventarios', 'Control de inventarios', 'inventarios/index.php', 'fas fa-boxes', 4, 'activo'),
('Clientes', 'Gestión de clientes', 'clientes/index.php', 'fas fa-user-tie', 5, 'activo'),
('Proveedores', 'Gestión de proveedores', 'proveedores/index.php', 'fas fa-truck', 6, 'activo'),
('Compras', 'Registro de compras de materiales', 'compras/index.php', 'fas fa-shopping-cart', 7, 'activo'),
('Ventas', 'Registro de ventas de materiales', 'ventas/index.php', 'fas fa-cash-register', 8, 'activo'),
('Reportes', 'Reportes y estadísticas', 'reportes/index.php', 'fas fa-chart-bar', 9, 'activo'),
('Configuración', 'Configuración del sistema', 'configuracion/index.php', 'fas fa-cog', 10, 'activo')
ON DUPLICATE KEY UPDATE nombre=nombre;

-- =====================================================
-- INSERTAR RELACIONES ROL-MODULO (Permisos de acceso)
-- =====================================================
-- Administrador tiene acceso a todos los módulos
INSERT INTO rol_modulos (rol_id, modulo_id) VALUES
(1, 1), (1, 2), (1, 3), (1, 4), (1, 5), (1, 6), (1, 7), (1, 8), (1, 9), (1, 10)
ON DUPLICATE KEY UPDATE rol_id=rol_id;

-- Gerente tiene acceso a gestión, operaciones y reportes
INSERT INTO rol_modulos (rol_id, modulo_id) VALUES
(2, 1), (2, 3), (2, 4), (2, 5), (2, 6), (2, 7), (2, 8), (2, 9)
ON DUPLICATE KEY UPDATE rol_id=rol_id;

-- Usuario normal tiene acceso limitado a inventarios y reportes
INSERT INTO rol_modulos (rol_id, modulo_id) VALUES
(3, 1), (3, 4), (3, 9)
ON DUPLICATE KEY UPDATE rol_id=rol_id;

-- =====================================================
-- INSERTAR USUARIOS INICIALES
-- =====================================================
-- Contraseña para admin: Admin123! (hash con password_hash de PHP)
-- Contraseña para usuario: Usuario123! (hash con password_hash de PHP)

INSERT INTO usuarios (nombre, email, cedula, password, telefono, rol_id, estado) VALUES
('Administrador del Sistema', 'admin@sistema.com', '0000000000', '$2y$10$5NszNfP7qVVqqixWA.cCR.gOg7/z7Cb.L9T2riaoePLUZFNGfTiKS', '1234567890', 1, 'activo'),
('Gerente del Sistema', 'gerente@sistema.com', '0000000001', '$2y$10$WPov5lh4ZElFv4KamSOEcukVdHW/4eoSnME.pQYmvz.8MdQU1Anqy', '0987654321', 2, 'activo'),
('Usuario Normal', 'usuario@sistema.com', '0000000002', '$2y$10$WPov5lh4ZElFv4KamSOEcukVdHW/4eoSnME.pQYmvz.8MdQU1Anqy', '0987654322', 3, 'activo')
ON DUPLICATE KEY UPDATE email=email;

-- =====================================================
-- INSERTAR SUCURSALES DE EJEMPLO
-- =====================================================
INSERT INTO sucursales (nombre, direccion, telefono, email, responsable_id, estado) VALUES
('Sucursal Central', 'Av. Principal 123, Ciudad', '555-1000', 'central@sistema.com', 1, 'activa'),
('Sucursal Norte', 'Calle Norte 456, Ciudad', '555-2000', 'norte@sistema.com', 1, 'activa'),
('Sucursal Sur', 'Av. Sur 789, Ciudad', '555-3000', 'sur@sistema.com', 2, 'activa')
ON DUPLICATE KEY UPDATE nombre=nombre;

-- =====================================================
-- INSERTAR MATERIALES (Categorías y subcategorías)
-- =====================================================
-- Categorías principales (categoria_padre_id = NULL)
-- Se insertan primero las categorías principales para obtener sus IDs
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

-- Subcategorías de Plástico (categoria_padre_id = 1)
INSERT IGNORE INTO materiales (nombre, categoria_padre_id, descripcion, orden, estado) VALUES
('PET', 1, 'Polietileno Tereftalato', 1, 'activo'),
('Hogar', 1, 'Plásticos de uso doméstico', 2, 'activo'),
('Soplado', 1, 'Plásticos soplados', 3, 'activo'),
('PVC', 1, 'Policloruro de Vinilo', 4, 'activo');

-- Subcategorías de Metales (categoria_padre_id = 2)
INSERT IGNORE INTO materiales (nombre, categoria_padre_id, descripcion, orden, estado) VALUES
('Chatarra', 2, 'Chatarra metálica', 1, 'activo'),
('Cobre', 2, 'Cobre reciclable', 2, 'activo'),
('Aluminio', 2, 'Aluminio reciclable', 3, 'activo'),
('Perfil', 2, 'Perfiles metálicos', 4, 'activo'),
('Rayador Cobre Aluminio', 2, 'Mezcla de cobre y aluminio rayado', 5, 'activo'),
('Rayador Aluminio', 2, 'Aluminio rayado', 6, 'activo');

-- Subcategorías de Fibroso (categoria_padre_id = 3)
INSERT IGNORE INTO materiales (nombre, categoria_padre_id, descripcion, orden, estado) VALUES
('Papel', 3, 'Papel reciclable', 1, 'activo'),
('Cartón', 3, 'Cartón reciclable', 2, 'activo'),
('Periódico', 3, 'Periódicos', 3, 'activo'),
('Químico', 3, 'Papel químico', 4, 'activo'),
('Dúplex', 3, 'Papel dúplex', 5, 'activo');

-- Subcategorías de Batería (categoria_padre_id = 4)
INSERT IGNORE INTO materiales (nombre, categoria_padre_id, descripcion, orden, estado) VALUES
('Seca', 4, 'Baterías secas', 1, 'activo'),
('Húmeda', 4, 'Baterías húmedas', 2, 'activo');

-- =====================================================
-- INSERTAR INVENTARIOS DE EJEMPLO
-- =====================================================
-- Nota: Los material_id corresponden a los IDs de los materiales insertados anteriormente
-- Se usan subconsultas para obtener los IDs dinámicamente
INSERT INTO inventarios (sucursal_id, nombre_producto, material_id, cantidad, unidad, precio_unitario, stock_minimo, stock_maximo, descripcion, estado, creado_por) VALUES
(1, 'Papel Reciclado', (SELECT id FROM materiales WHERE nombre = 'Papel' AND categoria_padre_id = 3 LIMIT 1), 150.50, 'kg', 2.50, 50.00, 500.00, 'Papel reciclado procesado', 'disponible', 1),
(1, 'Botellas PET', (SELECT id FROM materiales WHERE nombre = 'PET' AND categoria_padre_id = 1 LIMIT 1), 85.00, 'kg', 3.00, 30.00, 200.00, 'Botellas de plástico PET', 'disponible', 1),
(1, 'Vidrio Verde', (SELECT id FROM materiales WHERE nombre = 'Vidrio' AND categoria_padre_id IS NULL LIMIT 1), 120.75, 'kg', 1.50, 40.00, 300.00, 'Vidrio verde reciclable', 'disponible', 1),
(2, 'Papel Reciclado', (SELECT id FROM materiales WHERE nombre = 'Papel' AND categoria_padre_id = 3 LIMIT 1), 95.25, 'kg', 2.50, 50.00, 500.00, 'Papel reciclado procesado', 'disponible', 1),
(2, 'Latas de Aluminio', (SELECT id FROM materiales WHERE nombre = 'Aluminio' AND categoria_padre_id = 2 LIMIT 1), 45.50, 'kg', 4.00, 20.00, 150.00, 'Latas de aluminio compactadas', 'disponible', 1),
(3, 'Botellas PET', (SELECT id FROM materiales WHERE nombre = 'PET' AND categoria_padre_id = 1 LIMIT 1), 60.00, 'kg', 3.00, 30.00, 200.00, 'Botellas de plástico PET', 'disponible', 2)
ON DUPLICATE KEY UPDATE nombre_producto=nombre_producto;

-- =====================================================
-- INSERTAR CLIENTES DE EJEMPLO
-- =====================================================
INSERT INTO clientes (nombre, cedula_ruc, tipo_documento, direccion, telefono, email, contacto, tipo_cliente, estado, creado_por) VALUES
('Empresa ABC S.A.', '1234567890001', 'ruc', 'Av. Principal 123, Ciudad', '555-1001', 'contacto@empresaabc.com', 'Juan Pérez', 'empresa', 'activo', 1),
('María González', '0987654321', 'cedula', 'Calle 10 #5-20, Ciudad', '555-1002', 'maria.gonzalez@email.com', 'María González', 'minorista', 'activo', 1),
('Instituto Educativo XYZ', '9876543210001', 'ruc', 'Av. Educativa 456, Ciudad', '555-1003', 'info@institutoxyz.edu', 'Carlos Rodríguez', 'institucion', 'activo', 1)
ON DUPLICATE KEY UPDATE nombre=nombre;

-- =====================================================
-- INSERTAR PROVEEDORES DE EJEMPLO
-- =====================================================
INSERT INTO proveedores (nombre, cedula_ruc, tipo_documento, direccion, telefono, email, contacto, tipo_proveedor, materiales_suministra, estado, creado_por) VALUES
('Recicladora del Sur S.A.', '1112223330001', 'ruc', 'Zona Industrial, Calle 50', '555-2001', 'ventas@recicladora.com', 'Pedro Martínez', 'procesador', 'Papel, plástico, vidrio', 'activo', 1),
('Recolectores Unidos', '4445556660001', 'ruc', 'Barrio Norte, Av. 20', '555-2002', 'info@recolectores.com', 'Ana López', 'recolector', 'Materiales varios', 'activo', 1),
('Distribuidora de Metales', '7778889990001', 'ruc', 'Polígono Industrial, Sector 5', '555-2003', 'metales@distribuidora.com', 'Luis Fernández', 'mayorista', 'Metales, aluminio, hierro', 'activo', 1)
ON DUPLICATE KEY UPDATE nombre=nombre;

-- =====================================================
-- VISTAS ÚTILES
-- =====================================================

-- Vista de usuarios con información del rol
CREATE OR REPLACE VIEW v_usuarios_completos AS
SELECT 
    u.id,
    u.nombre,
    u.email,
    u.telefono,
    u.estado,
    r.nombre AS rol_nombre,
    r.descripcion AS rol_descripcion,
    u.fecha_creacion,
    u.fecha_actualizacion
FROM usuarios u
INNER JOIN roles r ON u.rol_id = r.id;

-- Vista de inventarios con información de sucursal y material
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

-- Vista de módulos por rol
CREATE OR REPLACE VIEW v_modulos_por_rol AS
SELECT 
    r.id AS rol_id,
    r.nombre AS rol_nombre,
    m.id AS modulo_id,
    m.nombre AS modulo_nombre,
    m.ruta AS modulo_ruta,
    m.icono AS modulo_icono,
    m.orden AS modulo_orden
FROM roles r
LEFT JOIN rol_modulos rm ON r.id = rm.rol_id
LEFT JOIN modulos m ON rm.modulo_id = m.id
WHERE m.estado = 'activo' OR m.estado IS NULL
ORDER BY r.nombre, m.orden;

-- Vista de resumen de inventarios por sucursal
CREATE OR REPLACE VIEW v_resumen_inventarios AS
SELECT 
    s.id AS sucursal_id,
    s.nombre AS sucursal_nombre,
    COUNT(i.id) AS total_productos,
    SUM(CASE WHEN i.estado = 'disponible' THEN 1 ELSE 0 END) AS productos_disponibles,
    SUM(CASE WHEN i.estado = 'agotado' THEN 1 ELSE 0 END) AS productos_agotados,
    SUM(i.cantidad * i.precio_unitario) AS valor_total_estimado
FROM sucursales s
LEFT JOIN inventarios i ON s.id = i.sucursal_id
WHERE s.estado = 'activa'
GROUP BY s.id, s.nombre;

-- Vista de resumen por categoría/material
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
-- TRIGGERS ÚTILES
-- =====================================================

-- Trigger para actualizar estado según stock y registrar movimiento
DELIMITER $$
CREATE TRIGGER IF NOT EXISTS trg_actualizar_estado_inventario
BEFORE UPDATE ON inventarios
FOR EACH ROW
BEGIN
    -- Actualizar estado basado en cantidad y stock mínimo
    IF NEW.cantidad < NEW.stock_minimo AND NEW.stock_minimo > 0 THEN
        SET NEW.estado = 'agotado';
    ELSEIF NEW.cantidad >= NEW.stock_minimo THEN
        SET NEW.estado = 'disponible';
    END IF;
END$$
DELIMITER ;

-- Trigger para registrar movimientos en el historial (después de actualizar)
DELIMITER $$
CREATE TRIGGER IF NOT EXISTS trg_registrar_movimiento_inventario
AFTER UPDATE ON inventarios
FOR EACH ROW
BEGIN
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
            NEW.creado_por,
            CONCAT('Actualización automática de inventario')
        );
    END IF;
END$$
DELIMITER ;

-- =====================================================
-- PROCEDIMIENTOS ALMACENADOS ÚTILES
-- =====================================================

-- Procedimiento para obtener el total de inventario por sucursal
DELIMITER $$
CREATE PROCEDURE IF NOT EXISTS sp_total_inventario_sucursal(IN p_sucursal_id INT)
BEGIN
    SELECT 
        s.nombre AS sucursal,
        COUNT(i.id) AS total_productos,
        SUM(i.cantidad) AS cantidad_total,
        SUM(i.cantidad * i.precio_unitario) AS valor_total
    FROM sucursales s
    LEFT JOIN inventarios i ON s.id = i.sucursal_id
    WHERE s.id = p_sucursal_id
    GROUP BY s.id, s.nombre;
END$$
DELIMITER ;

-- Procedimiento para obtener inventario por material
DELIMITER $$
CREATE PROCEDURE IF NOT EXISTS sp_inventario_por_material(IN p_material_id INT)
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
-- ÍNDICES ADICIONALES PARA MEJOR RENDIMIENTO
-- =====================================================
ALTER TABLE inventarios ADD INDEX idx_nombre_producto (nombre_producto);
ALTER TABLE inventarios ADD INDEX idx_fecha_creacion (fecha_creacion);
ALTER TABLE sucursales ADD INDEX idx_nombre (nombre);
ALTER TABLE usuarios ADD INDEX idx_nombre (nombre);

-- =====================================================
-- COMENTARIOS ADICIONALES PARA RELACIONES (phpMyAdmin)
-- =====================================================
-- RELACIONES DE LA BASE DE DATOS:
--
-- 1. roles (1) ──< (N) usuarios
--    - Un rol puede tener muchos usuarios
--    - Un usuario tiene un solo rol
--
-- 2. roles (N) ──< (N) modulos [tabla intermedia: rol_modulos]
--    - Un rol puede acceder a muchos módulos
--    - Un módulo puede ser accedido por muchos roles
--
-- 3. usuarios (1) ──< (N) sucursales
--    - Un usuario puede ser responsable de muchas sucursales
--    - Una sucursal tiene un solo responsable
--
-- 4. sucursales (1) ──< (N) inventarios
--    - Una sucursal puede tener muchos inventarios
--    - Un inventario pertenece a una sola sucursal
--
-- 5. usuarios (1) ──< (N) inventarios
--    - Un usuario puede crear muchos registros de inventario
--    - Un inventario es creado por un usuario
--
-- 6. inventarios (1) ──< (N) movimientos_inventario
--    - Un inventario puede tener muchos movimientos
--    - Un movimiento pertenece a un solo inventario
--
-- 7. usuarios (1) ──< (N) movimientos_inventario
--    - Un usuario puede realizar muchos movimientos
--    - Un movimiento es realizado por un usuario
--
-- =====================================================

-- =====================================================
-- NOTAS FINALES
-- =====================================================
-- 
-- CONTRASEÑAS POR DEFECTO (Cambiar después del primer inicio):
-- Admin:    Admin123!
-- Usuario:  Usuario123!
-- 
-- NOTA: Las contraseñas en la base de datos deben generarse con:
-- password_hash('tu_contraseña', PASSWORD_DEFAULT)
-- 
-- Para generar nuevas contraseñas, ejecuta en PHP:
-- echo password_hash('tu_contraseña', PASSWORD_DEFAULT);
-- 
-- ESTRUCTURA DE MATERIALES:
-- La tabla 'materiales' contiene categorías principales y subcategorías:
-- - Categorías principales: Plástico, Metales, Fibroso, Batería, Vidrio, Orgánico, Electrónico, Textil, Otro
-- - Subcategorías se relacionan mediante categoria_padre_id
-- - Ejemplo: PET, Hogar, Soplado, PVC son subcategorías de Plástico
-- - Ejemplo: Cobre, Aluminio, Perfil son subcategorías de Metales
-- - Ejemplo: Papel, Cartón, Periódico son subcategorías de Fibroso
-- 
-- =====================================================

