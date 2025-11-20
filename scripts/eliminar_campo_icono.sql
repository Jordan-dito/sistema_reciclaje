-- Script para eliminar el campo "icono" de las tablas materiales y categorias
-- Sistema de Gestión de Reciclaje
-- Fecha: 2024

-- Eliminar campo icono de la tabla materiales
ALTER TABLE materiales DROP COLUMN IF EXISTS icono;

-- Eliminar campo icono de la tabla categorias
ALTER TABLE categorias DROP COLUMN IF EXISTS icono;

-- Verificar que los campos fueron eliminados
-- (Opcional: estas consultas verifican que los campos ya no existen)
-- DESCRIBE materiales;
-- DESCRIBE categorias;

