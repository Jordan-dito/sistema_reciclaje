-- Script completo para instalar tabla clientes y triggers de actualización de inventario
-- Sistema de Gestión de Reciclaje
-- Ejecutar este script en orden para configurar todo el sistema

-- ============================================
-- PARTE 1: Crear tabla clientes
-- ============================================

CREATE TABLE IF NOT EXISTS clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_razon_social VARCHAR(255) NOT NULL COMMENT 'Nombre o Razón Social del cliente',
    cedula_ruc VARCHAR(20) UNIQUE COMMENT 'Cédula (10 dígitos) o RUC (13 dígitos)',
    tipo_documento ENUM('cedula', 'ruc', 'pasaporte') DEFAULT 'cedula' COMMENT 'Tipo de documento de identidad',
    email VARCHAR(255) COMMENT 'Correo electrónico del cliente',
    telefono VARCHAR(20) COMMENT 'Teléfono de contacto',
    direccion TEXT COMMENT 'Dirección del cliente',
    persona_contacto VARCHAR(255) COMMENT 'Nombre de la persona de contacto',
    tipo_cliente ENUM('mayorista', 'minorista', 'distribuidor') DEFAULT 'minorista' COMMENT 'Tipo de cliente',
    estado ENUM('activo', 'inactivo') DEFAULT 'activo' COMMENT 'Estado del cliente',
    notas TEXT COMMENT 'Notas adicionales sobre el cliente',
    creado_por INT COMMENT 'ID del usuario que creó el registro',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha de creación del registro',
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Fecha de última actualización',
    
    -- Índices para mejorar el rendimiento
    INDEX idx_cedula_ruc (cedula_ruc),
    INDEX idx_email (email),
    INDEX idx_estado (estado),
    INDEX idx_tipo_cliente (tipo_cliente),
    INDEX idx_fecha_creacion (fecha_creacion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabla de clientes del sistema';

-- ============================================
-- PARTE 2: Crear triggers para actualizar inventario automáticamente
-- ============================================

-- Eliminar triggers si ya existen
DROP TRIGGER IF EXISTS trg_actualizar_inventario_venta;
DROP TRIGGER IF EXISTS trg_completar_venta_actualizar_inventario;

-- Trigger 1: Actualizar inventario cuando se inserta un detalle de venta con estado completada
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

-- Trigger 2: Actualizar inventario cuando se cambia el estado de una venta a completada
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

-- ============================================
-- Verificación
-- ============================================

-- Verificar que la tabla se creó correctamente
SELECT 'Tabla clientes creada exitosamente' AS mensaje;
SELECT COUNT(*) AS total_clientes FROM clientes;

-- Verificar que los triggers se crearon correctamente
SELECT 'Triggers creados exitosamente' AS mensaje;
SHOW TRIGGERS LIKE 'trg_%inventario%';

