-- Script para eliminar el campo "valor_total" de la tabla inventarios
-- Sistema de Gestión de Reciclaje
-- Fecha: 2024

-- Eliminar campo valor_total de la tabla inventarios
ALTER TABLE inventarios DROP COLUMN IF EXISTS valor_total;

-- Verificar que el campo fue eliminado
-- (Opcional: esta consulta verifica que el campo ya no existe)
-- DESCRIBE inventarios;

