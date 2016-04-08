-- phpMyAdmin SQL Dump
-- version 4.4.13.1deb1
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 04-04-2016 a las 02:48:10
-- Versión del servidor: 5.6.28-0ubuntu0.15.10.1
-- Versión de PHP: 5.6.11-1ubuntu3.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `fut_temporada`
--
CREATE DATABASE IF NOT EXISTS `fut_temporada` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
USE `fut_temporada`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `equipos`
--

CREATE TABLE IF NOT EXISTS `equipos` (
  `identificador_l` int(2) NOT NULL,
  `identificador` int(3) NOT NULL,
  `nombre` varchar(400) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  `pais` varchar(200) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  `fundacion` int(4) NOT NULL,
  `web` varchar(600) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  `perfil` varchar(600) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  `imagen` varchar(600) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  `presidente` varchar(200) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  `entrandor` varchar(200) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `formatos`
--
INSERT INTO `equipos` (`identificador_l` , `identificador`, `nombre`, `pais`, `fundacion`, `web`, `perfil`, `imagen`, `presidente`, `entrandor`) VALUES
(0, 0, '', '', 0000, '', '', '', '', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `jornadas`
--

CREATE TABLE IF NOT EXISTS `jornadas` (
  `identificador_l` int(2) NOT NULL,
  `identificador` int(2) NOT NULL,
  `inicio` date NOT NULL,
  `fin` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `jornadas` (`identificador_l` , `identificador`, `inicio`, `fin`) VALUES
(0, 0, '', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `liga`
--

CREATE TABLE IF NOT EXISTS `liga` (
  `identificador` int(2) NOT NULL,
  `nombre` varchar(200) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  `pais` varchar(400) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `partidos`
--

CREATE TABLE IF NOT EXISTS `partidos` (
  `identificador_l` int(2) NOT NULL,
  `identificador_j` int(2) NOT NULL,
  `identificador_el` int(3) NOT NULL,
  `identificador_ev` int(3) NOT NULL,
  `gol_local` int(2) NOT NULL,
  `gol_visitante` int(2) NOT NULL,
  `locacion` varchar(200) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  `arbitro` varchar(200) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  `estadio` varchar(200) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  `goles` varchar(1000) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `liga`
--
ALTER TABLE `liga`
  ADD PRIMARY KEY (`identificador`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
