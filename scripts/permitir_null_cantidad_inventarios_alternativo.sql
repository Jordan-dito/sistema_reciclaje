-- Script alternativo para permitir NULL en el campo cantidad de la tabla inventarios
-- Para versiones de MySQL que no soportan MODIFY COLUMN directamente
-- Sistema de Gestión de Reciclaje

-- Primero, verificar la estructura actual
-- DESCRIBE inventarios;

-- Modificar la columna para permitir NULL
ALTER TABLE inventarios 
CHANGE COLUMN cantidad cantidad DECIMAL(10,2) NULL;

