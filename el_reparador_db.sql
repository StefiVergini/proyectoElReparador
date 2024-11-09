CREATE DATABASE  IF NOT EXISTS `el_reparador_db` /*!40100 DEFAULT CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish2_ci */;
USE `el_reparador_db`;
-- MySQL dump 10.13  Distrib 5.6.17, for Win32 (x86)
--
-- Host: 127.0.0.1    Database: el_reparador_db
-- ------------------------------------------------------
-- Server version	11.1.2-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `atencion_presupuesto`
--

DROP TABLE IF EXISTS `atencion_presupuesto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `atencion_presupuesto` (
  `clientes_idclientes` int(11) NOT NULL,
  `empleados_idempleados` int(11) NOT NULL,
  `presupuesto` decimal(9,2) NOT NULL,
  `fecha_consulta` date DEFAULT NULL,
  `observaciones` longtext DEFAULT NULL,
  PRIMARY KEY (`clientes_idclientes`,`empleados_idempleados`),
  KEY `fk_clientes_has_empleados_clientes1_idx` (`clientes_idclientes`),
  KEY `fk_atencion_presupuesto_empleados1_idx` (`empleados_idempleados`),
  CONSTRAINT `fk_atencion_presupuesto_empleados1` FOREIGN KEY (`empleados_idempleados`) REFERENCES `empleados` (`idempleados`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_clientes_has_empleados_clientes1` FOREIGN KEY (`clientes_idclientes`) REFERENCES `clientes` (`idclientes`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `atencion_presupuesto`
--

LOCK TABLES `atencion_presupuesto` WRITE;
/*!40000 ALTER TABLE `atencion_presupuesto` DISABLE KEYS */;
/*!40000 ALTER TABLE `atencion_presupuesto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `calendario`
--

DROP TABLE IF EXISTS `calendario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `calendario` (
  `idcalendario` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion_evento` mediumtext NOT NULL,
  `fecha_inicio` date NOT NULL,
  `hora_inicio` time DEFAULT NULL,
  `hora_fin` time DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `idempleados` int(11) NOT NULL,
  `estado_evento` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`idcalendario`),
  KEY `fk_calendario_empleados1_idx` (`idempleados`),
  CONSTRAINT `fk_calendario_empleados1` FOREIGN KEY (`idempleados`) REFERENCES `empleados` (`idempleados`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `calendario`
--

LOCK TABLES `calendario` WRITE;
/*!40000 ALTER TABLE `calendario` DISABLE KEYS */;
INSERT INTO `calendario` VALUES (1,'reparacion cel samsung - id cliente 4','2024-11-04','15:00:00','18:00:00','2024-11-18',2,1),(2,'reparacion lavarropas - cliente id 2','2024-11-04','16:00:00','12:00:00','2024-11-25',2,1),(3,'reparacion heladera - id cliente 1','2024-11-01','01:00:00','23:49:44','2024-11-04',2,0);
/*!40000 ALTER TABLE `calendario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categorias_empleados`
--

DROP TABLE IF EXISTS `categorias_empleados`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categorias_empleados` (
  `idcategorias_empleados` int(11) NOT NULL AUTO_INCREMENT,
  `tipo_empleado` varchar(45) NOT NULL,
  `sueldo_bruto` decimal(9,2) NOT NULL,
  PRIMARY KEY (`idcategorias_empleados`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categorias_empleados`
--

LOCK TABLES `categorias_empleados` WRITE;
/*!40000 ALTER TABLE `categorias_empleados` DISABLE KEYS */;
INSERT INTO `categorias_empleados` VALUES (1,'Tecnico Cat 3',850000.00),(2,'Gerente',1300000.00),(3,'Atencion Cliente',700000.00),(4,'Admin Cat 1',900000.00),(5,'Admin Cat 2',850000.00),(6,'Admin Cat 3',700000.00),(7,'Tecnico Cat 1',950000.00),(8,'Tecnico Cat 2',900000.00);
/*!40000 ALTER TABLE `categorias_empleados` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `clientes`
--

DROP TABLE IF EXISTS `clientes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `clientes` (
  `idclientes` int(11) NOT NULL AUTO_INCREMENT,
  `dni_cliente` int(8) NOT NULL,
  `nom_cliente` varchar(60) NOT NULL,
  `ape_cliente` varchar(60) NOT NULL,
  `tel_cliente` varchar(15) NOT NULL,
  `dir_cliente` varchar(150) NOT NULL,
  `email_cliente` varchar(100) NOT NULL,
  `estado_cliente` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`idclientes`),
  UNIQUE KEY `dni_cliente_UNIQUE` (`dni_cliente`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clientes`
--

LOCK TABLES `clientes` WRITE;
/*!40000 ALTER TABLE `clientes` DISABLE KEYS */;
INSERT INTO `clientes` VALUES (1,25332233,'pablo','pereyra','44442222','av. la plata 540','pablopereyra@gmail.com',NULL),(2,37223344,'olivia','malganti','1132887733','escalada 1130','olimalganti@hotmail.com',NULL),(3,34556699,'aylen','pierre','43833505','colombres 132','pierreaylen@gmail.com',NULL),(4,30400111,'luciano','tribiani','43358282','cnel. ramon falcon 2589','ltribiani@gmail.com',NULL),(5,17226856,'maria','perez','1155667722','olazabal 3323','marperez@gmail.com',1);
/*!40000 ALTER TABLE `clientes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `credenciales`
--

DROP TABLE IF EXISTS `credenciales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `credenciales` (
  `idcredenciales` int(11) NOT NULL AUTO_INCREMENT,
  `idempleados` int(11) NOT NULL,
  `usuario` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`idcredenciales`),
  KEY `fk_credenciales_empleados1_idx` (`idempleados`),
  CONSTRAINT `fk_credenciales_empleados1` FOREIGN KEY (`idempleados`) REFERENCES `empleados` (`idempleados`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `credenciales`
--

LOCK TABLES `credenciales` WRITE;
/*!40000 ALTER TABLE `credenciales` DISABLE KEYS */;
INSERT INTO `credenciales` VALUES (1,1,'ernesabato@gmail.com','$2y$10$vHykAhVish.QMVRx5ZQEKucI0O.YHpBlMAUFDZYaQJLc3v/7CZTeO');
/*!40000 ALTER TABLE `credenciales` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `electrodomesticos`
--

DROP TABLE IF EXISTS `electrodomesticos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `electrodomesticos` (
  `idelectrodomesticos` int(11) NOT NULL AUTO_INCREMENT,
  `marca` varchar(100) NOT NULL,
  `modelo` varchar(100) NOT NULL,
  `num_serie` varchar(100) NOT NULL,
  `descripcion` longtext NOT NULL,
  `idclientes` int(11) NOT NULL,
  `tipo_electro` int(11) NOT NULL,
  PRIMARY KEY (`idelectrodomesticos`),
  KEY `fk_electrodomesticos_clientes1_idx` (`idclientes`),
  KEY `fk_electrodomesticos_tipo_electro1_idx` (`tipo_electro`),
  CONSTRAINT `fk_electrodomesticos_clientes1` FOREIGN KEY (`idclientes`) REFERENCES `clientes` (`idclientes`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_electrodomesticos_tipo_electro1` FOREIGN KEY (`tipo_electro`) REFERENCES `tipo_electro` (`idtipo_electro`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `electrodomesticos`
--

LOCK TABLES `electrodomesticos` WRITE;
/*!40000 ALTER TABLE `electrodomesticos` DISABLE KEYS */;
INSERT INTO `electrodomesticos` VALUES (2,'samsung','a54','1155115wwww1','display roto',2,2),(3,'molinex','h121','1114444errr5','motor roto',4,5),(4,'assus','ryzer3456','1114444999yert5','cambio de disco rigido por solido',3,6),(5,'lenovo','ideapad330','1114444jjjj','agregar memoria ram 8GB',1,6),(6,'whirpool','w45','111444gg5','reparar placa',2,4),(7,'samsung','s456','11233445456','display roto',4,3),(8,'lenovo','teclado33','1123344000000','jajjajaja',1,7),(9,'molinex','mm444','123456767jjje','motor roto',1,5),(10,'whirpool','23sw','aaaa12111','nsoweihf',2,1),(11,'mouse','logitech','1123233ssdddd','jajajaja',3,7),(20,'PHILIPS','LUMEA','12345','FALLA LA LAMPARA',1,7),(21,'Motorola','E6 Plus','12346','No funciona modulo',2,2),(22,'samsung','s25','45454545','no enciende',3,2),(23,'Candy','ca111','12345678','enchufe roto',1,4),(24,'Xiaomi','xi1234','5454545','klsajdklasjdk',2,2),(25,'motorola','g52','545454','kldjfklds',1,2),(26,'Samsung','s125','123456','pantalla rota',4,3),(27,'candy','ca45454','45454545','kjshnjdhndask',1,4),(28,'kldjkls','jsdhkjsdh','5454564','kjhdkjfh',2,3),(29,'oster','kfghkldsh','5654654654','kfnhgkldf',1,5);
/*!40000 ALTER TABLE `electrodomesticos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `empleados`
--

DROP TABLE IF EXISTS `empleados`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `empleados` (
  `idempleados` int(11) NOT NULL AUTO_INCREMENT,
  `dni_empleado` int(8) NOT NULL,
  `nom_empleado` varchar(60) NOT NULL,
  `ape_empleado` varchar(60) NOT NULL,
  `tel_empleado` varchar(15) NOT NULL,
  `email_empleado` varchar(150) NOT NULL,
  `dir_empleado` varchar(150) NOT NULL,
  `idlocal` int(11) NOT NULL,
  PRIMARY KEY (`idempleados`),
  KEY `fk_idlocales_idx` (`idlocal`),
  CONSTRAINT `idlocal` FOREIGN KEY (`idlocal`) REFERENCES `locales` (`idlocal`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `empleados`
--

LOCK TABLES `empleados` WRITE;
/*!40000 ALTER TABLE `empleados` DISABLE KEYS */;
INSERT INTO `empleados` VALUES (1,10222222,'ernesto','sabato','44441111','ernesabato@gmail.com','av. rivadavia 5501',2),(2,19562223,'federico','garcia lorca','1162627733','felorca@hotmail.com','niceto vega 1502',1),(3,14502503,'luis alberto','spinetta','43813909','spinettajade@gmail.com','mario bravo 1318',3),(4,23405059,'Moria','Casan','44287755','laonemoria@gmail.com','Fitz Roy 1493',2),(5,28927055,'eric','clapton','43835596','claptoneric@hotmail.com','bonpland 1322',1),(6,39505733,'susana','gomez','43813909','susigomez@gmail.com','formosa 35 8to B',3),(10,42000000,'Charly','Garcia','1165656565','saynomore@gmail.com','Ugarteche 5030 3ro B',1);
/*!40000 ALTER TABLE `empleados` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `historial_empleados`
--

DROP TABLE IF EXISTS `historial_empleados`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `historial_empleados` (
  `idhistorial_empleados` int(11) NOT NULL AUTO_INCREMENT,
  `fecha_inicio_puesto` date NOT NULL,
  `fecha_fin_puesto` date DEFAULT NULL,
  `idcategorias_empleados` int(11) NOT NULL,
  `idempleados` int(11) NOT NULL,
  `estado_empleado` tinyint(1) NOT NULL,
  `descripcion_cambio` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`idhistorial_empleados`),
  KEY `fk_historial_empleados_categorias_empleados1_idx` (`idcategorias_empleados`),
  KEY `fk_historial_empleados_empleados1_idx` (`idempleados`),
  CONSTRAINT `fk_historial_empleados_categorias_empleados1` FOREIGN KEY (`idcategorias_empleados`) REFERENCES `categorias_empleados` (`idcategorias_empleados`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_historial_empleados_empleados1` FOREIGN KEY (`idempleados`) REFERENCES `empleados` (`idempleados`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `historial_empleados`
--

LOCK TABLES `historial_empleados` WRITE;
/*!40000 ALTER TABLE `historial_empleados` DISABLE KEYS */;
INSERT INTO `historial_empleados` VALUES (1,'2012-05-10',NULL,2,1,1,NULL),(2,'2018-07-01',NULL,1,2,1,NULL),(3,'2017-11-22',NULL,4,3,1,NULL),(4,'2019-04-10',NULL,7,4,1,NULL),(5,'2022-02-18',NULL,3,5,1,NULL),(6,'2023-09-25',NULL,5,6,1,NULL),(7,'2024-09-16',NULL,1,10,1,NULL);
/*!40000 ALTER TABLE `historial_empleados` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `locales`
--

DROP TABLE IF EXISTS `locales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `locales` (
  `idlocal` int(11) NOT NULL AUTO_INCREMENT,
  `dir_local` varchar(150) NOT NULL,
  `tel_local` varchar(15) NOT NULL,
  PRIMARY KEY (`idlocal`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `locales`
--

LOCK TABLES `locales` WRITE;
/*!40000 ALTER TABLE `locales` DISABLE KEYS */;
INSERT INTO `locales` VALUES (1,'Av. La Plata 418','48311515'),(2,'Av. Directorio 5225','46835554'),(3,'Av. Rivadavia 8132','46625757');
/*!40000 ALTER TABLE `locales` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pedidos`
--

DROP TABLE IF EXISTS `pedidos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pedidos` (
  `id_ped` int(11) NOT NULL AUTO_INCREMENT,
  `id_stock` int(11) NOT NULL,
  `idempleados` int(11) NOT NULL,
  `fecha_pedido` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `fecha_ingreso` date DEFAULT NULL,
  `cantidad_pedido` int(11) NOT NULL,
  `precio_pedido` int(11) DEFAULT NULL,
  `estado_pedido` tinytext DEFAULT '1',
  PRIMARY KEY (`id_ped`),
  KEY `idempleados` (`idempleados`),
  KEY `idstock` (`id_stock`),
  CONSTRAINT `fk_pedidos_empleados1` FOREIGN KEY (`idempleados`) REFERENCES `empleados` (`idempleados`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_stock_has_empleados_stock1` FOREIGN KEY (`id_stock`) REFERENCES `stock` (`idstock`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pedidos`
--

LOCK TABLES `pedidos` WRITE;
/*!40000 ALTER TABLE `pedidos` DISABLE KEYS */;
INSERT INTO `pedidos` VALUES (1,1,1,'2024-11-04 22:24:44','2024-11-04',20,200,'0'),(7,1,1,'2024-10-31 20:10:15',NULL,45,8000,'1'),(8,1,1,'2024-10-17 19:33:32','2024-10-29',1,1,'0'),(9,2,1,'0000-00-00 00:00:00',NULL,8,800,'1'),(10,1,3,'2024-09-30 15:52:59',NULL,100,2500,'1'),(11,2,3,'2024-09-30 17:40:23',NULL,3,5000,'1');
/*!40000 ALTER TABLE `pedidos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `proveedores`
--

DROP TABLE IF EXISTS `proveedores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `proveedores` (
  `idproveedores` int(11) NOT NULL AUTO_INCREMENT,
  `cuit` varchar(11) NOT NULL,
  `nombre_prov` varchar(100) NOT NULL,
  `tel_prov` varchar(15) NOT NULL,
  `dir_prov` varchar(150) NOT NULL,
  `email_prov` varchar(100) NOT NULL,
  `saldo` decimal(9,2) DEFAULT NULL,
  `estado_prov` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`idproveedores`),
  UNIQUE KEY `cuit_UNIQUE` (`cuit`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `proveedores`
--

LOCK TABLES `proveedores` WRITE;
/*!40000 ALTER TABLE `proveedores` DISABLE KEYS */;
INSERT INTO `proveedores` VALUES (1,'30663322333','Repuestos Del Plata','44442222','Fonrouge 550','repuestosdelplata@gmail.com',0.00,1),(2,'33662483731','Ferreteria Fito','1136368472','Jose Bonifacio 233','fitoferreteria@hotmail.com',0.00,1),(3,'36221155999','Materiales Importados Srl','58796666','Bartolome Mitre 3740','ventas@matimportados.com',0.00,1),(4,'11290400111','Yo Lo Tengo','1162323718','Av. Juan Bautista Alberdi 2889','yolotengo@gmail.com',0.00,1),(5,'13225225225','Electronica Mataderos','1138355451','Murguiondo 2555','electronicamataderos@gmail.com',0.00,1),(6,'30223223221','El Coso Del Cosito','1165892323','Av. Rivadadia 6845','cosodecosito@gmail.com',0.00,1);
/*!40000 ALTER TABLE `proveedores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reparaciones`
--

DROP TABLE IF EXISTS `reparaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reparaciones` (
  `id_reparacion` int(11) NOT NULL AUTO_INCREMENT,
  `idelectrodomesticos` int(11) NOT NULL,
  `idempleados` int(11) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin_estimada` date NOT NULL,
  `fecha_finalizacion` date DEFAULT NULL,
  `fecha_retiro_electro` date DEFAULT NULL,
  `fecha_finaliza_garantia` date DEFAULT NULL,
  `estado_reparacion` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_reparacion`),
  KEY `idelectrodomesticos` (`idelectrodomesticos`),
  KEY `idempleados` (`idempleados`),
  CONSTRAINT `fk_empleados_has_electrodomesticos_electrodomesticos1` FOREIGN KEY (`idelectrodomesticos`) REFERENCES `electrodomesticos` (`idelectrodomesticos`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_reparaciones_empleados1` FOREIGN KEY (`idempleados`) REFERENCES `empleados` (`idempleados`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reparaciones`
--

LOCK TABLES `reparaciones` WRITE;
/*!40000 ALTER TABLE `reparaciones` DISABLE KEYS */;
INSERT INTO `reparaciones` VALUES (1,2,1,'2024-11-04','2024-11-09',NULL,NULL,NULL,1),(2,3,1,'2024-11-04','2024-11-09',NULL,NULL,NULL,1),(3,4,1,'2024-11-04','2024-11-09',NULL,NULL,NULL,1),(4,5,1,'2024-11-04','2024-11-09',NULL,NULL,NULL,1),(5,6,1,'2024-11-04','2024-11-09',NULL,NULL,NULL,1),(6,7,1,'2024-11-04','2024-11-09',NULL,NULL,NULL,1),(7,8,1,'2024-11-04','2024-11-09',NULL,NULL,NULL,1),(8,9,1,'2024-11-04','2024-11-09',NULL,NULL,NULL,1),(9,10,1,'2024-11-04','2024-11-09',NULL,NULL,NULL,1),(10,11,1,'2024-11-04','2024-11-09',NULL,NULL,NULL,1),(21,2,1,'2024-10-21','2024-10-29',NULL,NULL,NULL,1),(22,3,1,'2024-10-31','2024-11-05',NULL,NULL,NULL,1),(23,4,1,'2024-10-31','2024-11-05',NULL,NULL,NULL,1),(24,5,1,'2024-10-31','2024-11-05',NULL,NULL,NULL,1),(25,6,1,'2024-10-31','2024-11-05',NULL,NULL,NULL,1),(26,6,1,'2024-10-31','2024-11-05',NULL,NULL,NULL,1),(27,7,1,'2024-11-04','2024-11-09',NULL,NULL,NULL,1),(28,8,1,'2024-11-04','2024-11-09',NULL,NULL,NULL,1),(29,9,1,'2024-11-04','2024-11-09',NULL,NULL,NULL,1);
/*!40000 ALTER TABLE `reparaciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stock`
--

DROP TABLE IF EXISTS `stock`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stock` (
  `idstock` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion_art` varchar(150) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `tipo_stock` varchar(50) NOT NULL,
  `idproveedores` int(11) NOT NULL,
  `cuit_prov` varchar(11) NOT NULL,
  `estado_stock` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`idstock`),
  KEY `fk_stock_proveedores1_idx` (`idproveedores`),
  CONSTRAINT `fk_stock_proveedores1` FOREIGN KEY (`idproveedores`) REFERENCES `proveedores` (`idproveedores`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stock`
--

LOCK TABLES `stock` WRITE;
/*!40000 ALTER TABLE `stock` DISABLE KEYS */;
INSERT INTO `stock` VALUES (1,'tornillos',10,'Herramientas',1,'30663322333',1),(2,'tuercas',100,'Herramientas',2,'33662483731',1),(3,'Tornillos 8cm',80,'Insumos',1,'',1),(4,'Resma Gloria',15,'Libreria',3,'',1),(5,'Estaño para soldar (1 klg)',1,'Herramientas',1,'',1),(6,'Modulo Moto G8 Plus',15,'Electronica',1,'',1),(7,'Fundas Moto A43',16,'Insumos',1,'',1);
/*!40000 ALTER TABLE `stock` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipo_electro`
--

DROP TABLE IF EXISTS `tipo_electro`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tipo_electro` (
  `idtipo_electro` int(11) NOT NULL AUTO_INCREMENT,
  `nom_tipo` varchar(60) NOT NULL,
  PRIMARY KEY (`idtipo_electro`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipo_electro`
--

LOCK TABLES `tipo_electro` WRITE;
/*!40000 ALTER TABLE `tipo_electro` DISABLE KEYS */;
INSERT INTO `tipo_electro` VALUES (1,'heladera'),(2,'celular'),(3,'smart tv'),(4,'lavarropas'),(5,'licuadora'),(6,'notebook'),(7,'pc');
/*!40000 ALTER TABLE `tipo_electro` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'el_reparador_db'
--

--
-- Dumping routines for database 'el_reparador_db'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-11-05  0:54:28
