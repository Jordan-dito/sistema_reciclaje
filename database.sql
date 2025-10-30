-- =====================================================
-- Base de Datos: Sistema de Gestión de Reciclaje
-- Tesis de Grado
-- =====================================================

-- Crear Base de Datos
CREATE DATABASE IF NOT EXISTS sistema_reciclaje CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sistema_reciclaje;

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
    password VARCHAR(255) NOT NULL COMMENT 'Contraseña hasheada',
    telefono VARCHAR(20) COMMENT 'Teléfono de contacto',
    rol_id INT NOT NULL COMMENT 'ID del rol del usuario',
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (rol_id) REFERENCES roles(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    INDEX idx_email (email),
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
-- TABLA: inventarios
-- =====================================================
CREATE TABLE IF NOT EXISTS inventarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sucursal_id INT NOT NULL COMMENT 'ID de la sucursal',
    nombre_producto VARCHAR(150) NOT NULL COMMENT 'Nombre del producto/material',
    categoria ENUM('papel', 'plastico', 'vidrio', 'metal', 'organico', 'electronico', 'textil', 'otro') NOT NULL COMMENT 'Categoría del material',
    cantidad DECIMAL(10,2) NOT NULL DEFAULT 0 COMMENT 'Cantidad disponible',
    unidad ENUM('kg', 'litros', 'unidades', 'toneladas', 'metros') NOT NULL DEFAULT 'kg' COMMENT 'Unidad de medida',
    precio_unitario DECIMAL(10,2) DEFAULT 0 COMMENT 'Precio por unidad',
    stock_minimo DECIMAL(10,2) DEFAULT 0 COMMENT 'Stock mínimo para alerta',
    stock_maximo DECIMAL(10,2) DEFAULT 0 COMMENT 'Stock máximo recomendado',
    descripcion TEXT COMMENT 'Descripción adicional',
    estado ENUM('disponible', 'agotado', 'reservado') DEFAULT 'disponible',
    creado_por INT COMMENT 'ID del usuario que creó el registro',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (sucursal_id) REFERENCES sucursales(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (creado_por) REFERENCES usuarios(id) ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX idx_sucursal (sucursal_id),
    INDEX idx_categoria (categoria),
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
-- INSERTAR ROLES INICIALES
-- =====================================================
INSERT INTO roles (nombre, descripcion, permisos, estado) VALUES
('administrador', 'Administrador del sistema con acceso completo', 
 '{"usuarios": ["crear", "leer", "actualizar", "eliminar"], "sucursales": ["crear", "leer", "actualizar", "eliminar"], "inventarios": ["crear", "leer", "actualizar", "eliminar"], "reportes": ["ver", "exportar"], "configuracion": ["modificar"]}', 
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
('Reportes', 'Reportes y estadísticas', 'reportes/index.php', 'fas fa-chart-bar', 5, 'activo'),
('Configuración', 'Configuración del sistema', 'configuracion/index.php', 'fas fa-cog', 6, 'activo')
ON DUPLICATE KEY UPDATE nombre=nombre;

-- =====================================================
-- INSERTAR RELACIONES ROL-MODULO (Permisos de acceso)
-- =====================================================
-- Administrador tiene acceso a todos los módulos
INSERT INTO rol_modulos (rol_id, modulo_id) VALUES
(1, 1), (1, 2), (1, 3), (1, 4), (1, 5), (1, 6)
ON DUPLICATE KEY UPDATE rol_id=rol_id;

-- Usuario normal tiene acceso limitado
INSERT INTO rol_modulos (rol_id, modulo_id) VALUES
(2, 1), (2, 4), (2, 5)
ON DUPLICATE KEY UPDATE rol_id=rol_id;

-- =====================================================
-- INSERTAR USUARIOS INICIALES
-- =====================================================
-- Contraseña para admin: Admin123! (hash con password_hash de PHP)
-- Contraseña para usuario: Usuario123! (hash con password_hash de PHP)

INSERT INTO usuarios (nombre, email, password, telefono, rol_id, estado) VALUES
('Administrador del Sistema', 'admin@sistema.com', '$2y$10$5NszNfP7qVVqqixWA.cCR.gOg7/z7Cb.L9T2riaoePLUZFNGfTiKS', '1234567890', 1, 'activo'),
('Usuario Normal', 'usuario@sistema.com', '$2y$10$WPov5lh4ZElFv4KamSOEcukVdHW/4eoSnME.pQYmvz.8MdQU1Anqy', '0987654321', 2, 'activo')
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
-- INSERTAR INVENTARIOS DE EJEMPLO
-- =====================================================
INSERT INTO inventarios (sucursal_id, nombre_producto, categoria, cantidad, unidad, precio_unitario, stock_minimo, stock_maximo, descripcion, estado, creado_por) VALUES
(1, 'Papel Reciclado', 'papel', 150.50, 'kg', 2.50, 50.00, 500.00, 'Papel reciclado procesado', 'disponible', 1),
(1, 'Botellas PET', 'plastico', 85.00, 'kg', 3.00, 30.00, 200.00, 'Botellas de plástico PET', 'disponible', 1),
(1, 'Vidrio Verde', 'vidrio', 120.75, 'kg', 1.50, 40.00, 300.00, 'Vidrio verde reciclable', 'disponible', 1),
(2, 'Papel Reciclado', 'papel', 95.25, 'kg', 2.50, 50.00, 500.00, 'Papel reciclado procesado', 'disponible', 1),
(2, 'Latas de Aluminio', 'metal', 45.50, 'kg', 4.00, 20.00, 150.00, 'Latas de aluminio compactadas', 'disponible', 1),
(3, 'Botellas PET', 'plastico', 60.00, 'kg', 3.00, 30.00, 200.00, 'Botellas de plástico PET', 'disponible', 2)
ON DUPLICATE KEY UPDATE nombre_producto=nombre_producto;

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

-- Vista de inventarios con información de sucursal
CREATE OR REPLACE VIEW v_inventarios_completos AS
SELECT 
    i.id,
    i.nombre_producto,
    i.categoria,
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

-- Vista de resumen por categoría
CREATE OR REPLACE VIEW v_inventario_por_categoria AS
SELECT 
    i.categoria,
    s.nombre AS sucursal_nombre,
    COUNT(i.id) AS total_items,
    SUM(i.cantidad) AS cantidad_total,
    i.unidad,
    SUM(i.cantidad * i.precio_unitario) AS valor_total
FROM inventarios i
INNER JOIN sucursales s ON i.sucursal_id = s.id
WHERE i.estado = 'disponible' AND s.estado = 'activa'
GROUP BY i.categoria, s.id, s.nombre, i.unidad;

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

-- Procedimiento para obtener inventario por categoría
DELIMITER $$
CREATE PROCEDURE IF NOT EXISTS sp_inventario_por_categoria(IN p_categoria VARCHAR(50))
BEGIN
    SELECT 
        i.nombre_producto,
        s.nombre AS sucursal,
        i.cantidad,
        i.unidad,
        i.precio_unitario,
        (i.cantidad * i.precio_unitario) AS valor_total
    FROM inventarios i
    INNER JOIN sucursales s ON i.sucursal_id = s.id
    WHERE i.categoria = p_categoria AND i.estado = 'disponible' AND s.estado = 'activa'
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
-- CATEGORÍAS DE MATERIALES:
-- - papel: Papel y cartón
-- - plastico: Plásticos reciclables
-- - vidrio: Vidrio reciclable
-- - metal: Metales (aluminio, hierro, etc.)
-- - organico: Material orgánico compostable
-- - electronico: Residuos electrónicos
-- - textil: Ropa y textiles
-- - otro: Otros materiales
-- 
-- =====================================================

