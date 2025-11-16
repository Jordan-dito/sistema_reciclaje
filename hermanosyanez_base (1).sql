-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: mysql-hermanosyanez.alwaysdata.net
-- Generation Time: Nov 16, 2025 at 04:32 AM
-- Server version: 10.11.14-MariaDB
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hermanosyanez_base`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`438328`@`%` PROCEDURE `sp_inventario_por_material` (IN `p_material_id` INT)   BEGIN
    SELECT 
        i.nombre_producto, m.nombre AS material_nombre,
        mp.nombre AS categoria_padre, s.nombre AS sucursal,
        i.cantidad, i.unidad, i.precio_unitario,
        (i.cantidad * i.precio_unitario) AS valor_total
    FROM inventarios i
    INNER JOIN sucursales s ON i.sucursal_id = s.id
    INNER JOIN materiales m ON i.material_id = m.id
    LEFT JOIN materiales mp ON m.categoria_padre_id = mp.id
    WHERE i.material_id = p_material_id 
    AND i.estado = 'disponible' AND s.estado = 'activa'
    ORDER BY s.nombre, i.nombre_producto;
END$$

CREATE DEFINER=`438328`@`%` PROCEDURE `sp_total_inventario_sucursal` (IN `p_sucursal_id` INT)   BEGIN
    SELECT 
        s.nombre AS sucursal,
        COUNT(i.id) AS total_productos,
        SUM(i.cantidad) AS cantidad_total,
        SUM(i.cantidad * i.precio_unitario) AS valor_total
    FROM sucursales s
    LEFT JOIN inventarios i ON s.id = i.sucursal_id
    WHERE s.id = p_sucursal_id
    GROUP BY s.id, s.nombre;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `clientes`
--

CREATE TABLE `clientes` (
  `id` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL COMMENT 'Nombre o razón social del cliente',
  `cedula_ruc` varchar(20) DEFAULT NULL COMMENT 'Cédula o RUC del cliente',
  `tipo_documento` enum('cedula','ruc','pasaporte','otro') DEFAULT 'cedula' COMMENT 'Tipo de documento',
  `direccion` text DEFAULT NULL COMMENT 'Dirección completa',
  `telefono` varchar(20) DEFAULT NULL COMMENT 'Teléfono de contacto',
  `email` varchar(150) DEFAULT NULL COMMENT 'Correo electrónico',
  `contacto` varchar(100) DEFAULT NULL COMMENT 'Nombre de persona de contacto',
  `tipo_cliente` enum('minorista','mayorista','empresa','institucion') DEFAULT 'minorista' COMMENT 'Tipo de cliente',
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `notas` text DEFAULT NULL COMMENT 'Notas adicionales sobre el cliente',
  `creado_por` int(11) DEFAULT NULL COMMENT 'ID del usuario que creó el registro',
  `fecha_creacion` timestamp NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabla de clientes del sistema';

--
-- Dumping data for table `clientes`
--

INSERT INTO `clientes` (`id`, `nombre`, `cedula_ruc`, `tipo_documento`, `direccion`, `telefono`, `email`, `contacto`, `tipo_cliente`, `estado`, `notas`, `creado_por`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(1, 'Empresa ABC S.A.', '1234567890001', 'ruc', 'Av. Principal 123, Ciudad', '555-1001', 'contacto@empresaabc.com', 'Juan Pérez', 'empresa', 'activo', NULL, 1, '2025-11-06 06:10:12', '2025-11-06 06:10:12'),
(2, 'María González', '0987654321', 'cedula', 'Calle 10 #5-20, Ciudad', '555-1002', 'maria.gonzalez@email.com', 'María González', 'minorista', 'activo', NULL, 1, '2025-11-06 06:10:12', '2025-11-06 06:10:12'),
(3, 'Instituto Educativo XYZ', '9876543210001', 'ruc', 'Av. Educativa 456, Ciudad', '555-1003', 'info@institutoxyz.edu', 'Carlos Rodríguez', 'institucion', 'activo', NULL, 1, '2025-11-06 06:10:12', '2025-11-06 06:10:12');

-- --------------------------------------------------------

--
-- Table structure for table `compras`
--

CREATE TABLE `compras` (
  `id` int(11) NOT NULL,
  `numero_factura` varchar(50) DEFAULT NULL COMMENT 'Número de factura o comprobante',
  `proveedor_id` int(11) NOT NULL COMMENT 'ID del proveedor',
  `sucursal_id` int(11) NOT NULL COMMENT 'ID de la sucursal donde se recibe',
  `fecha_compra` date NOT NULL COMMENT 'Fecha de la compra',
  `tipo_comprobante` enum('factura','boleta','recibo','nota_credito','otro') DEFAULT 'factura' COMMENT 'Tipo de comprobante',
  `subtotal` decimal(10,2) DEFAULT 0.00 COMMENT 'Subtotal de la compra',
  `iva` decimal(10,2) DEFAULT 0.00 COMMENT 'IVA de la compra',
  `descuento` decimal(10,2) DEFAULT 0.00 COMMENT 'Descuento aplicado',
  `total` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Total de la compra',
  `estado` enum('pendiente','completada','cancelada') DEFAULT 'pendiente' COMMENT 'Estado de la compra',
  `notas` text DEFAULT NULL COMMENT 'Notas adicionales',
  `creado_por` int(11) DEFAULT NULL COMMENT 'ID del usuario que creó el registro',
  `fecha_creacion` timestamp NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabla de compras de materiales';

-- --------------------------------------------------------

--
-- Table structure for table `compras_detalle`
--

CREATE TABLE `compras_detalle` (
  `id` int(11) NOT NULL,
  `compra_id` int(11) NOT NULL COMMENT 'ID de la compra',
  `inventario_id` int(11) DEFAULT NULL COMMENT 'ID del inventario (si existe)',
  `nombre_producto` varchar(150) NOT NULL COMMENT 'Nombre del producto/material',
  `material_id` int(11) NOT NULL,
  `cantidad` decimal(10,2) NOT NULL COMMENT 'Cantidad comprada',
  `unidad` enum('kg','litros','unidades','toneladas','metros') NOT NULL DEFAULT 'kg' COMMENT 'Unidad de medida',
  `precio_unitario` decimal(10,2) NOT NULL COMMENT 'Precio por unidad',
  `subtotal` decimal(10,2) NOT NULL COMMENT 'Subtotal (cantidad * precio_unitario)',
  `descripcion` text DEFAULT NULL COMMENT 'Descripción adicional'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Detalle de productos por compra';

-- --------------------------------------------------------

--
-- Table structure for table `inventarios`
--

CREATE TABLE `inventarios` (
  `id` int(11) NOT NULL,
  `sucursal_id` int(11) NOT NULL COMMENT 'ID de la sucursal',
  `nombre_producto` varchar(150) NOT NULL COMMENT 'Nombre del producto/material',
  `material_id` int(11) NOT NULL,
  `cantidad` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Cantidad disponible',
  `unidad` enum('kg','litros','unidades','toneladas','metros') NOT NULL DEFAULT 'kg' COMMENT 'Unidad de medida',
  `precio_unitario` decimal(10,2) DEFAULT 0.00 COMMENT 'Precio por unidad',
  `stock_minimo` decimal(10,2) DEFAULT 0.00 COMMENT 'Stock mínimo para alerta',
  `stock_maximo` decimal(10,2) DEFAULT 0.00 COMMENT 'Stock máximo recomendado',
  `descripcion` text DEFAULT NULL COMMENT 'Descripción adicional',
  `estado` enum('disponible','agotado','reservado','inactivo') NOT NULL DEFAULT 'disponible',
  `creado_por` int(11) DEFAULT NULL COMMENT 'ID del usuario que creó el registro',
  `fecha_creacion` timestamp NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabla de inventarios por sucursal';

--
-- Dumping data for table `inventarios`
--

INSERT INTO `inventarios` (`id`, `sucursal_id`, `nombre_producto`, `material_id`, `cantidad`, `unidad`, `precio_unitario`, `stock_minimo`, `stock_maximo`, `descripcion`, `estado`, `creado_por`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(1, 1, 'Papel Reciclado', 20, 150.50, 'kg', 2.50, 50.00, 500.00, 'Papel reciclado procesado', 'disponible', 1, '2025-11-06 06:10:12', '2025-11-13 03:41:25'),
(2, 1, 'Botellas PET', 10, 85.00, 'kg', 3.00, 30.00, 200.00, 'Botellas de plástico PET', 'disponible', 1, '2025-11-06 06:10:12', '2025-11-13 03:41:25'),
(3, 1, 'Vidrio Verde', 5, 120.75, 'kg', 1.50, 40.00, 300.00, 'Vidrio verde reciclable', 'disponible', 1, '2025-11-06 06:10:12', '2025-11-13 03:41:25'),
(4, 2, 'Papel Reciclado', 20, 95.25, 'kg', 2.50, 50.00, 500.00, 'Papel reciclado procesado', 'disponible', 1, '2025-11-06 06:10:12', '2025-11-13 03:41:25'),
(5, 2, 'Latas de Aluminio', 16, 45.50, 'kg', 4.00, 20.00, 150.00, 'Latas de aluminio compactadas', 'disponible', 1, '2025-11-06 06:10:12', '2025-11-13 03:41:25'),
(6, 3, 'Botellas PET', 10, 60.00, 'kg', 3.00, 30.00, 200.00, 'Botellas de plástico PET', 'disponible', 2, '2025-11-06 06:10:12', '2025-11-13 03:41:25');

--
-- Triggers `inventarios`
--
DELIMITER $$
CREATE TRIGGER `trg_actualizar_estado_inventario` BEFORE UPDATE ON `inventarios` FOR EACH ROW BEGIN
    -- Actualizar estado basado en cantidad y stock mínimo
    IF NEW.cantidad < NEW.stock_minimo AND NEW.stock_minimo > 0 THEN
        SET NEW.estado = 'agotado';
    ELSEIF NEW.cantidad >= NEW.stock_minimo THEN
        SET NEW.estado = 'disponible';
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_registrar_movimiento_inventario` AFTER UPDATE ON `inventarios` FOR EACH ROW BEGIN
    DECLARE v_tipo_movimiento VARCHAR(20);
    DECLARE v_cantidad_movida DECIMAL(10,2);
    
    -- Solo registrar si cambió la cantidad
    IF OLD.cantidad != NEW.cantidad THEN
        -- Determinar el tipo de movimiento
        IF NEW.cantidad > OLD.cantidad THEN
            SET v_tipo_movimiento = 'entrada';
            SET v_cantidad_movida = NEW.cantidad - OLD.cantidad;
        ELSEIF NEW.cantidad < OLD.cantidad THEN
            SET v_tipo_movimiento = 'salida';
            SET v_cantidad_movida = OLD.cantidad - NEW.cantidad;
        ELSE
            SET v_tipo_movimiento = 'ajuste';
            SET v_cantidad_movida = ABS(NEW.cantidad - OLD.cantidad);
        END IF;
        
        -- Insertar en el historial de movimientos
        INSERT INTO movimientos_inventario (
            inventario_id,
            tipo_movimiento,
            cantidad,
            cantidad_anterior,
            cantidad_nueva,
            usuario_id,
            motivo
        ) VALUES (
            NEW.id,
            v_tipo_movimiento,
            v_cantidad_movida,
            OLD.cantidad,
            NEW.cantidad,
            NEW.creado_por,
            CONCAT('Actualización automática de inventario')
        );
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `materiales`
--

