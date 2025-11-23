-- Script alternativo para crear la tabla clientes (sin IF NOT EXISTS para versiones antiguas de MySQL)
-- Sistema de Gestión de Reciclaje

-- Verificar si la tabla existe antes de crearla
-- Si la tabla ya existe, este script fallará. En ese caso, usa el script principal.

-- Crear tabla clientes
CREATE TABLE clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_razon_social VARCHAR(255) NOT NULL COMMENT 'Nombre o Razón Social del cliente',
    cedula_ruc VARCHAR(20) UNIQUE COMMENT 'Cédula (10 dígitos) o RUC (13 dígitos)',
    tipo_documento ENUM('cedula', 'ruc', 'pasaporte') DEFAULT 'cedula' COMMENT 'Tipo de documento de identidad',
    email VARCHAR(255) COMMENT 'Correo electrónico del cliente',
    telefono VARCHAR(20) COMMENT 'Teléfono de contacto',
    direccion TEXT COMMENT 'Dirección del cliente',
    persona_contacto VARCHAR(255) COMMENT 'Nombre de la persona de contacto',
    tipo_cliente ENUM('mayorista', 'minorista', 'distribuidor') DEFAULT 'minorista' COMMENT 'Tipo de cliente',
    estado ENUM('activo', 'inactivo') DEFAULT 'activo' COMMENT 'Estado del cliente',
    notas TEXT COMMENT 'Notas adicionales sobre el cliente',
    creado_por INT COMMENT 'ID del usuario que creó el registro',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha de creación del registro',
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Fecha de última actualización',
    
    -- Índices para mejorar el rendimiento
    INDEX idx_cedula_ruc (cedula_ruc),
    INDEX idx_email (email),
    INDEX idx_estado (estado),
    INDEX idx_tipo_cliente (tipo_cliente),
    INDEX idx_fecha_creacion (fecha_creacion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabla de clientes del sistema';

-- Agregar clave foránea si la tabla usuarios existe
-- ALTER TABLE clientes 
-- ADD CONSTRAINT fk_clientes_creado_por FOREIGN KEY (creado_por) 
--     REFERENCES usuarios(id) ON DELETE SET NULL ON UPDATE CASCADE;




