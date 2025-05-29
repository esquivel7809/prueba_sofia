-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 27, 2025 at 04:28 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `teamtalkssoia`
--

-- --------------------------------------------------------

--
-- Table structure for table `actividades`
--

CREATE TABLE `actividades` (
  `id_actividad` int(11) NOT NULL,
  `id_materia_ficha` int(11) DEFAULT NULL,
  `titulo` varchar(100) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `archivo` varchar(255) DEFAULT NULL,
  `fecha_entrega` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `actividades_user`
--

CREATE TABLE `actividades_user` (
  `id_actividad_user` int(11) NOT NULL,
  `id_actividad` int(11) DEFAULT NULL,
  `id_estado_actividad` int(11) DEFAULT NULL,
  `contenido` text DEFAULT NULL,
  `archivo` varchar(255) DEFAULT NULL,
  `fecha_entrega` date DEFAULT NULL,
  `id_user` bigint(20) DEFAULT NULL,
  `nota` decimal(5,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ambientes`
--

CREATE TABLE `ambientes` (
  `id_ambiente` int(11) NOT NULL,
  `ambiente` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `empresa`
--

CREATE TABLE `empresa` (
  `nit` int(11) NOT NULL,
  `empresa` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `empresa`
--

INSERT INTO `empresa` (`nit`, `empresa`) VALUES
(0, 'SENA'),
(295, 'SENA');

-- --------------------------------------------------------

--
-- Table structure for table `estado`
--

CREATE TABLE `estado` (
  `id_estado` int(11) NOT NULL,
  `estado` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `estado`
--

INSERT INTO `estado` (`id_estado`, `estado`) VALUES
(1, 'Activo'),
(2, 'Inactivo');

-- --------------------------------------------------------

--
-- Table structure for table `fichas`
--

CREATE TABLE `fichas` (
  `id_ficha` int(11) NOT NULL,
  `id_formacion` int(11) DEFAULT NULL,
  `id_ambiente` int(11) DEFAULT NULL,
  `fecha_creac` date DEFAULT NULL,
  `id_instructor` bigint(20) DEFAULT NULL,
  `id_jornada` int(11) DEFAULT NULL,
  `id_tipo_ficha` int(11) DEFAULT NULL,
  `id_estado` int(11) DEFAULT NULL,
  `id_trimestre` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `formacion`
--

CREATE TABLE `formacion` (
  `id_formacion` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha_creacion` date DEFAULT current_timestamp(),
  `id_estado` int(11) DEFAULT 1,
  `id_tipo_formacion` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `foros`
--

CREATE TABLE `foros` (
  `id_foro` int(11) NOT NULL,
  `id_materia_ficha` int(11) DEFAULT NULL,
  `fecha_foro` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `historial_contra`
--

CREATE TABLE `historial_contra` (
  `id` int(11) NOT NULL,
  `id_user` bigint(20) DEFAULT NULL,
  `contraseña_ant` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `horario`
--

CREATE TABLE `horario` (
  `id_horario` int(11) NOT NULL,
  `id_materia_ficha` int(11) DEFAULT NULL,
  `dia_semana` varchar(15) DEFAULT NULL,
  `hora_inicio` time DEFAULT NULL,
  `hora_fin` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jornada`
--

CREATE TABLE `jornada` (
  `id_jornada` int(11) NOT NULL,
  `jornada` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jornada`
--

INSERT INTO `jornada` (`id_jornada`, `jornada`) VALUES
(1, 'Mañana'),
(2, 'Tarde'),
(3, 'Noche'),
(4, 'Mixta');

-- --------------------------------------------------------

--
-- Table structure for table `licencias`
--

CREATE TABLE `licencias` (
  `id_licencia` varchar(10) NOT NULL,
  `id_tipo_licencia` int(3) NOT NULL,
  `fecha_ini` datetime NOT NULL,
  `fecha_fin` datetime NOT NULL,
  `nit` int(50) NOT NULL,
  `estado` enum('Activa','Expirada','Inactiva') DEFAULT 'Activa'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `materias`
--

CREATE TABLE `materias` (
  `id_materia` int(11) NOT NULL,
  `materia` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `materia_ficha`
--

CREATE TABLE `materia_ficha` (
  `id_materia_ficha` int(11) NOT NULL,
  `id_materia` int(11) DEFAULT NULL,
  `id_ficha` int(11) DEFAULT NULL,
  `id_instructor` bigint(20) DEFAULT NULL,
  `id_trimestre` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `recuperacion`
--

CREATE TABLE `recuperacion` (
  `id_recuperacion` int(11) NOT NULL,
  `id_usuario` bigint(20) DEFAULT NULL,
  `token` int(11) DEFAULT NULL,
  `fecha_expiracion` datetime NOT NULL,
  `fecha_creacion` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `respuesta_foro`
--

CREATE TABLE `respuesta_foro` (
  `id_respuesta_foro` int(11) NOT NULL,
  `id_tema_foro` int(11) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha_respuesta` date DEFAULT NULL,
  `id_user` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id_rol` int(11) NOT NULL,
  `rol` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id_rol`, `rol`) VALUES
(1, 'S_Admin'),
(2, 'Admin'),
(3, 'Instructor'),
(4, 'Aprendiz');

-- --------------------------------------------------------

--
-- Table structure for table `temas_foro`
--

CREATE TABLE `temas_foro` (
  `id_tema_foro` int(11) NOT NULL,
  `id_foro` int(11) DEFAULT NULL,
  `titulo` varchar(100) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha_creacion` date DEFAULT NULL,
  `id_user` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tipo_documento`
--

CREATE TABLE `tipo_documento` (
  `id_tipo` int(11) NOT NULL,
  `tipo_doc` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tipo_documento`
--

INSERT INTO `tipo_documento` (`id_tipo`, `tipo_doc`) VALUES
(1, 'Cedula'),
(2, 'Tarjeta Identidad');

-- --------------------------------------------------------

--
-- Table structure for table `tipo_ficha`
--

CREATE TABLE `tipo_ficha` (
  `id_tipo_ficha` int(11) NOT NULL,
  `tipo_ficha` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tipo_formacion`
--

CREATE TABLE `tipo_formacion` (
  `id_tipo_formacion` int(11) NOT NULL,
  `tipo_formacion` varchar(50) NOT NULL,
  `Duracion` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tipo_licencia`
--

CREATE TABLE `tipo_licencia` (
  `id_tipo_licencia` int(11) NOT NULL,
  `licencia` varchar(50) NOT NULL,
  `duracion` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `trimestre`
--

CREATE TABLE `trimestre` (
  `id_trimestre` int(11) NOT NULL,
  `trimestre` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_ficha`
--

CREATE TABLE `user_ficha` (
  `id_user_ficha` int(11) NOT NULL,
  `id_user` bigint(20) DEFAULT NULL,
  `id_ficha` int(11) DEFAULT NULL,
  `fecha_asig` date DEFAULT NULL,
  `id_estado` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `usuarios`
--

CREATE TABLE `usuarios` (
  `id` bigint(20) NOT NULL,
  `id_tipo` int(11) DEFAULT NULL,
  `nombres` varchar(100) DEFAULT NULL,
  `apellidos` varchar(100) DEFAULT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `contraseña` varchar(255) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `id_rol` int(11) DEFAULT NULL,
  `id_estado` int(11) DEFAULT NULL,
  `fecha_registro` date DEFAULT NULL,
  `nit` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `usuarios`
--

INSERT INTO `usuarios` (`id`, `id_tipo`, `nombres`, `apellidos`, `correo`, `contraseña`, `avatar`, `telefono`, `id_rol`, `id_estado`, `fecha_registro`, `nit`) VALUES
(1, 1, 'Administrador', '', 'admin@sena.edu.co', '$2y$10$Yv6czEuGk6nkpuZr99rIuOAUDBGH2GHSL5rpVGyL8n3w86TEFrUra', NULL, '3001112233', 1, 1, '2025-05-26', 0),
(2, 1, 'Instructor', '', 'instructor@sena.edu.co', '$2y$10$Yv6czEuGk6nkpuZr99rIuOAUDBGH2GHSL5rpVGyL8n3w86TEFrUra', NULL, '3002223344', 3, 1, '2025-05-26', 0),
(3, 1, 'Aprendiz', '', 'aprendiz@sena.edu.co', '$2y$10$Yv6czEuGk6nkpuZr99rIuOAUDBGH2GHSL5rpVGyL8n3w86TEFrUra', NULL, '3003334455', 4, 1, '2025-05-26', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `actividades_user`
--
ALTER TABLE `actividades_user`
  ADD PRIMARY KEY (`id_actividad_user`),
  ADD KEY `id_actividad` (`id_actividad`),
  ADD KEY `id_estado_actividad` (`id_estado_actividad`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `ambientes`
--
ALTER TABLE `ambientes`
  ADD PRIMARY KEY (`id_ambiente`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
