-- Script alternativo para eliminar el campo "valor_total" de la tabla inventarios
-- Sistema de Gestión de Reciclaje
-- Fecha: 2024
-- Nota: Si tu versión de MySQL/MariaDB no soporta "IF EXISTS", usa este script

-- Eliminar campo valor_total de la tabla inventarios
-- Si el campo no existe, se mostrará un error que puedes ignorar
ALTER TABLE inventarios DROP COLUMN valor_total;

