-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 05, 2025 at 08:05 PM
-- Server version: 11.1.2-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `el_reparador_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `atencion_presupuesto`
--

CREATE TABLE `atencion_presupuesto` (
  `id_reparacion` int(11) NOT NULL,
  `idemp_atencion` int(11) NOT NULL,
  `idemp_presup` int(11) DEFAULT NULL,
  `presupuesto` decimal(9,2) DEFAULT NULL,
  `fecha_ing_electro` date DEFAULT NULL,
  `fecha_env_presup` date DEFAULT NULL,
  `fecha_confirma_re` date DEFAULT NULL,
  `confirm_presup` tinyint(4) DEFAULT 0,
  `estado_presup` varchar(50) DEFAULT 'Presupuesto a Enviar',
  `observaciones` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish2_ci;

--
-- Dumping data for table `atencion_presupuesto`
--

INSERT INTO `atencion_presupuesto` (`id_reparacion`, `idemp_atencion`, `idemp_presup`, `presupuesto`, `fecha_ing_electro`, `fecha_env_presup`, `fecha_confirma_re`, `confirm_presup`, `estado_presup`, `observaciones`) VALUES
(45, 1, 12, 100000.00, '2025-05-22', '2025-06-05', NULL, 0, 'Presupuesto enviado', 'Materiales: - bateria moto edge 30 Detalle de la Reparación: cambiar la bateria, incluye limpieza interna del celular'),
(46, 1, NULL, NULL, '2025-05-22', NULL, NULL, 0, 'Presupuesto a Enviar', NULL),
(47, 1, NULL, NULL, '2025-05-22', NULL, NULL, 0, 'Presupuesto a Enviar', NULL),
(48, 1, NULL, NULL, '2025-05-22', NULL, NULL, 0, 'Presupuesto a Enviar', NULL),
(49, 1, NULL, NULL, '2025-05-22', NULL, NULL, 0, 'Presupuesto a Enviar', NULL),
(50, 1, NULL, NULL, '2025-05-22', NULL, NULL, 0, 'Presupuesto a Enviar', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `calendario`
--

CREATE TABLE `calendario` (
  `idcalendario` int(11) NOT NULL,
  `descripcion_evento` mediumtext NOT NULL,
  `fecha_inicio` date NOT NULL,
  `hora_inicio` time DEFAULT NULL,
  `hora_fin` time DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `idempleados` int(11) NOT NULL,
  `estado_evento` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish2_ci;

--
-- Dumping data for table `calendario`
--

INSERT INTO `calendario` (`idcalendario`, `descripcion_evento`, `fecha_inicio`, `hora_inicio`, `hora_fin`, `fecha_fin`, `idempleados`, `estado_evento`) VALUES
(1, 'reparacion cel samsung - id cliente 4', '2024-11-04', '15:00:00', '18:00:00', '2024-11-18', 1, 0),
(2, 'reparacion lavarropas - cliente id 2', '2024-11-04', '16:00:00', '12:00:00', '2024-11-25', 1, 0),
(3, 'reparacion heladera - id cliente 1', '2024-11-01', '01:00:00', '14:35:51', '2024-11-07', 1, 0),
(5, 'hol', '2024-11-04', '15:00:00', '15:00:00', '2024-11-06', 1, 0),
(6, 'reparacion lavarropas', '2024-10-14', '01:00:00', '01:00:00', '2024-10-22', 1, 0),
(7, 'hola', '2024-09-17', '01:00:00', '01:00:00', '2024-10-01', 1, 0),
(8, 'party', '2024-11-22', '01:00:00', '01:00:00', '2024-11-23', 1, 0),
(9, 'finalizar investigacion ', '2024-11-26', '01:00:00', '01:00:00', '2024-12-04', 1, 0),
(10, 'finalizar investigacion', '2024-11-22', '01:00:00', '01:00:00', '2024-12-03', 1, 0),
(11, 'cualquiera', '2024-11-27', '12:00:00', '15:00:00', '2024-12-02', 1, 0),
(12, 'chau', '2024-10-02', '10:00:00', '11:00:00', '2024-11-23', 1, 0),
(13, 'stefania vergini', '2024-11-12', '12:00:00', '12:00:00', '2024-11-15', 1, 0),
(14, 'stef', '2024-08-06', '11:00:00', '12:00:00', '2024-08-07', 1, 0),
(15, '678', '2024-12-05', '01:00:00', '16:33:00', '2024-12-06', 1, 0),
(16, 'sui generis', '2024-12-04', '15:00:00', '15:00:00', '2024-12-06', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `categorias_empleados`
--

CREATE TABLE `categorias_empleados` (
  `idcategorias_empleados` int(11) NOT NULL,
  `tipo_empleado` varchar(45) NOT NULL,
  `sueldo_bruto` decimal(9,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish2_ci;

--
-- Dumping data for table `categorias_empleados`
--

INSERT INTO `categorias_empleados` (`idcategorias_empleados`, `tipo_empleado`, `sueldo_bruto`) VALUES
(1, 'Tecnico Cat 3', 850000.00),
(2, 'Gerente', 1300000.00),
(3, 'Atencion Cliente', 700000.00),
(4, 'Admin Cat 1', 900000.00),
(5, 'Admin Cat 2', 850000.00),
(6, 'Admin Cat 3', 700000.00),
(7, 'Tecnico Cat 1', 950000.00),
(8, 'Tecnico Cat 2', 900000.00);

-- --------------------------------------------------------

--
-- Table structure for table `clientes`
--

CREATE TABLE `clientes` (
  `idclientes` int(11) NOT NULL,
  `dni_cliente` int(8) NOT NULL,
  `nom_cliente` varchar(60) NOT NULL,
  `ape_cliente` varchar(60) NOT NULL,
  `tel_cliente` varchar(15) NOT NULL,
  `dir_cliente` varchar(150) NOT NULL,
  `email_cliente` varchar(100) NOT NULL,
  `estado_cliente` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish2_ci;

--
-- Dumping data for table `clientes`
--

INSERT INTO `clientes` (`idclientes`, `dni_cliente`, `nom_cliente`, `ape_cliente`, `tel_cliente`, `dir_cliente`, `email_cliente`, `estado_cliente`) VALUES
(1, 25332233, 'pablo', 'pereyra', '44442222', 'av. la plata 540', 'stefanialvergini@gmail.com', 1),
(2, 37223344, 'olivia', 'malganti', '1132887733', 'escalada 1130', 'olimalganti@hotmail.com', 1),
(3, 34556699, 'aylen', 'pierre', '43833505', 'colombres 132', 'pierreaylen@gmail.com', 1),
(4, 30400111, 'luciano', 'tribiani', '43358282', 'cnel. ramon falcon 2589', 'ltribiani@gmail.com', 1),
(5, 17226856, 'maria', 'perez', '1155667722', 'olazabal 3323', 'marperez@gmail.com', 1),
(7, 37217999, 'stefania', 'vergini', '1164777337', 'lalala 1234', 'stefanialvergini@gmail.com', 1);

-- --------------------------------------------------------

--
-- Table structure for table `cobros`
--

CREATE TABLE `cobros` (
  `id_cobro` int(11) NOT NULL,
  `id_reparacion` int(11) NOT NULL,
  `fecha_cobro_inicial` datetime DEFAULT NULL,
  `arancel_fijo_cobrado` decimal(10,0) DEFAULT NULL,
  `medio_pago_inicial` varchar(50) DEFAULT NULL,
  `nro_comprobante_inicial` varchar(100) DEFAULT NULL,
  `fecha_cobro_final` datetime DEFAULT NULL,
  `monto_final_repa` decimal(10,0) DEFAULT NULL,
  `medio_pago_final` varchar(50) DEFAULT NULL,
  `nro_comprobante_final` varchar(50) DEFAULT NULL,
  `observacion` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

--
-- Dumping data for table `cobros`
--

INSERT INTO `cobros` (`id_cobro`, `id_reparacion`, `fecha_cobro_inicial`, `arancel_fijo_cobrado`, `medio_pago_inicial`, `nro_comprobante_inicial`, `fecha_cobro_final`, `monto_final_repa`, `medio_pago_final`, `nro_comprobante_final`, `observacion`) VALUES
(12, 45, '2025-05-22 17:18:39', 20000, 'transferencia', '5548879/6479845hd', NULL, NULL, NULL, NULL, 'de mp a mp'),
(13, 46, '2025-05-22 17:21:05', 20000, 'transferencia', 'ax3344jk325g', NULL, NULL, NULL, NULL, 'de santander a mp'),
(14, 47, '2025-05-22 17:22:48', 10000, 'transferencia', '33ertydfgba334567', NULL, NULL, NULL, NULL, 'de mp a mp'),
(15, 48, '2025-05-22 20:35:11', 20000, 'transferencia', '546788942331--bbvsc--4', NULL, NULL, NULL, NULL, 'de bna a mp'),
(16, 49, '2025-05-22 21:19:45', 15000, 'efectivo', '-', NULL, NULL, NULL, NULL, ''),
(17, 50, '2025-05-22 22:45:30', 15000, 'efectivo', '-', NULL, NULL, NULL, NULL, '');

-- --------------------------------------------------------

--
-- Table structure for table `credenciales`
--

CREATE TABLE `credenciales` (
  `idcredenciales` int(11) NOT NULL,
  `idempleados` int(11) NOT NULL,
  `usuario` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `credenciales`
--

INSERT INTO `credenciales` (`idcredenciales`, `idempleados`, `usuario`, `password`) VALUES
(1, 1, 'ernesabato@gmail.com', '$2y$08$VaUDHFhkbnvN75QxwZVzO.GTBaM16PT5ti6blPihUU9vLtEv5dSfO'),
(3, 2, 'felorca@hotmail.com', '$2y$08$.T7qRJ8qXC6YVCLIJ4zabOzC8gu4/QizS28jmShoUrpjb3bTbY1mi'),
(4, 12, 'jlennon@gmail.com', '$2y$08$AQy5CHUbc3eRWy/pPfS4du0Hjrn53yGueBoRKqjzBgw.CPSfOhsVS'),
(5, 13, 'mbenedetti@gmail.com', '$2y$08$nZ5gf21ky.u7plIMy2cR7ushxefDeujkCz4Qc0DGnFRuWOlxG5kyS');

-- --------------------------------------------------------

--
-- Table structure for table `electrodomesticos`
--

CREATE TABLE `electrodomesticos` (
  `idelectrodomesticos` int(11) NOT NULL,
  `marca` varchar(100) NOT NULL,
  `modelo` varchar(100) NOT NULL,
  `num_serie` varchar(100) DEFAULT NULL,
  `descripcion` longtext DEFAULT NULL,
  `idclientes` int(11) NOT NULL,
  `tipo_electro` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish2_ci;

--
-- Dumping data for table `electrodomesticos`
--

INSERT INTO `electrodomesticos` (`idelectrodomesticos`, `marca`, `modelo`, `num_serie`, `descripcion`, `idclientes`, `tipo_electro`) VALUES
(45, 'motorola', 'edge 30', '555555555555555555555', 'batería agotada', 1, 2),
(46, 'assus', 'ass324', '666666pwert2345hh', 'se apaga la computadora repentinamente', 1, 6),
(47, 'molinex', 'w45', '77755566666', 'no funciona el motor', 1, 5),
(48, 'samsung', 'smarttv542', 'ggggggggwws34567678', 'pantalla rota', 1, 3),
(49, 'lenovo', 'ideapad330', 'llffws34567', 'hfañoier', 1, 6),
(50, 'samsung', 's24', '777777777777777777', 'derthnh', 1, 2);

-- --------------------------------------------------------

--
-- Table structure for table `empleados`
--

CREATE TABLE `empleados` (
  `idempleados` int(11) NOT NULL,
  `dni_empleado` int(8) NOT NULL,
  `nom_empleado` varchar(60) NOT NULL,
  `ape_empleado` varchar(60) NOT NULL,
  `tel_empleado` varchar(15) NOT NULL,
  `email_empleado` varchar(150) NOT NULL,
  `dir_empleado` varchar(150) NOT NULL,
  `idlocal` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `empleados`
--

INSERT INTO `empleados` (`idempleados`, `dni_empleado`, `nom_empleado`, `ape_empleado`, `tel_empleado`, `email_empleado`, `dir_empleado`, `idlocal`) VALUES
(1, 10222222, 'ernesto', 'sabato', '44441111', 'ernesabato@gmail.com', 'av. rivadavia 5501', 2),
(2, 19562223, 'federico', 'garcia lorca', '1162627733', 'felorca@hotmail.com', 'niceto vega 1502', 1),
(3, 14502503, 'luis alberto', 'spinetta', '43813909', 'spinettajade@gmail.com', 'mario bravo 1318', 3),
(4, 23405059, 'Moria', 'Casan', '44287755', 'laonemoria@gmail.com', 'Fitz Roy 1493', 2),
(5, 28927055, 'eric', 'clapton', '43835596', 'claptoneric@hotmail.com', 'bonpland 1322', 1),
(6, 39505733, 'susana', 'gomez', '43813909', 'susigomez@gmail.com', 'formosa 35 8to B', 3),
(10, 42000000, 'Charly', 'Garcia', '1165656565', 'saynomore@gmail.com', 'Ugarteche 5030 3ro B', 1),
(12, 17225056, 'John', 'Lennon', '1156564477', 'jlennon@gmail.com', 'Lope De Vega 1324', 2),
(13, 35266869, 'Mario', 'Benedetti', '1145457896', 'mbenedetti@gmail.com', 'Gracias 1142', 2);

-- --------------------------------------------------------

--
-- Table structure for table `historial_empleados`
--

CREATE TABLE `historial_empleados` (
  `idhistorial_empleados` int(11) NOT NULL,
  `fecha_inicio_puesto` date NOT NULL,
  `fecha_fin_puesto` date DEFAULT NULL,
  `idcategorias_empleados` int(11) NOT NULL,
  `idempleados` int(11) NOT NULL,
  `estado_empleado` tinyint(1) NOT NULL,
  `descripcion_cambio` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish2_ci;

--
-- Dumping data for table `historial_empleados`
--

INSERT INTO `historial_empleados` (`idhistorial_empleados`, `fecha_inicio_puesto`, `fecha_fin_puesto`, `idcategorias_empleados`, `idempleados`, `estado_empleado`, `descripcion_cambio`) VALUES
(1, '2012-05-10', NULL, 2, 1, 1, NULL),
(2, '2018-07-01', NULL, 1, 2, 1, NULL),
(3, '2017-11-22', NULL, 4, 3, 1, NULL),
(4, '2019-04-10', '2024-11-07', 7, 4, 0, 'renuncio'),
(5, '2022-02-18', NULL, 3, 5, 1, NULL),
(6, '2023-09-25', NULL, 5, 6, 1, NULL),
(7, '2024-09-16', NULL, 1, 10, 1, NULL),
(9, '2025-03-10', NULL, 1, 12, 1, NULL),
(10, '2025-05-20', NULL, 8, 13, 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `locales`
--

CREATE TABLE `locales` (
  `idlocal` int(11) NOT NULL,
  `dir_local` varchar(150) NOT NULL,
  `tel_local` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `locales`
--

INSERT INTO `locales` (`idlocal`, `dir_local`, `tel_local`) VALUES
(1, 'Av. La Plata 418', '48311515'),
(2, 'Av. Directorio 5225', '46835554'),
(3, 'Av. Rivadavia 8132', '46625757');

-- --------------------------------------------------------

--
-- Table structure for table `mails`
--

CREATE TABLE `mails` (
  `idmail` int(11) NOT NULL,
  `id_reparacion` int(11) NOT NULL,
  `idemp_envia` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `destinatario_mail` varchar(250) DEFAULT NULL,
  `asunto` varchar(250) DEFAULT NULL,
  `token` varchar(64) NOT NULL,
  `fecha_hora` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

--
-- Dumping data for table `mails`
--

INSERT INTO `mails` (`idmail`, `id_reparacion`, `idemp_envia`, `id_cliente`, `destinatario_mail`, `asunto`, `token`, `fecha_hora`) VALUES
(1, 45, 12, 1, 'stefanialvergini@gmail.com', 'Cotizacion de la Reparacion', '987687c2640aac4680bf91b8562552e9', '2025-06-05 14:56:48');

-- --------------------------------------------------------

--
-- Table structure for table `notificaciones`
--

CREATE TABLE `notificaciones` (
  `id_notificacion` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `mensaje` varchar(500) NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `fue_leido` tinyint(1) DEFAULT 0,
  `fecha_creado` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

--
-- Dumping data for table `notificaciones`
--

INSERT INTO `notificaciones` (`id_notificacion`, `id_usuario`, `mensaje`, `link`, `fue_leido`, `fecha_creado`) VALUES
(1, 12, 'Ha ingresado un nuevo Electrodoméstico que te han asignado. Podrás visualizarlo acá:', '/php/proyectoElReparador/electrodomesticos/inicioElectro.php', 1, '2025-05-22 23:35:11'),
(2, 12, 'Ha ingresado un nuevo Electrodoméstico que te han asignado. Podrás visualizarlo acá:', '/php/proyectoElReparador/electrodomesticos/inicioElectro.php', 1, '2025-05-23 00:19:45'),
(3, 12, 'Ha ingresado un nuevo Electrodoméstico que te han asignado.', '/php/proyectoElReparador/electrodomesticos/inicioElectro.php', 1, '2025-05-23 01:45:30');

-- --------------------------------------------------------

--
-- Table structure for table `pedidos`
--

CREATE TABLE `pedidos` (
  `id_ped` int(11) NOT NULL,
  `idproveedores` int(11) NOT NULL,
  `idempleados` int(11) NOT NULL,
  `fecha_pedido` timestamp NULL DEFAULT current_timestamp(),
  `fecha_ingreso` date DEFAULT NULL,
  `estado_pedido` tinytext DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish2_ci;

--
-- Dumping data for table `pedidos`
--

INSERT INTO `pedidos` (`id_ped`, `idproveedores`, `idempleados`, `fecha_pedido`, `fecha_ingreso`, `estado_pedido`) VALUES
(1, 1, 1, '2025-02-10 13:01:00', '2024-11-04', '0'),
(7, 1, 1, '2025-02-11 14:08:05', NULL, '0'),
(8, 1, 1, '2025-02-10 13:01:07', '2024-10-29', '0'),
(9, 2, 1, '2025-02-11 14:08:10', NULL, '0'),
(10, 1, 3, '2025-02-11 14:08:19', NULL, '0'),
(11, 2, 3, '2025-02-11 14:08:15', NULL, '0'),
(14, 1, 1, '2025-02-10 14:17:24', '2025-02-14', '0'),
(15, 2, 1, '2025-02-17 17:18:59', '2025-02-17', '0'),
(16, 3, 1, '2025-02-17 16:57:53', '2025-02-17', '0'),
(17, 3, 1, '2025-02-17 18:46:37', NULL, '1'),
(18, 1, 2, '2025-02-19 13:08:48', NULL, '1'),
(19, 5, 1, '2025-03-04 18:37:52', NULL, '1');

-- --------------------------------------------------------

--
-- Table structure for table `pedidos_desc`
--

CREATE TABLE `pedidos_desc` (
  `id_pedido` int(11) NOT NULL,
  `idstock` int(11) NOT NULL,
  `cant_pedida` int(11) NOT NULL,
  `cant_ingresa` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish2_ci;

--
-- Dumping data for table `pedidos_desc`
--

INSERT INTO `pedidos_desc` (`id_pedido`, `idstock`, `cant_pedida`, `cant_ingresa`) VALUES
(14, 1, 50, 50),
(14, 5, 3, 2),
(15, 2, 100, 50),
(16, 4, 2, 2),
(17, 4, 2, NULL),
(18, 5, 3, NULL),
(19, 9, 2, NULL),
(19, 10, 2, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `proveedores`
--

CREATE TABLE `proveedores` (
  `idproveedores` int(11) NOT NULL,
  `cuit` varchar(11) NOT NULL,
  `nombre_prov` varchar(100) NOT NULL,
  `tel_prov` varchar(15) NOT NULL,
  `dir_prov` varchar(150) NOT NULL,
  `email_prov` varchar(100) NOT NULL,
  `saldo` decimal(9,2) DEFAULT NULL,
  `estado_prov` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish2_ci;

--
-- Dumping data for table `proveedores`
--

INSERT INTO `proveedores` (`idproveedores`, `cuit`, `nombre_prov`, `tel_prov`, `dir_prov`, `email_prov`, `saldo`, `estado_prov`) VALUES
(1, '30663322333', 'Repuestos Del Plata', '44442222', 'Fonrouge 550', 'repuestosdelplata@gmail.com', 0.00, 1),
(2, '33662483731', 'Ferreteria Fito', '1136368472', 'Jose Bonifacio 233', 'fitoferreteria@hotmail.com', 0.00, 1),
(3, '36221155999', 'Materiales Importados Srl', '58796666', 'Bartolome Mitre 3740', 'ventas@matimportados.com', 0.00, 1),
(4, '11290400111', 'Yo Lo Tengo', '1162323718', 'Av. Juan Bautista Alberdi 2889', 'yolotengo@gmail.com', 0.00, 1),
(5, '13225225225', 'Electronica Mataderos', '1138355451', 'Murguiondo 2555', 'electronicamataderos@gmail.com', 0.00, 1),
(6, '30223223221', 'El Coso Del Cosito', '1165892323', 'Av. Rivadadia 6845', 'cosodecosito@gmail.com', 0.00, 1);

-- --------------------------------------------------------

--
-- Table structure for table `reparaciones`
--

CREATE TABLE `reparaciones` (
  `id_reparacion` int(11) NOT NULL,
  `idelectrodomesticos` int(11) NOT NULL,
  `id_tecnico` int(11) NOT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin_estimada` date DEFAULT NULL,
  `fecha_finalizacion` date DEFAULT NULL,
  `fecha_retiro_electro` date DEFAULT NULL,
  `fecha_finaliza_garantia` date DEFAULT NULL,
  `descripcion_re` longtext DEFAULT NULL,
  `estado_reparacion` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `reparaciones`
