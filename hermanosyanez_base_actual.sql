-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: mysql-hermanosyanez.alwaysdata.net
-- Generation Time: Nov 16, 2025 at 06:25 AM
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

-- --------------------------------------------------------

--
-- Table structure for table `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL COMMENT 'Nombre de la categoría',
  `descripcion` text DEFAULT NULL COMMENT 'Descripción de la categoría',
  `icono` varchar(100) DEFAULT NULL COMMENT 'Icono de la categoría',
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `fecha_creacion` timestamp NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabla de categorías';

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
  `producto_id` int(11) NOT NULL COMMENT 'ID del producto',
  `precio_id` int(11) NOT NULL COMMENT 'ID del precio usado (tipo compra)',
  `cantidad` decimal(10,2) NOT NULL COMMENT 'Cantidad comprada',
  `subtotal` decimal(10,2) NOT NULL COMMENT 'Subtotal (cantidad * precio_unitario)',
  `descripcion` text DEFAULT NULL COMMENT 'Descripción adicional'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Detalle de productos por compra';

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

-- --------------------------------------------------------

--
-- Table structure for table `materiales`
--

CREATE TABLE `materiales` (
  `id` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `categoria_id` int(11) DEFAULT NULL COMMENT 'ID de la categoría',
  `descripcion` text DEFAULT NULL,
  `icono` varchar(100) DEFAULT NULL,
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `fecha_creacion` timestamp NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  `fecha_actualizacion` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabla de roles del sistema';

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
(5, 'Metros', 'm', 'longitud', 'activo', '2025-11-16 04:51:13', '2025-11-16 04:51:13');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `materiales`
--
ALTER TABLE `materiales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `movimientos_inventario`
--
ALTER TABLE `movimientos_inventario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `precios`
--
ALTER TABLE `precios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `proveedores`
--
ALTER TABLE `proveedores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sucursales`
--
ALTER TABLE `sucursales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `unidades`
--
ALTER TABLE `unidades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
