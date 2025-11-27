-- Tabla para almacenar información de clientes
-- Sistema de Gestión de Reciclaje

CREATE TABLE IF NOT EXISTS `clientes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL COMMENT 'Nombre o Razón Social del cliente',
  `cedula_ruc` varchar(20) DEFAULT NULL COMMENT 'Cédula (10 dígitos) o RUC (13 dígitos)',
  `tipo_documento` enum('cedula','ruc') DEFAULT 'cedula' COMMENT 'Tipo de documento',
  `direccion` text DEFAULT NULL COMMENT 'Dirección del cliente',
  `telefono` varchar(20) DEFAULT NULL COMMENT 'Teléfono de contacto',
  `email` varchar(150) DEFAULT NULL COMMENT 'Email del cliente',
  `contacto` varchar(255) DEFAULT NULL COMMENT 'Persona de contacto',
  `tipo_cliente` enum('minorista','mayorista','empresa') DEFAULT 'minorista' COMMENT 'Tipo de cliente',
  `estado` enum('activo','inactivo') DEFAULT 'activo' COMMENT 'Estado del cliente',
  `notas` text DEFAULT NULL COMMENT 'Notas adicionales',
  `creado_por` int(11) DEFAULT NULL COMMENT 'ID del usuario que creó el registro',
  `fecha_creacion` timestamp NULL DEFAULT current_timestamp() COMMENT 'Fecha de creación',
  `fecha_actualizacion` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp() COMMENT 'Fecha de última actualización',
  PRIMARY KEY (`id`),
  UNIQUE KEY `cedula_ruc` (`cedula_ruc`),
  KEY `idx_nombre` (`nombre`),
  KEY `idx_estado` (`estado`),
  KEY `idx_tipo_cliente` (`tipo_cliente`),
  KEY `idx_creado_por` (`creado_por`),
  CONSTRAINT `clientes_ibfk_1` FOREIGN KEY (`creado_por`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabla de clientes del sistema';