--

INSERT INTO `reparaciones` (`id_reparacion`, `idelectrodomesticos`, `id_tecnico`, `fecha_inicio`, `fecha_fin_estimada`, `fecha_finalizacion`, `fecha_retiro_electro`, `fecha_finaliza_garantia`, `descripcion_re`, `estado_reparacion`) VALUES
(45, 45, 12, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(46, 46, 12, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(47, 47, 12, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(48, 48, 12, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(49, 49, 12, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(50, 50, 12, NULL, NULL, NULL, NULL, NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `stock`
--

CREATE TABLE `stock` (
  `idstock` int(11) NOT NULL,
  `descripcion_art` varchar(150) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `tipo_stock` varchar(50) NOT NULL,
  `idproveedores` int(11) NOT NULL,
  `estado_stock` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish2_ci;

--
-- Dumping data for table `stock`
--

INSERT INTO `stock` (`idstock`, `descripcion_art`, `cantidad`, `tipo_stock`, `idproveedores`, `estado_stock`) VALUES
(1, 'tornillos', 60, 'Herramientas', 1, 1),
(2, 'tuercas', 150, 'Herramientas', 2, 1),
(3, 'Tornillos 8cm', 80, 'Insumos', 1, 1),
(4, 'Resma Gloria', 19, 'Libreria', 3, 1),
(5, 'Estaño para soldar (1 klg)', 3, 'Herramientas', 1, 1),
(6, 'Modulo Moto G8 Plus', 15, 'Electronica', 1, 1),
(7, 'Fundas Moto A43', 16, 'Insumos', 1, 1),
(9, 'Display Samsung A54', 0, 'Electronica', 5, 0),
(10, 'Placa Moto Edge 30', 0, 'Herramientas', 5, 0);

-- --------------------------------------------------------

--
-- Table structure for table `tipo_electro`
--

CREATE TABLE `tipo_electro` (
  `idtipo_electro` int(11) NOT NULL,
  `nom_tipo` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish2_ci;

--
-- Dumping data for table `tipo_electro`
--

INSERT INTO `tipo_electro` (`idtipo_electro`, `nom_tipo`) VALUES
(1, 'heladera'),
(2, 'celular'),
(3, 'smart tv'),
(4, 'lavarropas'),
(5, 'licuadora'),
(6, 'notebook'),
(7, 'pc');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `atencion_presupuesto`
--
ALTER TABLE `atencion_presupuesto`
  ADD KEY `empleados_idempleados` (`idemp_atencion`),
  ADD KEY `id_reparacion` (`id_reparacion`),
  ADD KEY `idemp_presup` (`idemp_presup`);

--
-- Indexes for table `calendario`
--
ALTER TABLE `calendario`
  ADD PRIMARY KEY (`idcalendario`),
  ADD KEY `fk_calendario_empleados1_idx` (`idempleados`);

--
-- Indexes for table `categorias_empleados`
--
ALTER TABLE `categorias_empleados`
  ADD PRIMARY KEY (`idcategorias_empleados`);

--
-- Indexes for table `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`idclientes`),
  ADD UNIQUE KEY `dni_cliente_UNIQUE` (`dni_cliente`);

--
-- Indexes for table `cobros`
--
ALTER TABLE `cobros`
  ADD PRIMARY KEY (`id_cobro`),
  ADD KEY `id_reparacion` (`id_reparacion`);

--
-- Indexes for table `credenciales`
--
ALTER TABLE `credenciales`
  ADD PRIMARY KEY (`idcredenciales`),
  ADD KEY `fk_credenciales_empleados1_idx` (`idempleados`);

--
-- Indexes for table `electrodomesticos`
--
ALTER TABLE `electrodomesticos`
  ADD PRIMARY KEY (`idelectrodomesticos`),
  ADD KEY `fk_electrodomesticos_clientes1_idx` (`idclientes`),
  ADD KEY `fk_electrodomesticos_tipo_electro1_idx` (`tipo_electro`);

--
-- Indexes for table `empleados`
--
ALTER TABLE `empleados`
  ADD PRIMARY KEY (`idempleados`),
  ADD KEY `fk_idlocales_idx` (`idlocal`);

--
-- Indexes for table `historial_empleados`
--
ALTER TABLE `historial_empleados`
  ADD PRIMARY KEY (`idhistorial_empleados`),
  ADD KEY `fk_historial_empleados_categorias_empleados1_idx` (`idcategorias_empleados`),
  ADD KEY `fk_historial_empleados_empleados1_idx` (`idempleados`);

--
-- Indexes for table `locales`
--
ALTER TABLE `locales`
  ADD PRIMARY KEY (`idlocal`);

--
-- Indexes for table `mails`
--
ALTER TABLE `mails`
  ADD PRIMARY KEY (`idmail`),
  ADD KEY `id_reparacion` (`id_reparacion`),
  ADD KEY `idclientes` (`id_cliente`);

--
-- Indexes for table `notificaciones`
--
ALTER TABLE `notificaciones`
  ADD PRIMARY KEY (`id_notificacion`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indexes for table `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id_ped`),
  ADD KEY `idempleados` (`idempleados`),
  ADD KEY `idproveedores` (`idproveedores`);

--
-- Indexes for table `pedidos_desc`
--
ALTER TABLE `pedidos_desc`
  ADD KEY `id_ped` (`id_pedido`),
  ADD KEY `idstock` (`idstock`);

--
-- Indexes for table `proveedores`
--
ALTER TABLE `proveedores`
  ADD PRIMARY KEY (`idproveedores`),
  ADD UNIQUE KEY `cuit_UNIQUE` (`cuit`);

--
-- Indexes for table `reparaciones`
--
ALTER TABLE `reparaciones`
  ADD PRIMARY KEY (`id_reparacion`),
  ADD KEY `idelectrodomesticos` (`idelectrodomesticos`),
  ADD KEY `idempleados` (`id_tecnico`);

--
-- Indexes for table `stock`
--
ALTER TABLE `stock`
  ADD PRIMARY KEY (`idstock`),
  ADD KEY `fk_stock_proveedores1_idx` (`idproveedores`);

--
-- Indexes for table `tipo_electro`
--
ALTER TABLE `tipo_electro`
  ADD PRIMARY KEY (`idtipo_electro`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `calendario`
--
ALTER TABLE `calendario`
  MODIFY `idcalendario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `categorias_empleados`
--
ALTER TABLE `categorias_empleados`
  MODIFY `idcategorias_empleados` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `clientes`
--
ALTER TABLE `clientes`
  MODIFY `idclientes` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `cobros`
--
ALTER TABLE `cobros`
  MODIFY `id_cobro` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `credenciales`
--
ALTER TABLE `credenciales`
  MODIFY `idcredenciales` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `electrodomesticos`
--
ALTER TABLE `electrodomesticos`
  MODIFY `idelectrodomesticos` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `empleados`
--
ALTER TABLE `empleados`
  MODIFY `idempleados` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `historial_empleados`
--
ALTER TABLE `historial_empleados`
  MODIFY `idhistorial_empleados` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `locales`
--
ALTER TABLE `locales`
  MODIFY `idlocal` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `mails`
--
ALTER TABLE `mails`
  MODIFY `idmail` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `notificaciones`
--
ALTER TABLE `notificaciones`
  MODIFY `id_notificacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id_ped` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `proveedores`
--
ALTER TABLE `proveedores`
  MODIFY `idproveedores` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `reparaciones`
--
ALTER TABLE `reparaciones`
  MODIFY `id_reparacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `stock`
--
ALTER TABLE `stock`
  MODIFY `idstock` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `tipo_electro`
--
ALTER TABLE `tipo_electro`
  MODIFY `idtipo_electro` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `atencion_presupuesto`
--
ALTER TABLE `atencion_presupuesto`
  ADD CONSTRAINT `fk_atencion_presupuesto_empleados1` FOREIGN KEY (`idemp_atencion`) REFERENCES `empleados` (`idempleados`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `calendario`
--
ALTER TABLE `calendario`
  ADD CONSTRAINT `fk_calendario_empleados1` FOREIGN KEY (`idempleados`) REFERENCES `empleados` (`idempleados`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `credenciales`
--
ALTER TABLE `credenciales`
  ADD CONSTRAINT `fk_credenciales_empleados1` FOREIGN KEY (`idempleados`) REFERENCES `empleados` (`idempleados`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `electrodomesticos`
--
ALTER TABLE `electrodomesticos`
  ADD CONSTRAINT `fk_electrodomesticos_clientes1` FOREIGN KEY (`idclientes`) REFERENCES `clientes` (`idclientes`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_electrodomesticos_tipo_electro1` FOREIGN KEY (`tipo_electro`) REFERENCES `tipo_electro` (`idtipo_electro`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `empleados`
--
ALTER TABLE `empleados`
  ADD CONSTRAINT `idlocal` FOREIGN KEY (`idlocal`) REFERENCES `locales` (`idlocal`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `historial_empleados`
--
ALTER TABLE `historial_empleados`
  ADD CONSTRAINT `fk_historial_empleados_categorias_empleados1` FOREIGN KEY (`idcategorias_empleados`) REFERENCES `categorias_empleados` (`idcategorias_empleados`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_historial_empleados_empleados1` FOREIGN KEY (`idempleados`) REFERENCES `empleados` (`idempleados`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `fk_pedidos_empleados1` FOREIGN KEY (`idempleados`) REFERENCES `empleados` (`idempleados`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_stock_has_empleados_stock1` FOREIGN KEY (`idproveedores`) REFERENCES `stock` (`idstock`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `reparaciones`
--
ALTER TABLE `reparaciones`
  ADD CONSTRAINT `fk_empleados_has_electrodomesticos_electrodomesticos1` FOREIGN KEY (`idelectrodomesticos`) REFERENCES `electrodomesticos` (`idelectrodomesticos`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_reparaciones_empleados1` FOREIGN KEY (`id_tecnico`) REFERENCES `empleados` (`idempleados`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `stock`
--
ALTER TABLE `stock`
  ADD CONSTRAINT `fk_stock_proveedores1` FOREIGN KEY (`idproveedores`) REFERENCES `proveedores` (`idproveedores`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
