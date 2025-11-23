-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: mysql-hermanosyanez.alwaysdata.net
-- Generation Time: Nov 23, 2025 at 02:22 PM
-- Server version: 10.11.14-MariaDB
-- PHP Version: 8.4.11

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

-- --------------------------------------------------------

--
-- Table structure for table `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL COMMENT 'Nombre de la categoría',
  `descripcion` text DEFAULT NULL COMMENT 'Descripción de la categoría',
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `fecha_creacion` timestamp NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabla de categorías';

--
-- Dumping data for table `categorias`
--

INSERT INTO `categorias` (`id`, `nombre`, `descripcion`, `estado`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(1, 'Plásticos', '\'\'Materiales plásticos reciclables\'\'', 'activo', '2025-11-16 05:34:48', '2025-11-20 02:07:36'),
(2, 'Metales', 'Metales ferrosos y no ferrosos', 'activo', '2025-11-16 05:34:48', '2025-11-16 05:34:48'),
(3, 'Papel y Cartón', 'Materiales de papel y cartón', 'activo', '2025-11-16 05:34:48', '2025-11-16 05:34:48'),
(4, 'Vidrio', 'Envases y productos de vidrio', 'activo', '2025-11-16 05:34:48', '2025-11-16 05:34:48'),
(5, 'Electrónicos', 'Desechos electrónicos', 'activo', '2025-11-16 05:34:48', '2025-11-16 05:34:48'),
(6, 'Ropa', 'Fibras de algodón', 'activo', '2025-11-16 07:31:25', '2025-11-16 07:31:25'),
(7, 'electrodomestico', 'cosa de hogar', 'activo', '2025-11-17 00:00:05', '2025-11-17 00:00:05'),
(8, 'Plastico', 'Materiales plásticos reciclable', 'inactivo', '2025-11-17 00:32:18', '2025-11-17 00:32:41'),
(9, 'Caucho', 'Poli', 'activo', '2025-11-19 13:45:11', '2025-11-19 13:45:11'),
(10, 'Madera', 'madera', 'activo', '2025-11-20 02:05:52', '2025-11-20 02:05:52'),
(11, 'Ola', 'Prueba', 'activo', '2025-11-20 02:21:44', '2025-11-20 15:40:40'),
(12, 'Materiales de Construcción', 'Todo material que se usa en casa', 'activo', '2025-11-20 15:57:25', '2025-11-20 15:57:25');

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

--
-- Dumping data for table `compras`
--

INSERT INTO `compras` (`id`, `numero_factura`, `proveedor_id`, `sucursal_id`, `fecha_compra`, `tipo_comprobante`, `subtotal`, `iva`, `descuento`, `total`, `estado`, `notas`, `creado_por`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(1, '00001', 1, 1, '2025-11-19', 'factura', 2.50, 0.00, 0.00, 2.50, 'completada', NULL, 3, '2025-11-19 04:12:18', '2025-11-19 04:12:18'),
(2, '00002', 2, 1, '2025-11-19', 'factura', 2799.50, 0.00, 0.00, 2799.50, 'completada', NULL, 3, '2025-11-19 13:28:46', '2025-11-19 13:28:46'),
(3, '00003', 1, 4, '2025-11-20', 'factura', 50.00, 0.00, 0.00, 50.00, 'completada', NULL, 3, '2025-11-20 05:25:19', '2025-11-20 05:25:19'),
(4, '00004', 1, 4, '2025-11-20', 'factura', 5.15, 0.00, 0.00, 5.15, 'completada', NULL, 3, '2025-11-20 05:27:22', '2025-11-20 05:27:22');

-- --------------------------------------------------------

--
-- Table structure for table `compras_detalle`
--

CREATE TABLE `compras_detalle` (
  `id` int(11) NOT NULL,
  `compra_id` int(11) NOT NULL COMMENT 'ID de la compra',
  `producto_id` int(11) NOT NULL COMMENT 'ID del producto',
  `precio_id` int(11) NOT NULL COMMENT 'ID del precio usado (tipo compra)',
  `cantidad` decimal(10,2) NOT NULL COMMENT 'Cantidad comprada',
  `subtotal` decimal(10,2) NOT NULL COMMENT 'Subtotal (cantidad * precio_unitario)',
  `descripcion` text DEFAULT NULL COMMENT 'Descripción adicional'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Detalle de productos por compra';

--
-- Dumping data for table `compras_detalle`
--

INSERT INTO `compras_detalle` (`id`, `compra_id`, `producto_id`, `precio_id`, `cantidad`, `subtotal`, `descripcion`) VALUES
(1, 1, 15, 93, 1.00, 2.50, NULL),
(2, 2, 7, 13, 12.00, 12.00, NULL),
(3, 2, 15, 61, 1085.00, 2712.50, NULL),
(4, 2, 1, 33, 11.00, 5.50, NULL),
(5, 2, 8, 15, 12.00, 54.00, NULL),
(6, 2, 16, 63, 1.00, 3.00, NULL),
(7, 2, 11, 85, 1.00, 0.20, NULL),
(8, 2, 4, 71, 1.00, 6.50, NULL),
(9, 2, 5, 73, 1.00, 5.80, NULL),
(10, 3, 18, 100, 1000.00, 50.00, NULL),
(11, 4, 18, 100, 103.00, 5.15, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `inventarios`
--

CREATE TABLE `inventarios` (
  `id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL COMMENT 'ID del producto',
  `sucursal_id` int(11) NOT NULL COMMENT 'ID de la sucursal',
  `cantidad` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Cantidad disponible',
  `stock_minimo` decimal(10,2) DEFAULT 0.00 COMMENT 'Stock mínimo para alerta',
  `stock_maximo` decimal(10,2) DEFAULT 0.00 COMMENT 'Stock máximo recomendado',
  `estado` enum('disponible','agotado','reservado','inactivo') NOT NULL DEFAULT 'disponible',
  `fecha_creacion` timestamp NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabla de inventarios por sucursal';

