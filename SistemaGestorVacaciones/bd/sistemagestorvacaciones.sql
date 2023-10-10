-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 08-10-2023 a las 05:15:28
-- Versión del servidor: 10.4.28-MariaDB
-- Versión de PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `sistemagestorvacaciones`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empleados`
--

CREATE TABLE `empleados` (
  `id` int(11) NOT NULL,
  `name` varchar(30) NOT NULL,
  `lastname` varchar(30) NOT NULL,
  `dni` int(10) NOT NULL,
  `telefono` int(30) NOT NULL,
  `nacionalidad` varchar(50) NOT NULL,
  `localidad` varchar(50) NOT NULL,
  `anios_ingreso` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `empleados`
--

INSERT INTO `empleados` (`id`, `name`, `lastname`, `dni`, `telefono`, `nacionalidad`, `localidad`, `anios_ingreso`) VALUES
(42, 'tomas', 'alcazar', 97796253, 1196322574, 'venezolano', 'caracas', 4),
(43, 'catalina', 'priscila', 55456852, 1146485236, 'uruguaya', 'montevideo', 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `login_register`
--

CREATE TABLE `login_register` (
  `id` int(11) NOT NULL,
  `name` varchar(30) NOT NULL,
  `username` varchar(30) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(300) NOT NULL,
  `role` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `login_register`
--

INSERT INTO `login_register` (`id`, `name`, `username`, `email`, `password`, `role`) VALUES
(1, 'administrador', 'admin', 'admin@gmail.com', '$2y$10$aydM9prlFOub59DAIlBRnecYNFeUt3raG4sVm3XQ9i1BRR4En9p4u', 'admin'),
(42, 'tomas', 'tomy', 'tomas@gmail.com', '$2y$10$lOnvCcluXkqnDyYqhE.nsuiFg5HC7QaYPaq1vuSqgaTvNjLpOz5Su', 'empleado'),
(43, 'catalina', 'cata', 'catu@gmail.com', '$2y$10$LxImeCt5u7N.GHRb..AhTeA4xfQIAsdBWbpj8E7NtTQkAu7yZDmhq', 'empleado');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitudes_vacaciones`
--

CREATE TABLE `solicitudes_vacaciones` (
  `id` int(11) NOT NULL,
  `empleado_id` int(11) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_end` date DEFAULT NULL,
  `days` int(50) NOT NULL,
  `estado` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `solicitudes_vacaciones`
--

INSERT INTO `solicitudes_vacaciones` (`id`, `empleado_id`, `fecha_inicio`, `fecha_end`, `days`, `estado`) VALUES
(43, 42, '2023-10-03', '2023-10-10', 7, 'Aprobado'),
(48, 43, '2023-10-12', '2023-10-22', 7, 'Rechazado'),
(53, 42, '2023-10-14', '2023-10-19', 14, 'Aprobado'),
(56, 42, '2023-10-07', '2023-10-28', 21, 'Pendiente'),
(65, 43, '2023-10-31', '2023-11-21', 21, 'Pendiente');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `empleados`
--
ALTER TABLE `empleados`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `login_register`
--
ALTER TABLE `login_register`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `solicitudes_vacaciones`
--
ALTER TABLE `solicitudes_vacaciones`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `empleados`
--
ALTER TABLE `empleados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT de la tabla `login_register`
--
ALTER TABLE `login_register`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT de la tabla `solicitudes_vacaciones`
--
ALTER TABLE `solicitudes_vacaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
