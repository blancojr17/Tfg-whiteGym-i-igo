-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 14-05-2026 a las 14:47:18
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `gym_tfg`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asistencia`
--
USE gym_tfg;

CREATE TABLE `asistencia` (
  `id_asistencia` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `fecha` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `asistencia`
--

INSERT INTO `asistencia` (`id_asistencia`, `id_usuario`, `fecha`) VALUES
(5, 3, '2026-05-06'),
(7, 5, '2026-05-07'),
(8, 5, '2026-05-08'),
(9, 5, '2026-05-11'),
(6, 14, '2026-05-07');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clases`
--

CREATE TABLE `clases` (
  `id_clase` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha` datetime NOT NULL,
  `capacidad` int(11) NOT NULL,
  `id_entrenador` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `clases`
--

INSERT INTO `clases` (`id_clase`, `nombre`, `descripcion`, `fecha`, `capacidad`, `id_entrenador`) VALUES
(2, 'Yoga', 'Clase de yoga relajante', '2026-05-11 17:00:00', 4, 3),
(3, 'Spinning', 'Clase de bicicleta indoor', '2026-05-12 19:00:00', 2, 3),
(5, 'zanza', 'wwww', '2026-05-15 13:30:00', 6, 3),
(6, 'Cross Training', 'Entrenamiento funcional intenso', '2026-05-10 18:00:00', 20, 12),
(7, 'Yoga Flow', 'Clase relajante y movilidad', '2026-05-11 10:00:00', 15, 13),
(8, 'HIIT', 'Cardio de alta intensidad', '2026-05-11 19:00:00', 18, 12),
(9, 'Spinning', 'Clase de bicicleta indoor', '2026-05-12 17:00:00', 22, 13),
(10, 'Boxeo Fitness', 'Boxeo combinado con cardio', '2026-05-13 20:00:00', 16, 12),
(11, 'Pilates', 'Trabajo de core y estabilidad', '2026-05-14 09:30:00', 14, 13),
(12, 'Full Body', 'Rutina completa de fuerza', '2026-05-15 18:30:00', 25, 12),
(13, 'Core Extreme', 'Abdominales y resistencia', '2026-05-16 18:00:00', 20, 13);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `medidas_usuario`
--

CREATE TABLE `medidas_usuario` (
  `id_medida` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `peso` decimal(5,2) NOT NULL,
  `altura` decimal(5,2) NOT NULL,
  `edad` int(11) NOT NULL,
  `sexo` enum('hombre','mujer') NOT NULL,
  `actividad_semanal` int(11) NOT NULL DEFAULT 3,
  `intensidad` enum('baja','media','alta') NOT NULL DEFAULT 'media',
  `objetivo` enum('perder_grasa','mantener','ganar_musculo') NOT NULL DEFAULT 'mantener',
  `imc` decimal(5,2) DEFAULT NULL,
  `calorias_recomendadas` int(11) DEFAULT NULL,
  `agua_litros` decimal(5,2) DEFAULT NULL,
  `calorias` int(11) DEFAULT NULL,
  `agua` int(11) DEFAULT NULL,
  `proteinas` int(11) DEFAULT NULL,
  `carbohidratos` int(11) DEFAULT NULL,
  `grasas` int(11) DEFAULT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `medidas_usuario`
--

INSERT INTO `medidas_usuario` (`id_medida`, `id_usuario`, `peso`, `altura`, `edad`, `sexo`, `actividad_semanal`, `intensidad`, `objetivo`, `imc`, `calorias_recomendadas`, `agua_litros`, `calorias`, `agua`, `proteinas`, `carbohidratos`, `grasas`, `fecha_registro`) VALUES
(1, 5, 83.00, 183.00, 20, 'hombre', 3, 'media', 'mantener', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-07 12:07:35'),
(2, 5, 83.00, 183.00, 20, 'mujer', 3, 'media', 'mantener', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-07 12:33:36'),
(3, 5, 83.00, 183.00, 20, 'hombre', 3, 'media', 'mantener', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-07 12:33:45'),
(4, 5, 83.00, 183.00, 20, 'mujer', 3, 'media', 'mantener', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-07 12:33:50'),
(5, 5, 83.00, 183.00, 20, 'hombre', 3, 'media', 'mantener', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-07 12:33:55'),
(6, 5, 83.00, 183.00, 20, 'hombre', 3, 'media', 'mantener', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-07 12:33:57'),
(7, 5, 87.00, 183.00, 20, 'hombre', 5, 'alta', '', 25.98, 3471, 3.77, NULL, NULL, 174, 498, 87, '2026-05-07 14:10:52'),
(8, 5, 87.00, 183.00, 20, 'hombre', 3, 'media', 'perder_grasa', 25.98, 2260, 3.37, NULL, NULL, 174, 234, 70, '2026-05-07 14:11:49'),
(9, 5, 87.00, 183.00, 20, 'hombre', 7, 'media', 'perder_grasa', 25.98, 2912, 3.77, NULL, NULL, 174, 397, 70, '2026-05-07 14:12:03'),
(10, 5, 83.60, 183.00, 20, 'hombre', 7, 'media', 'perder_grasa', 24.96, 2854, 3.66, NULL, NULL, 167, 396, 67, '2026-05-07 14:12:19'),
(11, 5, 30.00, 183.00, 20, 'hombre', 7, 'media', 'perder_grasa', 8.96, 1943, 1.89, NULL, NULL, 60, 372, 24, '2026-05-07 14:12:25'),
(12, 5, 30.00, 183.00, 93, 'hombre', 7, 'media', 'perder_grasa', 8.96, 1322, 1.89, NULL, NULL, 60, 216, 24, '2026-05-07 14:45:31'),
(13, 5, 30.00, 183.00, 124, 'hombre', 7, 'media', 'perder_grasa', 8.96, 1059, 1.89, NULL, NULL, 60, 151, 24, '2026-05-07 14:45:42'),
(14, 5, 30.00, 183.00, 164, 'hombre', 7, 'alta', '', 8.96, 1154, 2.09, NULL, NULL, 54, 174, 27, '2026-05-07 14:45:54'),
(15, 5, 30.00, 201.00, 164, 'hombre', 7, 'alta', '', 7.43, 1361, 2.09, NULL, NULL, 54, 226, 27, '2026-05-07 14:46:05'),
(16, 5, 700.00, 201.00, 164, 'hombre', 7, 'alta', '', 173.26, 13662, 24.20, NULL, NULL, 1260, 738, 630, '2026-05-07 14:46:14'),
(17, 5, 999.99, 201.00, 35, 'hombre', 7, 'alta', '', 999.99, 185894, 331.10, NULL, NULL, 20000, 3974, 10000, '2026-05-07 14:46:35'),
(18, 5, 83.99, 183.00, 20, 'hombre', 4, 'media', 'perder_grasa', 25.08, 2370, 3.37, NULL, NULL, 168, 273, 67, '2026-05-14 14:07:13');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `planes`
--

CREATE TABLE `planes` (
  `id_plan` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `precio` decimal(6,2) NOT NULL,
  `tipo` enum('suscripcion','bono') NOT NULL,
  `duracion_dias` int(11) DEFAULT NULL,
  `usos` int(11) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `planes`
--

INSERT INTO `planes` (`id_plan`, `nombre`, `precio`, `tipo`, `duracion_dias`, `usos`, `activo`) VALUES
(1, 'Bono 1 entrada', 7.99, 'bono', 30, 1, 1),
(2, 'Bono 5 entradas', 19.99, 'bono', 60, 5, 1),
(3, 'Bono 10 entradas', 34.99, 'bono', 90, 10, 1),
(4, 'Básico', 29.99, 'suscripcion', 30, NULL, 1),
(5, 'Premium', 34.99, 'suscripcion', 30, NULL, 1),
(6, 'Elite', 44.99, 'suscripcion', 30, NULL, 1),
(7, '21', 0.09, 'bono', 8, 20, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sugerencias`
--

CREATE TABLE `sugerencias` (
  `id_sugerencia` int(11) NOT NULL,
  `nombre` varchar(120) NOT NULL,
  `email` varchar(150) NOT NULL,
  `mensaje` text NOT NULL,
  `fecha` datetime NOT NULL DEFAULT current_timestamp(),
  `estado` enum('pendiente','leido') NOT NULL DEFAULT 'pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `sugerencias`
--

INSERT INTO `sugerencias` (`id_sugerencia`, `nombre`, `email`, `mensaje`, `fecha`, `estado`) VALUES
(1, 'qqq', 'ini@gmail.com', 'Asunto: Clases | Telefono: 666101010\ndsdgsfdsfsdfsfd', '2026-05-11 12:22:05', 'leido'),
(2, 'Iñigo', 'ini@gmail.com', 'Asunto: Informacion | Telefono: 666101010\nHOLA', '2026-05-14 12:23:42', 'leido');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellidos` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('usuario','entrenador','admin') DEFAULT 'usuario',
  `activo` tinyint(1) DEFAULT 1,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `telefono` varchar(20) DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `sexo` enum('hombre','mujer','otro') DEFAULT NULL,
  `ciudad` varchar(100) DEFAULT NULL,
  `fecha_registro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombre`, `apellidos`, `email`, `password`, `rol`, `activo`, `fecha_creacion`, `telefono`, `fecha_nacimiento`, `sexo`, `ciudad`, `fecha_registro`) VALUES
(1, 'ini', 'ini', 'ini@gmail.com', '$2y$10$tKsnWdxL6mMdcZGwzwsrTOI4ShMf0oEqq7oOKzbJfLlqQvoJsFbRe', 'admin', 1, '2026-05-06 08:36:11', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(2, '123', '123', 'inii@gmail.com', '$2y$10$7ew71vyBS3tvMRt309D0xuvTquPdHqckC6tkY8ASw0UOHaZmTVcU.', 'usuario', 0, '2026-05-06 08:38:58', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(3, '2', '2', '2@gmail.com', '$2y$10$MW2hM9bXDpR1yDbVzkGGOu0X5MRTzZC0Xgo2HAfPRCXoLd2DTuHsW', 'entrenador', 1, '2026-05-06 10:20:44', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(4, 'Carlos', 'Trainer', 'entrenador@whitegym.com', '$2y$10$Q7xJY6D5D4l3G5V4yQ7A0uKzQJY9h4KzK8mWQ0X5s7M4J8l2xP9hS', 'entrenador', 1, '2026-05-06 10:42:58', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(5, '21', '21', '21@gmail.com', '$2y$10$nXLiUry1jkJJULhy9PYzQ.AWgyVFCyWIV2qIKpxBZS2UgBUmKbT66', 'usuario', 1, '2026-05-06 11:19:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(6, 'Usuario1', 'Test', 'usuario1@test.com', '$2y$10$wH0V6W7j9A9D9jY2L6m0Oe5K4r8Jm7Vq3zRk6wL9nY3tP8uW4yB9a', 'usuario', 1, '2026-05-06 11:45:02', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(7, 'Usuario2', 'Test', 'usuario2@test.com', '$2y$10$wH0V6W7j9A9D9jY2L6m0Oe5K4r8Jm7Vq3zRk6wL9nY3tP8uW4yB9a', 'usuario', 1, '2026-05-06 11:45:02', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(8, 'Usuario3', 'Test', 'usuario3@test.com', '$2y$10$wH0V6W7j9A9D9jY2L6m0Oe5K4r8Jm7Vq3zRk6wL9nY3tP8uW4yB9a', 'usuario', 1, '2026-05-06 11:45:02', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(9, 'Usuario4', 'Test', 'usuario4@test.com', '$2y$10$wH0V6W7j9A9D9jY2L6m0Oe5K4r8Jm7Vq3zRk6wL9nY3tP8uW4yB9a', 'usuario', 1, '2026-05-06 11:45:02', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(10, 'Usuario5', 'Test', 'usuario5@test.com', '$2y$10$wH0V6W7j9A9D9jY2L6m0Oe5K4r8Jm7Vq3zRk6wL9nY3tP8uW4yB9a', 'usuario', 1, '2026-05-06 11:45:02', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(11, 'Usuario6', 'Test', 'usuario6@test.com', '$2y$10$wH0V6W7j9A9D9jY2L6m0Oe5K4r8Jm7Vq3zRk6wL9nY3tP8uW4yB9a', 'usuario', 1, '2026-05-06 11:45:02', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(12, 'Usuario7', 'Test', 'usuario7@test.com', '$2y$10$wH0V6W7j9A9D9jY2L6m0Oe5K4r8Jm7Vq3zRk6wL9nY3tP8uW4yB9a', 'usuario', 1, '2026-05-06 11:45:02', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(13, 'Admin', 'WhiteGym', 'admin@whitegym.com', '$2y$10$wH0V6W7j9A9D9jY2L6m0Oe5K4r8Jm7Vq3zRk6wL9nY3tP8uW4yB9a', 'entrenador', 1, '2026-05-06 12:03:04', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(14, 'q', 'q', 'q@gmail.com', '$2y$10$3JLy87jw2L8iCOml7ehH7.iJDIEVurbp6F48r6Rv8s1kionbP55Ma', 'usuario', 1, '2026-05-07 07:31:38', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(15, 'Carlos', 'Martinez', 'carlos1@gmail.com', '1234', 'usuario', 1, '2026-05-07 12:14:42', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(16, 'Lucia', 'Fernandez', 'lucia1@gmail.com', '1234', 'usuario', 1, '2026-05-07 12:14:42', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(17, 'Sergio', 'Lopez', 'sergio1@gmail.com', '1234', 'usuario', 1, '2026-05-07 12:14:42', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(18, 'Marta', 'Ruiz', 'marta1@gmail.com', '1234', 'usuario', 1, '2026-05-07 12:14:42', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(19, 'Pablo', 'Sanchez', 'pablo1@gmail.com', '1234', 'usuario', 1, '2026-05-07 12:14:42', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(20, 'Andrea', 'Gil', 'andrea1@gmail.com', '1234', 'usuario', 1, '2026-05-07 12:14:42', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(21, 'David', 'Navarro', 'david1@gmail.com', '1234', 'usuario', 1, '2026-05-07 12:14:42', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(22, 'Elena', 'Torres', 'elena1@gmail.com', '1234', 'usuario', 1, '2026-05-07 12:14:42', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(23, 'Javier', 'Romero', 'javier1@gmail.com', '1234', 'usuario', 1, '2026-05-07 12:14:42', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(24, 'Sara', 'Molina', 'sara1@gmail.com', '1234', 'usuario', 1, '2026-05-07 12:14:42', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(25, 'Raul', 'Jimenez', 'raultrainer@gmail.com', '1234', 'entrenador', 1, '2026-05-07 12:14:42', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(26, 'Alberto', 'Diaz', 'albertotrainer@gmail.com', '1234', 'entrenador', 1, '2026-05-07 12:14:42', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(27, 'Usuario26', '', 'usuario26@gmail.com', '1234', 'usuario', 1, '2026-05-11 09:52:50', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(28, 'Usuario27', '', 'usuario27@gmail.com', '1234', 'usuario', 1, '2026-05-11 09:52:50', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(29, 'Usuario28', '', 'usuario28@gmail.com', '1234', 'usuario', 1, '2026-05-11 09:52:50', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(30, 'Usuario29', '', 'usuario29@gmail.com', '1234', 'usuario', 1, '2026-05-11 09:52:50', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(31, 'Usuario30', '', 'usuario30@gmail.com', '1234', 'usuario', 1, '2026-05-11 09:52:50', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(32, 'Usuario31', '', 'usuario31@gmail.com', '1234', 'usuario', 1, '2026-05-11 09:52:50', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(33, 'Usuario32', '', 'usuario32@gmail.com', '1234', 'usuario', 1, '2026-05-11 09:52:50', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(34, 'Usuario33', '', 'usuario33@gmail.com', '1234', 'usuario', 1, '2026-05-11 09:52:50', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(35, 'Usuario34', '', 'usuario34@gmail.com', '1234', 'usuario', 1, '2026-05-11 09:52:50', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(36, 'Usuario35', '', 'usuario35@gmail.com', '1234', 'usuario', 1, '2026-05-11 09:52:50', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(37, 'Usuario36', '', 'usuario36@gmail.com', '1234', 'usuario', 1, '2026-05-11 09:52:50', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(38, 'Usuario37', '', 'usuario37@gmail.com', '1234', 'usuario', 1, '2026-05-11 09:52:50', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(39, 'Usuario38', '', 'usuario38@gmail.com', '1234', 'usuario', 1, '2026-05-11 09:52:50', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(40, 'Usuario39', '', 'usuario39@gmail.com', '1234', 'usuario', 1, '2026-05-11 09:52:50', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(41, 'Usuario40', '', 'usuario40@gmail.com', '1234', 'usuario', 1, '2026-05-11 09:52:50', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(42, 'Usuario41', '', 'usuario41@gmail.com', '1234', 'usuario', 1, '2026-05-11 09:52:50', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(43, 'Usuario42', '', 'usuario42@gmail.com', '1234', 'usuario', 1, '2026-05-11 09:52:50', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(44, 'Usuario43', '', 'usuario43@gmail.com', '1234', 'usuario', 1, '2026-05-11 09:52:50', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(45, 'Usuario44', '', 'usuario44@gmail.com', '1234', 'usuario', 1, '2026-05-11 09:52:50', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(46, 'Usuario45', '', 'usuario45@gmail.com', '1234', 'usuario', 1, '2026-05-11 09:52:50', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(47, 'Usuario46', '', 'usuario46@gmail.com', '1234', 'usuario', 1, '2026-05-11 09:52:50', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(48, 'Usuario47', '', 'usuario47@gmail.com', '1234', 'usuario', 1, '2026-05-11 09:52:50', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(49, 'Usuario48', '', 'usuario48@gmail.com', '1234', 'usuario', 1, '2026-05-11 09:52:50', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(50, 'Usuario49', '', 'usuario49@gmail.com', '1234', 'usuario', 1, '2026-05-11 09:52:50', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(51, 'Usuario50', '', 'usuario50@gmail.com', '1234', 'usuario', 1, '2026-05-11 09:52:50', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(52, 'Usuario51', '', 'usuario51@gmail.com', '1234', 'usuario', 1, '2026-05-11 09:52:50', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(53, 'Usuario52', '', 'usuario52@gmail.com', '1234', 'usuario', 1, '2026-05-11 09:52:50', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(54, 'Usuario53', '', 'usuario53@gmail.com', '1234', 'usuario', 1, '2026-05-11 09:52:50', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(55, 'Usuario54', '', 'usuario54@gmail.com', '1234', 'usuario', 1, '2026-05-11 09:52:50', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(56, 'Usuario55', '', 'usuario55@gmail.com', '1234', 'usuario', 1, '2026-05-11 09:52:50', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(57, 'Usuario56', '', 'usuario56@gmail.com', '1234', 'usuario', 1, '2026-05-11 09:52:50', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(58, 'Usuario57', '', 'usuario57@gmail.com', '1234', 'usuario', 1, '2026-05-11 09:52:50', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(59, 'Usuario58', '', 'usuario58@gmail.com', '1234', 'usuario', 1, '2026-05-11 09:52:50', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(60, 'Usuario59', '', 'usuario59@gmail.com', '1234', 'usuario', 1, '2026-05-11 09:52:50', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(61, 'Usuario60', '', 'usuario60@gmail.com', '1234', 'usuario', 1, '2026-05-11 09:52:50', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(62, 'Usuario61', '', 'usuario61@gmail.com', '1234', 'usuario', 1, '2026-05-11 09:52:50', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(63, 'Usuario62', '', 'usuario62@gmail.com', '1234', 'usuario', 1, '2026-05-11 09:52:50', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(64, 'Usuario63', '', 'usuario63@gmail.com', '1234', 'usuario', 1, '2026-05-11 09:52:50', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(65, 'Usuario64', '', 'usuario64@gmail.com', '1234', 'usuario', 1, '2026-05-11 09:52:50', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(66, 'Usuario65', '', 'usuario65@gmail.com', '1234', 'usuario', 1, '2026-05-11 09:52:50', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(67, 'Usuario66', '', 'usuario66@gmail.com', '1234', 'usuario', 1, '2026-05-11 09:52:50', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(68, 'Usuario67', '', 'usuario67@gmail.com', '1234', 'usuario', 1, '2026-05-11 09:52:50', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(69, 'Usuario68', '', 'usuario68@gmail.com', '1234', 'usuario', 1, '2026-05-11 09:52:50', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(70, 'Usuario69', '', 'usuario69@gmail.com', '1234', 'usuario', 1, '2026-05-11 09:52:50', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(71, 'Usuario70', '', 'usuario70@gmail.com', '1234', 'usuario', 1, '2026-05-11 09:52:50', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(72, 'Usuario71', '', 'usuario71@gmail.com', '1234', 'usuario', 1, '2026-05-11 09:52:50', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(73, 'Usuario72', '', 'usuario72@gmail.com', '1234', 'usuario', 1, '2026-05-11 09:52:50', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(74, 'Usuario73', '', 'usuario73@gmail.com', '1234', 'usuario', 1, '2026-05-11 09:52:50', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(75, 'Usuario74', '', 'usuario74@gmail.com', '1234', 'usuario', 1, '2026-05-11 09:52:50', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(76, 'Usuario75', '', 'usuario75@gmail.com', '1234', 'usuario', 1, '2026-05-11 09:52:50', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(77, 'Usuario76', '', 'usuario76@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:10:12', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(78, 'Usuario77', '', 'usuario77@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:10:12', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(79, 'Usuario78', '', 'usuario78@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:10:12', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(80, 'Usuario79', '', 'usuario79@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:10:12', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(81, 'Usuario80', '', 'usuario80@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:10:12', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(82, 'Usuario81', '', 'usuario81@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:10:12', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(83, 'Usuario82', '', 'usuario82@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:10:12', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(84, 'Usuario83', '', 'usuario83@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:10:12', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(85, 'Usuario84', '', 'usuario84@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:10:12', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(86, 'Usuario85', '', 'usuario85@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:10:12', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(87, 'Usuario86', '', 'usuario86@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:10:12', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(88, 'Usuario87', '', 'usuario87@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:10:12', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(89, 'Usuario88', '', 'usuario88@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:10:12', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(90, 'Usuario89', '', 'usuario89@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:10:12', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(91, 'Usuario90', '', 'usuario90@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:10:12', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(92, 'Usuario91', '', 'usuario91@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:10:12', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(93, 'Usuario92', '', 'usuario92@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:10:12', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(94, 'Usuario93', '', 'usuario93@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:10:12', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(95, 'Usuario94', '', 'usuario94@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:10:12', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(96, 'Usuario95', '', 'usuario95@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:10:12', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(97, 'Usuario96', '', 'usuario96@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:12:28', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(98, 'Usuario97', '', 'usuario97@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:12:28', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(99, 'Usuario98', '', 'usuario98@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:12:28', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(100, 'Usuario99', '', 'usuario99@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:12:28', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(101, 'Usuario100', '', 'usuario100@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:12:28', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(102, 'Usuario101', '', 'usuario101@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:12:28', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(103, 'Usuario102', '', 'usuario102@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:12:28', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(104, 'Usuario103', '', 'usuario103@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:12:28', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(105, 'Usuario104', '', 'usuario104@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:12:28', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(106, 'Usuario105', '', 'usuario105@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:12:28', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(107, 'Usuario106', '', 'usuario106@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:12:28', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(108, 'Usuario107', '', 'usuario107@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:12:28', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(109, 'Usuario108', '', 'usuario108@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:12:28', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(110, 'Usuario109', '', 'usuario109@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:12:28', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(111, 'Usuario110', '', 'usuario110@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:12:28', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(112, 'Usuario111', '', 'usuario111@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:12:28', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(113, 'Usuario112', '', 'usuario112@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:12:28', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(114, 'Usuario113', '', 'usuario113@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:12:28', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(115, 'Usuario114', '', 'usuario114@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:12:28', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(116, 'Usuario115', '', 'usuario115@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:12:28', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(117, 'Usuario116', '', 'usuario116@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:12:28', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(118, 'Usuario117', '', 'usuario117@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:12:28', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(119, 'Usuario118', '', 'usuario118@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:12:28', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(120, 'Usuario119', '', 'usuario119@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:12:28', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(121, 'Usuario120', '', 'usuario120@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:12:28', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(122, 'Usuario121', '', 'usuario121@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:12:28', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(123, 'Usuario122', '', 'usuario122@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:12:28', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(124, 'Usuario123', '', 'usuario123@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:12:28', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(125, 'Usuario124', '', 'usuario124@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:12:28', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(126, 'Usuario125', '', 'usuario125@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:12:28', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(127, 'Usuario126', '', 'usuario126@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:12:28', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(128, 'Usuario127', '', 'usuario127@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:12:28', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(129, 'Usuario128', '', 'usuario128@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:12:28', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(130, 'Usuario129', '', 'usuario129@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:12:28', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(131, 'Usuario130', '', 'usuario130@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:12:28', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(132, 'Usuario131', '', 'usuario131@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:12:28', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(133, 'Usuario132', '', 'usuario132@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:12:28', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(134, 'Usuario133', '', 'usuario133@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:12:28', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(135, 'Usuario134', '', 'usuario134@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:12:28', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(136, 'Usuario135', '', 'usuario135@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:12:28', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(137, 'Usuario136', '', 'usuario136@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:12:28', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(138, 'Usuario137', '', 'usuario137@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:12:28', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(139, 'Usuario138', '', 'usuario138@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:12:28', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(140, 'Usuario139', '', 'usuario139@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:12:28', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(141, 'Usuario140', '', 'usuario140@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:12:28', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(142, 'Usuario141', '', 'usuario141@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:12:28', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(143, 'Usuario142', '', 'usuario142@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:12:28', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(144, 'Usuario143', '', 'usuario143@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:12:28', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(145, 'Usuario144', '', 'usuario144@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:12:28', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(146, 'Usuario145', '', 'usuario145@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:12:28', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(147, 'Usuario146', '', 'usuario146@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:12:28', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(148, 'Usuario147', '', 'usuario147@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:12:28', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(149, 'Usuario148', '', 'usuario148@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:12:28', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(150, 'Usuario149', '', 'usuario149@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:12:28', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(151, 'Usuario150', '', 'usuario150@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:12:28', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(152, 'Usuario151', '', 'usuario151@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:12:28', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(153, 'Usuario152', '', 'usuario152@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:12:28', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(154, 'Usuario153', '', 'usuario153@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:12:28', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(155, 'Usuario154', '', 'usuario154@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:12:28', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(156, 'Usuario155', '', 'usuario155@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:12:28', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(157, 'Usuario156', '', 'usuario156@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:12:28', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(158, 'Usuario157', '', 'usuario157@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:12:28', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(159, 'Usuario158', '', 'usuario158@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:12:28', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(160, 'Usuario159', '', 'usuario159@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:12:28', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(161, 'Usuario160', '', 'usuario160@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:12:28', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(162, 'Usuario161', '', 'usuario161@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:12:28', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(163, 'Usuario162', '', 'usuario162@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:12:28', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(164, 'Usuario163', '', 'usuario163@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:12:28', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(165, 'Usuario164', '', 'usuario164@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:12:28', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(166, 'Usuario165', '', 'usuario165@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:12:28', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(167, 'Usuario166', '', 'usuario166@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(168, 'Usuario167', '', 'usuario167@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(169, 'Usuario168', '', 'usuario168@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(170, 'Usuario169', '', 'usuario169@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(171, 'Usuario170', '', 'usuario170@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(172, 'Usuario171', '', 'usuario171@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(173, 'Usuario172', '', 'usuario172@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(174, 'Usuario173', '', 'usuario173@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(175, 'Usuario174', '', 'usuario174@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(176, 'Usuario175', '', 'usuario175@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(177, 'Usuario176', '', 'usuario176@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(178, 'Usuario177', '', 'usuario177@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(179, 'Usuario178', '', 'usuario178@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(180, 'Usuario179', '', 'usuario179@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(181, 'Usuario180', '', 'usuario180@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(182, 'Usuario181', '', 'usuario181@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(183, 'Usuario182', '', 'usuario182@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(184, 'Usuario183', '', 'usuario183@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(185, 'Usuario184', '', 'usuario184@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(186, 'Usuario185', '', 'usuario185@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(187, 'Usuario186', '', 'usuario186@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(188, 'Usuario187', '', 'usuario187@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(189, 'Usuario188', '', 'usuario188@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(190, 'Usuario189', '', 'usuario189@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(191, 'Usuario190', '', 'usuario190@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(192, 'Usuario191', '', 'usuario191@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(193, 'Usuario192', '', 'usuario192@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(194, 'Usuario193', '', 'usuario193@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(195, 'Usuario194', '', 'usuario194@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(196, 'Usuario195', '', 'usuario195@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(197, 'Usuario196', '', 'usuario196@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(198, 'Usuario197', '', 'usuario197@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(199, 'Usuario198', '', 'usuario198@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(200, 'Usuario199', '', 'usuario199@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(201, 'Usuario200', '', 'usuario200@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(202, 'Usuario201', '', 'usuario201@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(203, 'Usuario202', '', 'usuario202@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(204, 'Usuario203', '', 'usuario203@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(205, 'Usuario204', '', 'usuario204@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(206, 'Usuario205', '', 'usuario205@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(207, 'Usuario206', '', 'usuario206@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(208, 'Usuario207', '', 'usuario207@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(209, 'Usuario208', '', 'usuario208@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(210, 'Usuario209', '', 'usuario209@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(211, 'Usuario210', '', 'usuario210@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(212, 'Usuario211', '', 'usuario211@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(213, 'Usuario212', '', 'usuario212@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(214, 'Usuario213', '', 'usuario213@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(215, 'Usuario214', '', 'usuario214@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(216, 'Usuario215', '', 'usuario215@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(217, 'Usuario216', '', 'usuario216@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(218, 'Usuario217', '', 'usuario217@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(219, 'Usuario218', '', 'usuario218@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(220, 'Usuario219', '', 'usuario219@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(221, 'Usuario220', '', 'usuario220@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(222, 'Usuario221', '', 'usuario221@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(223, 'Usuario222', '', 'usuario222@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(224, 'Usuario223', '', 'usuario223@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(225, 'Usuario224', '', 'usuario224@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(226, 'Usuario225', '', 'usuario225@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(227, 'Usuario226', '', 'usuario226@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(228, 'Usuario227', '', 'usuario227@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(229, 'Usuario228', '', 'usuario228@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(230, 'Usuario229', '', 'usuario229@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(231, 'Usuario230', '', 'usuario230@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(232, 'Usuario231', '', 'usuario231@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(233, 'Usuario232', '', 'usuario232@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(234, 'Usuario233', '', 'usuario233@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(235, 'Usuario234', '', 'usuario234@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(236, 'Usuario235', '', 'usuario235@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(237, 'Usuario236', '', 'usuario236@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(238, 'Usuario237', '', 'usuario237@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(239, 'Usuario238', '', 'usuario238@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(240, 'Usuario239', '', 'usuario239@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(241, 'Usuario240', '', 'usuario240@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(242, 'Usuario241', '', 'usuario241@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(243, 'Usuario242', '', 'usuario242@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(244, 'Usuario243', '', 'usuario243@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(245, 'Usuario244', '', 'usuario244@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(246, 'Usuario245', '', 'usuario245@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(247, 'Usuario246', '', 'usuario246@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(248, 'Usuario247', '', 'usuario247@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(249, 'Usuario248', '', 'usuario248@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(250, 'Usuario249', '', 'usuario249@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(251, 'Usuario250', '', 'usuario250@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(252, 'Usuario251', '', 'usuario251@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(253, 'Usuario252', '', 'usuario252@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(254, 'Usuario253', '', 'usuario253@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(255, 'Usuario254', '', 'usuario254@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(256, 'Usuario255', '', 'usuario255@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(257, 'Usuario256', '', 'usuario256@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(258, 'Usuario257', '', 'usuario257@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(259, 'Usuario258', '', 'usuario258@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(260, 'Usuario259', '', 'usuario259@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(261, 'Usuario260', '', 'usuario260@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(262, 'Usuario261', '', 'usuario261@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(263, 'Usuario262', '', 'usuario262@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(264, 'Usuario263', '', 'usuario263@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(265, 'Usuario264', '', 'usuario264@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11'),
(266, 'Usuario265', '', 'usuario265@gmail.com', '1234', 'usuario', 1, '2026-05-11 10:15:30', NULL, NULL, NULL, NULL, '2026-05-11 12:39:11');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios_clases`
--

CREATE TABLE `usuarios_clases` (
  `id_usuario_clase` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_clase` int(11) NOT NULL,
  `fecha_reserva` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios_clases`
--

INSERT INTO `usuarios_clases` (`id_usuario_clase`, `id_usuario`, `id_clase`, `fecha_reserva`) VALUES
(3, 2, 2, '2026-05-06 10:55:46'),
(4, 2, 3, '2026-05-06 10:55:47'),
(14, 6, 2, '2026-05-06 11:47:08'),
(15, 7, 2, '2026-05-06 11:47:08'),
(16, 8, 2, '2026-05-06 11:47:08'),
(17, 9, 2, '2026-05-06 11:47:08'),
(18, 10, 2, '2026-05-06 11:47:08'),
(19, 11, 2, '2026-05-06 11:47:08'),
(20, 14, 3, '2026-05-07 07:44:56'),
(21, 14, 5, '2026-05-07 09:30:44'),
(25, 5, 3, '2026-05-07 10:45:40'),
(26, 5, 5, '2026-05-07 10:45:45'),
(46, 15, 6, '2026-05-07 12:16:41'),
(47, 16, 6, '2026-05-07 12:16:41'),
(48, 17, 6, '2026-05-07 12:16:41'),
(49, 18, 7, '2026-05-07 12:16:41'),
(50, 19, 7, '2026-05-07 12:16:41'),
(51, 20, 8, '2026-05-07 12:16:41'),
(52, 21, 8, '2026-05-07 12:16:41'),
(53, 22, 8, '2026-05-07 12:16:41'),
(54, 23, 9, '2026-05-07 12:16:41'),
(55, 24, 9, '2026-05-07 12:16:41'),
(56, 15, 10, '2026-05-07 12:16:41'),
(57, 16, 10, '2026-05-07 12:16:41'),
(58, 17, 11, '2026-05-07 12:16:41'),
(59, 18, 11, '2026-05-07 12:16:41'),
(60, 19, 12, '2026-05-07 12:16:41'),
(61, 20, 12, '2026-05-07 12:16:41'),
(62, 21, 13, '2026-05-07 12:16:41'),
(63, 22, 13, '2026-05-07 12:16:41'),
(64, 23, 13, '2026-05-07 12:16:41'),
(65, 5, 13, '2026-05-07 12:17:43'),
(66, 5, 6, '2026-05-07 12:17:44'),
(67, 5, 7, '2026-05-07 12:17:45'),
(68, 5, 8, '2026-05-07 12:17:45'),
(69, 5, 9, '2026-05-07 12:17:46'),
(70, 76, 2, '2026-05-11 10:10:32'),
(71, 77, 2, '2026-05-11 10:10:32'),
(72, 78, 2, '2026-05-11 10:10:32'),
(73, 79, 3, '2026-05-11 10:10:32'),
(74, 80, 3, '2026-05-11 10:10:32'),
(77, 83, 5, '2026-05-11 10:10:32'),
(78, 84, 5, '2026-05-11 10:10:32'),
(79, 85, 6, '2026-05-11 10:10:32'),
(80, 86, 7, '2026-05-11 10:10:32'),
(81, 87, 7, '2026-05-11 10:10:32'),
(82, 88, 8, '2026-05-11 10:10:32'),
(83, 89, 8, '2026-05-11 10:10:32'),
(84, 90, 9, '2026-05-11 10:10:32'),
(85, 91, 10, '2026-05-11 10:10:32'),
(86, 92, 10, '2026-05-11 10:10:32'),
(87, 93, 11, '2026-05-11 10:10:32'),
(88, 94, 12, '2026-05-11 10:10:32'),
(89, 95, 13, '2026-05-11 10:10:32'),
(90, 76, 3, '2026-05-11 10:10:32'),
(92, 78, 5, '2026-05-11 10:10:32'),
(93, 79, 6, '2026-05-11 10:10:32'),
(94, 80, 7, '2026-05-11 10:10:32'),
(95, 81, 8, '2026-05-11 10:10:32'),
(96, 82, 9, '2026-05-11 10:10:32'),
(97, 83, 10, '2026-05-11 10:10:32'),
(98, 84, 11, '2026-05-11 10:10:32'),
(99, 85, 12, '2026-05-11 10:10:32');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios_planes`
--

CREATE TABLE `usuarios_planes` (
  `id_usuario_plan` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_plan` int(11) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date DEFAULT NULL,
  `usos_restantes` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios_planes`
--

INSERT INTO `usuarios_planes` (`id_usuario_plan`, `id_usuario`, `id_plan`, `fecha_inicio`, `fecha_fin`, `usos_restantes`) VALUES
(13, 2, 1, '2026-05-06', '2026-06-05', 1),
(14, 1, 1, '2026-05-06', '2026-06-05', 0),
(15, 3, 1, '2026-05-06', '2026-06-05', 0),
(16, 3, 2, '2026-05-06', '2026-07-05', 4),
(17, 14, 1, '2026-05-07', '2026-06-06', 0),
(18, 14, 2, '2026-05-07', '2026-07-06', 5),
(19, 14, 1, '2026-05-07', '2026-06-06', 1),
(20, 5, 2, '2026-05-07', '2026-05-06', 0),
(21, 5, 4, '2026-05-07', '2026-05-06', 0),
(22, 5, 1, '2026-05-07', '2026-06-06', 0),
(23, 5, 6, '2026-05-08', '2026-06-07', NULL),
(24, 6, 4, '2026-05-01', '2026-06-01', NULL),
(25, 7, 4, '2026-05-02', '2026-06-02', NULL),
(26, 8, 4, '2026-05-03', '2026-06-03', NULL),
(27, 9, 4, '2026-05-04', '2026-06-04', NULL),
(28, 10, 4, '2026-05-05', '2026-06-05', NULL),
(29, 11, 4, '2026-05-06', '2026-06-06', NULL),
(30, 12, 4, '2026-05-07', '2026-06-07', NULL),
(31, 14, 4, '2026-05-08', '2026-06-08', NULL),
(32, 15, 4, '2026-05-09', '2026-06-09', NULL),
(33, 16, 4, '2026-05-10', '2026-06-10', NULL),
(34, 17, 5, '2026-05-01', '2026-06-01', NULL),
(35, 18, 5, '2026-05-02', '2026-06-02', NULL),
(36, 19, 5, '2026-05-03', '2026-06-03', NULL),
(37, 20, 5, '2026-05-04', '2026-06-04', NULL),
(38, 21, 5, '2026-05-05', '2026-06-05', NULL),
(39, 22, 5, '2026-05-06', '2026-06-06', NULL),
(40, 23, 5, '2026-05-07', '2026-06-07', NULL),
(41, 24, 5, '2026-05-08', '2026-06-08', NULL),
(42, 5, 6, '2026-05-01', '2026-06-01', NULL),
(43, 6, 6, '2026-05-02', '2026-06-02', NULL),
(44, 7, 6, '2026-05-03', '2026-06-03', NULL),
(45, 8, 6, '2026-05-04', '2026-06-04', NULL),
(46, 9, 6, '2026-05-05', '2026-06-05', NULL),
(47, 10, 1, '2026-05-01', '2026-06-01', 1),
(48, 11, 1, '2026-05-02', '2026-06-02', 1),
(49, 12, 1, '2026-05-03', '2026-06-03', 1),
(50, 14, 1, '2026-05-04', '2026-06-04', 1),
(51, 15, 1, '2026-05-05', '2026-06-05', 1),
(52, 16, 2, '2026-05-01', '2026-07-01', 5),
(53, 17, 2, '2026-05-02', '2026-07-02', 4),
(54, 18, 2, '2026-05-03', '2026-07-03', 3),
(55, 19, 2, '2026-05-04', '2026-07-04', 5),
(56, 20, 2, '2026-05-05', '2026-07-05', 2),
(57, 21, 2, '2026-05-06', '2026-07-06', 4),
(58, 22, 3, '2026-05-01', '2026-08-01', 10),
(59, 23, 3, '2026-05-02', '2026-08-02', 8),
(60, 24, 3, '2026-05-03', '2026-08-03', 6),
(61, 5, 3, '2026-05-04', '2026-08-04', 9),
(62, 6, 3, '2026-05-05', '2026-08-05', 7),
(63, 7, 3, '2026-05-06', '2026-08-06', 5),
(64, 8, 4, '2026-02-01', '2026-03-01', NULL),
(65, 9, 5, '2026-01-01', '2026-02-01', NULL),
(66, 10, 6, '2026-03-01', '2026-04-01', NULL),
(67, 11, 2, '2026-01-01', '2026-03-01', 0),
(68, 12, 3, '2026-02-01', '2026-04-01', 0),
(69, 26, 4, '2026-05-01', '2026-06-01', NULL),
(70, 27, 4, '2026-05-01', '2026-06-01', NULL),
(71, 28, 4, '2026-05-01', '2026-06-01', NULL),
(72, 29, 4, '2026-05-01', '2026-06-01', NULL),
(73, 30, 4, '2026-05-01', '2026-06-01', NULL),
(74, 31, 4, '2026-05-01', '2026-06-01', NULL),
(75, 32, 4, '2026-05-01', '2026-06-01', NULL),
(76, 33, 4, '2026-05-01', '2026-06-01', NULL),
(77, 34, 4, '2026-05-01', '2026-06-01', NULL),
(78, 35, 4, '2026-05-01', '2026-06-01', NULL),
(79, 36, 5, '2026-05-01', '2026-06-01', NULL),
(80, 37, 5, '2026-05-01', '2026-06-01', NULL),
(81, 38, 5, '2026-05-01', '2026-06-01', NULL),
(82, 39, 5, '2026-05-01', '2026-06-01', NULL),
(83, 40, 5, '2026-05-01', '2026-06-01', NULL),
(84, 41, 5, '2026-05-01', '2026-06-01', NULL),
(85, 42, 5, '2026-05-01', '2026-06-01', NULL),
(86, 43, 5, '2026-05-01', '2026-06-01', NULL),
(87, 44, 5, '2026-05-01', '2026-06-01', NULL),
(88, 45, 5, '2026-05-01', '2026-06-01', NULL),
(89, 46, 6, '2026-05-01', '2026-06-01', NULL),
(90, 47, 6, '2026-05-01', '2026-06-01', NULL),
(91, 48, 6, '2026-05-01', '2026-06-01', NULL),
(92, 49, 6, '2026-05-01', '2026-06-01', NULL),
(93, 50, 6, '2026-05-01', '2026-06-01', NULL),
(94, 51, 6, '2026-05-01', '2026-06-01', NULL),
(95, 52, 6, '2026-05-01', '2026-06-01', NULL),
(96, 53, 6, '2026-05-01', '2026-06-01', NULL),
(97, 54, 6, '2026-05-01', '2026-06-01', NULL),
(98, 55, 6, '2026-05-01', '2026-06-01', NULL),
(99, 56, 1, '2026-05-01', '2026-06-01', 1),
(100, 57, 1, '2026-05-01', '2026-06-01', 1),
(101, 58, 1, '2026-05-01', '2026-06-01', 1),
(102, 59, 1, '2026-05-01', '2026-06-01', 1),
(103, 60, 1, '2026-05-01', '2026-06-01', 1),
(104, 61, 2, '2026-05-01', '2026-07-01', 5),
(105, 62, 2, '2026-05-01', '2026-07-01', 4),
(106, 63, 2, '2026-05-01', '2026-07-01', 3),
(107, 64, 2, '2026-05-01', '2026-07-01', 5),
(108, 65, 2, '2026-05-01', '2026-07-01', 2),
(109, 66, 3, '2026-05-01', '2026-08-01', 10),
(110, 67, 3, '2026-05-01', '2026-08-01', 8),
(111, 68, 3, '2026-05-01', '2026-08-01', 7),
(112, 69, 3, '2026-05-01', '2026-08-01', 9),
(113, 70, 3, '2026-05-01', '2026-08-01', 6),
(114, 71, 4, '2026-05-01', '2026-06-01', NULL),
(115, 72, 5, '2026-05-01', '2026-06-01', NULL),
(116, 73, 6, '2026-05-01', '2026-06-01', NULL),
(117, 74, 2, '2026-05-01', '2026-07-01', 5),
(118, 75, 3, '2026-05-01', '2026-08-01', 10),
(119, 76, 4, '2026-05-01', '2026-06-01', NULL),
(120, 77, 4, '2026-05-01', '2026-06-01', NULL),
(121, 78, 4, '2026-05-01', '2026-06-01', NULL),
(122, 79, 4, '2026-05-01', '2026-06-01', NULL),
(123, 80, 4, '2026-05-01', '2026-06-01', NULL),
(124, 81, 5, '2026-05-01', '2026-06-01', NULL),
(125, 82, 5, '2026-05-01', '2026-06-01', NULL),
(126, 83, 5, '2026-05-01', '2026-06-01', NULL),
(127, 84, 5, '2026-05-01', '2026-06-01', NULL),
(128, 85, 5, '2026-05-01', '2026-06-01', NULL),
(129, 86, 6, '2026-05-01', '2026-06-01', NULL),
(130, 87, 6, '2026-05-01', '2026-06-01', NULL),
(131, 88, 6, '2026-05-01', '2026-06-01', NULL),
(132, 89, 6, '2026-05-01', '2026-06-01', NULL),
(133, 90, 6, '2026-05-01', '2026-06-01', NULL),
(134, 91, 2, '2026-05-01', '2026-07-01', 5),
(135, 92, 2, '2026-05-01', '2026-07-01', 4),
(136, 93, 2, '2026-05-01', '2026-07-01', 3),
(137, 94, 3, '2026-05-01', '2026-08-01', 10),
(138, 95, 3, '2026-05-01', '2026-08-01', 8),
(139, 96, 4, '2026-05-01', '2026-06-01', NULL),
(140, 97, 4, '2026-05-01', '2026-06-01', NULL),
(141, 98, 4, '2026-05-01', '2026-06-01', NULL),
(142, 99, 4, '2026-05-01', '2026-06-01', NULL),
(143, 100, 4, '2026-05-01', '2026-06-01', NULL),
(144, 101, 4, '2026-05-01', '2026-06-01', NULL),
(145, 102, 4, '2026-05-01', '2026-06-01', NULL),
(146, 103, 4, '2026-05-01', '2026-06-01', NULL),
(147, 104, 4, '2026-05-01', '2026-06-01', NULL),
(148, 105, 4, '2026-05-01', '2026-06-01', NULL),
(149, 106, 5, '2026-05-01', '2026-06-01', NULL),
(150, 107, 5, '2026-05-01', '2026-06-01', NULL),
(151, 108, 5, '2026-05-01', '2026-06-01', NULL),
(152, 109, 5, '2026-05-01', '2026-06-01', NULL),
(153, 110, 5, '2026-05-01', '2026-06-01', NULL),
(154, 111, 5, '2026-05-01', '2026-06-01', NULL),
(155, 112, 5, '2026-05-01', '2026-06-01', NULL),
(156, 113, 5, '2026-05-01', '2026-06-01', NULL),
(157, 114, 5, '2026-05-01', '2026-06-01', NULL),
(158, 115, 5, '2026-05-01', '2026-06-01', NULL),
(159, 116, 6, '2026-05-01', '2026-06-01', NULL),
(160, 117, 6, '2026-05-01', '2026-06-01', NULL),
(161, 118, 6, '2026-05-01', '2026-06-01', NULL),
(162, 119, 6, '2026-05-01', '2026-06-01', NULL),
(163, 120, 6, '2026-05-01', '2026-06-01', NULL),
(164, 121, 6, '2026-05-01', '2026-06-01', NULL),
(165, 122, 6, '2026-05-01', '2026-06-01', NULL),
(166, 123, 6, '2026-05-01', '2026-06-01', NULL),
(167, 124, 6, '2026-05-01', '2026-06-01', NULL),
(168, 125, 6, '2026-05-01', '2026-06-01', NULL),
(169, 126, 1, '2026-05-01', '2026-06-01', 1),
(170, 127, 1, '2026-05-01', '2026-06-01', 1),
(171, 128, 1, '2026-05-01', '2026-06-01', 1),
(172, 129, 1, '2026-05-01', '2026-06-01', 1),
(173, 130, 1, '2026-05-01', '2026-06-01', 1),
(174, 131, 1, '2026-05-01', '2026-06-01', 1),
(175, 132, 1, '2026-05-01', '2026-06-01', 1),
(176, 133, 1, '2026-05-01', '2026-06-01', 1),
(177, 134, 1, '2026-05-01', '2026-06-01', 1),
(178, 135, 1, '2026-05-01', '2026-06-01', 1),
(179, 136, 2, '2026-05-01', '2026-07-01', 5),
(180, 137, 2, '2026-05-01', '2026-07-01', 4),
(181, 138, 2, '2026-05-01', '2026-07-01', 3),
(182, 139, 2, '2026-05-01', '2026-07-01', 5),
(183, 140, 2, '2026-05-01', '2026-07-01', 2),
(184, 141, 2, '2026-05-01', '2026-07-01', 5),
(185, 142, 2, '2026-05-01', '2026-07-01', 4),
(186, 143, 2, '2026-05-01', '2026-07-01', 3),
(187, 144, 2, '2026-05-01', '2026-07-01', 5),
(188, 145, 2, '2026-05-01', '2026-07-01', 2),
(189, 146, 3, '2026-05-01', '2026-08-01', 10),
(190, 147, 3, '2026-05-01', '2026-08-01', 8),
(191, 148, 3, '2026-05-01', '2026-08-01', 7),
(192, 149, 3, '2026-05-01', '2026-08-01', 9),
(193, 150, 3, '2026-05-01', '2026-08-01', 6),
(194, 151, 3, '2026-05-01', '2026-08-01', 10),
(195, 152, 3, '2026-05-01', '2026-08-01', 8),
(196, 153, 3, '2026-05-01', '2026-08-01', 7),
(197, 154, 3, '2026-05-01', '2026-08-01', 9),
(198, 155, 3, '2026-05-01', '2026-08-01', 6),
(199, 156, 4, '2026-05-01', '2026-06-01', NULL),
(200, 157, 5, '2026-05-01', '2026-06-01', NULL),
(201, 158, 6, '2026-05-01', '2026-06-01', NULL),
(202, 159, 2, '2026-05-01', '2026-07-01', 5),
(203, 160, 3, '2026-05-01', '2026-08-01', 10),
(204, 161, 4, '2026-05-01', '2026-06-01', NULL),
(205, 162, 5, '2026-05-01', '2026-06-01', NULL),
(206, 163, 6, '2026-05-01', '2026-06-01', NULL),
(207, 164, 2, '2026-05-01', '2026-07-01', 4),
(208, 165, 3, '2026-05-01', '2026-08-01', 8),
(209, 166, 4, '2026-05-11', '2026-06-10', NULL),
(210, 167, 4, '2026-05-11', '2026-06-10', NULL),
(211, 168, 4, '2026-05-11', '2026-06-10', NULL),
(212, 169, 4, '2026-05-11', '2026-06-10', NULL),
(213, 170, 4, '2026-05-11', '2026-06-10', NULL),
(214, 171, 4, '2026-05-11', '2026-06-10', NULL),
(215, 172, 4, '2026-05-11', '2026-06-10', NULL),
(216, 173, 4, '2026-05-11', '2026-06-10', NULL),
(217, 174, 4, '2026-05-11', '2026-06-10', NULL),
(218, 175, 4, '2026-05-11', '2026-06-10', NULL),
(219, 176, 4, '2026-05-11', '2026-06-10', NULL),
(220, 177, 4, '2026-05-11', '2026-06-10', NULL),
(221, 178, 4, '2026-05-11', '2026-06-10', NULL),
(222, 179, 4, '2026-05-11', '2026-06-10', NULL),
(223, 180, 4, '2026-05-11', '2026-06-10', NULL),
(224, 181, 5, '2026-05-11', '2026-06-10', NULL),
(225, 182, 5, '2026-05-11', '2026-06-10', NULL),
(226, 183, 5, '2026-05-11', '2026-06-10', NULL),
(227, 184, 5, '2026-05-11', '2026-06-10', NULL),
(228, 185, 5, '2026-05-11', '2026-06-10', NULL),
(229, 186, 5, '2026-05-11', '2026-06-10', NULL),
(230, 187, 5, '2026-05-11', '2026-06-10', NULL),
(231, 188, 5, '2026-05-11', '2026-06-10', NULL),
(232, 189, 5, '2026-05-11', '2026-06-10', NULL),
(233, 190, 5, '2026-05-11', '2026-06-10', NULL),
(234, 191, 5, '2026-05-11', '2026-06-10', NULL),
(235, 192, 5, '2026-05-11', '2026-06-10', NULL),
(236, 193, 5, '2026-05-11', '2026-06-10', NULL),
(237, 194, 5, '2026-05-11', '2026-06-10', NULL),
(238, 195, 5, '2026-05-11', '2026-06-10', NULL),
(239, 196, 6, '2026-05-11', '2026-06-10', NULL),
(240, 197, 6, '2026-05-11', '2026-06-10', NULL),
(241, 198, 6, '2026-05-11', '2026-06-10', NULL),
(242, 199, 6, '2026-05-11', '2026-06-10', NULL),
(243, 200, 6, '2026-05-11', '2026-06-10', NULL),
(244, 201, 6, '2026-05-11', '2026-06-10', NULL),
(245, 202, 6, '2026-05-11', '2026-06-10', NULL),
(246, 203, 6, '2026-05-11', '2026-06-10', NULL),
(247, 204, 6, '2026-05-11', '2026-06-10', NULL),
(248, 205, 6, '2026-05-11', '2026-06-10', NULL),
(249, 206, 6, '2026-05-11', '2026-06-10', NULL),
(250, 207, 6, '2026-05-11', '2026-06-10', NULL),
(251, 208, 6, '2026-05-11', '2026-06-10', NULL),
(252, 209, 6, '2026-05-11', '2026-06-10', NULL),
(253, 210, 6, '2026-05-11', '2026-06-10', NULL),
(254, 211, 1, '2026-05-11', '2026-06-10', 1),
(255, 212, 1, '2026-05-11', '2026-06-10', 1),
(256, 213, 1, '2026-05-11', '2026-06-10', 1),
(257, 214, 1, '2026-05-11', '2026-06-10', 1),
(258, 215, 1, '2026-05-11', '2026-06-10', 1),
(259, 216, 1, '2026-05-11', '2026-06-10', 1),
(260, 217, 1, '2026-05-11', '2026-06-10', 1),
(261, 218, 1, '2026-05-11', '2026-06-10', 1),
(262, 219, 1, '2026-05-11', '2026-06-10', 1),
(263, 220, 1, '2026-05-11', '2026-06-10', 1),
(264, 221, 2, '2026-05-11', '2026-07-10', 5),
(265, 222, 2, '2026-05-11', '2026-07-10', 5),
(266, 223, 2, '2026-05-11', '2026-07-10', 5),
(267, 224, 2, '2026-05-11', '2026-07-10', 5),
(268, 225, 2, '2026-05-11', '2026-07-10', 5),
(269, 226, 2, '2026-05-11', '2026-07-10', 5),
(270, 227, 2, '2026-05-11', '2026-07-10', 5),
(271, 228, 2, '2026-05-11', '2026-07-10', 5),
(272, 229, 2, '2026-05-11', '2026-07-10', 5),
(273, 230, 2, '2026-05-11', '2026-07-10', 5),
(274, 231, 3, '2026-05-11', '2026-08-09', 10),
(275, 232, 3, '2026-05-11', '2026-08-09', 10),
(276, 233, 3, '2026-05-11', '2026-08-09', 10),
(277, 234, 3, '2026-05-11', '2026-08-09', 10),
(278, 235, 3, '2026-05-11', '2026-08-09', 10),
(279, 236, 3, '2026-05-11', '2026-08-09', 10),
(280, 237, 3, '2026-05-11', '2026-08-09', 10),
(281, 238, 3, '2026-05-11', '2026-08-09', 10),
(282, 239, 3, '2026-05-11', '2026-08-09', 10),
(283, 240, 3, '2026-05-11', '2026-08-09', 10),
(284, 241, 4, '2026-05-11', '2026-06-10', NULL),
(285, 242, 4, '2026-05-11', '2026-06-10', NULL),
(286, 243, 5, '2026-05-11', '2026-06-10', NULL),
(287, 244, 5, '2026-05-11', '2026-06-10', NULL),
(288, 245, 6, '2026-05-11', '2026-06-10', NULL),
(289, 246, 6, '2026-05-11', '2026-06-10', NULL),
(290, 247, 1, '2026-05-11', '2026-06-10', 1),
(291, 248, 1, '2026-05-11', '2026-06-10', 1),
(292, 249, 2, '2026-05-11', '2026-07-10', 5),
(293, 250, 2, '2026-05-11', '2026-07-10', 5),
(294, 251, 3, '2026-05-11', '2026-08-09', 10),
(295, 252, 3, '2026-05-11', '2026-08-09', 10),
(296, 253, 5, '2026-05-11', '2026-06-10', NULL),
(297, 254, 4, '2026-05-11', '2026-06-10', NULL),
(298, 255, 6, '2026-05-11', '2026-06-10', NULL),
(299, 256, 5, '2026-05-11', '2026-06-10', NULL),
(300, 257, 4, '2026-05-11', '2026-06-10', NULL),
(301, 258, 3, '2026-05-11', '2026-08-09', 10),
(302, 259, 2, '2026-05-11', '2026-07-10', 5),
(303, 260, 1, '2026-05-11', '2026-06-10', 1),
(304, 261, 6, '2026-05-11', '2026-06-10', NULL),
(305, 262, 5, '2026-05-11', '2026-06-10', NULL),
(306, 263, 4, '2026-05-11', '2026-06-10', NULL),
(307, 264, 3, '2026-05-11', '2026-08-09', 10),
(308, 265, 2, '2026-05-11', '2026-07-10', 5);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `asistencia`
--
ALTER TABLE `asistencia`
  ADD PRIMARY KEY (`id_asistencia`),
  ADD UNIQUE KEY `id_usuario` (`id_usuario`,`fecha`);

--
-- Indices de la tabla `clases`
--
ALTER TABLE `clases`
  ADD PRIMARY KEY (`id_clase`),
  ADD KEY `id_entrenador` (`id_entrenador`);

--
-- Indices de la tabla `medidas_usuario`
--
ALTER TABLE `medidas_usuario`
  ADD PRIMARY KEY (`id_medida`),
  ADD KEY `idx_medidas_usuario` (`id_usuario`);

--
-- Indices de la tabla `planes`
--
ALTER TABLE `planes`
  ADD PRIMARY KEY (`id_plan`);

--
-- Indices de la tabla `sugerencias`
--
ALTER TABLE `sugerencias`
  ADD PRIMARY KEY (`id_sugerencia`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indices de la tabla `usuarios_clases`
--
ALTER TABLE `usuarios_clases`
  ADD PRIMARY KEY (`id_usuario_clase`),
  ADD UNIQUE KEY `id_usuario` (`id_usuario`,`id_clase`),
  ADD KEY `id_clase` (`id_clase`);

--
-- Indices de la tabla `usuarios_planes`
--
ALTER TABLE `usuarios_planes`
  ADD PRIMARY KEY (`id_usuario_plan`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_plan` (`id_plan`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `asistencia`
--
ALTER TABLE `asistencia`
  MODIFY `id_asistencia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `clases`
--
ALTER TABLE `clases`
  MODIFY `id_clase` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `medidas_usuario`
--
ALTER TABLE `medidas_usuario`
  MODIFY `id_medida` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `planes`
--
ALTER TABLE `planes`
  MODIFY `id_plan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `sugerencias`
--
ALTER TABLE `sugerencias`
  MODIFY `id_sugerencia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=267;

--
-- AUTO_INCREMENT de la tabla `usuarios_clases`
--
ALTER TABLE `usuarios_clases`
  MODIFY `id_usuario_clase` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100;

--
-- AUTO_INCREMENT de la tabla `usuarios_planes`
--
ALTER TABLE `usuarios_planes`
  MODIFY `id_usuario_plan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=309;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `asistencia`
--
ALTER TABLE `asistencia`
  ADD CONSTRAINT `asistencia_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `clases`
--
ALTER TABLE `clases`
  ADD CONSTRAINT `clases_ibfk_1` FOREIGN KEY (`id_entrenador`) REFERENCES `usuarios` (`id_usuario`) ON DELETE SET NULL;

--
-- Filtros para la tabla `medidas_usuario`
--
ALTER TABLE `medidas_usuario`
  ADD CONSTRAINT `fk_medidas_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `usuarios_clases`
--
ALTER TABLE `usuarios_clases`
  ADD CONSTRAINT `usuarios_clases_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE,
  ADD CONSTRAINT `usuarios_clases_ibfk_2` FOREIGN KEY (`id_clase`) REFERENCES `clases` (`id_clase`) ON DELETE CASCADE;

--
-- Filtros para la tabla `usuarios_planes`
--
ALTER TABLE `usuarios_planes`
  ADD CONSTRAINT `usuarios_planes_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE,
  ADD CONSTRAINT `usuarios_planes_ibfk_2` FOREIGN KEY (`id_plan`) REFERENCES `planes` (`id_plan`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
