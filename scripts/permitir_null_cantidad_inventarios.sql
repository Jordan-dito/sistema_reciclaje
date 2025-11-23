-- Script para permitir NULL en el campo cantidad de la tabla inventarios
-- Sistema de Gestión de Reciclaje

-- Verificar si la columna existe y permitir NULL
ALTER TABLE inventarios 
MODIFY COLUMN cantidad DECIMAL(10,2) NULL;

-- Verificar el cambio
-- SELECT COLUMN_NAME, IS_NULLABLE, DATA_TYPE, COLUMN_TYPE 
-- FROM INFORMATION_SCHEMA.COLUMNS 
-- WHERE TABLE_SCHEMA = DATABASE() 
-- AND TABLE_NAME = 'inventarios' 
-- AND COLUMN_NAME = 'cantidad';

