-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 08, 2026 at 06:26 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sistema_tickets`
--

-- --------------------------------------------------------

--
-- Table structure for table `colegios`
--

CREATE TABLE `colegios` (
  `colegio_id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `codigo_institucion` varchar(50) NOT NULL,
  `correo_contacto` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `colegios`
--

INSERT INTO `colegios` (`colegio_id`, `nombre`, `codigo_institucion`, `correo_contacto`) VALUES
(1, 'Colegio Técnico Experimental', '', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `comentarios_tickets`
--

CREATE TABLE `comentarios_tickets` (
  `comentario_id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `mensaje` text NOT NULL,
  `es_interno` tinyint(1) DEFAULT 0,
  `archivo_adjunto` varchar(255) DEFAULT NULL,
  `fecha` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tickets`
--

CREATE TABLE `tickets` (
  `ticket_id` int(11) NOT NULL,
  `colegio_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `asignado_a` int(11) DEFAULT NULL,
  `categoria` varchar(50) NOT NULL,
  `prioridad` enum('Baja','Media','Alta','Urgente') DEFAULT 'Media',
  `estado` enum('Pendiente','En Proceso','Resuelto','Cerrado') DEFAULT 'Pendiente',
  `asunto` varchar(150) NOT NULL,
  `descripcion` text NOT NULL,
  `archivo_adjunto` varchar(255) DEFAULT NULL,
  `fecha_creacion` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tickets`
--

INSERT INTO `tickets` (`ticket_id`, `colegio_id`, `usuario_id`, `asignado_a`, `categoria`, `prioridad`, `estado`, `asunto`, `descripcion`, `archivo_adjunto`, `fecha_creacion`) VALUES
(8, 1, 7, NULL, 'Registro de Notas', 'Alta', 'Pendiente', 'asd', 'asd', NULL, '2026-10-07 11:50:26'),
(9, 1, 3, NULL, 'Control de Entradas', 'Media', 'Pendiente', 'asd', 'asd', NULL, '2026-10-07 12:17:09'),
(10, 1, 7, NULL, 'Control de Entradas', 'Urgente', 'Pendiente', 'Concierto', 'Concierto de Iron Maiden el día de hoy 8/10/2026 a las 6pm en el estadio nacional, primera vez desde 2016. Estoy como loco si', '20261008_180208_6ac7be80d7aab.jpg', '2026-10-08 10:02:08');

-- --------------------------------------------------------

--
-- Table structure for table `usuarios`
--

CREATE TABLE `usuarios` (
  `usuario_id` int(11) NOT NULL,
  `colegio_id` int(11) DEFAULT NULL,
  `nombre` varchar(100) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `rol` enum('profesor','administrativo','soporte') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `usuarios`
--

INSERT INTO `usuarios` (`usuario_id`, `colegio_id`, `nombre`, `correo`, `contrasena`, `rol`) VALUES
(3, 1, 'Profe Carlos', 'profe@colegio.com', '123456', 'profesor'),
(4, NULL, 'Soporte Admin', 'admin@suempresa.com', '123456', 'soporte'),
(5, NULL, 'idmv06', 'idmv06@gmail.com', '293300', 'soporte'),
(6, NULL, 'Don Jonathan', 'Jona@gmail.com', '112300', 'soporte'),
(7, 1, 'susta', 'susta@gmail.com', '112300', 'profesor');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `colegios`
--
ALTER TABLE `colegios`
  ADD PRIMARY KEY (`colegio_id`),
  ADD UNIQUE KEY `codigo_institucion` (`codigo_institucion`);

--
-- Indexes for table `comentarios_tickets`
--
ALTER TABLE `comentarios_tickets`
  ADD PRIMARY KEY (`comentario_id`),
  ADD KEY `ticket_id` (`ticket_id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indexes for table `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`ticket_id`),
  ADD KEY `colegio_id` (`colegio_id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `asignado_a` (`asignado_a`);

--
-- Indexes for table `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`usuario_id`),
  ADD UNIQUE KEY `correo` (`correo`),
  ADD KEY `colegio_id` (`colegio_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `colegios`
--
ALTER TABLE `colegios`
  MODIFY `colegio_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `comentarios_tickets`
--
ALTER TABLE `comentarios_tickets`
  MODIFY `comentario_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tickets`
--
ALTER TABLE `tickets`
  MODIFY `ticket_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `usuario_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comentarios_tickets`
--
ALTER TABLE `comentarios_tickets`
  ADD CONSTRAINT `comentarios_tickets_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`ticket_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comentarios_tickets_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`usuario_id`);

--
-- Constraints for table `tickets`
--
ALTER TABLE `tickets`
  ADD CONSTRAINT `tickets_ibfk_1` FOREIGN KEY (`colegio_id`) REFERENCES `colegios` (`colegio_id`),
  ADD CONSTRAINT `tickets_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`usuario_id`),
  ADD CONSTRAINT `tickets_ibfk_3` FOREIGN KEY (`asignado_a`) REFERENCES `usuarios` (`usuario_id`);

--
-- Constraints for table `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`colegio_id`) REFERENCES `colegios` (`colegio_id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
