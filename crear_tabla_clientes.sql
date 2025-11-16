-- ============================================
-- Crear tabla CLIENTES
-- Sistema de Gestión de Reciclaje
-- ============================================

CREATE TABLE IF NOT EXISTS `clientes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) NOT NULL COMMENT 'Nombre o razón social del cliente',
  `cedula_ruc` varchar(20) DEFAULT NULL COMMENT 'Cédula o RUC del cliente',
  `tipo_documento` enum('cedula','ruc','pasaporte','otro') DEFAULT 'cedula' COMMENT 'Tipo de documento',
  `direccion` text DEFAULT NULL COMMENT 'Dirección del cliente',
  `telefono` varchar(20) DEFAULT NULL COMMENT 'Teléfono de contacto',
  `email` varchar(150) DEFAULT NULL COMMENT 'Correo electrónico',
  `contacto` varchar(150) DEFAULT NULL COMMENT 'Persona de contacto',
  `tipo_cliente` enum('minorista','mayorista','empresa','institucion') DEFAULT 'minorista' COMMENT 'Tipo de cliente',
  `estado` enum('activo','inactivo') DEFAULT 'activo' COMMENT 'Estado del cliente',
  `notas` text DEFAULT NULL COMMENT 'Notas adicionales',
  `creado_por` int(11) DEFAULT NULL COMMENT 'ID del usuario que creó el registro',
  `fecha_creacion` timestamp NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_cedula_ruc` (`cedula_ruc`),
  KEY `idx_estado` (`estado`),
  KEY `idx_tipo_cliente` (`tipo_cliente`),
  KEY `idx_nombre` (`nombre`),
  KEY `idx_creado_por` (`creado_por`),
  CONSTRAINT `clientes_ibfk_1` FOREIGN KEY (`creado_por`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabla de clientes del sistema';

