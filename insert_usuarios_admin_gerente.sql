-- ============================================
-- INSERT de Roles y Usuarios Admin y Gerente
-- Sistema de Gestión de Reciclaje
-- ============================================

-- IMPORTANTE: Ejecutar primero los INSERT de roles, luego los de usuarios

-- ============================================
-- PASO 1: INSERTAR ROLES (si no existen)
-- ============================================

-- IMPORTANTE: Si los roles ya existen, estos INSERT no harán nada
-- Si no existen, los creará

-- Insertar roles (sin campo permisos, según la estructura actual de la base)
INSERT IGNORE INTO `roles` (`id`, `nombre`, `descripcion`, `estado`) VALUES
(1, 'Administrador', 'Administrador del sistema con acceso completo', 'activo');

INSERT IGNORE INTO `roles` (`id`, `nombre`, `descripcion`, `estado`) VALUES
(2, 'Gerente', 'Gerente con permisos de gestión y reportes', 'activo');

-- ============================================
-- PASO 2: INSERTAR USUARIOS
-- ============================================

-- ============================================
-- USUARIO ADMINISTRADOR
-- ============================================
-- Email: admin@sistema.com
-- Contraseña: Admin123!
-- Rol: Administrador (rol_id = 1)
-- ============================================

INSERT INTO `usuarios` (
    `nombre`, 
    `email`, 
    `cedula`, 
    `password`, 
    `telefono`, 
    `rol_id`, 
    `estado`
) VALUES (
    'Administrador del Sistema',
    'admin@sistema.com',
    '0000000000',
    '$2y$10$npxsM8vXbrYrbwcKH2JhF.VQkkdiupxdOZEgX6XftdzNFSxRnI1tW',
    '1234567890',
    1,
    'activo'
)
ON DUPLICATE KEY UPDATE 
    `nombre` = VALUES(`nombre`),
    `password` = VALUES(`password`),
    `telefono` = VALUES(`telefono`),
    `rol_id` = VALUES(`rol_id`),
    `estado` = VALUES(`estado`);

-- ============================================
-- USUARIO GERENTE
-- ============================================
-- Email: gerente@sistema.com
-- Contraseña: Gerente123!
-- Rol: Gerente (rol_id = 2)
-- ============================================

INSERT INTO `usuarios` (
    `nombre`, 
    `email`, 
    `cedula`, 
    `password`, 
    `telefono`, 
    `rol_id`, 
    `estado`
) VALUES (
    'Gerente del Sistema',
    'gerente@sistema.com',
    '0000000001',
    '$2y$10$Ta3x0FJu3xP/HrRGKuCb5.RkuJNA2Duj/7f/L0MZGqy.PFSKTXf42',
    '0987654321',
    2,
    'activo'
)
ON DUPLICATE KEY UPDATE 
    `nombre` = VALUES(`nombre`),
    `password` = VALUES(`password`),
    `telefono` = VALUES(`telefono`),
    `rol_id` = VALUES(`rol_id`),
    `estado` = VALUES(`estado`);

-- ============================================
-- NOTAS:
-- ============================================
-- 1. Las contraseñas están hasheadas con password_hash() de PHP
-- 2. Admin123! = hash: $2y$10$npxsM8vXbrYrbwcKH2JhF.VQkkdiupxdOZEgX6XftdzNFSxRnI1tW
-- 3. Gerente123! = hash: $2y$10$Ta3x0FJu3xP/HrRGKuCb5.RkuJNA2Duj/7f/L0MZGqy.PFSKTXf42
-- 4. Si los usuarios ya existen, usar UPDATE en lugar de INSERT
-- 5. Los campos fecha_creacion y fecha_actualizacion se llenan automáticamente
-- ============================================

-- ============================================
-- Si necesitas actualizar usuarios existentes:
-- ============================================

-- UPDATE `usuarios` 
-- SET 
--     `nombre` = 'Administrador del Sistema',
--     `password` = '$2y$10$npxsM8vXbrYrbwcKH2JhF.VQkkdiupxdOZEgX6XftdzNFSxRnI1tW',
--     `rol_id` = 1,
--     `estado` = 'activo'
-- WHERE `email` = 'admin@sistema.com';

-- UPDATE `usuarios` 
-- SET 
--     `nombre` = 'Gerente del Sistema',
--     `password` = '$2y$10$Ta3x0FJu3xP/HrRGKuCb5.RkuJNA2Duj/7f/L0MZGqy.PFSKTXf42',
--     `rol_id` = 2,
--     `estado` = 'activo'
-- WHERE `email` = 'gerente@sistema.com';

