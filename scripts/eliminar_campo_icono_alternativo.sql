-- Script alternativo para eliminar el campo "icono" de las tablas materiales y categorias
-- Sistema de Gestión de Reciclaje
-- Fecha: 2024
-- Nota: Si tu versión de MySQL/MariaDB no soporta "IF EXISTS", usa este script

-- Eliminar campo icono de la tabla materiales
-- Si el campo no existe, se mostrará un error que puedes ignorar
ALTER TABLE materiales DROP COLUMN icono;

-- Eliminar campo icono de la tabla categorias
-- Si el campo no existe, se mostrará un error que puedes ignorar
ALTER TABLE categorias DROP COLUMN icono;