CREATE TABLE `materiales` (
  `id` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `categoria_padre_id` int(11) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `icono` varchar(100) DEFAULT NULL,
  `orden` int(11) DEFAULT 0,
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `fecha_creacion` timestamp NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `materiales`
--

INSERT INTO `materiales` (`id`, `nombre`, `categoria_padre_id`, `descripcion`, `icono`, `orden`, `estado`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(1, 'Plástico', NULL, 'Materiales plásticos reciclables', 'fas fa-recycle', 1, 'activo', '2025-11-13 03:41:25', '2025-11-13 03:41:25'),
(2, 'Metales', NULL, 'Metales reciclables', 'fas fa-cog', 2, 'activo', '2025-11-13 03:41:25', '2025-11-13 03:41:25'),
(3, 'Fibroso', NULL, 'Materiales fibrosos (papel, cartón)', 'fas fa-file-alt', 3, 'activo', '2025-11-13 03:41:25', '2025-11-13 03:41:25'),
(4, 'Batería', NULL, 'Baterías reciclables', 'fas fa-battery-half', 4, 'activo', '2025-11-13 03:41:25', '2025-11-13 03:41:25'),
(5, 'Vidrio', NULL, 'Vidrio reciclable', 'fas fa-wine-bottle', 5, 'activo', '2025-11-13 03:41:25', '2025-11-13 03:41:25'),
(6, 'Orgánico', NULL, 'Material orgánico compostable', 'fas fa-leaf', 6, 'activo', '2025-11-13 03:41:25', '2025-11-13 03:41:25'),
(7, 'Electrónico', NULL, 'Residuos electrónicos', 'fas fa-microchip', 7, 'activo', '2025-11-13 03:41:25', '2025-11-13 03:41:25'),
(8, 'Textil', NULL, 'Ropa y textiles', 'fas fa-tshirt', 8, 'activo', '2025-11-13 03:41:25', '2025-11-13 03:41:25'),
(9, 'Otro', NULL, 'Otros materiales', 'fas fa-box', 9, 'activo', '2025-11-13 03:41:25', '2025-11-13 03:41:25'),
(10, 'PET', 1, 'Polietileno Tereftalato', NULL, 1, 'activo', '2025-11-13 03:41:25', '2025-11-13 03:41:25'),
(11, 'Hogar', 1, 'Plásticos de uso doméstico', NULL, 2, 'activo', '2025-11-13 03:41:25', '2025-11-13 03:41:25'),
(12, 'Soplado', 1, 'Plásticos soplados', NULL, 3, 'activo', '2025-11-13 03:41:25', '2025-11-13 03:41:25'),
(13, 'PVC', 1, 'Policloruro de Vinilo', NULL, 4, 'activo', '2025-11-13 03:41:25', '2025-11-13 03:41:25'),
(14, 'Chatarra', 2, 'Chatarra metálica', NULL, 1, 'activo', '2025-11-13 03:41:25', '2025-11-13 03:41:25'),
(15, 'Cobre', 2, 'Cobre reciclable', NULL, 2, 'activo', '2025-11-13 03:41:25', '2025-11-13 03:41:25'),
(16, 'Aluminio', 2, 'Aluminio reciclable', NULL, 3, 'activo', '2025-11-13 03:41:25', '2025-11-13 03:41:25'),
(17, 'Perfil', 2, 'Perfiles metálicos', NULL, 4, 'activo', '2025-11-13 03:41:25', '2025-11-13 03:41:25'),
(18, 'Rayador Cobre Aluminio', 2, 'Mezcla de cobre y aluminio rayado', NULL, 5, 'activo', '2025-11-13 03:41:25', '2025-11-13 03:41:25'),
(19, 'Rayador Aluminio', 2, 'Aluminio rayado', NULL, 6, 'activo', '2025-11-13 03:41:25', '2025-11-13 03:41:25'),
(20, 'Papel', 3, 'Papel reciclable', NULL, 1, 'activo', '2025-11-13 03:41:25', '2025-11-13 03:41:25'),
(21, 'Cartón', 3, 'Cartón reciclable', NULL, 2, 'activo', '2025-11-13 03:41:25', '2025-11-13 03:41:25'),
(22, 'Periódico', 3, 'Periódicos', NULL, 3, 'activo', '2025-11-13 03:41:25', '2025-11-13 03:41:25'),
(23, 'Químico', 3, 'Papel químico', NULL, 4, 'activo', '2025-11-13 03:41:25', '2025-11-13 03:41:25'),
(24, 'Dúplex', 3, 'Papel dúplex', NULL, 5, 'activo', '2025-11-13 03:41:25', '2025-11-13 03:41:25'),
(25, 'Seca', 4, 'Baterías secas', NULL, 1, 'activo', '2025-11-13 03:41:25', '2025-11-13 03:41:25'),
(26, 'Húmeda', 4, 'Baterías húmedas', NULL, 2, 'activo', '2025-11-13 03:41:25', '2025-11-13 03:41:25');

-- --------------------------------------------------------

--
-- Table structure for table `modulos`
--

CREATE TABLE `modulos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL COMMENT 'Nombre del módulo',
  `descripcion` text DEFAULT NULL COMMENT 'Descripción del módulo',
  `ruta` varchar(200) DEFAULT NULL COMMENT 'Ruta del módulo en el sistema',
  `icono` varchar(100) DEFAULT NULL COMMENT 'Icono del módulo (clase de Font Awesome)',
  `orden` int(11) DEFAULT 0 COMMENT 'Orden de aparición en el menú',
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `fecha_creacion` timestamp NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabla de módulos del sistema';

--
-- Dumping data for table `modulos`
--

INSERT INTO `modulos` (`id`, `nombre`, `descripcion`, `ruta`, `icono`, `orden`, `estado`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(1, 'Dashboard', 'Panel principal del sistema', 'Dashboard.php', 'fas fa-home', 1, 'activo', '2025-11-06 06:10:12', '2025-11-06 06:10:12'),
(2, 'Usuarios', 'Gestión de usuarios del sistema', 'usuarios/index.php', 'fas fa-users', 2, 'activo', '2025-11-06 06:10:12', '2025-11-06 06:10:12'),
(3, 'Sucursales', 'Gestión de sucursales', 'sucursales/index.php', 'fas fa-building', 3, 'activo', '2025-11-06 06:10:12', '2025-11-06 06:10:12'),
(4, 'Inventarios', 'Control de inventarios', 'inventarios/index.php', 'fas fa-boxes', 4, 'activo', '2025-11-06 06:10:12', '2025-11-06 06:10:12'),
(5, 'Clientes', 'Gestión de clientes', 'clientes/index.php', 'fas fa-user-tie', 5, 'activo', '2025-11-06 06:10:12', '2025-11-06 06:10:12'),
(6, 'Proveedores', 'Gestión de proveedores', 'proveedores/index.php', 'fas fa-truck', 6, 'activo', '2025-11-06 06:10:12', '2025-11-06 06:10:12'),
(7, 'Compras', 'Registro de compras de materiales', 'compras/index.php', 'fas fa-shopping-cart', 7, 'activo', '2025-11-06 06:10:12', '2025-11-06 06:10:12'),
(8, 'Ventas', 'Registro de ventas de materiales', 'ventas/index.php', 'fas fa-cash-register', 8, 'activo', '2025-11-06 06:10:12', '2025-11-06 06:10:12'),
(9, 'Reportes', 'Reportes y estadísticas', 'reportes/index.php', 'fas fa-chart-bar', 9, 'activo', '2025-11-06 06:10:12', '2025-11-06 06:10:12'),
(10, 'Configuración', 'Configuración del sistema', 'configuracion/index.php', 'fas fa-cog', 10, 'activo', '2025-11-06 06:10:12', '2025-11-06 06:10:12');

-- --------------------------------------------------------

--
-- Table structure for table `movimientos_inventario`
--

CREATE TABLE `movimientos_inventario` (
  `id` int(11) NOT NULL,
  `inventario_id` int(11) NOT NULL COMMENT 'ID del inventario afectado',
  `tipo_movimiento` enum('entrada','salida','ajuste') NOT NULL COMMENT 'Tipo de movimiento',
  `cantidad` decimal(10,2) NOT NULL COMMENT 'Cantidad movida',
  `cantidad_anterior` decimal(10,2) DEFAULT NULL COMMENT 'Cantidad antes del movimiento',
  `cantidad_nueva` decimal(10,2) DEFAULT NULL COMMENT 'Cantidad después del movimiento',
  `motivo` text DEFAULT NULL COMMENT 'Motivo del movimiento',
  `usuario_id` int(11) DEFAULT NULL COMMENT 'Usuario que realizó el movimiento',
  `fecha_movimiento` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Historial de movimientos de inventario';

-- --------------------------------------------------------

--
-- Table structure for table `proveedores`
--

CREATE TABLE `proveedores` (
  `id` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL COMMENT 'Nombre o razón social del proveedor',
  `cedula_ruc` varchar(20) DEFAULT NULL COMMENT 'Cédula o RUC del proveedor',
  `tipo_documento` enum('cedula','ruc','pasaporte','otro') DEFAULT 'ruc' COMMENT 'Tipo de documento',
  `direccion` text DEFAULT NULL COMMENT 'Dirección completa',
  `telefono` varchar(20) DEFAULT NULL COMMENT 'Teléfono de contacto',
  `email` varchar(150) DEFAULT NULL COMMENT 'Correo electrónico',
  `contacto` varchar(100) DEFAULT NULL COMMENT 'Nombre de persona de contacto',
  `tipo_proveedor` enum('recolector','procesador','mayorista','otro') DEFAULT 'recolector' COMMENT 'Tipo de proveedor',
  `materiales_suministra` text DEFAULT NULL COMMENT 'Materiales que suministra el proveedor',
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `notas` text DEFAULT NULL COMMENT 'Notas adicionales sobre el proveedor',
  `creado_por` int(11) DEFAULT NULL COMMENT 'ID del usuario que creó el registro',
  `fecha_creacion` timestamp NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabla de proveedores del sistema';

--
-- Dumping data for table `proveedores`
--

INSERT INTO `proveedores` (`id`, `nombre`, `cedula_ruc`, `tipo_documento`, `direccion`, `telefono`, `email`, `contacto`, `tipo_proveedor`, `materiales_suministra`, `estado`, `notas`, `creado_por`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(1, 'Recicladora del Sur S.A.', '1112223330001', 'ruc', 'Zona Industrial, Calle 50', '555-2001', 'ventas@recicladora.com', 'Pedro Martínez', 'procesador', 'Papel, plástico, vidrio', 'activo', NULL, 1, '2025-11-06 06:10:12', '2025-11-06 06:10:12'),
(2, 'Recolectores Unidos', '4445556660001', 'ruc', 'Barrio Norte, Av. 20', '555-2002', 'info@recolectores.com', 'Ana López', 'recolector', 'Materiales varios', 'activo', NULL, 1, '2025-11-06 06:10:12', '2025-11-06 06:10:12'),
(3, 'Distribuidora de Metales', '7778889990001', 'ruc', 'Polígono Industrial, Sector 5', '555-2003', 'metales@distribuidora.com', 'Luis Fernández', 'mayorista', 'Metales, aluminio, hierro', 'activo', NULL, 1, '2025-11-06 06:10:12', '2025-11-06 06:10:12');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL COMMENT 'Nombre del rol (administrador, usuario)',
  `descripcion` text DEFAULT NULL COMMENT 'Descripción del rol',
  `permisos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Permisos del rol en formato JSON' CHECK (json_valid(`permisos`)),
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `fecha_creacion` timestamp NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabla de roles del sistema';

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `nombre`, `descripcion`, `permisos`, `estado`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(1, 'Administrador', 'Administrador del sistema con acceso completo', '{\"usuarios\": [\"crear\", \"leer\", \"actualizar\", \"eliminar\"], \"sucursales\": [\"crear\", \"leer\", \"actualizar\", \"eliminar\"], \"inventarios\": [\"crear\", \"leer\", \"actualizar\", \"eliminar\"], \"reportes\": [\"ver\", \"exportar\"], \"configuracion\": [\"modificar\"]}', 'activo', '2025-11-06 06:10:12', '2025-11-06 06:10:12'),
(2, 'Gerente', 'Gerente con permisos de gestión y reportes', '{\"usuarios\": [\"leer\", \"actualizar\"], \"sucursales\": [\"crear\", \"leer\", \"actualizar\", \"eliminar\"], \"inventarios\": [\"crear\", \"leer\", \"actualizar\", \"eliminar\"], \"reportes\": [\"ver\", \"exportar\"], \"configuracion\": [\"leer\"]}', 'activo', '2025-11-06 06:10:12', '2025-11-06 06:10:12'),
(3, 'usuario', 'Usuario normal del sistema con acceso limitado', '{\"perfil\": [\"leer\", \"actualizar\"], \"inventarios\": [\"leer\", \"actualizar\"], \"reportes\": [\"ver\"]}', 'activo', '2025-11-06 06:10:12', '2025-11-06 06:10:12');

-- --------------------------------------------------------

--
-- Table structure for table `rol_modulos`
--

CREATE TABLE `rol_modulos` (
  `id` int(11) NOT NULL,
  `rol_id` int(11) NOT NULL COMMENT 'ID del rol',
  `modulo_id` int(11) NOT NULL COMMENT 'ID del módulo',
  `fecha_creacion` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Relación entre roles y módulos accesibles';

--
-- Dumping data for table `rol_modulos`
--

INSERT INTO `rol_modulos` (`id`, `rol_id`, `modulo_id`, `fecha_creacion`) VALUES
(1, 1, 1, '2025-11-06 06:10:12'),
(2, 1, 2, '2025-11-06 06:10:12'),
(3, 1, 3, '2025-11-06 06:10:12'),
(4, 1, 4, '2025-11-06 06:10:12'),
(5, 1, 5, '2025-11-06 06:10:12'),
(6, 1, 6, '2025-11-06 06:10:12'),
(7, 1, 7, '2025-11-06 06:10:12'),
(8, 1, 8, '2025-11-06 06:10:12'),
(9, 1, 9, '2025-11-06 06:10:12'),
(10, 1, 10, '2025-11-06 06:10:12'),
(11, 2, 1, '2025-11-06 06:10:12'),
(12, 2, 3, '2025-11-06 06:10:12'),
(13, 2, 4, '2025-11-06 06:10:12'),
(14, 2, 5, '2025-11-06 06:10:12'),
(15, 2, 6, '2025-11-06 06:10:12'),
(16, 2, 7, '2025-11-06 06:10:12'),
(17, 2, 8, '2025-11-06 06:10:12'),
(18, 2, 9, '2025-11-06 06:10:12'),
(19, 3, 1, '2025-11-06 06:10:12'),
(20, 3, 4, '2025-11-06 06:10:12'),
(21, 3, 9, '2025-11-06 06:10:12');

-- --------------------------------------------------------

--
-- Table structure for table `sucursales`
--

CREATE TABLE `sucursales` (
  `id` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL COMMENT 'Nombre de la sucursal',
  `direccion` text DEFAULT NULL COMMENT 'Dirección completa',
  `telefono` varchar(20) DEFAULT NULL COMMENT 'Teléfono de contacto',
  `email` varchar(150) DEFAULT NULL COMMENT 'Correo electrónico',
  `responsable_id` int(11) DEFAULT NULL COMMENT 'ID del usuario responsable',
  `estado` enum('activa','inactiva') DEFAULT 'activa',
  `fecha_creacion` timestamp NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabla de sucursales';

--
-- Dumping data for table `sucursales`
--

INSERT INTO `sucursales` (`id`, `nombre`, `direccion`, `telefono`, `email`, `responsable_id`, `estado`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(1, 'Sucursal Iguanas', 'W27Q+6Q5, Calle 27 NO, Guayaquil', '555-1000', 'central@sistema.com', 1, 'activa', '2025-11-06 06:10:12', '2025-11-13 12:31:36'),
(2, 'Sucursal Florida', 'Km 7.5, vía a Daule', '555-2000', 'norte@sistema.com', 1, 'activa', '2025-11-06 06:10:12', '2025-11-13 12:31:26'),
(3, 'Sucursal Montebello', 'W357+XF6, Guayaquil', '555-3000', 'sur@sistema.com', 1, 'activa', '2025-11-06 06:10:12', '2025-11-13 12:32:33'),
(4, 'NORTE', 'hdbbdsdhsbd', '5355355', 'jkasbjdsb@sistema.com', 2, 'inactiva', '2025-11-06 11:40:57', '2025-11-13 12:32:45');

-- --------------------------------------------------------

--
-- Table structure for table `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL COMMENT 'Nombre completo del usuario',
  `email` varchar(150) NOT NULL COMMENT 'Correo electrónico',
  `cedula` varchar(20) NOT NULL COMMENT 'Cédula de identidad',
  `password` varchar(255) NOT NULL COMMENT 'Contraseña hasheada',
  `telefono` varchar(20) DEFAULT NULL COMMENT 'Teléfono de contacto',
  `rol_id` int(11) NOT NULL COMMENT 'ID del rol del usuario',
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `fecha_creacion` timestamp NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabla de usuarios del sistema';

--
-- Dumping data for table `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `email`, `cedula`, `password`, `telefono`, `rol_id`, `estado`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(1, 'Administrador del Sistema', 'admin@sistema.com', '0000000000', '$2y$10$5NszNfP7qVVqqixWA.cCR.gOg7/z7Cb.L9T2riaoePLUZFNGfTiKS', '1234567890', 1, 'activo', '2025-11-06 06:10:12', '2025-11-06 06:10:12'),
(2, 'Gerente del Sistema', 'gerente@sistema.com', '0000000001', '$2y$10$WPov5lh4ZElFv4KamSOEcukVdHW/4eoSnME.pQYmvz.8MdQU1Anqy', '0987654321', 2, 'activo', '2025-11-06 06:10:12', '2025-11-06 06:10:12'),
(3, 'Usuario Normal', 'usuario@sistema.com', '0000000002', '$2y$10$WPov5lh4ZElFv4KamSOEcukVdHW/4eoSnME.pQYmvz.8MdQU1Anqy', '0987654322', 3, 'activo', '2025-11-06 06:10:12', '2025-11-06 06:10:12'),
(4, 'cxccxxccx', 'a@sistema.com', '0952491157', '$2y$10$xhNH9HYsIG/HIiTvYAucN.7li5gVTPl6ZFjOAAvjzPB7myCV0NViy', '0990122698', 1, 'inactivo', '2025-11-06 06:45:14', '2025-11-13 04:55:17'),
(5, 'Clarizza Suarez', 'Clarizza_belen@hotmail.com', '0951925460', '$2y$10$cPJy1bLJhnkRTOIHcItyvOuJhXeDsHsZNfN4wUZD4Lx2wXYExS07q', '0968145442', 1, 'activo', '2025-11-06 11:56:41', '2025-11-06 11:56:41'),
(6, 'Thiago Zuñiga', 'clarizzasuarez@gmail.com', '0968145442', '$2y$10$cMUpJV7lU.izxEBaWTsib.eF5sUOjR4M.mB2aqxkRzIif9FK7o4iu', '0968145442', 1, 'inactivo', '2025-11-06 11:57:57', '2025-11-13 12:34:04');

-- --------------------------------------------------------

--
-- Table structure for table `ventas`
--

CREATE TABLE `ventas` (
  `id` int(11) NOT NULL,
  `numero_factura` varchar(50) DEFAULT NULL COMMENT 'Número de factura o comprobante',
  `cliente_id` int(11) NOT NULL COMMENT 'ID del cliente',
  `sucursal_id` int(11) NOT NULL COMMENT 'ID de la sucursal donde se realiza la venta',
  `fecha_venta` date NOT NULL COMMENT 'Fecha de la venta',
  `tipo_comprobante` enum('factura','boleta','recibo','nota_credito','otro') DEFAULT 'factura' COMMENT 'Tipo de comprobante',
  `subtotal` decimal(10,2) DEFAULT 0.00 COMMENT 'Subtotal de la venta',
  `iva` decimal(10,2) DEFAULT 0.00 COMMENT 'IVA de la venta',
  `descuento` decimal(10,2) DEFAULT 0.00 COMMENT 'Descuento aplicado',
  `total` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Total de la venta',
  `metodo_pago` enum('efectivo','transferencia','cheque','tarjeta','credito','otro') DEFAULT 'efectivo' COMMENT 'Método de pago',
  `estado` enum('pendiente','completada','cancelada','devuelta') DEFAULT 'pendiente' COMMENT 'Estado de la venta',
  `notas` text DEFAULT NULL COMMENT 'Notas adicionales',
  `creado_por` int(11) DEFAULT NULL COMMENT 'ID del usuario que creó el registro',
  `fecha_creacion` timestamp NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabla de ventas de materiales';

-- --------------------------------------------------------

--
-- Table structure for table `ventas_detalle`
--

CREATE TABLE `ventas_detalle` (
  `id` int(11) NOT NULL,
  `venta_id` int(11) NOT NULL COMMENT 'ID de la venta',
  `inventario_id` int(11) NOT NULL COMMENT 'ID del inventario vendido',
  `nombre_producto` varchar(150) NOT NULL COMMENT 'Nombre del producto/material',
  `material_id` int(11) NOT NULL,
  `cantidad` decimal(10,2) NOT NULL COMMENT 'Cantidad vendida',
  `unidad` enum('kg','litros','unidades','toneladas','metros') NOT NULL DEFAULT 'kg' COMMENT 'Unidad de medida',
  `precio_unitario` decimal(10,2) NOT NULL COMMENT 'Precio por unidad',
  `subtotal` decimal(10,2) NOT NULL COMMENT 'Subtotal (cantidad * precio_unitario)',
  `descripcion` text DEFAULT NULL COMMENT 'Descripción adicional'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Detalle de productos por venta';

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_inventarios_completos`
-- (See below for the actual view)
--
CREATE TABLE `v_inventarios_completos` (
`id` int(11)
,`nombre_producto` varchar(150)
,`material_id` int(11)
,`material_nombre` varchar(150)
,`categoria_padre_id` int(11)
,`categoria_padre_nombre` varchar(150)
,`cantidad` decimal(10,2)
,`unidad` enum('kg','litros','unidades','toneladas','metros')
,`precio_unitario` decimal(10,2)
,`stock_minimo` decimal(10,2)
,`stock_maximo` decimal(10,2)
,`estado_inventario` enum('disponible','agotado','reservado','inactivo')
,`sucursal_nombre` varchar(150)
,`sucursal_direccion` text
,`responsable_id` int(11)
,`responsable_nombre` varchar(100)
,`creado_por_nombre` varchar(100)
,`fecha_creacion` timestamp
,`fecha_actualizacion` timestamp
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_inventario_por_categoria`
-- (See below for the actual view)
--
CREATE TABLE `v_inventario_por_categoria` (
`material_id` int(11)
,`material_nombre` varchar(150)
,`categoria_padre_nombre` varchar(150)
,`sucursal_nombre` varchar(150)
,`total_items` bigint(21)
,`cantidad_total` decimal(32,2)
,`unidad` enum('kg','litros','unidades','toneladas','metros')
,`valor_total` decimal(42,4)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_modulos_por_rol`
-- (See below for the actual view)
--
CREATE TABLE `v_modulos_por_rol` (
`rol_id` int(11)
,`rol_nombre` varchar(50)
,`modulo_id` int(11)
,`modulo_nombre` varchar(100)
,`modulo_ruta` varchar(200)
,`modulo_icono` varchar(100)
,`modulo_orden` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_resumen_inventarios`
-- (See below for the actual view)
--
CREATE TABLE `v_resumen_inventarios` (
`sucursal_id` int(11)
,`sucursal_nombre` varchar(150)
,`total_productos` bigint(21)
,`productos_disponibles` decimal(22,0)
,`productos_agotados` decimal(22,0)
,`valor_total_estimado` decimal(42,4)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_usuarios_completos`
-- (See below for the actual view)
--
CREATE TABLE `v_usuarios_completos` (
`id` int(11)
,`nombre` varchar(100)
,`email` varchar(150)
,`telefono` varchar(20)
,`estado` enum('activo','inactivo')
,`rol_nombre` varchar(50)
,`rol_descripcion` text
,`fecha_creacion` timestamp
,`fecha_actualizacion` timestamp
);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_nombre` (`nombre`),
  ADD KEY `idx_cedula_ruc` (`cedula_ruc`),
  ADD KEY `idx_estado` (`estado`),
  ADD KEY `idx_tipo_cliente` (`tipo_cliente`),
  ADD KEY `idx_creado_por` (`creado_por`);

--
-- Indexes for table `compras`
--
ALTER TABLE `compras`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_proveedor` (`proveedor_id`),
  ADD KEY `idx_sucursal` (`sucursal_id`),
  ADD KEY `idx_fecha_compra` (`fecha_compra`),
  ADD KEY `idx_estado` (`estado`),
  ADD KEY `idx_numero_factura` (`numero_factura`),
  ADD KEY `idx_creado_por` (`creado_por`);

--
-- Indexes for table `compras_detalle`
--
ALTER TABLE `compras_detalle`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_compra` (`compra_id`),
  ADD KEY `idx_inventario` (`inventario_id`),
  ADD KEY `idx_material` (`material_id`);

--
-- Indexes for table `inventarios`
--
ALTER TABLE `inventarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_sucursal` (`sucursal_id`),
  ADD KEY `idx_estado` (`estado`),
  ADD KEY `idx_creado_por` (`creado_por`),
  ADD KEY `idx_fecha_actualizacion` (`fecha_actualizacion`),
  ADD KEY `idx_nombre_producto` (`nombre_producto`),
  ADD KEY `idx_fecha_creacion` (`fecha_creacion`),
  ADD KEY `idx_material` (`material_id`);

--
-- Indexes for table `materiales`
--
ALTER TABLE `materiales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_categoria_padre` (`categoria_padre_id`),
  ADD KEY `idx_estado` (`estado`),
  ADD KEY `idx_orden` (`orden`),
  ADD KEY `idx_nombre` (`nombre`);

--
-- Indexes for table `modulos`
--
ALTER TABLE `modulos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_estado` (`estado`),
  ADD KEY `idx_orden` (`orden`);

--
-- Indexes for table `movimientos_inventario`
--
ALTER TABLE `movimientos_inventario`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_inventario` (`inventario_id`),
  ADD KEY `idx_usuario` (`usuario_id`),
  ADD KEY `idx_tipo` (`tipo_movimiento`),
  ADD KEY `idx_fecha` (`fecha_movimiento`);

--
-- Indexes for table `proveedores`
--
ALTER TABLE `proveedores`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_nombre` (`nombre`),
  ADD KEY `idx_cedula_ruc` (`cedula_ruc`),
  ADD KEY `idx_estado` (`estado`),
  ADD KEY `idx_tipo_proveedor` (`tipo_proveedor`),
  ADD KEY `idx_creado_por` (`creado_por`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`),
  ADD KEY `idx_estado` (`estado`);

--
-- Indexes for table `rol_modulos`
--
ALTER TABLE `rol_modulos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_rol_modulo` (`rol_id`,`modulo_id`),
  ADD KEY `idx_rol` (`rol_id`),
  ADD KEY `idx_modulo` (`modulo_id`);

--
-- Indexes for table `sucursales`
--
ALTER TABLE `sucursales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_estado` (`estado`),
  ADD KEY `idx_responsable` (`responsable_id`),
  ADD KEY `idx_nombre` (`nombre`);

--
-- Indexes for table `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `cedula` (`cedula`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_cedula` (`cedula`),
  ADD KEY `idx_rol` (`rol_id`),
  ADD KEY `idx_estado` (`estado`),
  ADD KEY `idx_nombre` (`nombre`);

--
-- Indexes for table `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_cliente` (`cliente_id`),
  ADD KEY `idx_sucursal` (`sucursal_id`),
  ADD KEY `idx_fecha_venta` (`fecha_venta`),
  ADD KEY `idx_estado` (`estado`),
  ADD KEY `idx_numero_factura` (`numero_factura`),
  ADD KEY `idx_metodo_pago` (`metodo_pago`),
  ADD KEY `idx_creado_por` (`creado_por`);

--
-- Indexes for table `ventas_detalle`
--
ALTER TABLE `ventas_detalle`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_venta` (`venta_id`),
  ADD KEY `idx_inventario` (`inventario_id`),
  ADD KEY `idx_material` (`material_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `compras`
--
ALTER TABLE `compras`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `compras_detalle`
--
ALTER TABLE `compras_detalle`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventarios`
--
ALTER TABLE `inventarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `materiales`
--
ALTER TABLE `materiales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `modulos`
--
ALTER TABLE `modulos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `movimientos_inventario`
--
ALTER TABLE `movimientos_inventario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `proveedores`
--
ALTER TABLE `proveedores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `rol_modulos`
--
ALTER TABLE `rol_modulos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `sucursales`
--
ALTER TABLE `sucursales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ventas_detalle`
--
ALTER TABLE `ventas_detalle`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

-- --------------------------------------------------------

--
-- Structure for view `v_inventarios_completos`
--
DROP TABLE IF EXISTS `v_inventarios_completos`;

CREATE ALGORITHM=UNDEFINED DEFINER=`438328`@`%` SQL SECURITY DEFINER VIEW `v_inventarios_completos`  AS SELECT `i`.`id` AS `id`, `i`.`nombre_producto` AS `nombre_producto`, `i`.`material_id` AS `material_id`, `m`.`nombre` AS `material_nombre`, `m`.`categoria_padre_id` AS `categoria_padre_id`, `mp`.`nombre` AS `categoria_padre_nombre`, `i`.`cantidad` AS `cantidad`, `i`.`unidad` AS `unidad`, `i`.`precio_unitario` AS `precio_unitario`, `i`.`stock_minimo` AS `stock_minimo`, `i`.`stock_maximo` AS `stock_maximo`, `i`.`estado` AS `estado_inventario`, `s`.`nombre` AS `sucursal_nombre`, `s`.`direccion` AS `sucursal_direccion`, `s`.`responsable_id` AS `responsable_id`, `ur`.`nombre` AS `responsable_nombre`, `uc`.`nombre` AS `creado_por_nombre`, `i`.`fecha_creacion` AS `fecha_creacion`, `i`.`fecha_actualizacion` AS `fecha_actualizacion` FROM (((((`inventarios` `i` join `sucursales` `s` on(`i`.`sucursal_id` = `s`.`id`)) join `materiales` `m` on(`i`.`material_id` = `m`.`id`)) left join `materiales` `mp` on(`m`.`categoria_padre_id` = `mp`.`id`)) left join `usuarios` `ur` on(`s`.`responsable_id` = `ur`.`id`)) left join `usuarios` `uc` on(`i`.`creado_por` = `uc`.`id`)) ;

-- --------------------------------------------------------

--
-- Structure for view `v_inventario_por_categoria`
--
DROP TABLE IF EXISTS `v_inventario_por_categoria`;

CREATE ALGORITHM=UNDEFINED DEFINER=`438328`@`%` SQL SECURITY DEFINER VIEW `v_inventario_por_categoria`  AS SELECT `m`.`id` AS `material_id`, `m`.`nombre` AS `material_nombre`, `mp`.`nombre` AS `categoria_padre_nombre`, `s`.`nombre` AS `sucursal_nombre`, count(`i`.`id`) AS `total_items`, sum(`i`.`cantidad`) AS `cantidad_total`, `i`.`unidad` AS `unidad`, sum(`i`.`cantidad` * `i`.`precio_unitario`) AS `valor_total` FROM (((`inventarios` `i` join `sucursales` `s` on(`i`.`sucursal_id` = `s`.`id`)) join `materiales` `m` on(`i`.`material_id` = `m`.`id`)) left join `materiales` `mp` on(`m`.`categoria_padre_id` = `mp`.`id`)) WHERE `i`.`estado` = 'disponible' AND `s`.`estado` = 'activa' GROUP BY `m`.`id`, `m`.`nombre`, `mp`.`nombre`, `s`.`id`, `s`.`nombre`, `i`.`unidad` ;

-- --------------------------------------------------------

--
-- Structure for view `v_modulos_por_rol`
--
DROP TABLE IF EXISTS `v_modulos_por_rol`;

CREATE ALGORITHM=UNDEFINED DEFINER=`438328`@`%` SQL SECURITY DEFINER VIEW `v_modulos_por_rol`  AS SELECT `r`.`id` AS `rol_id`, `r`.`nombre` AS `rol_nombre`, `m`.`id` AS `modulo_id`, `m`.`nombre` AS `modulo_nombre`, `m`.`ruta` AS `modulo_ruta`, `m`.`icono` AS `modulo_icono`, `m`.`orden` AS `modulo_orden` FROM ((`roles` `r` left join `rol_modulos` `rm` on(`r`.`id` = `rm`.`rol_id`)) left join `modulos` `m` on(`rm`.`modulo_id` = `m`.`id`)) WHERE `m`.`estado` = 'activo' OR `m`.`estado` is null ORDER BY `r`.`nombre` ASC, `m`.`orden` ASC ;

-- --------------------------------------------------------

--
-- Structure for view `v_resumen_inventarios`
--
DROP TABLE IF EXISTS `v_resumen_inventarios`;

CREATE ALGORITHM=UNDEFINED DEFINER=`438328`@`%` SQL SECURITY DEFINER VIEW `v_resumen_inventarios`  AS SELECT `s`.`id` AS `sucursal_id`, `s`.`nombre` AS `sucursal_nombre`, count(`i`.`id`) AS `total_productos`, sum(case when `i`.`estado` = 'disponible' then 1 else 0 end) AS `productos_disponibles`, sum(case when `i`.`estado` = 'agotado' then 1 else 0 end) AS `productos_agotados`, sum(`i`.`cantidad` * `i`.`precio_unitario`) AS `valor_total_estimado` FROM (`sucursales` `s` left join `inventarios` `i` on(`s`.`id` = `i`.`sucursal_id`)) WHERE `s`.`estado` = 'activa' GROUP BY `s`.`id`, `s`.`nombre` ;

-- --------------------------------------------------------

--
-- Structure for view `v_usuarios_completos`
--
DROP TABLE IF EXISTS `v_usuarios_completos`;

CREATE ALGORITHM=UNDEFINED DEFINER=`438328`@`%` SQL SECURITY DEFINER VIEW `v_usuarios_completos`  AS SELECT `u`.`id` AS `id`, `u`.`nombre` AS `nombre`, `u`.`email` AS `email`, `u`.`telefono` AS `telefono`, `u`.`estado` AS `estado`, `r`.`nombre` AS `rol_nombre`, `r`.`descripcion` AS `rol_descripcion`, `u`.`fecha_creacion` AS `fecha_creacion`, `u`.`fecha_actualizacion` AS `fecha_actualizacion` FROM (`usuarios` `u` join `roles` `r` on(`u`.`rol_id` = `r`.`id`)) ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `clientes`
--
ALTER TABLE `clientes`
  ADD CONSTRAINT `clientes_ibfk_1` FOREIGN KEY (`creado_por`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `compras`
--
ALTER TABLE `compras`
  ADD CONSTRAINT `compras_ibfk_1` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedores` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `compras_ibfk_2` FOREIGN KEY (`sucursal_id`) REFERENCES `sucursales` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `compras_ibfk_3` FOREIGN KEY (`creado_por`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `compras_detalle`
--
ALTER TABLE `compras_detalle`
  ADD CONSTRAINT `compras_detalle_ibfk_1` FOREIGN KEY (`compra_id`) REFERENCES `compras` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `compras_detalle_ibfk_2` FOREIGN KEY (`inventario_id`) REFERENCES `inventarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `compras_detalle_ibfk_material` FOREIGN KEY (`material_id`) REFERENCES `materiales` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `inventarios`
--
ALTER TABLE `inventarios`
  ADD CONSTRAINT `inventarios_ibfk_1` FOREIGN KEY (`sucursal_id`) REFERENCES `sucursales` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `inventarios_ibfk_2` FOREIGN KEY (`creado_por`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `inventarios_ibfk_material` FOREIGN KEY (`material_id`) REFERENCES `materiales` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `materiales`
--
ALTER TABLE `materiales`
  ADD CONSTRAINT `materiales_ibfk_1` FOREIGN KEY (`categoria_padre_id`) REFERENCES `materiales` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `movimientos_inventario`
--
ALTER TABLE `movimientos_inventario`
  ADD CONSTRAINT `movimientos_inventario_ibfk_1` FOREIGN KEY (`inventario_id`) REFERENCES `inventarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `movimientos_inventario_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `proveedores`
--
ALTER TABLE `proveedores`
  ADD CONSTRAINT `proveedores_ibfk_1` FOREIGN KEY (`creado_por`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `rol_modulos`
--
ALTER TABLE `rol_modulos`
  ADD CONSTRAINT `rol_modulos_ibfk_1` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `rol_modulos_ibfk_2` FOREIGN KEY (`modulo_id`) REFERENCES `modulos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `sucursales`
--
ALTER TABLE `sucursales`
  ADD CONSTRAINT `sucursales_ibfk_1` FOREIGN KEY (`responsable_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `ventas`
--
ALTER TABLE `ventas`
  ADD CONSTRAINT `ventas_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `ventas_ibfk_2` FOREIGN KEY (`sucursal_id`) REFERENCES `sucursales` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `ventas_ibfk_3` FOREIGN KEY (`creado_por`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `ventas_detalle`
--
ALTER TABLE `ventas_detalle`
  ADD CONSTRAINT `ventas_detalle_ibfk_1` FOREIGN KEY (`venta_id`) REFERENCES `ventas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ventas_detalle_ibfk_2` FOREIGN KEY (`inventario_id`) REFERENCES `inventarios` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `ventas_detalle_ibfk_material` FOREIGN KEY (`material_id`) REFERENCES `materiales` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
