-- Script para crear trigger que actualiza automáticamente el inventario al realizar una venta
-- Sistema de Gestión de Reciclaje

-- Eliminar el trigger si ya existe
DROP TRIGGER IF EXISTS trg_actualizar_inventario_venta;

-- Crear el trigger que se ejecuta después de insertar un detalle de venta
-- Este trigger resta automáticamente la cantidad vendida del inventario
DELIMITER $$

CREATE TRIGGER trg_actualizar_inventario_venta
AFTER INSERT ON ventas_detalle
FOR EACH ROW
BEGIN
    DECLARE estado_venta VARCHAR(50);
    
    -- Obtener el estado de la venta
    SELECT estado INTO estado_venta
    FROM ventas
    WHERE id = NEW.venta_id;
    
    -- Solo actualizar el inventario si la venta está completada
    IF estado_venta = 'completada' THEN
        -- Verificar que la cantidad no sea NULL y sea mayor a 0
        IF NEW.cantidad IS NOT NULL AND NEW.cantidad > 0 THEN
            -- Obtener la cantidad actual del inventario (puede ser NULL)
            SET @cantidad_actual = (SELECT COALESCE(cantidad, 0) FROM inventarios WHERE id = NEW.inventario_id);
            
            -- Verificar que hay suficiente stock
            IF @cantidad_actual >= NEW.cantidad THEN
                -- Restar la cantidad vendida del inventario
                UPDATE inventarios 
                SET cantidad = COALESCE(cantidad, 0) - NEW.cantidad,
                    fecha_actualizacion = NOW()
                WHERE id = NEW.inventario_id;
            ELSE
                -- Si no hay suficiente stock, lanzar un error
                SIGNAL SQLSTATE '45000'
                SET MESSAGE_TEXT = CONCAT('Stock insuficiente. Disponible: ', @cantidad_actual, ', Solicitado: ', NEW.cantidad);
            END IF;
        END IF;
    END IF;
END$$

DELIMITER ;

-- Crear trigger para cuando se actualiza el estado de una venta a 'completada'
-- Esto permite actualizar el inventario si la venta se completa después de ser creada
DROP TRIGGER IF EXISTS trg_completar_venta_actualizar_inventario;

DELIMITER $$

CREATE TRIGGER trg_completar_venta_actualizar_inventario
AFTER UPDATE ON ventas
FOR EACH ROW
BEGIN
    -- Si el estado cambió a 'completada'
    IF NEW.estado = 'completada' AND (OLD.estado IS NULL OR OLD.estado != 'completada') THEN
        -- Actualizar inventario para todos los detalles de esta venta
        UPDATE inventarios i
        INNER JOIN ventas_detalle vd ON i.id = vd.inventario_id
        SET i.cantidad = COALESCE(i.cantidad, 0) - COALESCE(vd.cantidad, 0),
            i.fecha_actualizacion = NOW()
        WHERE vd.venta_id = NEW.id
        AND vd.cantidad IS NOT NULL
        AND vd.cantidad > 0
        AND COALESCE(i.cantidad, 0) >= vd.cantidad;
    END IF;
    
    -- Si el estado cambió de 'completada' a otro (cancelación o devolución)
    IF OLD.estado = 'completada' AND NEW.estado != 'completada' AND NEW.estado != 'cancelada' THEN
        -- Restaurar el inventario (sumar las cantidades vendidas)
        UPDATE inventarios i
        INNER JOIN ventas_detalle vd ON i.id = vd.inventario_id
        SET i.cantidad = COALESCE(i.cantidad, 0) + COALESCE(vd.cantidad, 0),
            i.fecha_actualizacion = NOW()
        WHERE vd.venta_id = NEW.id
        AND vd.cantidad IS NOT NULL
        AND vd.cantidad > 0;
    END IF;
END$$

DELIMITER ;

-- Verificar que los triggers se crearon correctamente
-- SHOW TRIGGERS LIKE 'trg_%inventario%';