--
-- Dumping data for table `inventarios`
--

INSERT INTO `inventarios` (`id`, `producto_id`, `sucursal_id`, `cantidad`, `stock_minimo`, `stock_maximo`, `estado`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(1, 1, 1, 511.00, 100.00, 1000.00, 'disponible', '2025-11-16 05:34:48', '2025-11-19 13:28:46'),
(2, 4, 1, 201.00, 50.00, 500.00, 'disponible', '2025-11-16 05:34:48', '2025-11-19 13:28:46'),
(3, 6, 1, 300.00, 100.00, 800.00, 'disponible', '2025-11-16 05:34:48', '2025-11-16 05:34:48'),
(4, 10, 1, 1000.00, 200.00, 2000.00, 'disponible', '2025-11-16 05:34:48', '2025-11-16 05:34:48'),
(5, 11, 1, 801.00, 150.00, 1500.00, 'disponible', '2025-11-16 05:34:48', '2025-11-19 13:28:46'),
(6, 1, 2, 300.00, 100.00, 1000.00, 'disponible', '2025-11-16 05:34:48', '2025-11-16 05:34:48'),
(7, 5, 2, 150.00, 50.00, 500.00, 'disponible', '2025-11-16 05:34:48', '2025-11-16 05:34:48'),
(8, 7, 2, 250.00, 100.00, 800.00, 'disponible', '2025-11-16 05:34:48', '2025-11-16 05:34:48'),
(9, 13, 2, 400.00, 100.00, 1000.00, 'disponible', '2025-11-16 05:34:48', '2025-11-16 05:34:48'),
(10, 2, 3, 200.00, 50.00, 500.00, 'disponible', '2025-11-16 05:34:48', '2025-11-16 05:34:48'),
(11, 3, 3, 180.00, 50.00, 500.00, 'disponible', '2025-11-16 05:34:48', '2025-11-16 05:34:48'),
(12, 8, 3, 100.00, 30.00, 300.00, 'disponible', '2025-11-16 05:34:48', '2025-11-16 05:34:48'),
(13, 14, 3, 350.00, 100.00, 1000.00, 'disponible', '2025-11-16 05:34:48', '2025-11-16 05:34:48'),
(40, 15, 1, 1086.00, 0.00, 0.00, 'disponible', '2025-11-19 04:12:18', '2025-11-19 13:28:46'),
(41, 7, 1, 12.00, 0.00, 0.00, 'disponible', '2025-11-19 13:28:46', '2025-11-19 13:28:46'),
(42, 8, 1, 12.00, 0.00, 0.00, 'disponible', '2025-11-19 13:28:46', '2025-11-19 13:28:46'),
(43, 16, 1, 1.00, 0.00, 0.00, 'disponible', '2025-11-19 13:28:46', '2025-11-19 13:28:46'),
(44, 5, 1, 1.00, 0.00, 0.00, 'disponible', '2025-11-19 13:28:46', '2025-11-19 13:28:46'),
(45, 18, 4, 1103.00, 0.00, 0.00, 'disponible', '2025-11-20 05:25:19', '2025-11-20 05:27:22');

-- --------------------------------------------------------

--
-- Table structure for table `materiales`
--

CREATE TABLE `materiales` (
  `id` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `categoria_id` int(11) DEFAULT NULL COMMENT 'ID de la categoría',
  `descripcion` text DEFAULT NULL,
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `fecha_creacion` timestamp NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `materiales`
--

INSERT INTO `materiales` (`id`, `nombre`, `categoria_id`, `descripcion`, `estado`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(1, 'PET', 1, '\"Polietileno Tereftalato - Botellas de plástico\"', 'activo', '2025-11-16 05:34:48', '2025-11-20 02:07:58'),
(2, 'PVC', 1, 'Policloruro de Vinilo', 'activo', '2025-11-16 05:34:48', '2025-11-16 05:34:48'),
(3, 'HDPE', 1, 'Polietileno de Alta Densidad', 'activo', '2025-11-16 05:34:48', '2025-11-16 05:34:48'),
(4, 'LDPE', 1, 'Polietileno de Baja Densidad', 'activo', '2025-11-16 05:34:48', '2025-11-16 05:34:48'),
(5, 'Cobre', 2, 'Cobre puro y aleaciones', 'activo', '2025-11-16 05:34:48', '2025-11-16 05:34:48'),
(6, 'Aluminio', 2, 'Aluminio y latas de aluminio', 'activo', '2025-11-16 05:34:48', '2025-11-16 05:34:48'),
(7, 'Bronce', 2, 'Aleación de cobre y estaño', 'activo', '2025-11-16 05:34:48', '2025-11-16 05:34:48'),
(8, 'Hierro', 2, 'Chatarra de hierro', 'activo', '2025-11-16 05:34:48', '2025-11-16 05:34:48'),
(9, 'Papel Blanco', 3, 'Papel de oficina y documentos', 'activo', '2025-11-16 05:34:48', '2025-11-16 05:34:48'),
(10, 'Cartón', 3, 'Cajas y embalajes de cartón', 'activo', '2025-11-16 05:34:48', '2025-11-16 05:34:48'),
(11, 'Periódico', 3, 'Periódicos y revistas', 'activo', '2025-11-16 05:34:48', '2025-11-16 05:34:48'),
(12, 'Vidrio Verde', 4, 'Botellas de vidrio verde', 'activo', '2025-11-16 05:34:48', '2025-11-16 05:34:48'),
(13, 'Vidrio Transparente', 4, 'Botellas de vidrio transparente', 'activo', '2025-11-16 05:34:48', '2025-11-16 05:34:48'),
(14, 'Baterías', 5, 'Baterías usadas', 'activo', '2025-11-16 05:34:48', '2025-11-16 05:34:48'),
(15, 'Cables', 5, 'Cables eléctricos', 'activo', '2025-11-16 05:34:48', '2025-11-16 05:34:48'),
(16, 'belen', 2, 'dhdshjhdsd', 'activo', '2025-11-16 07:12:44', '2025-11-16 07:12:44'),
(17, 'Camisa', 6, 'Algodón', 'activo', '2025-11-16 07:31:25', '2025-11-16 07:31:25'),
(18, 'Camisa', 6, 'Algodón', 'inactivo', '2025-11-16 07:36:01', '2025-11-16 07:54:59'),
(19, 'Camisa', 6, 'Algodón', 'activo', '2025-11-16 07:54:40', '2025-11-16 07:54:40'),
(20, 'Lavadora', 7, 'digitales', 'activo', '2025-11-17 00:00:54', '2025-11-17 00:00:54'),
(21, 'Caucho quemado', 9, 'Prueba', 'activo', '2025-11-19 13:45:33', '2025-11-19 13:45:33'),
(22, 'Pino', 10, 'carbon', 'activo', '2025-11-20 02:06:43', '2025-11-20 02:06:43'),
(23, 'casi te creo', 11, '-.-', 'activo', '2025-11-20 02:22:14', '2025-11-20 02:22:14'),
(24, 'Obra muerta', 12, 'sAASADASD', 'activo', '2025-11-20 15:59:46', '2025-11-20 15:59:46');

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
-- Table structure for table `precios`
--

CREATE TABLE `precios` (
  `id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL COMMENT 'ID del producto',
  `precio_unitario` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Precio por unidad',
  `tipo_precio` enum('compra','venta','referencia') DEFAULT 'venta' COMMENT 'Tipo de precio',
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `fecha_creacion` timestamp NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabla de precios de productos';

--
-- Dumping data for table `precios`
--

INSERT INTO `precios` (`id`, `producto_id`, `precio_unitario`, `tipo_precio`, `estado`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(1, 1, 0.50, 'compra', 'activo', '2025-11-16 05:34:48', '2025-11-16 05:34:48'),
(2, 1, 0.80, 'venta', 'activo', '2025-11-16 05:34:48', '2025-11-16 05:34:48'),
(3, 2, 0.40, 'compra', 'activo', '2025-11-16 05:34:48', '2025-11-16 05:34:48'),
(4, 2, 0.65, 'venta', 'activo', '2025-11-16 05:34:48', '2025-11-16 05:34:48'),
(5, 3, 0.45, 'compra', 'activo', '2025-11-16 05:34:48', '2025-11-16 05:34:48'),
(6, 3, 0.70, 'venta', 'activo', '2025-11-16 05:34:48', '2025-11-16 05:34:48'),
(7, 4, 6.50, 'compra', 'activo', '2025-11-16 05:34:48', '2025-11-16 05:34:48'),
(8, 4, 8.00, 'venta', 'activo', '2025-11-16 05:34:48', '2025-11-16 05:34:48'),
(9, 5, 5.80, 'compra', 'activo', '2025-11-16 05:34:48', '2025-11-16 05:34:48'),
(10, 5, 7.20, 'venta', 'activo', '2025-11-16 05:34:48', '2025-11-16 05:34:48'),
(11, 6, 1.20, 'compra', 'activo', '2025-11-16 05:34:48', '2025-11-16 05:34:48'),
(12, 6, 1.50, 'venta', 'activo', '2025-11-16 05:34:48', '2025-11-16 05:34:48'),
(13, 7, 1.00, 'compra', 'activo', '2025-11-16 05:34:48', '2025-11-16 05:34:48'),
(14, 7, 1.30, 'venta', 'activo', '2025-11-16 05:34:48', '2025-11-16 05:34:48'),
(15, 8, 4.50, 'compra', 'activo', '2025-11-16 05:34:48', '2025-11-16 05:34:48'),
(16, 8, 5.80, 'venta', 'activo', '2025-11-16 05:34:48', '2025-11-16 05:34:48'),
(17, 9, 0.30, 'compra', 'activo', '2025-11-16 05:34:48', '2025-11-16 05:34:48'),
(18, 9, 0.45, 'venta', 'activo', '2025-11-16 05:34:48', '2025-11-16 05:34:48'),
(19, 10, 0.25, 'compra', 'activo', '2025-11-16 05:34:48', '2025-11-16 05:34:48'),
(20, 10, 0.40, 'venta', 'activo', '2025-11-16 05:34:48', '2025-11-16 05:34:48'),
(21, 11, 0.20, 'compra', 'activo', '2025-11-16 05:34:48', '2025-11-16 05:34:48'),
(22, 11, 0.35, 'venta', 'activo', '2025-11-16 05:34:48', '2025-11-16 05:34:48'),
(23, 12, 0.15, 'compra', 'activo', '2025-11-16 05:34:48', '2025-11-16 05:34:48'),
(24, 12, 0.25, 'venta', 'activo', '2025-11-16 05:34:48', '2025-11-16 05:34:48'),
(25, 13, 0.10, 'compra', 'activo', '2025-11-16 05:34:48', '2025-11-16 05:34:48'),
(26, 13, 0.18, 'venta', 'activo', '2025-11-16 05:34:48', '2025-11-16 05:34:48'),
(27, 14, 0.12, 'compra', 'activo', '2025-11-16 05:34:48', '2025-11-16 05:34:48'),
(28, 14, 0.20, 'venta', 'activo', '2025-11-16 05:34:48', '2025-11-16 05:34:48'),
(29, 15, 2.50, 'compra', 'activo', '2025-11-16 05:34:48', '2025-11-16 05:34:48'),
(30, 15, 3.50, 'venta', 'activo', '2025-11-16 05:34:48', '2025-11-16 05:34:48'),
(31, 16, 3.00, 'compra', 'activo', '2025-11-16 05:34:48', '2025-11-16 05:34:48'),
(32, 16, 4.00, 'venta', 'activo', '2025-11-16 05:34:48', '2025-11-16 05:34:48'),
(33, 1, 0.50, 'compra', 'activo', '2025-11-16 05:46:54', '2025-11-16 05:46:54'),
(34, 1, 0.80, 'venta', 'activo', '2025-11-16 05:46:54', '2025-11-16 05:46:54'),
(35, 2, 0.40, 'compra', 'activo', '2025-11-16 05:46:54', '2025-11-16 05:46:54'),
(36, 2, 0.65, 'venta', 'activo', '2025-11-16 05:46:54', '2025-11-16 05:46:54'),
(37, 3, 0.45, 'compra', 'activo', '2025-11-16 05:46:54', '2025-11-16 05:46:54'),
(38, 3, 0.70, 'venta', 'activo', '2025-11-16 05:46:54', '2025-11-16 05:46:54'),
(39, 4, 6.50, 'compra', 'activo', '2025-11-16 05:46:54', '2025-11-16 05:46:54'),
(40, 4, 8.00, 'venta', 'activo', '2025-11-16 05:46:54', '2025-11-16 05:46:54'),
(41, 5, 5.80, 'compra', 'activo', '2025-11-16 05:46:54', '2025-11-16 05:46:54'),
(42, 5, 7.20, 'venta', 'activo', '2025-11-16 05:46:54', '2025-11-16 05:46:54'),
(43, 6, 1.20, 'compra', 'activo', '2025-11-16 05:46:54', '2025-11-16 05:46:54'),
(44, 6, 1.50, 'venta', 'activo', '2025-11-16 05:46:54', '2025-11-16 05:46:54'),
(45, 7, 1.00, 'compra', 'activo', '2025-11-16 05:46:54', '2025-11-16 05:46:54'),
(46, 7, 1.30, 'venta', 'activo', '2025-11-16 05:46:54', '2025-11-16 05:46:54'),
(47, 8, 4.50, 'compra', 'activo', '2025-11-16 05:46:54', '2025-11-16 05:46:54'),
(48, 8, 5.80, 'venta', 'activo', '2025-11-16 05:46:54', '2025-11-16 05:46:54'),
(49, 9, 0.30, 'compra', 'activo', '2025-11-16 05:46:54', '2025-11-16 05:46:54'),
(50, 9, 0.45, 'venta', 'activo', '2025-11-16 05:46:54', '2025-11-16 05:46:54'),
(51, 10, 0.25, 'compra', 'activo', '2025-11-16 05:46:54', '2025-11-16 05:46:54'),
(52, 10, 0.40, 'venta', 'activo', '2025-11-16 05:46:54', '2025-11-16 05:46:54'),
(53, 11, 0.20, 'compra', 'activo', '2025-11-16 05:46:54', '2025-11-16 05:46:54'),
(54, 11, 0.35, 'venta', 'activo', '2025-11-16 05:46:54', '2025-11-16 05:46:54'),
(55, 12, 0.15, 'compra', 'activo', '2025-11-16 05:46:54', '2025-11-16 05:46:54'),
(56, 12, 0.25, 'venta', 'activo', '2025-11-16 05:46:54', '2025-11-16 05:46:54'),
(57, 13, 0.10, 'compra', 'activo', '2025-11-16 05:46:54', '2025-11-16 05:46:54'),
(58, 13, 0.18, 'venta', 'activo', '2025-11-16 05:46:54', '2025-11-16 05:46:54'),
(59, 14, 0.12, 'compra', 'activo', '2025-11-16 05:46:54', '2025-11-16 05:46:54'),
(60, 14, 0.20, 'venta', 'activo', '2025-11-16 05:46:54', '2025-11-16 05:46:54'),
(61, 15, 2.50, 'compra', 'activo', '2025-11-16 05:46:54', '2025-11-16 05:46:54'),
(62, 15, 3.50, 'venta', 'activo', '2025-11-16 05:46:54', '2025-11-16 05:46:54'),
(63, 16, 3.00, 'compra', 'activo', '2025-11-16 05:46:54', '2025-11-16 05:46:54'),
(64, 16, 4.00, 'venta', 'activo', '2025-11-16 05:46:54', '2025-11-16 05:46:54'),
(65, 1, 0.50, 'compra', 'activo', '2025-11-16 05:47:20', '2025-11-16 05:47:20'),
(66, 1, 0.80, 'venta', 'activo', '2025-11-16 05:47:20', '2025-11-16 05:47:20'),
(67, 2, 0.40, 'compra', 'activo', '2025-11-16 05:47:20', '2025-11-16 05:47:20'),
(68, 2, 0.65, 'venta', 'activo', '2025-11-16 05:47:20', '2025-11-16 05:47:20'),
(69, 3, 0.45, 'compra', 'activo', '2025-11-16 05:47:20', '2025-11-16 05:47:20'),
(70, 3, 0.70, 'venta', 'activo', '2025-11-16 05:47:20', '2025-11-16 05:47:20'),
(71, 4, 6.50, 'compra', 'activo', '2025-11-16 05:47:20', '2025-11-16 05:47:20'),
(72, 4, 8.00, 'venta', 'activo', '2025-11-16 05:47:20', '2025-11-16 05:47:20'),
(73, 5, 5.80, 'compra', 'activo', '2025-11-16 05:47:20', '2025-11-16 05:47:20'),
(74, 5, 7.20, 'venta', 'activo', '2025-11-16 05:47:20', '2025-11-16 05:47:20'),
(75, 6, 1.20, 'compra', 'activo', '2025-11-16 05:47:20', '2025-11-16 05:47:20'),
(76, 6, 1.50, 'venta', 'activo', '2025-11-16 05:47:20', '2025-11-16 05:47:20'),
(77, 7, 1.00, 'compra', 'activo', '2025-11-16 05:47:20', '2025-11-16 05:47:20'),
(78, 7, 1.30, 'venta', 'activo', '2025-11-16 05:47:20', '2025-11-16 05:47:20'),
(79, 8, 4.50, 'compra', 'activo', '2025-11-16 05:47:20', '2025-11-16 05:47:20'),
(80, 8, 5.80, 'venta', 'activo', '2025-11-16 05:47:20', '2025-11-16 05:47:20'),
(81, 9, 0.30, 'compra', 'activo', '2025-11-16 05:47:20', '2025-11-16 05:47:20'),
(82, 9, 0.45, 'venta', 'activo', '2025-11-16 05:47:20', '2025-11-16 05:47:20'),
(83, 10, 0.25, 'compra', 'activo', '2025-11-16 05:47:20', '2025-11-16 05:47:20'),
(84, 10, 0.40, 'venta', 'activo', '2025-11-16 05:47:20', '2025-11-16 05:47:20'),
(85, 11, 0.20, 'compra', 'activo', '2025-11-16 05:47:20', '2025-11-16 05:47:20'),
(86, 11, 0.35, 'venta', 'activo', '2025-11-16 05:47:20', '2025-11-16 05:47:20'),
(87, 12, 0.15, 'compra', 'activo', '2025-11-16 05:47:20', '2025-11-16 05:47:20'),
(88, 12, 0.25, 'venta', 'activo', '2025-11-16 05:47:20', '2025-11-16 05:47:20'),
(89, 13, 0.10, 'compra', 'activo', '2025-11-16 05:47:20', '2025-11-16 05:47:20'),
(90, 13, 0.18, 'venta', 'activo', '2025-11-16 05:47:20', '2025-11-16 05:47:20'),
(91, 14, 0.12, 'compra', 'activo', '2025-11-16 05:47:20', '2025-11-16 05:47:20'),
(92, 14, 0.20, 'venta', 'activo', '2025-11-16 05:47:20', '2025-11-16 05:47:20'),
(93, 15, 2.50, 'compra', 'activo', '2025-11-16 05:47:20', '2025-11-16 05:47:20'),
(94, 15, 3.50, 'venta', 'activo', '2025-11-16 05:47:20', '2025-11-16 05:47:20'),
(95, 16, 3.00, 'compra', 'activo', '2025-11-16 05:47:20', '2025-11-16 05:47:20'),
(96, 16, 4.00, 'venta', 'activo', '2025-11-16 05:47:20', '2025-11-16 05:47:20'),
(97, 17, 0.20, 'venta', 'activo', '2025-11-17 00:04:28', '2025-11-17 00:04:28'),
(98, 17, 0.12, 'compra', 'activo', '2025-11-17 00:04:28', '2025-11-17 00:04:28'),
(99, 18, 0.05, 'venta', 'activo', '2025-11-20 02:34:34', '2025-11-20 02:34:34'),
(100, 18, 0.05, 'compra', 'activo', '2025-11-20 02:34:34', '2025-11-20 02:34:34'),
(101, 19, 0.20, 'venta', 'activo', '2025-11-20 10:39:31', '2025-11-20 10:39:31'),
(102, 19, 0.05, 'compra', 'activo', '2025-11-20 10:39:31', '2025-11-20 10:39:31'),
(103, 20, 2.00, 'venta', 'activo', '2025-11-20 16:01:19', '2025-11-20 16:01:19'),
(104, 20, 0.50, 'compra', 'activo', '2025-11-20 16:01:19', '2025-11-20 16:01:19');

-- --------------------------------------------------------

--
-- Table structure for table `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL COMMENT 'Nombre del producto',
  `material_id` int(11) NOT NULL COMMENT 'ID del material/categoría',
  `unidad_id` int(11) NOT NULL COMMENT 'ID de la unidad de medida',
  `descripcion` text DEFAULT NULL COMMENT 'Descripción adicional',
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `fecha_creacion` timestamp NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabla de productos del sistema';

--
-- Dumping data for table `productos`
--

INSERT INTO `productos` (`id`, `nombre`, `material_id`, `unidad_id`, `descripcion`, `estado`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(1, 'Botellas PET', 1, 1, 'Botellas de plástico PET recicladas', 'activo', '2025-11-16 05:34:48', '2025-11-16 05:34:48'),
(2, 'Envases PVC', 2, 1, 'Envases y productos de PVC', 'activo', '2025-11-16 05:34:48', '2025-11-16 05:34:48'),
(3, 'Contenedores HDPE', 3, 1, 'Contenedores de polietileno de alta densidad', 'activo', '2025-11-16 05:34:48', '2025-11-16 05:34:48'),
(4, 'Cobre Cable', 5, 1, 'Cable de cobre reciclado', 'activo', '2025-11-16 05:34:48', '2025-11-16 05:34:48'),
(5, 'Cobre Chatarra', 5, 1, 'Chatarra de cobre', 'activo', '2025-11-16 05:34:48', '2025-11-16 05:34:48'),
(6, 'Latas de Aluminio', 6, 1, 'Latas de aluminio comprimidas', 'activo', '2025-11-16 05:34:48', '2025-11-16 05:34:48'),
(7, 'Aluminio Chatarra', 6, 1, 'Chatarra de aluminio', 'activo', '2025-11-16 05:34:48', '2025-11-16 05:34:48'),
(8, 'Bronce Reciclado', 7, 1, 'Bronce reciclado', 'activo', '2025-11-16 05:34:48', '2025-11-16 05:34:48'),
(9, 'Hierro Chatarra', 8, 1, 'Chatarra de hierro', 'activo', '2025-11-16 05:34:48', '2025-11-16 05:34:48'),
(10, 'Papel Reciclado', 9, 1, 'Papel blanco reciclado', 'activo', '2025-11-16 05:34:48', '2025-11-16 05:34:48'),
(11, 'Cartón Corrugado', 10, 1, 'Cartón corrugado reciclado', 'activo', '2025-11-16 05:34:48', '2025-11-16 05:34:48'),
(12, 'Periódico Reciclado', 11, 1, 'Periódicos y revistas recicladas', 'activo', '2025-11-16 05:34:48', '2025-11-16 05:34:48'),
(13, 'Vidrio Verde Reciclado', 12, 1, 'Botellas de vidrio verde', 'activo', '2025-11-16 05:34:48', '2025-11-16 05:34:48'),
(14, 'Vidrio Transparente Reciclado', 13, 1, 'Botellas de vidrio transparente', 'activo', '2025-11-16 05:34:48', '2025-11-16 05:34:48'),
(15, 'Baterías Usadas', 14, 3, 'Baterías recicladas', 'activo', '2025-11-16 05:34:48', '2025-11-16 05:34:48'),
(16, 'Cables Eléctricos', 15, 1, 'Cables eléctricos reciclados', 'activo', '2025-11-16 05:34:48', '2025-11-16 05:34:48'),
(17, 'prueba', 10, 2, 'prueba', 'activo', '2025-11-17 00:04:28', '2025-11-17 00:04:28'),
(18, '002', 22, 8, '-.--.-...,', 'activo', '2025-11-20 02:34:34', '2025-11-20 10:40:01'),
(19, '0001', 5, 7, NULL, 'activo', '2025-11-20 10:39:31', '2025-11-20 10:39:31'),
(20, 'Cerámica', 24, 5, 'dqdqwwdqwdqwd', 'activo', '2025-11-20 16:01:19', '2025-11-20 16:01:59');

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
(1, 'Reciclajes del Norte S.A.', '0998765432001', 'ruc', 'Av. 10 de Agosto 234, Quito', '02-2345001', 'contacto@reciclajesnorte.com', 'Juan Pérez', 'recolector', 'PET, Aluminio, Papel', 'activo', NULL, NULL, '2025-11-16 05:34:48', '2025-11-16 05:34:48'),
(2, 'EcoMateriales Ecuador', '0998765432002', 'ruc', 'Av. Colón 567, Quito', '02-2345002', 'ventas@ecomateriales.com', 'María González', 'procesador', 'Cobre, Hierro, Bronce', 'activo', NULL, NULL, '2025-11-16 05:34:48', '2025-11-16 05:34:48'),
(3, 'Reciclaje Express', '0998765432003', 'ruc', 'Av. América 890, Quito', '02-2345003', 'info@reciclajeexpress.com', 'Carlos Ramírez', 'mayorista', 'Cartón, Vidrio, Plásticos', 'activo', NULL, NULL, '2025-11-16 05:34:48', '2025-11-16 05:34:48'),
(4, 'Recolectores Unidos', '0998765432004', 'ruc', 'Calle 24 de Mayo 123, Quito', '02-2345004', 'unidos@recolectores.com', 'Ana Martínez', 'recolector', 'Baterías, Cables, Electrónicos', 'activo', NULL, NULL, '2025-11-16 05:34:48', '2025-11-16 05:34:48');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL COMMENT 'Nombre del rol',
  `descripcion` text DEFAULT NULL COMMENT 'Descripción del rol',
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `fecha_creacion` timestamp NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `permisos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Permisos del rol en formato JSON' CHECK (json_valid(`permisos`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabla de roles del sistema';

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `nombre`, `descripcion`, `estado`, `fecha_creacion`, `fecha_actualizacion`, `permisos`) VALUES
(1, 'Administrador', 'Administrador del sistema con acceso completo', 'activo', '2025-11-16 05:52:34', '2025-11-16 05:52:34', NULL),
(2, 'Gerente', 'Gerente con permisos de gestión y reportes', 'activo', '2025-11-16 05:52:34', '2025-11-16 05:52:34', NULL);

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
(1, 'Sucursal Central', 'Av. Principal 123, Quito', '0951925460', 'central@reciclaje.com', 6, 'activa', '2025-11-16 05:34:48', '2025-11-23 04:00:26'),
(2, 'Sucursal Norte', 'Av. Amazonas 456, Quito', '02-2345679', 'norte@reciclaje.com', NULL, 'activa', '2025-11-16 05:34:48', '2025-11-16 05:34:48'),
(3, 'Sucursal Sur', 'Av. Maldonado 789, Quito', '02-2345680', 'sur@reciclaje.com', NULL, 'activa', '2025-11-16 05:34:48', '2025-11-16 05:34:48'),
(4, 'Montebello', 'Bastion popular bloque 6', '0900', NULL, 6, 'activa', '2025-11-19 14:14:53', '2025-11-19 14:14:53');

-- --------------------------------------------------------

--
-- Table structure for table `unidades`
--

CREATE TABLE `unidades` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL COMMENT 'Nombre de la unidad (kilogramos, litros, etc.)',
  `simbolo` varchar(10) DEFAULT NULL COMMENT 'Símbolo de la unidad (kg, L, und, etc.)',
  `tipo` enum('peso','volumen','longitud','cantidad') DEFAULT 'peso' COMMENT 'Tipo de unidad',
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `fecha_creacion` timestamp NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabla de unidades de medida';

--
-- Dumping data for table `unidades`
--

INSERT INTO `unidades` (`id`, `nombre`, `simbolo`, `tipo`, `estado`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(1, 'Kilogramos', 'kg', 'peso', 'activo', '2025-11-16 04:51:13', '2025-11-16 04:51:13'),
(2, 'Litros', 'L', 'volumen', 'activo', '2025-11-16 04:51:13', '2025-11-16 04:51:13'),
(3, 'Unidades', 'und', 'cantidad', 'activo', '2025-11-16 04:51:13', '2025-11-16 04:51:13'),
(4, 'Toneladas', 'ton', 'peso', 'activo', '2025-11-16 04:51:13', '2025-11-16 04:51:13'),
(5, 'Metros', 'm', 'longitud', 'activo', '2025-11-16 04:51:13', '2025-11-16 04:51:13'),
(6, 'Libras', 'Lb', 'peso', 'activo', '2025-11-17 02:35:08', '2025-11-17 02:35:08'),
(7, 'Gramo', 'Gr', 'longitud', 'activo', '2025-11-19 13:45:55', '2025-11-19 13:45:55'),
(8, 'dinero', '$', 'cantidad', 'activo', '2025-11-20 02:23:46', '2025-11-20 02:23:46');

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
(3, 'Administrador del Sistema', 'admin@sistema.com', '0000000000', '$2y$10$npxsM8vXbrYrbwcKH2JhF.VQkkdiupxdOZEgX6XftdzNFSxRnI1tW', '1234567890', 1, 'activo', '2025-11-16 05:52:34', '2025-11-16 05:52:34'),
(4, 'Gerente del Sistema', 'gerente@sistema.com', '0000000001', '$2y$10$Ta3x0FJu3xP/HrRGKuCb5.RkuJNA2Duj/7f/L0MZGqy.PFSKTXf42', '0987654321', 2, 'activo', '2025-11-16 05:52:34', '2025-11-16 05:52:34'),
(5, 'Clarizza Suarez', 'clarizza_belen@hotmail.com', '0951925460', '$2y$10$Q/ErEFeuIT9wHQcjRoBw2u6nnumOc9NW1CEBQoAcF3rZTYWrqEm4C', '0951925460', 2, 'activo', '2025-11-16 23:31:00', '2025-11-19 01:46:30'),
(6, 'Belen', 'clarizza.suarez.pihuave@uagraria.edu.ec', '0931146724', '$2y$10$A35LktpLfl6et4JX3yy.qujbz2wlMxd92/O43h7rkd5RmCK85RVk2', '+593968145442', 2, 'activo', '2025-11-16 23:33:48', '2025-11-16 23:33:48'),
(7, 'Melina Alexandra Meza Alcívar', 'mezamelina3@gmail.com', '0932381742', '$2y$10$PAowlH72mCqBxnF2IXMMCOcFALcsSRkn17F5YAY7AnsVTXC1ajUD.', '0994373414', 2, 'activo', '2025-11-19 21:35:47', '2025-11-19 21:35:47');

-- --------------------------------------------------------

--
-- Table structure for table `ventas`
--

CREATE TABLE `ventas` (
  `id` int(11) NOT NULL,
  `numero_factura` varchar(50) DEFAULT NULL COMMENT 'Número de factura o comprobante',
  `cliente_nombre` varchar(150) DEFAULT NULL COMMENT 'Nombre del cliente (opcional, sin tabla clientes)',
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
  `producto_id` int(11) NOT NULL COMMENT 'ID del producto',
  `precio_id` int(11) NOT NULL COMMENT 'ID del precio usado (tipo venta)',
  `cantidad` decimal(10,2) NOT NULL COMMENT 'Cantidad vendida',
  `subtotal` decimal(10,2) NOT NULL COMMENT 'Subtotal (cantidad * precio_unitario)',
  `descripcion` text DEFAULT NULL COMMENT 'Descripción adicional'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Detalle de productos por venta';

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_estado` (`estado`),
  ADD KEY `idx_nombre` (`nombre`);

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
  ADD KEY `idx_producto` (`producto_id`),
  ADD KEY `idx_precio` (`precio_id`);

--
-- Indexes for table `inventarios`
--
ALTER TABLE `inventarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_producto_sucursal` (`producto_id`,`sucursal_id`),
  ADD KEY `idx_sucursal` (`sucursal_id`),
  ADD KEY `idx_estado` (`estado`),
  ADD KEY `idx_fecha_actualizacion` (`fecha_actualizacion`),
  ADD KEY `idx_fecha_creacion` (`fecha_creacion`),
  ADD KEY `idx_producto` (`producto_id`);

--
-- Indexes for table `materiales`
--
ALTER TABLE `materiales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_categoria` (`categoria_id`),
  ADD KEY `idx_estado` (`estado`),
  ADD KEY `idx_nombre` (`nombre`);

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
-- Indexes for table `precios`
--
ALTER TABLE `precios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_producto` (`producto_id`),
  ADD KEY `idx_tipo_precio` (`tipo_precio`),
  ADD KEY `idx_estado` (`estado`);

--
-- Indexes for table `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_material` (`material_id`),
  ADD KEY `idx_estado` (`estado`),
  ADD KEY `idx_nombre` (`nombre`),
  ADD KEY `idx_unidad` (`unidad_id`);

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
-- Indexes for table `sucursales`
--
ALTER TABLE `sucursales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_estado` (`estado`),
  ADD KEY `idx_responsable` (`responsable_id`),
  ADD KEY `idx_nombre` (`nombre`);

--
-- Indexes for table `unidades`
--
ALTER TABLE `unidades`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`),
  ADD KEY `idx_tipo` (`tipo`),
  ADD KEY `idx_estado` (`estado`);

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
  ADD KEY `idx_producto` (`producto_id`),
  ADD KEY `idx_precio` (`precio_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `compras`
--
ALTER TABLE `compras`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `compras_detalle`
--
ALTER TABLE `compras_detalle`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `inventarios`
--
ALTER TABLE `inventarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `materiales`
--
ALTER TABLE `materiales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `movimientos_inventario`
--
ALTER TABLE `movimientos_inventario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `precios`
--
ALTER TABLE `precios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=105;

--
-- AUTO_INCREMENT for table `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `proveedores`
--
ALTER TABLE `proveedores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `sucursales`
--
ALTER TABLE `sucursales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `unidades`
--
ALTER TABLE `unidades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

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

--
-- Constraints for dumped tables
--

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
  ADD CONSTRAINT `compras_detalle_ibfk_precio` FOREIGN KEY (`precio_id`) REFERENCES `precios` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `compras_detalle_ibfk_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `inventarios`
--
ALTER TABLE `inventarios`
  ADD CONSTRAINT `inventarios_ibfk_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `inventarios_ibfk_sucursal` FOREIGN KEY (`sucursal_id`) REFERENCES `sucursales` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `materiales`
--
ALTER TABLE `materiales`
  ADD CONSTRAINT `materiales_ibfk_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `movimientos_inventario`
--
ALTER TABLE `movimientos_inventario`
  ADD CONSTRAINT `movimientos_inventario_ibfk_1` FOREIGN KEY (`inventario_id`) REFERENCES `inventarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `movimientos_inventario_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `precios`
--
ALTER TABLE `precios`
  ADD CONSTRAINT `precios_ibfk_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `productos_ibfk_material` FOREIGN KEY (`material_id`) REFERENCES `materiales` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `productos_ibfk_unidad` FOREIGN KEY (`unidad_id`) REFERENCES `unidades` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `proveedores`
--
ALTER TABLE `proveedores`
  ADD CONSTRAINT `proveedores_ibfk_1` FOREIGN KEY (`creado_por`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

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
  ADD CONSTRAINT `ventas_ibfk_2` FOREIGN KEY (`sucursal_id`) REFERENCES `sucursales` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `ventas_ibfk_3` FOREIGN KEY (`creado_por`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `ventas_detalle`
--
ALTER TABLE `ventas_detalle`
  ADD CONSTRAINT `ventas_detalle_ibfk_1` FOREIGN KEY (`venta_id`) REFERENCES `ventas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ventas_detalle_ibfk_2` FOREIGN KEY (`inventario_id`) REFERENCES `inventarios` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `ventas_detalle_ibfk_precio` FOREIGN KEY (`precio_id`) REFERENCES `precios` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `ventas_detalle_ibfk_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
