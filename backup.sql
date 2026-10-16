-- MySQL dump 10.13  Distrib 9.7.2, for Linux (x86_64)
--
-- Host: localhost    Database: railway
-- ------------------------------------------------------
-- Server version	9.7.2

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `colegios`
--

DROP TABLE IF EXISTS `colegios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `colegios` (
  `colegio_id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `codigo_institucion` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `correo_contacto` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`colegio_id`),
  UNIQUE KEY `codigo_institucion` (`codigo_institucion`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `colegios`
--

LOCK TABLES `colegios` WRITE;
/*!40000 ALTER TABLE `colegios` DISABLE KEYS */;
INSERT INTO `colegios` VALUES (1,'Colegio Técnico Experimental','',NULL);
/*!40000 ALTER TABLE `colegios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `comentarios_tickets`
--

DROP TABLE IF EXISTS `comentarios_tickets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `comentarios_tickets` (
  `comentario_id` int NOT NULL AUTO_INCREMENT,
  `ticket_id` int NOT NULL,
  `usuario_id` int NOT NULL,
  `mensaje` text COLLATE utf8mb4_general_ci NOT NULL,
  `es_interno` tinyint(1) DEFAULT '0',
  `archivo_adjunto` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `fecha` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`comentario_id`),
  KEY `ticket_id` (`ticket_id`),
  KEY `usuario_id` (`usuario_id`),
  CONSTRAINT `comentarios_tickets_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`ticket_id`) ON DELETE CASCADE,
  CONSTRAINT `comentarios_tickets_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`usuario_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comentarios_tickets`
--

LOCK TABLES `comentarios_tickets` WRITE;
/*!40000 ALTER TABLE `comentarios_tickets` DISABLE KEYS */;
INSERT INTO `comentarios_tickets` VALUES (3,12,5,'asd',0,NULL,'2026-10-12 15:19:47');
/*!40000 ALTER TABLE `comentarios_tickets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tickets`
--

DROP TABLE IF EXISTS `tickets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tickets` (
  `ticket_id` int NOT NULL AUTO_INCREMENT,
  `colegio_id` int NOT NULL,
  `usuario_id` int NOT NULL,
  `asignado_a` int DEFAULT NULL,
  `categoria` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `prioridad` enum('Baja','Media','Alta','Urgente') COLLATE utf8mb4_general_ci DEFAULT 'Media',
  `estado` enum('Pendiente','En Proceso','Resuelto','Cerrado') COLLATE utf8mb4_general_ci DEFAULT 'Pendiente',
  `asunto` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_general_ci NOT NULL,
  `archivo_adjunto` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `fecha_creacion` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ticket_id`),
  KEY `colegio_id` (`colegio_id`),
  KEY `usuario_id` (`usuario_id`),
  KEY `asignado_a` (`asignado_a`),
  CONSTRAINT `tickets_ibfk_1` FOREIGN KEY (`colegio_id`) REFERENCES `colegios` (`colegio_id`),
  CONSTRAINT `tickets_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`usuario_id`),
  CONSTRAINT `tickets_ibfk_3` FOREIGN KEY (`asignado_a`) REFERENCES `usuarios` (`usuario_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tickets`
--

LOCK TABLES `tickets` WRITE;
/*!40000 ALTER TABLE `tickets` DISABLE KEYS */;
INSERT INTO `tickets` VALUES (12,1,3,5,'Anuncios','Alta','Pendiente','hola','asdasd','20261008_171456_6ac7cf90dd3d9.jpg','2026-10-08 17:14:56'),(13,1,7,6,'Registro de Notas','Alta','Cerrado','asdad','asdasd','ad2481d6995e353825f96e9d6a98847e.jpg','2026-10-12 15:33:20');
/*!40000 ALTER TABLE `tickets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios` (
  `usuario_id` int NOT NULL AUTO_INCREMENT,
  `colegio_id` int DEFAULT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `correo` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `contrasena` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `rol` enum('profesor','administrativo','soporte') COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`usuario_id`),
  UNIQUE KEY `correo` (`correo`),
  KEY `colegio_id` (`colegio_id`),
  CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`colegio_id`) REFERENCES `colegios` (`colegio_id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (3,1,'Profe Carlos','profe@colegio.com','$2y$10$kbpQKXgvBwGeBGFzReFdH.DzfPlGkqAsMEqzTzPnHQOIHhz6xe2mi','profesor'),(5,NULL,'idmv06','idmv06@gmail.com','$2y$10$QrTEbB.3cLhyxmD8t5LuZueiOFPzVhadwiO2yTN9XvlyEZJbD5qou','soporte'),(6,NULL,'Don Jonathan','Jona@gmail.com','$2y$10$lPk0ANDFAeeh0CcngrppKeoAdeRBKBLptGpFUIrZdFPjr2ls51i5S','soporte'),(7,1,'susta','susta@gmail.com','$2y$10$L3i6bCcdhTOl1IbRipapXejQizjlvwwhJox71/tZ6vB/NX7y0.FS6','profesor'),(9,NULL,'abc','abc1@gmail.com','$2y$10$llVdQhOcg.hsgvrWWe8E5.YsKAIK5vMCLCXRDqSYk4L8HDEs9QSNm','soporte');
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-10-16 18:00:33
