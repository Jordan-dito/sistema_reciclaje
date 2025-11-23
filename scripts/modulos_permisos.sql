-- =====================================================
-- Script para crear tablas de Módulos y Permisos
-- Sistema de Gestión de Reciclaje
-- =====================================================

-- Tabla de módulos del sistema
CREATE TABLE IF NOT EXISTS `modulos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL COMMENT 'Nombre del módulo',
  `descripcion` text DEFAULT NULL COMMENT 'Descripción del módulo',
  `icono` varchar(50) DEFAULT NULL COMMENT 'Clase del icono (FontAwesome)',
  `orden` int(11) DEFAULT 0 COMMENT 'Orden de visualización',
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `fecha_creacion` timestamp NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`),
  KEY `idx_estado` (`estado`),
  KEY `idx_orden` (`orden`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabla de módulos del sistema';

-- Tabla de relación entre roles y módulos (permisos)
CREATE TABLE IF NOT EXISTS `rol_modulo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rol_id` int(11) NOT NULL COMMENT 'ID del rol',
  `modulo_id` int(11) NOT NULL COMMENT 'ID del módulo',
  `estado` enum('asignado','no_asignado') DEFAULT 'asignado' COMMENT 'Estado de asignación',
  `fecha_asignacion` timestamp NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_rol_modulo` (`rol_id`,`modulo_id`),
  KEY `idx_rol` (`rol_id`),
  KEY `idx_modulo` (`modulo_id`),
  KEY `idx_estado` (`estado`),
  CONSTRAINT `rol_modulo_ibfk_rol` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `rol_modulo_ibfk_modulo` FOREIGN KEY (`modulo_id`) REFERENCES `modulos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Relación entre roles y módulos (permisos)';

-- =====================================================
-- Insertar módulos del sistema
-- =====================================================

-- Módulos comunes (para ambos roles)
INSERT INTO `modulos` (`nombre`, `descripcion`, `icono`, `orden`, `estado`) VALUES
('Dashboard', 'Panel principal del sistema', 'fas fa-home', 1, 'activo'),

-- Módulos para Gerente
('Gestión de Usuario', 'Gestión de usuarios y roles del sistema', 'fas fa-users-cog', 2, 'activo'),
('Módulo de Parámetros', 'Configuración de categorías, materiales, unidades y productos', 'fas fa-cog', 3, 'activo'),
('Control de Sucursales', 'Gestión de sucursales e inventarios', 'fas fa-building', 4, 'activo'),
('Administración de Personal', 'Gestión del personal de la empresa', 'fas fa-user-tie', 5, 'activo'),
('Reporte', 'Reportes y estadísticas del sistema', 'fas fa-chart-line', 6, 'activo'),

-- Módulos para Administrador
('Gestión de Inventario', 'Gestión de compras y ventas', 'fas fa-warehouse', 2, 'activo'),
('Relaciones Comerciales', 'Gestión de proveedores y clientes', 'fas fa-handshake', 3, 'activo')
ON DUPLICATE KEY UPDATE 
  `descripcion` = VALUES(`descripcion`),
  `icono` = VALUES(`icono`),
  `orden` = VALUES(`orden`);

-- =====================================================
-- Asignar módulos a roles
-- =====================================================

-- Obtener IDs de roles (asumiendo que Administrador=1 y Gerente=2)
SET @admin_id = (SELECT id FROM roles WHERE nombre = 'Administrador' LIMIT 1);
SET @gerente_id = (SELECT id FROM roles WHERE nombre = 'Gerente' LIMIT 1);

-- Módulos para Administrador
INSERT INTO `rol_modulo` (`rol_id`, `modulo_id`, `estado`) VALUES
(@admin_id, (SELECT id FROM modulos WHERE nombre = 'Dashboard' LIMIT 1), 'asignado'),
(@admin_id, (SELECT id FROM modulos WHERE nombre = 'Gestión de Inventario' LIMIT 1), 'asignado'),
(@admin_id, (SELECT id FROM modulos WHERE nombre = 'Relaciones Comerciales' LIMIT 1), 'asignado'),
(@admin_id, (SELECT id FROM modulos WHERE nombre = 'Reporte' LIMIT 1), 'asignado')
ON DUPLICATE KEY UPDATE `estado` = VALUES(`estado`);

-- Módulos para Gerente
INSERT INTO `rol_modulo` (`rol_id`, `modulo_id`, `estado`) VALUES
(@gerente_id, (SELECT id FROM modulos WHERE nombre = 'Dashboard' LIMIT 1), 'asignado'),
(@gerente_id, (SELECT id FROM modulos WHERE nombre = 'Gestión de Usuario' LIMIT 1), 'asignado'),
(@gerente_id, (SELECT id FROM modulos WHERE nombre = 'Módulo de Parámetros' LIMIT 1), 'asignado'),
(@gerente_id, (SELECT id FROM modulos WHERE nombre = 'Control de Sucursales' LIMIT 1), 'asignado'),
(@gerente_id, (SELECT id FROM modulos WHERE nombre = 'Administración de Personal' LIMIT 1), 'asignado'),
(@gerente_id, (SELECT id FROM modulos WHERE nombre = 'Reporte' LIMIT 1), 'asignado')
ON DUPLICATE KEY UPDATE `estado` = VALUES(`estado`);

-- =====================================================
-- Verificar datos insertados
-- =====================================================

-- Ver módulos creados
SELECT * FROM modulos ORDER BY orden;

-- Ver asignaciones de módulos por rol
SELECT 
  r.nombre as rol,
  m.nombre as modulo,
  m.descripcion,
  rm.estado,
  rm.fecha_asignacion
FROM rol_modulo rm
INNER JOIN roles r ON rm.rol_id = r.id
INNER JOIN modulos m ON rm.modulo_id = m.id
ORDER BY r.nombre, m.orden;

