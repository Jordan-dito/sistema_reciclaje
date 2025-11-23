-- =====================================================
-- Script para agregar campo de foto de perfil a usuarios
-- Sistema de Gestión de Reciclaje
-- =====================================================

-- Agregar campo foto_perfil a la tabla usuarios
ALTER TABLE `usuarios` 
ADD COLUMN `foto_perfil` VARCHAR(255) DEFAULT NULL COMMENT 'Ruta de la foto de perfil del usuario' 
AFTER `telefono`;

-- Crear índice para mejorar búsquedas
ALTER TABLE `usuarios`
ADD KEY `idx_foto_perfil` (`foto_perfil`);

