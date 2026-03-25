-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 25-03-2026 a las 22:07:34
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
-- Base de datos: `casino_gquimicas`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `area_visita`
--

CREATE TABLE `area_visita` (
  `id_area_visita` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `area_visita`
--

INSERT INTO `area_visita` (`id_area_visita`, `nombre`, `activo`, `created_at`, `updated_at`) VALUES
(1, 'CONTABILIDAD', 1, '2026-03-11 19:48:58', '2026-03-11 20:09:55'),
(2, 'TIC', 1, '2026-03-11 19:48:58', '2026-03-11 20:09:30'),
(3, 'SST', 1, '2026-03-11 19:48:58', '2026-03-11 20:10:55'),
(4, 'PRODUCCIÓN', 1, '2026-03-11 19:48:58', '2026-03-11 20:11:11'),
(5, 'GESTIÓN HUMANA', 1, '2026-03-11 19:48:58', '2026-03-11 20:10:15'),
(6, 'GERENCIA DE PLANTA', 1, '2026-03-11 20:12:10', '2026-03-11 20:12:10'),
(7, 'MANTENIMIENTO', 1, '2026-03-11 20:12:19', '2026-03-11 20:12:19'),
(8, 'ALMACÉN Y COMPRAS', 1, '2026-03-11 20:12:48', '2026-03-11 20:12:48');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `casinos`
--

CREATE TABLE `casinos` (
  `id_casino` int(10) UNSIGNED NOT NULL,
  `NIT` varchar(30) NOT NULL,
  `nombre` varchar(120) NOT NULL,
  `id_empresa` int(10) UNSIGNED NOT NULL,
  `id_sede` int(10) UNSIGNED DEFAULT NULL,
  `tipo_casino` varchar(20) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `casinos`
--

INSERT INTO `casinos` (`id_casino`, `NIT`, `nombre`, `id_empresa`, `id_sede`, `tipo_casino`, `activo`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, '800050622', 'RESTAURANTE LOS LAGOS', 1, 3, 'DOMICILIO', 1, '2026-02-17 00:01:57', '2026-02-26 01:42:30', NULL),
(2, '1060651196', 'CASINO JUAN MATEO VALENCIA', 1, 3, 'INTERNO', 1, '2026-02-17 01:05:41', '2026-02-26 01:42:19', NULL),
(3, '900918418', 'BRISTO', 4, 1, 'INTERNO', 1, '2026-03-05 19:29:52', '2026-03-05 19:29:52', NULL),
(4, '31989468', 'EL MOLINO', 4, 2, 'EXTERNO', 1, '2026-03-24 23:29:50', '2026-03-24 23:29:50', NULL),
(5, '31982231', 'JAMUNDO', 4, 2, 'EXTERNO', 1, '2026-03-24 23:30:56', '2026-03-24 23:30:56', NULL),
(6, '900522550', 'EL RANCHO', 4, 2, 'EXTERNO', 1, '2026-03-24 23:37:29', '2026-03-24 23:37:29', NULL),
(7, '11440920251', 'SIN ARREPENTIMIENTOS', 4, 2, 'EXTERNO', 1, '2026-03-24 23:38:34', '2026-03-24 23:38:34', NULL),
(8, '8903990113', 'LA PIZZERIA', 4, 2, 'EXTERNO', 1, '2026-03-24 23:39:58', '2026-03-24 23:39:58', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `casino_empresa`
--

CREATE TABLE `casino_empresa` (
  `id_casino` int(10) UNSIGNED NOT NULL,
  `id_empresa` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `casino_empresa`
--

INSERT INTO `casino_empresa` (`id_casino`, `id_empresa`) VALUES
(1, 1),
(2, 1),
(3, 2),
(3, 3),
(3, 4),
(4, 2),
(4, 3),
(4, 4),
(5, 2),
(5, 3),
(5, 4),
(6, 2),
(6, 3),
(6, 4),
(7, 2),
(7, 3),
(7, 4),
(8, 2),
(8, 3),
(8, 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cuentas_cobro`
--

CREATE TABLE `cuentas_cobro` (
  `id_cuenta` int(10) UNSIGNED NOT NULL,
  `id_casino` int(10) UNSIGNED NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `total_vales` int(10) UNSIGNED DEFAULT NULL,
  `valor_total` decimal(12,2) DEFAULT NULL,
  `archivo_pdf` varchar(255) DEFAULT NULL,
  `fecha_generacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `cuentas_cobro`
--

INSERT INTO `cuentas_cobro` (`id_cuenta`, `id_casino`, `fecha_inicio`, `fecha_fin`, `total_vales`, `valor_total`, `archivo_pdf`, `fecha_generacion`, `created_at`, `updated_at`) VALUES
(1, 1, '2026-03-04', '2026-03-04', 1, 17500.00, 'cuentas_cobro/cuenta-cobro-1-2026-03-04-2026-03-04.pdf', '2026-03-04 20:49:28', '2026-03-05 01:49:28', '2026-03-05 01:49:31'),
(2, 2, '2026-01-01', '2026-03-20', 3, 36007.00, 'cuentas_cobro/soporte-factura-2-2026-01-01-2026-03-20.pdf', '2026-03-20 14:42:23', '2026-03-20 19:42:23', '2026-03-20 19:42:27'),
(3, 2, '2026-03-20', '2026-03-20', 1, 7.00, 'cuentas_cobro/soporte-factura-3-2026-03-20-2026-03-20.pdf', '2026-03-20 14:43:01', '2026-03-20 19:43:01', '2026-03-20 19:43:01'),
(4, 2, '2026-03-20', '2026-03-20', 1, 7.00, 'cuentas_cobro/soporte-factura-4-2026-03-20-2026-03-20.pdf', '2026-03-20 14:45:10', '2026-03-20 19:45:10', '2026-03-20 19:45:10'),
(5, 2, '2026-01-01', '2026-03-20', 3, 36007.00, 'cuentas_cobro/soporte-factura-5-2026-01-01-2026-03-20.pdf', '2026-03-20 14:45:43', '2026-03-20 19:45:43', '2026-03-20 19:45:43'),
(6, 2, '2026-01-01', '2026-03-20', 3, 36007.00, 'cuentas_cobro/soporte-factura-6-2026-01-01-2026-03-20.pdf', '2026-03-20 16:35:29', '2026-03-20 21:35:29', '2026-03-20 21:35:30'),
(7, 2, '2026-01-01', '2026-03-20', 3, 36007.00, 'cuentas_cobro/soporte-factura-7-2026-01-01-2026-03-20.pdf', '2026-03-20 16:37:43', '2026-03-20 21:37:43', '2026-03-20 21:37:43'),
(8, 2, '2026-01-01', '2026-03-20', 3, 36007.00, 'cuentas_cobro/soporte-factura-8-2026-01-01-2026-03-20.pdf', '2026-03-20 16:44:15', '2026-03-20 21:44:15', '2026-03-20 21:44:15');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empresas`
--

CREATE TABLE `empresas` (
  `id_empresa` int(10) UNSIGNED NOT NULL,
  `NIT` varchar(30) NOT NULL,
  `nombre` varchar(120) NOT NULL,
  `activa` tinyint(1) NOT NULL DEFAULT 1,
  `correos_cuenta_cobro` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `empresas`
--

INSERT INTO `empresas` (`id_empresa`, `NIT`, `nombre`, `activa`, `correos_cuenta_cobro`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, '800027867', 'IBC', 1, NULL, '2026-02-16 06:57:38', '2026-02-16 06:57:38', NULL),
(2, '890312652', 'QBC', 1, NULL, '2026-02-16 21:31:54', '2026-02-16 21:31:54', NULL),
(3, '817000771', 'QC', 1, NULL, '2026-02-16 21:38:00', '2026-02-16 21:38:00', NULL),
(4, '901392625', 'ECOVITTA', 1, NULL, '2026-02-16 21:38:42', '2026-02-16 21:38:42', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empresas_contratistas`
--

CREATE TABLE `empresas_contratistas` (
  `id_empresa_contratista` int(10) UNSIGNED NOT NULL,
  `nit` varchar(30) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `activa` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `empresas_contratistas`
--

INSERT INTO `empresas_contratistas` (`id_empresa_contratista`, `nit`, `nombre`, `activa`, `created_at`, `updated_at`) VALUES
(1, '890312749', 'SEGURIDAD ATLAS', 1, '2026-03-25 20:53:25', '2026-03-25 21:02:35');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empresas_temporales`
--

CREATE TABLE `empresas_temporales` (
  `id_empresa_temporal` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `activa` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `empresas_temporales`
--

INSERT INTO `empresas_temporales` (`id_empresa_temporal`, `nombre`, `activa`, `created_at`, `updated_at`) VALUES
(1, 'TEMPLEAMOS', 1, '2026-03-12 23:11:08', '2026-03-12 23:11:08'),
(2, 'OCUPAR', 1, '2026-03-18 01:16:53', '2026-03-18 01:16:53');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empresa_usuario`
--

CREATE TABLE `empresa_usuario` (
  `id_usuario` int(10) UNSIGNED NOT NULL,
  `id_empresa` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `empresa_usuario`
--

INSERT INTO `empresa_usuario` (`id_usuario`, `id_empresa`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(7, 1),
(8, 2),
(8, 3),
(8, 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `horarios_consumo`
--

CREATE TABLE `horarios_consumo` (
  `id_horario` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `horarios_consumo`
--

INSERT INTO `horarios_consumo` (`id_horario`, `nombre`, `hora_inicio`, `hora_fin`, `activo`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'REFRIGERIO', '15:01:00', '11:29:00', 1, '2026-02-16 02:49:11', '2026-03-25 22:08:13', NULL),
(2, 'ALMUERZO', '11:30:00', '15:00:00', 1, '2026-02-16 02:49:11', '2026-03-25 22:06:41', NULL),
(3, 'CENA', '15:01:00', '11:29:00', 1, '2026-02-16 02:49:11', '2026-03-25 22:08:41', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_02_16_000000_drop_old_roles_structure_for_empresarial', 1),
(5, '2025_02_16_000001_create_roles_empresarial_table', 1),
(6, '2025_02_16_000002_create_empresas_table', 1),
(7, '2025_02_16_000003_create_tipos_usuario_table', 1),
(8, '2025_02_16_000004_create_usuarios_table', 1),
(9, '2025_02_16_000005_create_visitantes_table', 1),
(10, '2025_02_16_000006_create_casinos_table', 1),
(11, '2025_02_16_000007_create_horarios_consumo_table', 1),
(12, '2025_02_16_000008_create_registro_consumos_table', 1),
(13, '2025_02_16_000009_create_cuentas_cobro_table', 1),
(14, '2025_02_17_000001_add_role_to_users_table', 2),
(15, '2025_02_18_000001_create_precios_table', 3),
(16, '2025_02_19_000001_add_domicilio_to_registro_consumos', 4),
(17, '2025_02_18_000001_create_empresa_user_table', 5),
(18, '2025_02_23_000001_add_documento_to_users_table', 6),
(19, '2025_02_23_000002_drop_users_and_pivot_tables', 7),
(20, '2025_02_23_000003_create_empresa_usuario_table', 8),
(21, '2025_02_23_000004_add_correos_cuenta_cobro_to_empresas_table', 9),
(22, '2025_02_23_000005_add_id_casino_asignado_to_usuarios_table', 10),
(23, '2025_02_23_000006_create_sedes_table', 11),
(24, '2025_02_23_000007_add_id_sede_to_casinos_table', 11),
(25, '2025_02_23_000008_add_id_sede_principal_to_usuarios_table', 11),
(26, '2025_02_23_000009_create_sede_usuario_table', 11),
(27, '2025_02_23_000001_add_snapshot_to_registro_consumos', 12),
(28, '2025_02_23_000010_drop_precio_texto_from_registro_consumos', 13),
(29, '2025_03_04_000001_create_casino_empresa_table', 14),
(30, '2025_03_04_000002_create_area_visita_table', 15),
(31, '2025_03_04_000003_add_id_area_visita_to_registro_consumos', 15),
(32, '2025_03_11_000001_create_empresas_temporales_table', 16),
(33, '2025_03_11_000002_add_id_empresa_temporal_to_usuarios_table', 16),
(34, '2025_03_11_000003_add_empresa_temporal_nombre_to_registro_consumos', 17),
(35, '2025_03_12_000001_add_cambiar_clave_obligatorio_to_usuarios', 18),
(36, '2025_03_12_000002_drop_email_from_usuarios_table', 19),
(37, '2026_03_25_000001_add_tipo_comida_to_registro_consumos', 20),
(38, '2026_03_25_100000_create_empresas_contratistas_table', 21),
(39, '2026_03_25_100001_add_id_empresa_contratista_to_usuarios_table', 21);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `precios`
--

CREATE TABLE `precios` (
  `id_precio` int(10) UNSIGNED NOT NULL,
  `id_horario` int(10) UNSIGNED NOT NULL,
  `id_casino` int(10) UNSIGNED DEFAULT NULL,
  `precio_empleado` decimal(10,0) DEFAULT NULL,
  `precio_casino` decimal(10,0) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `precios`
--

INSERT INTO `precios` (`id_precio`, `id_horario`, `id_casino`, `precio_empleado`, `precio_casino`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 7, 7, '2026-02-17 01:05:41', '2026-03-04 00:24:04'),
(2, 2, 2, 3600, 18000, '2026-02-17 01:05:41', '2026-02-17 01:05:41'),
(3, 3, 2, 3600, 18000, '2026-02-17 01:05:41', '2026-02-17 01:05:41'),
(4, 1, 1, 6000, 6000, '2026-02-26 01:42:30', '2026-03-04 00:24:42'),
(5, 2, 1, 17500, 17500, '2026-02-26 01:42:30', '2026-03-04 00:24:42'),
(6, 3, 1, 17500, 17500, '2026-02-26 01:42:30', '2026-03-04 00:24:42'),
(7, 1, 3, 0, 0, '2026-03-05 19:29:52', '2026-03-05 19:29:52'),
(8, 2, 3, 3336, 16680, '2026-03-05 19:29:52', '2026-03-05 19:29:52'),
(9, 3, 3, 3336, 16680, '2026-03-05 19:29:52', '2026-03-05 19:29:52'),
(10, 1, 4, 0, 0, '2026-03-24 23:29:50', '2026-03-24 23:29:50'),
(11, 2, 4, 3200, 16000, '2026-03-24 23:29:50', '2026-03-24 23:29:50'),
(12, 3, 4, 3200, 16000, '2026-03-24 23:29:50', '2026-03-24 23:29:50'),
(13, 1, 5, 0, 0, '2026-03-24 23:30:56', '2026-03-24 23:30:56'),
(14, 2, 5, 3100, 15500, '2026-03-24 23:30:56', '2026-03-24 23:30:56'),
(15, 3, 5, 3100, 15500, '2026-03-24 23:30:56', '2026-03-24 23:30:56'),
(16, 1, 6, 0, 0, '2026-03-24 23:37:29', '2026-03-24 23:37:29'),
(17, 2, 6, 4835, 24173, '2026-03-24 23:37:29', '2026-03-24 23:37:29'),
(18, 3, 6, 4835, 24173, '2026-03-24 23:37:29', '2026-03-24 23:37:29'),
(19, 1, 7, 0, 0, '2026-03-24 23:38:34', '2026-03-24 23:38:34'),
(20, 2, 7, 4642, 23312, '2026-03-24 23:38:34', '2026-03-24 23:38:34'),
(21, 3, 7, 4642, 23312, '2026-03-24 23:38:34', '2026-03-24 23:38:34'),
(22, 1, 8, 0, 0, '2026-03-24 23:39:58', '2026-03-24 23:39:58'),
(23, 2, 8, 4000, 20000, '2026-03-24 23:39:58', '2026-03-24 23:39:58'),
(24, 3, 8, 4000, 20000, '2026-03-24 23:39:58', '2026-03-24 23:39:58');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `registro_consumos`
--

CREATE TABLE `registro_consumos` (
  `id_consumo` int(10) UNSIGNED NOT NULL,
  `id_usuario` int(10) UNSIGNED DEFAULT NULL,
  `id_visitante` int(10) UNSIGNED DEFAULT NULL,
  `id_area_visita` int(10) UNSIGNED DEFAULT NULL,
  `documento` varchar(50) DEFAULT NULL,
  `nombres_consumidor` varchar(255) DEFAULT NULL,
  `tipo_usuario_nombre` varchar(50) DEFAULT NULL,
  `empresa_nombre` varchar(255) DEFAULT NULL,
  `empresa_temporal_nombre` varchar(255) DEFAULT NULL,
  `id_empresa` int(10) UNSIGNED NOT NULL,
  `id_casino` int(10) UNSIGNED NOT NULL,
  `casino_nombre` varchar(255) DEFAULT NULL,
  `id_horario` int(10) UNSIGNED NOT NULL,
  `horario_nombre` varchar(100) DEFAULT NULL,
  `tipo_comida` varchar(50) DEFAULT NULL,
  `fecha_consumo` date NOT NULL,
  `hora_consumo` time NOT NULL,
  `precio_casino` decimal(10,2) NOT NULL,
  `precio_empleado` decimal(10,2) NOT NULL,
  `registrado_por` int(10) UNSIGNED DEFAULT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `estado` varchar(20) NOT NULL,
  `tipo_pedido` varchar(20) NOT NULL DEFAULT 'en_sitio',
  `direccion_entrega` varchar(500) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id_rol` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id_rol`, `nombre`, `created_at`, `updated_at`) VALUES
(1, 'empleado', '2026-02-16 02:49:11', '2026-02-16 02:49:11'),
(2, 'casino', '2026-02-16 02:49:11', '2026-02-16 02:49:11'),
(3, 'gestionhumana', '2026-02-16 02:49:11', '2026-02-16 02:49:11'),
(4, 'administrador', '2026-02-16 02:49:11', '2026-02-16 02:49:11'),
(5, 'operativo', '2026-02-16 02:49:11', '2026-02-16 02:49:11');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sedes`
--

CREATE TABLE `sedes` (
  `id_sede` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(120) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `sedes`
--

INSERT INTO `sedes` (`id_sede`, `nombre`, `created_at`, `updated_at`) VALUES
(1, 'PLANTA CALOTO', '2026-02-26 01:41:18', '2026-02-26 01:41:18'),
(2, 'OFICINAS CALI', '2026-02-26 01:41:28', '2026-02-26 01:41:28'),
(3, 'IBC MANIZALES', '2026-02-26 01:41:35', '2026-02-26 01:41:45');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sede_usuario`
--

CREATE TABLE `sede_usuario` (
  `id_usuario` int(10) UNSIGNED NOT NULL,
  `id_sede` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `sede_usuario`
--

INSERT INTO `sede_usuario` (`id_usuario`, `id_sede`) VALUES
(2, 1),
(2, 2),
(2, 3),
(5, 3),
(10, 1),
(10, 2),
(15, 1),
(22, 1),
(22, 2),
(30, 1),
(30, 2),
(30, 3),
(36, 1),
(40, 1),
(40, 2),
(41, 1),
(44, 1),
(52, 1),
(52, 2),
(53, 1),
(53, 2),
(54, 1),
(54, 2),
(56, 1),
(56, 2),
(57, 1),
(57, 2),
(62, 1),
(63, 1),
(92, 2),
(98, 2),
(98, 3),
(102, 1),
(102, 2),
(102, 3),
(105, 1),
(105, 2),
(105, 3),
(122, 1),
(122, 2),
(122, 3),
(131, 1),
(131, 2),
(131, 3),
(166, 1),
(166, 2),
(166, 3),
(179, 1),
(179, 2),
(179, 3),
(210, 1),
(210, 2),
(210, 3),
(211, 1),
(211, 2),
(211, 3),
(235, 1),
(236, 1),
(237, 1),
(237, 2),
(237, 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('XewbgczDpAjKZPKJPIr7Wp2KxwsZWwUXVBI1LdXi', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiWTdCdjZlT2ZkdHRoUjc5SWFQN0MxYWswR2tkUnR0b1R3eUZkY2FUSCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NjU6Imh0dHA6Ly9sb2NhbGhvc3QvQ2FzaW5vR3F1aW1pY2FzL3B1YmxpYy9hZG1pbi9lbXByZXNhcy10ZW1wb3JhbGVzIjtzOjU6InJvdXRlIjtzOjMxOiJhZG1pbi5lbXByZXNhcy10ZW1wb3JhbGVzLmluZGV4Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTt9', 1774471537);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipos_usuario`
--

CREATE TABLE `tipos_usuario` (
  `id_tipo_usuario` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tipos_usuario`
--

INSERT INTO `tipos_usuario` (`id_tipo_usuario`, `nombre`, `created_at`, `updated_at`) VALUES
(1, 'FIJO', '2026-02-16 02:49:11', '2026-02-16 02:49:11'),
(2, 'TEMPORAL', '2026-02-16 02:49:11', '2026-02-16 02:49:11'),
(3, 'SENA', '2026-02-16 02:49:11', '2026-02-16 02:49:11'),
(4, 'PASANTE', '2026-02-16 02:49:11', '2026-02-16 02:49:11'),
(5, 'CONTRATISTA', '2026-02-16 02:49:11', '2026-02-16 02:49:11');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(10) UNSIGNED NOT NULL,
  `documento` varchar(20) NOT NULL,
  `nombres` varchar(80) NOT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `cambiar_clave_obligatorio` tinyint(1) NOT NULL DEFAULT 0,
  `id_empresa` int(10) UNSIGNED NOT NULL,
  `id_casino_asignado` int(10) UNSIGNED DEFAULT NULL,
  `id_sede_principal` int(10) UNSIGNED DEFAULT NULL,
  `id_rol` int(10) UNSIGNED NOT NULL,
  `id_tipo_usuario` int(10) UNSIGNED DEFAULT NULL,
  `id_empresa_temporal` int(10) UNSIGNED DEFAULT NULL,
  `id_empresa_contratista` int(10) UNSIGNED DEFAULT NULL,
  `codigo_qr` varchar(255) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `documento`, `nombres`, `password_hash`, `cambiar_clave_obligatorio`, `id_empresa`, `id_casino_asignado`, `id_sede_principal`, `id_rol`, `id_tipo_usuario`, `id_empresa_temporal`, `id_empresa_contratista`, `codigo_qr`, `activo`, `fecha_creacion`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, '1060652062', 'LUISA FERNANDA ARROYAVE', '$2y$12$2Uf64PyMlI6z/.ap3FOlX.HgTk5Rxnbike5ITTTj1gS.KK3MWuSUO', 0, 1, 2, NULL, 4, 1, NULL, NULL, NULL, 1, '2026-02-17 20:50:42', '2026-02-18 01:50:42', '2026-02-18 01:50:42', NULL),
(2, '1060590534', 'HERNAN LARGO', '$2y$12$T/dAmV787WbxrLz/QT/mfu87N0RMayXa0hM1QfoZrGS3V2UK5neHG', 0, 1, 2, 3, 1, 1, NULL, NULL, NULL, 1, '2026-02-23 18:32:47', '2026-02-23 23:32:47', '2026-03-10 01:15:08', '2026-03-10 01:15:08'),
(3, '1060651196', 'MATEO VALENCIA', '$2y$12$iFw/deNIpX9rmqHjFHXejun6HZzfym3mtAv47hY0XJMWINdowtm8a', 0, 1, 2, NULL, 2, 5, NULL, NULL, NULL, 1, '2026-02-24 17:22:32', '2026-02-24 22:22:32', '2026-03-20 18:12:33', NULL),
(5, '54321', 'MARIA ALEJANDRA ARROYAVE', '$2y$12$WINjievzgUOOa.BBL.X9Pupj8kJTFq0kUQ8snsWNJH8X8kakv1GXO', 0, 1, NULL, 3, 1, 1, NULL, NULL, NULL, 1, '2026-02-27 19:24:27', '2026-02-28 00:24:27', '2026-03-10 01:15:01', '2026-03-10 01:15:01'),
(6, '800050622', 'RESTAURANTE LOS LAGOS', '$2y$12$BYS8vO56wBOe36Usjv8Yqee8qrXI37TSBz.LTn5nF9UYDeuWVM.Ja', 0, 1, 1, NULL, 2, 5, NULL, NULL, NULL, 1, '2026-03-04 20:48:10', '2026-03-05 01:48:10', '2026-03-05 01:48:10', NULL),
(7, '800027867', 'INDUSTRIAS BÁSICAS DE CALDAS', '$2y$12$s9qq1qJG5vNJXlrHxD873.Y3K.N/4dAdgEONhnB0VBk2NjhMmTP/W', 0, 1, 2, NULL, 4, 1, NULL, NULL, NULL, 1, '2026-03-11 14:23:22', '2026-03-11 19:23:22', '2026-03-13 21:16:58', NULL),
(8, '817000771', 'QUIMICOS DEL CAUCA', '$2y$12$cfmqfZxeQOdYeukfCX2KWOcD5HBbA.9CuD27R8au5/p6UevH0.hpa', 0, 3, 2, 3, 4, 1, NULL, NULL, NULL, 1, '2026-03-11 14:28:33', '2026-03-11 19:28:33', '2026-03-11 19:28:33', NULL),
(9, '1002542797', 'NELSON ALEJANDRO LOAIZA', '$2y$12$ypq51bXMpQmtoCkcbPzY9eSlqDRlj8rEo9UATSDjOX1JKMp1JX.b6', 1, 1, 2, 3, 1, 1, NULL, NULL, NULL, 1, '2026-03-13 18:44:36', '2026-03-13 23:44:36', '2026-03-13 23:44:36', NULL),
(10, '1002566120', 'SARA MANUELA CARDONA MONTOYA', '$2y$12$7uYlpY.BPNATPA8/b2sjQew9igK4XFLDRfcQnBIX7q4JNbm/7/Hu2', 1, 1, NULL, 3, 1, 1, NULL, NULL, NULL, 1, '2026-03-13 18:44:37', '2026-03-13 23:44:37', '2026-03-17 19:47:39', NULL),
(11, '1002633812', 'JUAN ESTEBAN CASTAÑO OTALVARO', '$2y$12$ihkfPIh4FakZdLAHW7eQb.7DBkfMY1nMXsB51OO1devamJrqi1vuy', 1, 1, 2, 3, 1, 1, NULL, NULL, NULL, 1, '2026-03-13 18:44:37', '2026-03-13 23:44:37', '2026-03-13 23:44:37', NULL),
(12, '1002634393', 'JORGE HERNAN GIRALDO FAJARDO', '$2y$12$jKCMhW5wshwubm0eiXaRS.MU5enBHnrFcTJA1lspEwEykQ91qNtty', 1, 1, 2, 3, 1, 1, NULL, NULL, NULL, 1, '2026-03-13 18:44:38', '2026-03-13 23:44:38', '2026-03-13 23:44:38', NULL),
(13, '1002654570', 'JUAN DIEGO VALENCIA ARANGO', '$2y$12$DSivxPAZoT9CnUEJQJSSEeptH4Zk6ccnsFkmpGewFUPLxT/bhulu.', 1, 1, 2, 3, 1, 1, NULL, NULL, NULL, 1, '2026-03-13 18:44:38', '2026-03-13 23:44:38', '2026-03-13 23:44:38', NULL),
(14, '10027177', 'DIEGO FERNANDO RIOS RINCON', '$2y$12$YJEWXWMD43dCN2WLf4k3d.1bwO6MRLYxvqU5iYjfoBuCEvOy9Q44m', 1, 1, 2, 3, 1, 1, NULL, NULL, NULL, 1, '2026-03-13 18:44:38', '2026-03-13 23:44:38', '2026-03-13 23:44:38', NULL),
(15, '1002718773', 'YHULEXY YHOXANA PARRA HERNANDEZ', '$2y$12$iT2vwGgqQm2dokIYyVnBUOI.VoIAlLrL.efFVDyEdueDA6WV4414G', 1, 1, NULL, 3, 1, 1, NULL, NULL, NULL, 1, '2026-03-13 18:44:39', '2026-03-13 23:44:39', '2026-03-17 19:45:03', NULL),
(16, '1006440660', 'ANGY KATHERINE LAGOS PEÑA', '$2y$12$T2MF/O9SY/PZFWHWGNDbeuglP9s7rst4IR5kxf8B4QB4zaEUCt4Je', 1, 1, 2, 3, 1, 1, NULL, NULL, NULL, 1, '2026-03-13 18:44:39', '2026-03-13 23:44:39', '2026-03-13 23:44:39', NULL),
(17, '1010036297', 'JHOAN SEBASTIAN ECHEVERRY HURTADO', '$2y$12$zn3MvM8UaFW8V.062Hgzy.iguPcnITFXU.7Hnek/sU3DbQmOa8zSy', 1, 1, 2, 3, 1, 1, NULL, NULL, NULL, 1, '2026-03-13 18:44:40', '2026-03-13 23:44:40', '2026-03-13 23:44:40', NULL),
(18, '1013115240', 'SARAY CATALINA PEREZ NOVA', '$2y$12$AMf7u4kNgt41ZfLL3cHZiuAAeyEAI6ImW8LGaI6BCuO9CsBcPpJVG', 1, 1, NULL, 3, 1, 3, NULL, NULL, NULL, 1, '2026-03-13 18:44:40', '2026-03-13 23:44:40', '2026-03-17 19:47:22', NULL),
(19, '10195448', 'DUVAN DE JESUS TUBERQUI RAMIREZ', '$2y$12$8v/yIgA/5VTFffpOrsEd2eClxuv0Qdf/cjLDwf6BtPd3fsBuwxtKu', 1, 1, 2, 3, 1, 1, NULL, NULL, NULL, 1, '2026-03-13 18:44:40', '2026-03-13 23:44:40', '2026-03-13 23:44:40', NULL),
(20, '1020713358', 'ANDRES FELIPE MEDINA CAMACHO', '$2y$12$J3uggxNWvPuLAmqPE5lAwevAIImwICFUjGRQyZPa4TVirHVeMg7SW', 1, 1, 2, 3, 1, 1, NULL, NULL, NULL, 1, '2026-03-13 18:44:41', '2026-03-13 23:44:41', '2026-03-13 23:44:41', NULL),
(21, '10270612', 'JAVIER GALLEGO ZAPATA', '$2y$12$Xq.AWfOmjxqo3wLLSQuMIO8aAVsIiVahbyBP/1HU52pGAua6gWEFa', 1, 1, 2, 3, 1, 1, NULL, NULL, NULL, 1, '2026-03-13 18:44:41', '2026-03-13 23:44:41', '2026-03-13 23:44:41', NULL),
(22, '10271283', 'HECTOR CIPRIANO GONZALEZ VARGAS', '$2y$12$ozfUCfEnfqQYuqZOsZGgd.ae7doX9xY/UmVZKBLAxq9.puHvk5fjC', 1, 1, NULL, 3, 1, 1, NULL, NULL, NULL, 1, '2026-03-13 18:44:41', '2026-03-13 23:44:41', '2026-03-14 00:18:35', NULL),
(23, '10278795', 'MARTIN FERNANDO GUTIERREZ VARGAS', '$2y$12$pj9zjZOnBaR1eqPeRxVqReCjrVBLayRKnRDCsSM8C3ddnXWERgd8i', 1, 1, 2, 3, 1, 1, NULL, NULL, NULL, 1, '2026-03-13 18:44:42', '2026-03-13 23:44:42', '2026-03-13 23:44:42', NULL),
(24, '10287098', 'LEONARDO RIOS CATAÑO', '$2y$12$UHY5NialXpXGK375CupUiOHBdb/BcdfxFXI3ocPdHOtX4SowuCsYS', 1, 1, 2, 3, 1, 1, NULL, NULL, NULL, 1, '2026-03-13 18:44:42', '2026-03-13 23:44:42', '2026-03-13 23:44:42', NULL),
(25, '10288462', 'WILLIAM PEREZ SALAZAR', '$2y$12$2Z2teJI7gzqF4ufGs2rZke2yN/wFmCrmlY.BG.hYj8gQy15uOxzqK', 1, 1, 2, 3, 1, 1, NULL, NULL, NULL, 1, '2026-03-13 18:44:43', '2026-03-13 23:44:43', '2026-03-13 23:44:43', NULL),
(26, '10289003', 'JORGE HERNAN RIVERA', '$2y$12$G5eoEaDreIIqMPIBJpHb4ukP6zRGoE9iRH38P3gh7iaf/ootfJoPC', 1, 1, 2, 3, 1, 1, NULL, NULL, NULL, 1, '2026-03-13 18:44:43', '2026-03-13 23:44:43', '2026-03-13 23:44:43', NULL),
(27, '1033739307', 'ANGIE MILENA CASTAÑEDA MAHECHA', '$2y$12$fP450pfqd0mFWZsOao8UoOwwMptrTuMdrcv/mSVP1LLAOTzNdsKR6', 1, 1, 2, 3, 1, 1, NULL, NULL, NULL, 1, '2026-03-13 18:44:43', '2026-03-13 23:44:43', '2026-03-13 23:44:43', NULL),
(28, '1053773199', 'JOSE GILBERTO HURTADO', '$2y$12$xZNzFneRDJvCehFpA6zNJObUjnPWm/dLg4JBvkEjB7W/I50rHO/6e', 1, 1, 2, 3, 1, 1, NULL, NULL, NULL, 1, '2026-03-13 18:44:44', '2026-03-13 23:44:44', '2026-03-13 23:44:44', NULL),
(29, '1053786029', 'JHON JAIRO SERNA MANRIQUE', '$2y$12$kRydyl585yxfub.CAh6Qo.dJhCLy/fnQ6gdCtHDuMDbbcdXtgLpYC', 1, 1, 2, 3, 1, 1, NULL, NULL, NULL, 1, '2026-03-13 18:44:44', '2026-03-13 23:44:44', '2026-03-13 23:44:44', NULL),
(30, '1053793116', 'PAULA ANDREA CARMONA HURTADO', '$2y$12$R8SEQoAXDq6NOvQPNy2lG.wC5/BNqfgRPu.gRMQb8b.gMm3.RszBa', 1, 1, NULL, 2, 1, 1, NULL, NULL, NULL, 1, '2026-03-13 18:44:44', '2026-03-13 23:44:44', '2026-03-17 19:50:56', NULL),
(31, '1053815209', 'CRISTIAN CAMILO TABORDA OCAMPO', '$2y$12$ipi0usJjnKj5HpUsq8olJuotYzpgQltTXWkQIWxn5SXESfDg/2yi6', 1, 1, 2, 3, 1, 1, NULL, NULL, NULL, 1, '2026-03-13 18:44:45', '2026-03-13 23:44:45', '2026-03-13 23:44:45', NULL),
(32, '1053817404', 'JHONNATAN LONDOÑO CASTAÑEDA', '$2y$12$Zzrh/JRUmd4Tj3/l0RM2T.MfbAgp0nzRjzy.p6wQRfZabVduTTM22', 1, 1, 2, 3, 1, 1, NULL, NULL, NULL, 1, '2026-03-13 18:44:45', '2026-03-13 23:44:45', '2026-03-13 23:44:45', NULL),
(33, '1053821091', 'MATEO CARDONA SANCHEZ', '$2y$12$fgKwaQgUa57guAk6y/NG/ecuTeukPvkF7uByaLKa3OBH760PspsTG', 1, 1, 2, 3, 1, 1, NULL, NULL, NULL, 1, '2026-03-13 18:44:45', '2026-03-13 23:44:45', '2026-03-13 23:44:45', NULL),
(34, '1053826860', 'JONATTAN OROZCO LOPEZ', '$2y$12$cLmG0g4JtGNnRKnNmMcbZO6FtY9gbd8w7MJHQ9OV/SR.7WN/q.aim', 1, 1, 2, 3, 1, 1, NULL, NULL, NULL, 1, '2026-03-13 18:44:46', '2026-03-13 23:44:46', '2026-03-13 23:44:46', NULL),
(35, '1053827299', 'MATEO GARCIA CASTELLANOS', '$2y$12$oLZ85uOWE4xgDgIagpMBROwZsCDlIai9qrP0Zh1n2FjfrVnkZIqZ2', 1, 1, 2, 3, 1, 1, NULL, NULL, NULL, 1, '2026-03-13 18:44:46', '2026-03-13 23:44:46', '2026-03-13 23:44:46', NULL),
(36, '1053832079', 'CRISTIAN CAMILO MUÑOZ GONZALEZ', '$2y$12$/wwinu/Kthx2Polkjs44qOSIbJC.D.adR8pQZysqLLL0aULZLPS3S', 1, 1, NULL, 3, 1, 1, NULL, NULL, NULL, 1, '2026-03-13 18:44:47', '2026-03-13 23:44:47', '2026-03-14 00:17:37', NULL),
(37, '1053832370', 'LEIDY JOHANNA RODRIGUEZ PUENTES', '$2y$12$PKXGvKHN6iyS4zQnyxx7Hu5rgHxURn07sHU9FE2OGRnfXZpmKlPkq', 1, 1, 2, 3, 1, 1, NULL, NULL, NULL, 1, '2026-03-13 18:44:47', '2026-03-13 23:44:47', '2026-03-13 23:44:47', NULL),
(38, '1053841646', 'NATALIA CARDENAS VILLEGAS', '$2y$12$RbqITD40roNTFnOTyJaprOWlkEnLCRU4J/XPHpKMgwSHiTKvgn4kW', 1, 1, 2, 3, 1, 1, NULL, NULL, NULL, 1, '2026-03-13 18:44:47', '2026-03-13 23:44:47', '2026-03-13 23:44:47', NULL),
(39, '1053862705', 'CRISTIAN DANIEL ESQUIVEL JARAMILLO', '$2y$12$EBpp2Us9LbhjRQiAmB5j4e/nI393REddwJImteKHBzXvJTTwod9SO', 1, 1, 2, 3, 1, 1, NULL, NULL, NULL, 1, '2026-03-13 18:44:48', '2026-03-13 23:44:48', '2026-03-13 23:44:48', NULL),
(40, '1053871891', 'DANIELA GRAJALES VILLA', '$2y$12$fyGQ4.4Pw/y2lxmntO3Gweow5NeFUjU95b.D.EWUk1iVKmirX8FpC', 1, 1, NULL, 3, 1, 1, NULL, NULL, NULL, 1, '2026-03-13 18:44:48', '2026-03-13 23:44:48', '2026-03-14 00:17:58', NULL),
(41, '1054570289', 'ALEXANDER HENAO TORO', '$2y$12$UMJluJJm2lCha3P2d7sDY.mzGLWH3ipqd39/.FeynopUR8XxXG/Lq', 1, 1, 2, 3, 1, 1, NULL, NULL, NULL, 1, '2026-03-13 18:44:48', '2026-03-13 23:44:48', '2026-03-13 23:51:08', NULL),
(42, '1055753926', 'DIEGO FERNANDO SANTA MONTOYA', '$2y$12$Pe01R4bNT94dQP4m35HRgOmEbngtApgwvHpc6bRJo4q8pZ.7x45rS', 1, 1, 2, 3, 1, 1, NULL, NULL, NULL, 1, '2026-03-13 18:44:49', '2026-03-13 23:44:49', '2026-03-13 23:44:49', NULL),
(43, '1055836817', 'MAICOL JOHANY GALVEZ BETANCUR', '$2y$12$85cOhIBUEGK7UtYTT4y5BuqFFO3bGOC9gs.pA4Hn8Toa5LDXO3C5.', 1, 1, 2, 3, 1, 1, NULL, NULL, NULL, 1, '2026-03-13 18:44:49', '2026-03-13 23:44:49', '2026-03-13 23:44:49', NULL),
(44, '1109068129', 'CARLOS ALFONSO GOMEZ RAMIREZ', '$2y$12$gXXQ98Aj1ZIfhZn1kO/xJOfSmVufqjPOLfYlqVZd4GNjbEfWeRYAG', 1, 1, NULL, 3, 1, 1, NULL, NULL, NULL, 1, '2026-03-13 18:44:49', '2026-03-13 23:44:49', '2026-03-14 00:17:20', NULL),
(45, '1110480822', 'BRAYAN ANDRES CRUZ', '$2y$12$jOU8hiKJyp1eZGu6XCWKhui2EscJaFFh2mHXuzooY.eLd6x84z9JK', 1, 1, 2, 3, 1, 1, NULL, NULL, NULL, 1, '2026-03-13 18:44:50', '2026-03-13 23:44:50', '2026-03-13 23:44:50', NULL),
(46, '15905849', 'JAIME ALBERTO HINESTROZA CASTRO', '$2y$12$4QVK1j5UbX.mojrRFAit5.Yh983i01GfXOaUqFfj5El1EQXjR9kci', 1, 1, 2, 3, 1, 1, NULL, NULL, NULL, 1, '2026-03-13 18:44:50', '2026-03-13 23:44:50', '2026-03-13 23:44:50', NULL),
(47, '15907900', 'JORGE HERNAN RAMIREZ GARCIA', '$2y$12$lPCB8lBMpxZG/OJuGO6ldu6NoKnaFIaw/Bx8PSSg4aJ.iI.OZ5iHi', 1, 1, 2, 3, 1, 1, NULL, NULL, NULL, 1, '2026-03-13 18:44:51', '2026-03-13 23:44:51', '2026-03-13 23:44:51', NULL),
(48, '16078236', 'JORGE IVAN GOMEZ CASTAÑO', '$2y$12$x36rkwqtI2klNQWK9SeLt.B3w8pDTBFzo2BCGodRr0YucjWypF1xS', 1, 1, 2, 3, 1, 1, NULL, NULL, NULL, 1, '2026-03-13 18:44:51', '2026-03-13 23:44:51', '2026-03-13 23:44:51', NULL),
(49, '18398904', 'EDISON LOPEZ OSORNO', '$2y$12$kjC5rnZnUV4ki6exfH68ruY4cBgjof1BDz14QA0XF7Pb9Yv4LZPUK', 1, 1, 2, 3, 1, 1, NULL, NULL, NULL, 1, '2026-03-13 18:44:51', '2026-03-13 23:44:51', '2026-03-13 23:44:51', NULL),
(50, '18506044', 'WILLIAM DE JESUS GAVIRIA GRISALES', '$2y$12$TTKpFNAWTW89721/jnKCXuXtE5.xsPc9g01RnTjREIgocax9bgYTK', 1, 1, 2, 3, 1, 1, NULL, NULL, NULL, 1, '2026-03-13 18:44:52', '2026-03-13 23:44:52', '2026-03-13 23:44:52', NULL),
(51, '24331839', 'SANDRA MILENA RAMIREZ VANEGAS', '$2y$12$Y5trNR.3QOvZr9/L0zVQV.ufSdJfOhnWSiuwCEJT/3ZNgS3SyyxNC', 1, 1, 2, 3, 1, 1, NULL, NULL, NULL, 1, '2026-03-13 18:44:52', '2026-03-13 23:44:52', '2026-03-13 23:44:52', NULL),
(52, '30239375', 'CAROLINA CALDERON GONZALEZ', '$2y$12$/QZhhuJu3Tz1x1mcsdvAS.SJHAbM8UPvgEj0LFoHXCPVotzEluT8u', 1, 1, NULL, 3, 1, 1, NULL, NULL, NULL, 1, '2026-03-13 18:44:52', '2026-03-13 23:44:52', '2026-03-14 00:17:29', NULL),
(53, '30296906', 'GLADYS CLEMENCIA NUÑEZ VALENCIA', '$2y$12$quePnCZGtx.2dx3n.7slwOkqYlik.kvEae56vWByP0XWkI6aNPvtK', 1, 1, NULL, 3, 1, 1, NULL, NULL, NULL, 1, '2026-03-13 18:44:53', '2026-03-13 23:44:53', '2026-03-14 00:18:24', NULL),
(54, '30315268', 'ADRIANA MARIA OCAMPO SALAZAR', '$2y$12$rmfI9Cfwfubuf0BPi2xAiO.JI1TmGds82j6HOPRCtYO5D0s7jTHEC', 1, 1, 2, 3, 1, 1, NULL, NULL, NULL, 1, '2026-03-13 18:44:53', '2026-03-13 23:44:53', '2026-03-13 23:45:59', NULL),
(55, '30318725', 'JACKELINE RAMOS GALVIS', '$2y$12$/KSC4//MmBjqJrd.15dNeOyjpkb6WtpVF6RVSxWLpcJLD3Jslb3.K', 1, 1, 2, 3, 1, 1, NULL, NULL, NULL, 1, '2026-03-13 18:44:54', '2026-03-13 23:44:54', '2026-03-13 23:44:54', NULL),
(56, '30397560', 'ELSA MARIA CARO BUSTAMANTE', '$2y$12$4imAadcTNgU1Uo.q6LLmheoE/Rq.D2HVjo9BFHMqgMon45P7SBfUO', 1, 1, NULL, 3, 1, 1, NULL, NULL, NULL, 1, '2026-03-13 18:44:54', '2026-03-13 23:44:54', '2026-03-14 00:18:13', NULL),
(57, '30401323', 'ANA MARIA CATAÑO OSORIO', '$2y$12$HgKeZU6NjeskOMBHTG4zuO/bnk.c/I.i1u8Da/Ny3P3Wv8IYXyhUK', 1, 1, 2, 3, 1, 1, NULL, NULL, NULL, 1, '2026-03-13 18:44:54', '2026-03-13 23:44:54', '2026-03-13 23:51:18', NULL),
(58, '4470762', 'HIGINIO DE JESUS VILLA BONILLA', '$2y$12$dCctR9YPSChIBDiuL.wfQeFxXinImQQrZWDDj5K0uDia6LQ5SdCTC', 1, 1, 2, 3, 1, 1, NULL, NULL, NULL, 1, '2026-03-13 18:44:55', '2026-03-13 23:44:55', '2026-03-13 23:44:55', NULL),
(59, '5269368', 'PABLO DANIEL MORILLO RODRIGUEZ', '$2y$12$OwFdFsxc/I27JsxWzxfbnOYFI911maMFi2DVcGiqzPzt7wAO5VVU6', 1, 1, 2, 3, 1, 1, NULL, NULL, NULL, 1, '2026-03-13 18:44:55', '2026-03-13 23:44:55', '2026-03-13 23:44:55', NULL),
(60, '75072244', 'LEONARDO VELEZ ALVAREZ', '$2y$12$szWoHyozASb0cVZ1E5u0NOy64dYxT/mdDc9uvPRaXbNU5SD5Dl15y', 1, 1, 2, 3, 1, 1, NULL, NULL, NULL, 1, '2026-03-13 18:44:55', '2026-03-13 23:44:55', '2026-03-13 23:44:55', NULL),
(61, '75086314', 'JORGE HERNAN TABARES PULGARIN', '$2y$12$DVJE8VRZ3f.PLiIwPAgJ1eJbuPD8NGo69gTrp67pX7PAhbwx2nxny', 1, 1, 2, 3, 1, 1, NULL, NULL, NULL, 1, '2026-03-13 18:44:56', '2026-03-13 23:44:56', '2026-03-13 23:44:56', NULL),
(62, '75096598', 'ANDRES FELIPE CORREA ROJAS', '$2y$12$.lDQhfk7GcENK0oT79BkE.Rkyu7.kpLPwfzPtGlL0GNwOzBHKOggu', 1, 1, 2, 3, 1, 1, NULL, NULL, NULL, 1, '2026-03-13 18:44:56', '2026-03-13 23:44:56', '2026-03-13 23:58:45', NULL),
(63, '75098517', 'ALEXANDER ALVAREZ RAVE', '$2y$12$P8KiTt5lJT/fEsIIkmBwOuqbm2tugsjkWeW.l1Ndsm8njzUEhn4ZG', 1, 1, 2, 3, 1, 1, NULL, NULL, NULL, 1, '2026-03-13 18:44:57', '2026-03-13 23:44:57', '2026-03-13 23:46:16', NULL),
(64, '79691692', 'ROGELIO GRISALES RINCON', '$2y$12$noc.Hi7f9VuJtoNDgSSUHOdeQ3M4YxcZ34KxawwAhcuMP6y.2yS6q', 1, 1, NULL, 3, 1, 1, NULL, NULL, NULL, 1, '2026-03-13 18:44:57', '2026-03-13 23:44:57', '2026-03-17 19:50:08', NULL),
(65, '88263429', 'ANDRES FELIPE OMAÑA VALENCIA', '$2y$12$kkiBg1qufYL.2WCer996Leoo9TH0pVaVGrhb9f9SBHEGPb0cvIHPa', 1, 1, 2, 3, 1, 1, NULL, NULL, NULL, 1, '2026-03-13 18:44:57', '2026-03-13 23:44:57', '2026-03-13 23:44:57', NULL),
(66, '75103720', 'JUAN PABLO ARANGO', '$2y$12$0ClPkytcwUgDQDdRh8yUfecS1ReX3wSGwcfPHX1TE.ihqLt1YgQcK', 1, 1, NULL, 3, 1, 2, 1, NULL, NULL, 1, '2026-03-17 14:40:47', '2026-03-17 19:40:47', '2026-03-17 19:40:47', NULL),
(67, '1019987873', 'JUAN CAMILO ZULUAGA MARIN', '$2y$12$qIDPOGmQcrzoQe/TyV0UWeCMcF7XZ/Iek7gm3c6cbKnghmeWq8Bk2', 1, 1, NULL, 3, 1, 2, 1, NULL, NULL, 1, '2026-03-17 14:41:26', '2026-03-17 19:41:26', '2026-03-17 19:41:26', NULL),
(68, '1076501002', 'ALEJANDRA MENDEZ', '$2y$12$TVstFhyEhW8LRB7olQh8EuxPIBKLpF/pIS/tDLf6xVzXC5mxBbX1S', 1, 1, NULL, NULL, 1, 2, 1, NULL, NULL, 1, '2026-03-17 14:42:02', '2026-03-17 19:42:02', '2026-03-17 19:42:02', NULL),
(69, '1002593082', 'CAROLINA TORRES MEDINA', '$2y$12$Qt9/2Rn1zslWxj8xwI55qO5ACiErnSbDXGe7zieL53D5Rl5jdxEg2', 1, 1, NULL, 3, 1, 2, 1, NULL, NULL, 1, '2026-03-17 14:42:32', '2026-03-17 19:42:32', '2026-03-25 01:13:12', NULL),
(70, '1053867528', 'DANIELA GONZALEZ OCAMPO', '$2y$12$nUAxf4PTGrp9eqy/Xdr0p.2ewXakZshd46hQG8CKDNywBjp9WK48a', 1, 1, NULL, 3, 1, 2, 1, NULL, NULL, 1, '2026-03-17 14:43:13', '2026-03-17 19:43:13', '2026-03-17 19:43:13', NULL),
(71, '1053850259', 'MARIA JOSE VILLA RIOS', '$2y$12$W8Mg4qofkKuY9/UIopr04ugEZ55dMV5vC.yWu5Ii9jdoQiWqe5Jze', 1, 1, NULL, 3, 1, 2, 1, NULL, NULL, 1, '2026-03-17 14:43:48', '2026-03-17 19:43:48', '2026-03-17 19:52:32', NULL),
(72, '1002858790', 'VANESSA TROCHEZ TALAGA', '$2y$12$ix0WTmUAIHM3xgCeA8F3kO1EakSxRj5yijNWr5fBUDXrT97Nm5o2C', 1, 2, NULL, 1, 1, 3, NULL, NULL, NULL, 1, '2026-03-17 20:27:35', '2026-03-18 01:27:35', '2026-03-18 01:39:31', NULL),
(73, '1002859536', 'YINETH LEANDRA LEON QUINTANA', '$2y$12$kMi/5VC//Z.xwN2spTEF3OY6arZ9gd8pREFda.MYNKTmS7TRsBHDa', 1, 2, NULL, 1, 1, 3, NULL, NULL, NULL, 1, '2026-03-17 20:27:35', '2026-03-18 01:27:35', '2026-03-18 01:40:37', NULL),
(74, '1002946078', 'JUAN CAMILO QUINTERO NORIEGA', '$2y$12$4OPZ9IqmRu7b017j7EKEAO5wT5cnmoVCrlf1U3FS/GWZ.tHJjsUca', 1, 2, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-17 20:27:36', '2026-03-18 01:27:36', '2026-03-18 01:27:36', NULL),
(75, '1002952460', 'INGRID NATALIA CALAMBAS MONTES', '$2y$12$D8oJX/ykg.g6Udk/QSyDLumzy4xFJ1THWo1CIHZF9yYaU5fdQNhrG', 1, 2, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-17 20:27:36', '2026-03-18 01:27:36', '2026-03-18 01:27:36', NULL),
(76, '10484974', 'ANTONIO CRUZ LEON ISAZA', '$2y$12$cu7rm.Y3Z4hYJF2WEHXufOdoaJJHNNA7W8Ptd7AbMdXi4bVGHAu1C', 1, 2, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-17 20:27:37', '2026-03-18 01:27:37', '2026-03-18 01:27:37', NULL),
(77, '10486623', 'CESAR AUGUSTO MORALES YUNDA', '$2y$12$/POGj46hlMlwNZcmbaxFWuHC4U1o0JAq262Nr9bX4Q2EVBOKx.GB.', 1, 2, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-17 20:27:37', '2026-03-18 01:27:37', '2026-03-18 01:27:37', NULL),
(78, '10487669', 'JAVIER MUÑOZ BOLAÑOS', '$2y$12$m0oSOqBYPQPs7XduN9EalOX2.wvRSKMjt/fmuhYv7LYeTjM3M2ooO', 1, 2, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-17 20:27:37', '2026-03-18 01:27:37', '2026-03-18 01:27:37', NULL),
(79, '10491546', 'ADEMIR MINA VIVEROS', '$2y$12$dLImtbXHgwOHjzoco.Kr4.lLj96518U.37CZj1B0JNjMIPV2fk8Ly', 1, 2, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-17 20:27:38', '2026-03-18 01:27:38', '2026-03-18 01:27:38', NULL),
(80, '10493655', 'EDGAR ANDRES MERA GARCIA', '$2y$12$tQtgdG.a0l0eOFbnQb3zn.MMxtOzXq3P.U0xhfLUMbJNt6J99969C', 1, 2, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-17 20:27:38', '2026-03-18 01:27:38', '2026-03-18 01:27:38', NULL),
(81, '10496268', 'ROLANDO CHAVEZ BALANTA', '$2y$12$A11NEEC6vdoNewcB9OcTqeM0SsmEDxbVmYI2abpi/CNkLCf8RU0q2', 1, 2, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-17 20:27:38', '2026-03-18 01:27:38', '2026-03-18 01:27:38', NULL),
(82, '10498601', 'JOSE ELIECER COLLAZOS YONDA', '$2y$12$/UrCg0wRCgaLc2ysLw7UveCaZ3egWJV0wInjegd7w2zdG4EnL8Uqa', 1, 2, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-17 20:27:39', '2026-03-18 01:27:39', '2026-03-18 01:27:39', NULL),
(83, '10499255', 'MILTON ANDERSON PALACIOS TAQUINAS', '$2y$12$hndCNVuyW.qikVCD0OYDAOLwyha45xsWyp5Qnzakw8zbHWBg5LkBe', 1, 2, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-17 20:27:39', '2026-03-18 01:27:39', '2026-03-18 01:27:39', NULL),
(84, '10499528', 'JULIAN ESTEBAN LARRAHONDO VIVEROS', '$2y$12$wDljNmgRu7ObLI4Q9DPpQuZzpgnpn1kFeVhJ9TwnXGXRRD0ZdpdPW', 1, 2, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-17 20:27:40', '2026-03-18 01:27:40', '2026-03-18 01:27:40', NULL),
(85, '1053801169', 'ANA ISABEL GAVIRIA SOTO', '$2y$12$577XyzPWQQKOuca6.gBqfOgkLVvsWnGMX1X6B29NEW8WEPlu9doxa', 1, 2, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-17 20:27:40', '2026-03-18 01:27:40', '2026-03-18 01:27:40', NULL),
(86, '1061431593', 'EDUARD ALONSO VALENCIA CAICEDO', '$2y$12$5CzL50QUrUFQoNZTjulKUuTy9Umf8M.oiApIKtALUlO95ftqG04tK', 1, 2, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-17 20:27:40', '2026-03-18 01:27:40', '2026-03-18 01:27:40', NULL),
(87, '1061432540', 'IGNACIO GARCIA RIVERA', '$2y$12$MfwzsR0/seIWP21KlQsz.Ov64XOhdCQgTD09zMc30l0AgWX1APl3a', 1, 2, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-17 20:27:41', '2026-03-18 01:27:41', '2026-03-18 01:27:41', NULL),
(88, '1061433888', 'DIEGO FELIPE CARABALI ORTIZ', '$2y$12$HvRj3Wsjay3AzZOBsALFH.IECMF7MUlfy62IrHhXRFZ.pxXB01J1W', 1, 2, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-17 20:27:41', '2026-03-18 01:27:41', '2026-03-18 01:27:41', NULL),
(89, '1061436396', 'ANDRES FELIPE CAMPO MEJIA', '$2y$12$lCItuIyCuxqQbiNYEmvHn.8Nfi/m3lTsra0/3YaEXAWzUhKrWj7aW', 1, 2, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-17 20:27:41', '2026-03-18 01:27:41', '2026-03-18 01:27:41', NULL),
(90, '1061439945', 'LAURA ISABEL CUARTAS MEDINA', '$2y$12$ctw.zA5w0k7PhfOZEFTfL.yLKbozSByI3FxYs70ziBlHTwZXR7snG', 1, 2, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-17 20:27:42', '2026-03-18 01:27:42', '2026-03-18 01:27:42', NULL),
(91, '1062287956', 'EDWIN YOBANI GOMEZ DIAZ', '$2y$12$tZl06yaeuBEa7VDYq8MZEOBl5GXHdku8MTcCgCWsyx7PlGAyQW1uy', 1, 2, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-17 20:27:42', '2026-03-18 01:27:42', '2026-03-18 01:27:42', NULL),
(92, '1062290962', 'JAVIER ALBERTO ZAMORA LOPEZ', '$2y$12$itWEOTEw31Mo9bTOWd3jSeUQt/CcyD10lflGHWwoY1lcZVqyci3si', 1, 2, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-17 20:27:42', '2026-03-18 01:27:42', '2026-03-18 01:27:42', NULL),
(93, '1062298262', 'LUIS FABIO CHAVEZ AVENDANO', '$2y$12$OQ2.ZgDCYe2NAmFuoTcR5ea/xbp7mQIwyYHx4dYW1.2l6g35j3cHi', 1, 2, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-17 20:27:43', '2026-03-18 01:27:43', '2026-03-18 01:27:43', NULL),
(94, '1062299260', 'BAYARDO GOMEZ HOLGUIN', '$2y$12$DPXNKMV68sq4Whe2G36b7uECnU9hZnCmjwj3whZQV.Eo7v4zf4SUm', 1, 2, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-17 20:27:43', '2026-03-18 01:27:43', '2026-03-18 01:27:43', NULL),
(95, '1062309602', 'GLORIA JUDID VELASCO GUZMAN', '$2y$12$3mN2qc9M.rCyGzJ82nII9.3Kg.DtXVwcc3BqagEv1OgMwCKHeesUS', 1, 2, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-17 20:27:43', '2026-03-18 01:27:43', '2026-03-18 01:27:43', NULL),
(96, '1062332163', 'JOVANI ALEJANDRO PALACIOS MINA', '$2y$12$9tDccYGFpZ1jwt1g6.Iz/u57q2txJPhSpBxr9ezYFZSz3Kw/0TSIC', 1, 2, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-17 20:27:44', '2026-03-18 01:27:44', '2026-03-18 01:27:44', NULL),
(97, '1063812135', 'ELMER JAVIT SALAZAR MUÑOZ', '$2y$12$xn3OX1UEQRvmZ/1azgp/Iu9nUcaYz/aGMZo6cNLHSK5pC7scWHmdG', 1, 2, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-17 20:27:44', '2026-03-18 01:27:44', '2026-03-18 01:27:44', NULL),
(98, '10740939', 'FLOWER EMILSON PAZ RIVERA', '$2y$12$2ougBlLS/XSYlfKVjdkiqOh52xdHCThRDnCiArN2gtN9AuzzfM5K2', 1, 2, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-17 20:27:45', '2026-03-18 01:27:45', '2026-03-18 01:27:45', NULL),
(99, '10741009', 'JAIME VICUÑA POSCUE', '$2y$12$jEdKzgwKL64Tx8uaE4C5Yuoc12v6b6Erp9oD1TSsAUer4cky3NPfK', 1, 2, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-17 20:27:45', '2026-03-18 01:27:45', '2026-03-18 01:27:45', NULL),
(100, '1086328300', 'JEIMMY ALEXANDRA PINTA HIGIDIO', '$2y$12$orwM3wTlUknbJXZRGKc/0uJ41Bwp6IvBLkncq.98lFYucwIj0iOR6', 1, 2, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-17 20:27:45', '2026-03-18 01:27:45', '2026-03-18 01:27:45', NULL),
(101, '1122921285', 'JHONATAN DAVID PEREZ SANTOS', '$2y$12$UOoiFpoSMqtV6jSHbRawuOqPCfu1L08ElmB1CfDbfNL4pAcCyjgna', 1, 2, NULL, 1, 1, 3, NULL, NULL, NULL, 1, '2026-03-17 20:27:46', '2026-03-18 01:27:46', '2026-03-18 01:41:07', NULL),
(102, '1130637110', 'LINA MARCELA VIVEROS ARAQUE', '$2y$12$2pxDh.pXy5ph89LvzZ7pI.DjUMQLK.mFyCySgPnrkFX5BFTKZv6j6', 1, 2, NULL, 2, 1, 1, NULL, NULL, NULL, 1, '2026-03-17 20:27:46', '2026-03-18 01:27:46', '2026-03-25 00:05:42', NULL),
(103, '1144090933', 'DANIELA SUAREZ CORREA', '$2y$12$z61GF9vw1HutYz2nfBfsrOnUBwRa105sdB92OZxqkjgo.XK5HFsq2', 1, 2, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-17 20:27:46', '2026-03-18 01:27:46', '2026-03-18 01:27:46', NULL),
(104, '1144094756', 'YULIETH ALEJANDRA MILLAN MANQUILLO', '$2y$12$7eNW/w/8PQdLOgH9xLrpBOdx/6kjrQuUAX/u5DYfV.Y7zdqVP5kqO', 1, 2, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-17 20:27:47', '2026-03-18 01:27:47', '2026-03-18 01:27:47', NULL),
(105, '1144151740', 'ANGIE JULIETH SANTIAGO ACHICUE', '$2y$12$LdrnMdSarwQafFbn.L7z6eppMqK0sMvN.gwIkSQfioWsGlRoGGrRa', 1, 2, NULL, 2, 1, 1, NULL, NULL, NULL, 1, '2026-03-17 20:27:47', '2026-03-18 01:27:47', '2026-03-18 01:28:37', NULL),
(106, '1144153341', 'STEFANIA HERNANDEZ BOLAÑOS', '$2y$12$79qKjLoN8/MCR5ho9bUK6efpw8E0iISjyhwJsGoVAsoyz9mK9TjL2', 1, 2, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-17 20:27:48', '2026-03-18 01:27:48', '2026-03-18 01:27:48', NULL),
(107, '1144180393', 'DIEGO ALEXANDER SOLARTE BENAVIDES', '$2y$12$YfrlVdxKpm61knqiTN6sveHo/hf9qZF0dcZM31iJT3nkSUpAURc6W', 1, 2, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-17 20:27:48', '2026-03-18 01:27:48', '2026-03-18 01:27:48', NULL),
(108, '16721561', 'JOSE LUIS CARMONA GAVIRIA', '$2y$12$tCHwzdVCSra2WWE9w6xfl.ioEbjdBRW.y6nMD9IgIGL.0jCduw2Vi', 1, 2, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-17 20:27:48', '2026-03-18 01:27:48', '2026-03-18 01:27:48', NULL),
(109, '18504039', 'JULIAN ALONSO ARIAS LOAIZA', '$2y$12$KEKLFJSyOgaTR7LEWxLtfuBEGJBF4QZTxP1jtdRxYH1ArHgZAfNCq', 1, 2, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-17 20:27:49', '2026-03-18 01:27:49', '2026-03-18 01:27:49', NULL),
(110, '24331824', 'ANGELA MARIA CARDONA MURILLO', '$2y$12$cNg7OgGeXCDljQ8nkqgtwexQ..szAaOF4.rLn1rfyLbWiM4/ZoYke', 1, 2, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-17 20:27:49', '2026-03-18 01:27:49', '2026-03-18 01:27:49', NULL),
(111, '25364476', 'MARIA SOCORRO ZAPATA DAZA', '$2y$12$mABwdKWjrddTuhfFrrhYCOK2l7hjWAi2V2DtlSfTTMADkJvMuCnBi', 1, 2, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-17 20:27:50', '2026-03-18 01:27:50', '2026-03-18 01:27:50', NULL),
(112, '4652563', 'JOSE TOBIAS ALFARO LARROHONDO', '$2y$12$iyCLP8RyogRVpvQ7VgKUbeoOKkQ8UOD45MUekd9nXqUm/mE.b8U2.', 1, 2, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-17 20:27:50', '2026-03-18 01:27:50', '2026-03-18 01:27:50', NULL),
(113, '4652617', 'ELCIADES DAZA CANAS', '$2y$12$6ghNFpl/4G.A5fo7/FqSKeATX7EMN0LPQZuJ.77QJrdf0tdhNPacO', 1, 2, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-17 20:27:50', '2026-03-18 01:27:50', '2026-03-18 01:27:50', NULL),
(114, '4652937', 'NORBERTO EDISON CAICEDO FAJARDO', '$2y$12$CZpGNmnsduaGtuM1Wnn33eN68jdUj7TPxoMkLDxcelc7eLd1yM1Ja', 1, 2, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-17 20:27:51', '2026-03-18 01:27:51', '2026-03-18 01:27:51', NULL),
(115, '4652975', 'AIMER JOSE BRAND MEJIA', '$2y$12$A.uC3XqgTOjLHfpQqfxrUetPoP6wBAv1orAMGMiN1cIyTNmpJePDq', 1, 2, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-17 20:27:51', '2026-03-18 01:27:51', '2026-03-18 01:27:51', NULL),
(116, '75064775', 'LEONARDO GUTIERREZ VELEZ', '$2y$12$Wwie8CrIOw4qHLGWpaJnfew4Kgbi9SfPcL53cMnP5H.6qNjP5LVM6', 1, 2, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-17 20:27:51', '2026-03-18 01:27:51', '2026-03-18 01:27:51', NULL),
(117, '76141402', 'JHON EDUARD RUIZ MEDINA', '$2y$12$yGWdKHHfoQd9czL4cXypbe.zeWVuer83ReGu/Nj8NnXZbv1pso5MC', 1, 2, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-17 20:27:52', '2026-03-18 01:27:52', '2026-03-18 01:27:52', NULL),
(118, '76141499', 'LUIS FERNANDO RUIZ REINOSA', '$2y$12$acjFXq1UoYKh8ZQV/mkh.uS4Mu8nWx1yHo8mdS4fGHkwZjdtHVM3G', 1, 2, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-17 20:27:52', '2026-03-18 01:27:52', '2026-03-18 01:27:52', NULL),
(119, '76142075', 'ALEX GEOVANNY ANGEL', '$2y$12$xFZQihEtw2H8TRfu0FrJP.1lLEuVgYHi9DnNF5Ggb6HJBluoYQobW', 1, 2, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-17 20:27:52', '2026-03-18 01:27:52', '2026-03-18 01:27:52', NULL),
(120, '76231546', 'OSCAR HAROL SANCHEZ PEÑA', '$2y$12$4wYl7eao2TYCUyXxQTanwui1C/X6cUd0G5VFeAcril80VlgoXAM8e', 1, 2, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-17 20:27:53', '2026-03-18 01:27:53', '2026-03-18 01:27:53', NULL),
(121, '76231585', 'VICTOR HUGO DIAZ FORY', '$2y$12$XJ8psxu/8T7gMkoGkFNbU.8cIZR2uiWx1JqEgSq.WCieMTHOVPZ5.', 1, 2, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-17 20:27:53', '2026-03-18 01:27:53', '2026-03-18 01:27:53', NULL),
(122, '79399666', 'LUIS GABRIEL HERRERA CASTILLO', '$2y$12$zAI9kTSATR05QfYFi7Mm5eXpq9g1LLhZViARtRhQGRXSraP.AFnES', 1, 2, NULL, 2, 1, 1, NULL, NULL, NULL, 1, '2026-03-17 20:27:54', '2026-03-18 01:27:54', '2026-03-25 00:04:37', NULL),
(123, '91276039', 'MARIO SANCHEZ TORRES', '$2y$12$b1RLKUwBozWCR4Ugn8VpCu1wJS2f3hMAp1ffO0U0dHIwsAysaQkYO', 1, 2, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-17 20:27:54', '2026-03-18 01:27:54', '2026-03-18 01:27:54', NULL),
(124, '94365971', 'WALTHER RAMIREZ CASTANO', '$2y$12$UkaXOi.D2W4YBKJUeIn/ROjIUJuhpYzCgINdBDAzyBhEVyHFpb2CO', 1, 2, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-17 20:27:54', '2026-03-18 01:27:54', '2026-03-18 01:27:54', NULL),
(125, '94527526', 'HERIBERTO PALTA VILLEGAS', '$2y$12$NA30rQJY6yNutewHcpsIpOF9AlfH77n0cPGsz0T5ar7Ghu3L.rdM2', 1, 2, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-17 20:27:55', '2026-03-18 01:27:55', '2026-03-18 01:27:55', NULL),
(126, '1112042426', 'SEBASTIAN OROZCO FAJARDO', '$2y$12$HgKP33wqjCB7Z4cECZvnAOenI91Mr1kmz/7jvi3v5tM/Y9GU7OVmG', 1, 2, NULL, 1, 1, 2, 2, NULL, NULL, 1, '2026-03-17 20:27:55', '2026-03-18 01:27:55', '2026-03-18 01:27:55', NULL),
(127, '1144184860', 'LORENAAGUDELO PATIÑO', '$2y$12$WYBIprPiNefFKaknGUvoLO9AJ/jixsG2uzCVmhpdDc3kIFVAIzuxm', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:20:31', '2026-03-21 01:20:31', '2026-03-21 01:20:31', NULL),
(128, '10491477', 'JOSE ONEL AGUIRRE CORTES', '$2y$12$aKpzRPMDHeHauVr3upr2E.I//Nh9kw5RcfLI3V77AiYcCCSuR2Bau', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:20:32', '2026-03-21 01:20:32', '2026-03-21 01:20:32', NULL),
(129, '30397609', 'CAROLIN AANGARITA LUGO', '$2y$12$Xdxdj23G25tUGTpex0URg.ehq0nPMnY6iV.UTjcAcU/2FIK78z/vW', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:20:32', '2026-03-21 01:20:32', '2026-03-21 01:20:32', NULL),
(130, '1053819868', 'LAURA MARIA ARANZAZU RIOS', '$2y$12$FI4tkZpvh4cQPTtYNGX3YeOPA0UEVafOASjuqg2ZOc/Y4qs6FRlb6', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:20:33', '2026-03-21 01:20:33', '2026-03-21 01:20:33', NULL),
(131, '30238349', 'DIANA LORENA ARIAS', '$2y$12$/0izehwi2ghhBisiSMxwhuYM0mABTi2CrbabAVod47jYMolHSZUIS', 1, 3, NULL, 2, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:20:33', '2026-03-21 01:20:33', '2026-03-25 00:06:16', NULL),
(132, '76140133', 'WILSON BALANTA AGUILAR', '$2y$12$0F7Le2ctw4w0u7kdyYn3IO7CBoL30QTJgM.2gKLYIM8071jQagv7K', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:20:33', '2026-03-21 01:20:33', '2026-03-21 01:20:33', NULL),
(133, '1062325470', 'MICHEL FERNANDA BALANTA GALVIS', '$2y$12$VqAf0H72CXLUUXG.k3EhHOPSUa9AFhQDjCR/VtWVCMhUyWSUNWM6y', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:20:34', '2026-03-21 01:20:34', '2026-03-21 01:20:34', NULL),
(134, '1061437539', 'FANOR DARIO BALANTA TROCHEZ', '$2y$12$FMmcfJ/5X6WJTAZryD1uX.MRGeY9s1acHSvZj/8t5JTQeR6/5A0q.', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:20:34', '2026-03-21 01:20:34', '2026-03-21 01:20:34', NULL),
(135, '1006235342', 'CAROLINA BARRERA PILLIMUE', '$2y$12$dLT3JOAMM6mfAPFfudsiHub7dWNZ6d7V2W9NOpqO7PaiQ74MdygoK', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:20:35', '2026-03-21 01:20:35', '2026-03-21 01:20:35', NULL),
(136, '1062325070', 'BRAYAN DUVANBASTO PIÑEROS', '$2y$12$lJM31YRzrB/7ldSeKlOfxO1O5ocJXlpi2HUp5kGZhumCirRd90I0K', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:20:35', '2026-03-21 01:20:35', '2026-03-21 01:20:35', NULL),
(137, '1105362109', 'EDWIN CAMILOBENAVIDES GONZALEZ', '$2y$12$jrDz/FfFD60I0Ib1t.f12.sihZF6Qxz0xbKQei7EErG6/QNA2RlXa', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:20:35', '2026-03-21 01:20:35', '2026-03-21 01:20:35', NULL),
(138, '1006536082', 'LAURA SOFIA CADAVID BARRERA', '$2y$12$cibNzMRuhDXI7OXei5WMDueRuwZ7t9NDUoHQTuKWYUzOknme3FEqG', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:20:36', '2026-03-21 01:20:36', '2026-03-21 01:20:36', NULL),
(139, '1061433359', 'ANTONIO JOSE CAICEDO DAZA', '$2y$12$qSfzaaGFW6foErQComKJjON9LYH5j0lpaUWa9syservlJf2iA4Ri2', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:20:36', '2026-03-21 01:20:36', '2026-03-21 01:20:36', NULL),
(140, '1062307627', 'JHON DAVID CARABALI MEZU', '$2y$12$Fg6Rk7DQZQ4QLzgpl8swQ.sC0W3eRk6MfwTxS.wfZUDk.2jDXSlIm', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:20:36', '2026-03-21 01:20:36', '2026-03-21 01:20:36', NULL),
(141, '1110536494', 'CESAR HUMBERTO CARDONA FORERO', '$2y$12$0xTJPkCO9mDF1E2Tbne70u4hWFrGK8iSJ65y0A8Dx6tqSX5.c7IMy', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:20:37', '2026-03-21 01:20:37', '2026-03-21 01:20:37', NULL),
(142, '1144109745', 'VALENTINA CARMONA VELASCO', '$2y$12$jhG1iBNK3Nr6.KA26fO8VeZ5mRqWN3AYo0LyFzqCYEJTCD2o98CQa', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:20:37', '2026-03-21 01:20:37', '2026-03-21 01:20:37', NULL),
(143, '1007610745', 'ANYELIN CASTRILLON OROZCO', '$2y$12$aLQdO6gDRUdRGGc4Ochr7.v5Kgxd.tJ78cug/B8EPJgRmgF4Mgu/i', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:20:38', '2026-03-21 01:20:38', '2026-03-21 01:20:38', NULL),
(144, '1007217388', 'ARELEEN FERNANDA CASTRO SUAREZ', '$2y$12$u9ZU9qMVlcdN9GxfT238hOGEWhRSSTgmwXReJZaPLahDM8Jv3Vmv6', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:20:38', '2026-03-21 01:20:38', '2026-03-21 01:20:38', NULL),
(145, '1004735294', 'JUAN CAMILOCRUZ GOMEZ', '$2y$12$ZuSfgbfFltg0CM6sJ4zskej9vIHa7gqONvbCqYHL2Xxz55dtpp4sS', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:20:38', '2026-03-21 01:20:38', '2026-03-21 01:20:38', NULL),
(146, '1143851408', 'LINA MARCELA CUELLAR PADILLA', '$2y$12$atUVJj76Lr8rmy8K82gYxOgNr7TP7fKqRV7f15/izffNVVEF8Q1bm', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:20:39', '2026-03-21 01:20:39', '2026-03-21 01:20:39', NULL),
(147, '10499962', 'JOSE DAGUA MINA BIVIER', '$2y$12$J2jfdwwL.1XPmfCK6v7BzOeUvZKxjn5SZ7UVoIBnsxbMiClZhq44W', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:20:39', '2026-03-21 01:20:39', '2026-03-21 01:20:39', NULL),
(148, '1052403184', 'JAIME EDUARDO DAVILA CASTRO', '$2y$12$0JpHwnE06SIq9aGEb5KLVerwD/6YU7vfX9esLxXaPFzMTaJRrGY9u', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:20:39', '2026-03-21 01:20:39', '2026-03-21 01:20:39', NULL),
(149, '1059701363', 'LAURA VANESSA DAVILA PORRAS', '$2y$12$Q/naqtkHjB.mMUrvgLW7/uGvod.ptbyaM6nq3/D8AEGmmJ/DOceWG', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:20:40', '2026-03-21 01:20:40', '2026-03-21 01:20:40', NULL),
(150, '1144186690', 'DIANA CAROLINA DELGADO CARVAJAL', '$2y$12$6wZJ7dj8yB2wh0ELfbinh.ZV0nUCUKv.GmhEaY8R40bTjQ9jlEW9C', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:20:40', '2026-03-21 01:20:40', '2026-03-21 01:20:40', NULL),
(151, '1130633206', 'LEIDY JOHANNA DUQUE SOSA', '$2y$12$tgVZpUrvVILd6kmdk8gp/.pB8deCy9Bz9oHaFcr.MLl3Fr3DbpAH.', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:20:41', '2026-03-21 01:20:41', '2026-03-21 01:20:41', NULL),
(152, '1002859508', 'SANTIAGO DURAN LOAIZA', '$2y$12$i0yvKEiGUmY0.8OnQFitaeINF3OazkQONJYUzJm7sfwagE49TgRUW', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:20:41', '2026-03-21 01:20:41', '2026-03-21 01:20:41', NULL),
(153, '1105362246', 'GABRIELA ESGUERRA FAJARDO', '$2y$12$OYcFmJNywITwRyrh79wSr.KgDfyZNjZqDuuyaeiHv6B7YhmbtZdI.', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:20:41', '2026-03-21 01:20:41', '2026-03-21 01:20:41', NULL),
(154, '1061436176', 'LIZETH ANDREA FERNANDEZ ZAPATA', '$2y$12$Lp8SXVgySM74XZt/qFYPJ.vQhXwAvz5xS40e0uZA9HZOOghHL20Lq', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:20:42', '2026-03-21 01:20:42', '2026-03-21 01:20:42', NULL),
(155, '1062332435', 'LAURA ALEJANDRA FIERRO MONTOYA', '$2y$12$MxpOp85T03iDVri5JDy3Hu9D5XV.wdJPz3ha3HDm89PXlVc6.Wppa', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:20:42', '2026-03-21 01:20:42', '2026-03-21 01:20:42', NULL),
(156, '1062300163', 'VICTOR ALFONSO FLOR MERCADO', '$2y$12$qPVi.R2AQbi3YiBHQJl65.cY/hUBPdG9P/S0CHu817QRfyrwybWbu', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:20:43', '2026-03-21 01:20:43', '2026-03-21 01:20:43', NULL),
(157, '1062285174', 'GLORIA MARCELA FLOR TOBAR', '$2y$12$AeTPZxL/7ymwuPmSZ8Pvyu2OUgSE02h0qDDaE9fvC1xUNQXXnYWye', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:20:43', '2026-03-21 01:20:43', '2026-03-21 01:20:43', NULL),
(158, '1002636455', 'NATALIA GARCIA GIRALDO', '$2y$12$gTPVrBoDxnV76RFRqK.jGOLlf0sPRpp.X9S9pmhQu9WZzPFUAyKQi', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:20:43', '2026-03-21 01:20:43', '2026-03-21 01:20:43', NULL),
(159, '1144029116', 'ARBEY FELIPE GARCIA GONZALEZ', '$2y$12$Tul/u6UDoDRjnM3JM6zR.OwMgQWDLIFC6KO.5PmmX2LwMbVv2dwkO', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:20:44', '2026-03-21 01:20:44', '2026-03-21 01:20:44', NULL),
(160, '1062321311', 'EDINSON DAVID GETIAL ULCUE', '$2y$12$T.RiWK5niJFOYS.HzXEF1evNhkb0NdMrv8BQ3heh.uzuhKPmAY.FG', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:20:44', '2026-03-21 01:20:44', '2026-03-21 01:20:44', NULL),
(161, '1007151276', 'LUISA MARIA GIL MERA', '$2y$12$VDTO2oNBqXWO4OKz4GXIS.q1ygMev7eH/TWQAa/ipS8VSOXbb1n.m', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:20:44', '2026-03-21 01:20:44', '2026-03-21 01:20:44', NULL),
(162, '10741624', 'JORGE IVAN GONZALEZ CASTRO', '$2y$12$/K8gL9X9rYXDLF2x7K1m7.TtPA5Vbt4g09mzukDIwycnQt5nc/fGO', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:20:45', '2026-03-21 01:20:45', '2026-03-21 01:20:45', NULL),
(163, '1005745683', 'ANA MARIA GORDILLO DIAZ', '$2y$12$/EIo3CqqOJ4SjRUy5QUvzu/8WgwXaOqQtw2XSMSE7VYIBEDoU/cFa', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:20:45', '2026-03-21 01:20:45', '2026-03-21 01:20:45', NULL),
(164, '1151958699', 'JENNIFER GUEJIA VERA', '$2y$12$GQblC8bPgXiTMrjbvxpz3.J/8NMr.fZUBQ/iAT3c0ayv82cmPzAWO', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:20:46', '2026-03-21 01:20:46', '2026-03-21 01:20:46', NULL),
(165, '16784655', 'PEDRO NEL HERNANDEZ NARVAEZ', '$2y$12$vienREdo5fMYSLP.edGUuuKESlUdI9sWXj6J5iDrJCRCjL.vaHzV.', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:20:46', '2026-03-21 01:20:46', '2026-03-21 01:20:46', NULL),
(166, '1113670810', 'ANA MARIA HERRERA MARIN', '$2y$12$iNNF2Qjt8XE2IRR2AxfuG.9JeJZcYKOwVzqLynYY1s576LFDKzWmq', 1, 3, NULL, 2, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:20:46', '2026-03-21 01:20:46', '2026-03-25 00:03:43', NULL),
(167, '1059597180', 'DUVIER ALEXIS HURTADO HURTADO', '$2y$12$uL6dqaUR0e7roo/eLHJMX.B76Nani9VW0VRp.9mwx.7StKaE0XT/W', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:20:47', '2026-03-21 01:20:47', '2026-03-21 01:20:47', NULL),
(168, '1113689885', 'NATALIA ISAZA LENIS', '$2y$12$21gnuuaaA/kyydhj/hP9PeQxc.6t5iVRQZevaspV.kABwLRHOT83y', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:20:47', '2026-03-21 01:20:47', '2026-03-21 01:20:47', NULL),
(169, '1144026722', 'WILSON JAVIER IZA SANCHEZ', '$2y$12$uwD35FOHCuin2FSDe5OSweX3620zI5wUsq/8PMZRDvw3ybAP886uS', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:20:48', '2026-03-21 01:20:48', '2026-03-21 01:20:48', NULL),
(170, '1062307044', 'CARLOS MARIO JARAMILLO TABORDA', '$2y$12$atCoOBNClsyLWgi4NtVKcOEezl.oGcKC0xM/6pCHXjsApHntB3mXC', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:20:48', '2026-03-21 01:20:48', '2026-03-21 01:20:48', NULL),
(171, '14605592', 'FABIO NELSON JIMENEZ PERAFAN', '$2y$12$uspQPyrrivZoKEwdlDKK1eNCwyif3LoluPhQhTihJkAWaMLq4TAoK', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:20:48', '2026-03-21 01:20:48', '2026-03-21 01:20:48', NULL),
(172, '1062320111', 'CARLOS ALBERTO JOJOA ROJAS', '$2y$12$I69hQMu11pDM5kdA/FJ5B.gr7KF3PVXXq3PDGeMZa5w7/8So.GqO.', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:20:49', '2026-03-21 01:20:49', '2026-03-21 01:20:49', NULL),
(173, '4653023', 'RICARDO LARRAHONDO FERNANDEZ', '$2y$12$iSD0cTggyab8ukWfKTVgI./SnYUnnEQAkG3LbT9cjjI.pvg/X5a/.', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:20:49', '2026-03-21 01:20:49', '2026-03-21 01:20:49', NULL),
(174, '1007146039', 'CRISTHIAN ESNEIDER LARRAHONDO MUÑOZ', '$2y$12$sTvaWBe1Vo7UW0BXgviRp.Fbo7aA/D/h.oQdcjKY7kkAZyNDDvpUa', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:20:50', '2026-03-21 01:20:50', '2026-03-21 01:20:50', NULL),
(175, '1061428032', 'JUAN ANDRES LARRAHONDO OREJUELA', '$2y$12$fW9RUKA1hRJQZoF6otpHwOZA3BPXgj2Rorq1fyKugqhbKfZaeOhQm', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:20:50', '2026-03-21 01:20:50', '2026-03-21 01:20:50', NULL),
(176, '1062295607', 'HUGO HERNAN LASPRILLA LASPRILLA', '$2y$12$tVI.ThsEQJETeXG9YVn58O3m2VUJgJTBaBl/1kAOBV7qpxGGbNCYe', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:20:50', '2026-03-21 01:20:50', '2026-03-21 01:20:50', NULL),
(177, '4758767', 'ALEXANDER LLANTEN ARBOLEDA', '$2y$12$EBD42Ip8tYIhI6KigupGbeb/6O.jssd7yciRxZlzGnwQkyyE7Md7e', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:20:51', '2026-03-21 01:20:51', '2026-03-21 01:20:51', NULL),
(178, '1062277307', 'ASTRID ELENA LONDOÑO RODRIGUEZ', '$2y$12$QhCjVosOx54/Pc4VbiVIo.yaUYNq5sxDxOco5UHdnEqCMt1ooELbi', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:20:51', '2026-03-21 01:20:51', '2026-03-21 01:20:51', NULL),
(179, '1053859052', 'NATALIA LOPEZ GOMEZ', '$2y$12$uqWf096BrfYfUGeTT29mQOA0LGfwAMLryQk3gqaUuNQTEKF5vI2UW', 1, 3, NULL, 2, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:20:51', '2026-03-21 01:20:51', '2026-03-25 00:13:18', NULL),
(180, '1144105737', 'EDINSON MATEO MARTINEZ VICTORIA', '$2y$12$/GUucuYws54AvmJAN18ireF2yuuS6l.Xo60mToXWzWJ5T/8p..6w2', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:20:52', '2026-03-21 01:20:52', '2026-03-21 01:20:52', NULL),
(181, '1061436261', 'JEFFERSON ANDRES MEDINA ALEGRIAS', '$2y$12$j1RGL2kTp94O66GA7UXFGus9W9CCzdLtohV5DZM0WPayjBF/VMOSu', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:20:52', '2026-03-21 01:20:52', '2026-03-21 01:20:52', NULL),
(182, '76141193', 'ANGEL MIRO MEJIA CHAMIZAS', '$2y$12$LZedK/FeR1MlWS0wpdRwJ.tiZsV9Dx20VR2jYzFY4WetDb/vD526C', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:20:53', '2026-03-21 01:20:53', '2026-03-21 01:20:53', NULL),
(183, '1007683800', 'EDWIN ALEXIS MEJIA MINA', '$2y$12$gblUnciMGsm1rrOrxUK/4ugNmdas5iBY4guFGPHhIgJilKhzl7Iq6', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:20:53', '2026-03-21 01:20:53', '2026-03-21 01:20:53', NULL),
(184, '1061433639', 'ARLINSON MINA FIGUEROA', '$2y$12$eLCVxMiuTnkE6CWf.CjpUu8jtjDsUGABE0z.KIBw/Fpj2WGGvFkCy', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:20:53', '2026-03-21 01:20:53', '2026-03-21 01:20:53', NULL),
(185, '76142444', 'EDILSON MINA ZAPATA', '$2y$12$Vg9OqgA1yT2TF1qM08wTXOenmozZeo7TiXsOk9J86olBwY3WiAhAq', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:20:54', '2026-03-21 01:20:54', '2026-03-21 01:20:54', NULL),
(186, '1061436323', 'ANDERSSON ANDRES MORALES RODRIGUEZ', '$2y$12$UppzB1FFDalT2cvoUYDqhuKSo8doOmNjKGTH/69oxw0kBKu3Uch2G', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:20:54', '2026-03-21 01:20:54', '2026-03-21 01:20:54', NULL),
(187, '1007145050', 'YUREM DALLANY MORENO CASTILLO', '$2y$12$/wruwYZZB27k7ORxm3s5Qew61jJ2HmJVN1vlr/rQll0t5f5oL1adC', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:20:54', '2026-03-21 01:20:54', '2026-03-21 01:20:54', NULL),
(188, '1130677774', 'DIANA CAROLINA MORENO CHAMORRO', '$2y$12$FvCzE9rV9i2U6NiyMdD97..h3w.F3wA.7CbUq.iy/jpz7/6FpMxC.', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:20:55', '2026-03-21 01:20:55', '2026-03-21 01:20:55', NULL),
(189, '10487744', 'FANOR ELI MOSQUERA PAYARES', '$2y$12$UKBBxIGZymD46CpxfFY8Z.91nTofErjz6JD7BbJvlHDQRwNaATsqy', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:20:55', '2026-03-21 01:20:55', '2026-03-21 01:20:55', NULL),
(190, '94483157', 'WILLIAN ESNEIDER MOSQUERA RAMIREZ', '$2y$12$e7QyEM7R9jsOvQ0Wl9WWHOXNgpYD0dS0.gDBNE09cDNzXKHvOCXpO', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:20:55', '2026-03-21 01:20:55', '2026-03-21 01:20:55', NULL),
(191, '1061534019', 'ALEXIS JAVIER MUELAS PILLIMUE', '$2y$12$x.xxZVcsmbs9M3Zx5CnVTu24W.O5rWRjQKHGAaZhwmwJu5UFu35Vq', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:20:56', '2026-03-21 01:20:56', '2026-03-21 01:20:56', NULL),
(192, '1062308805', 'OLMER ERNEY MUÑOZ BOLAÑOS', '$2y$12$BbD0EmWutB1vT3b1tpp60uT9/Ant3IaUNBArmGscVhBPjUHpj2g1e', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:20:56', '2026-03-21 01:20:56', '2026-03-21 01:20:56', NULL),
(193, '1007440622', 'CRISTHIAN FERNANDO NARANJO PECHUCUE', '$2y$12$OlT7TBNg5W/pBLwhl235keHPrTrhgT9YSbHWSrWExV6iKUbEtaAbO', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:20:57', '2026-03-21 01:20:57', '2026-03-21 01:20:57', NULL),
(194, '1002946646', 'JUAN SEBASTIAN NORIEGA CAICEDO', '$2y$12$mT.1hRFfhlp2XFOPz00/3.FoUeGv.mlZUbv2jkusSoQrXOlP09vzq', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:20:57', '2026-03-21 01:20:57', '2026-03-21 01:20:57', NULL),
(195, '10496628', 'CRISTIAN LEONARDO OCAMPO NOGUERA', '$2y$12$kp2xslcm9RP8lwDoUfcBm.TKDmnMSAbP53f5iE72v8CEMCXAlUqUy', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:20:57', '2026-03-21 01:20:57', '2026-03-21 01:20:57', NULL),
(196, '1061437050', 'DIEGO ALEXANDER ORTIZ DOMINGUEZ', '$2y$12$0LgBqZgrLfr87akofZ9Yae6no5gNzhmnBqbw88VcKhY2i/ZyLtHgy', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:20:58', '2026-03-21 01:20:58', '2026-03-21 01:20:58', NULL),
(197, '10497326', 'JHIN ALBER PABON HERRERA', '$2y$12$hk0oHM4sf0Uc9sBq58xLh.UGy9FkBNXfuahjLTSs/JaFhkQV6Q1V.', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:20:58', '2026-03-21 01:20:58', '2026-03-21 01:20:58', NULL),
(198, '76141010', 'CESAR AUGUSTO PALACIOS OREJUELA', '$2y$12$RHXbnyG007Pb4v5cGiDpeumvK2N6MO0Xph0MAK4YoiRa4UjF1xAIu', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:20:58', '2026-03-21 01:20:58', '2026-03-21 01:20:58', NULL),
(199, '38562605', 'JENIFFER PALOMINO', '$2y$12$RcM6qOP0vlRQEye/BBtpzuUV/t2yY5SfpxiUqnWR6DlcZzLssKJ4W', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:20:59', '2026-03-21 01:20:59', '2026-03-21 01:20:59', NULL),
(200, '1062313060', 'OSCAR EDUARDO PALOMINO POPO', '$2y$12$nJVBgT7fikLIMASqI0e8z.a2.xyIOCfl9zdOo49EfxskvOvwSzv.6', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:20:59', '2026-03-21 01:20:59', '2026-03-21 01:20:59', NULL),
(201, '1107034815', 'YURY ALEXANDRA PEREZ PEREZ', '$2y$12$h/mx.yJWo9uPlupY6wGLu.e2RyY1al0p/jRrj8CjioPco7GXOpU1y', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:21:00', '2026-03-21 01:21:00', '2026-03-21 01:21:00', NULL),
(202, '1061438537', 'JOHAN DAVID RAMIREZ ORTIZ', '$2y$12$0tESivehwOFTc/Dm.X162.3./8b313c51O0mJpj2VHqTIuFc1BY7O', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:21:00', '2026-03-21 01:21:00', '2026-03-21 01:21:00', NULL),
(203, '10742457', 'JORGE EDUARDO RAMOS ANDRADE', '$2y$12$luxb.VVpphS4Q5.55Cl3n./ekkS9XpYTITjkOziI.nb1WaEGTBVXi', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:21:00', '2026-03-21 01:21:00', '2026-03-21 01:21:00', NULL),
(204, '1114883016', 'LORENZO RAMOS GERMAN', '$2y$12$VbHaugXOuWl.2vcpwB9xvudN4b.n7ZYAaeERDUlFytSBisXvjg8qO', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:21:01', '2026-03-21 01:21:01', '2026-03-21 01:21:01', NULL),
(205, '1062274269', 'BAYRON CAMILO RAMOS IDROBO', '$2y$12$SjbRuBWGbku8SjhvvSMPR.uYMLgTxFbtI6ngrfBztnSA3CyATlg7G', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:21:01', '2026-03-21 01:21:01', '2026-03-21 01:21:01', NULL),
(206, '1144063351', 'IVAN STEVEN RENDON VALENCIA', '$2y$12$Z8.NQo.lqKE7MLPyjHYTf.JPvvV0MUIa8Sz77wS/rpTs02B6WmWGO', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:21:01', '2026-03-21 01:21:01', '2026-03-21 01:21:01', NULL),
(207, '1061822508', 'STEFANIA RENGIFO OSPINA', '$2y$12$RWb9AG3Hope1wXe8i.QHyuaRwVXaHFMJqswpfiomS3TQZkOhU7R7K', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:21:02', '2026-03-21 01:21:02', '2026-03-21 01:21:02', NULL),
(208, '1061714480', 'CARLOS MANUEL REYES RAMOS', '$2y$12$ZfrJ0QrEl3N/Pk7L5N9WiORIKttO9rLqSryStutX7PA60aJGe3n7u', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:21:02', '2026-03-21 01:21:02', '2026-03-21 01:21:02', NULL),
(209, '79826812', 'PEDRO LEONARDO ROA HUERTAS', '$2y$12$nPLCNk/txhMx.Pywxf4..O7sBQJ0PulVN71.43qm6mhrz04LonU5G', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:21:02', '2026-03-21 01:21:02', '2026-03-21 01:21:02', NULL),
(210, '1088326992', 'DANIELA RODAS QUICENO', '$2y$12$9CC9JG.Ck2yvlVNnZ.KFWOZVTKIMFljMCxti2wa8Z6E/f6PvicsWq', 1, 3, NULL, 2, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:21:03', '2026-03-21 01:21:03', '2026-03-24 23:42:02', NULL),
(211, '1130606616', 'JUAN PABLORO DRIGUEZ CRUZ', '$2y$12$Xk6pntI3d9GisK6vrLQLz.Py2uCFr8q4YkH5fNs708RdzS0QlN5/m', 1, 3, NULL, 2, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:21:03', '2026-03-21 01:21:03', '2026-03-25 00:04:18', NULL),
(212, '1061435258', 'IVAN ALBEIRORO DRIGUEZ NORIEGA', '$2y$12$fY/NcJcr0scF2mKoFG5.h.aW/oSv9YEIExES//PuDLs5kdqLvd9ya', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:21:04', '2026-03-21 01:21:04', '2026-03-21 01:21:04', NULL),
(213, '4653436', 'HERMINSON ROMERO ZUÑIGA', '$2y$12$Dvi5gw19RG9lHvIiG6aghu5X9smcUTUdlGAchgmBGr0B9riHUs79a', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:21:04', '2026-03-21 01:21:04', '2026-03-21 01:21:04', NULL),
(214, '67000754', 'MARLEN ROSA ROSERO MORALES', '$2y$12$chyyoLjukVbAuqJeee3.e.KObxLpSvG41d5akvcTJLXYqJOSDMK2W', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:21:04', '2026-03-21 01:21:04', '2026-03-21 01:21:04', NULL),
(215, '6327356', 'DIEGO FERNANDO SAAVEDRA MEJIA', '$2y$12$GNP20EChkifGdEsi9543H.F6gsvX4ViB5HARYSmqLGlX.kUbcMqn6', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:21:05', '2026-03-21 01:21:05', '2026-03-21 01:21:05', NULL),
(216, '88233277', 'ANDRES ERNESTO SALDAÑA OCAMPO', '$2y$12$LjpG5GVjQ7/XRpBvuDzC6OznXVrNEng/sAxSLby.Xl91FX/z4dUJa', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:21:05', '2026-03-21 01:21:05', '2026-03-21 01:21:05', NULL),
(217, '1063809915', 'LUIS ALBERTO SANCHEZ MUÑOZ', '$2y$12$RPMLRSCvQNHivSeaUxMRjOimbYKV/f.hrnGXH0izfrsjgOSYnZjDe', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:21:05', '2026-03-21 01:21:05', '2026-03-21 01:21:05', NULL),
(218, '1053852412', 'CAMILO SANTA OLARTE', '$2y$12$idz8VqSOaxLBt.luvyD9E.3VA8AZSXLznWDlrIoPiLtAptO57voH6', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:21:06', '2026-03-21 01:21:06', '2026-03-21 01:21:06', NULL),
(219, '18496375', 'HOOVER SERNA RESTREPO', '$2y$12$mFRdZNp16iuue9i21hUkQuh3diCa3uS.bVXfbDRJ8X4MbZpJDkUw2', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:21:06', '2026-03-21 01:21:06', '2026-03-21 01:21:06', NULL);
INSERT INTO `usuarios` (`id_usuario`, `documento`, `nombres`, `password_hash`, `cambiar_clave_obligatorio`, `id_empresa`, `id_casino_asignado`, `id_sede_principal`, `id_rol`, `id_tipo_usuario`, `id_empresa_temporal`, `id_empresa_contratista`, `codigo_qr`, `activo`, `fecha_creacion`, `created_at`, `updated_at`, `deleted_at`) VALUES
(220, '1062332920', 'EVELYN DAYANA SILVA GUTIERREZ', '$2y$12$5Brgzga43oJQ3keFhN33X.At5V.P16pnftfT4VLQ3PTWWlL2l8qCm', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:21:06', '2026-03-21 01:21:06', '2026-03-21 01:21:06', NULL),
(221, '1062324870', 'CHRISTIAN FELIPE SINISTERRA IZQUIERDA', '$2y$12$72Q9iTWic3vcgU3N308VfuxCbc.rJBEzQh/ML2ripVr3XekBPSqQG', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:21:07', '2026-03-21 01:21:07', '2026-03-21 01:21:07', NULL),
(222, '10490803', 'HUGO ANDRES SOLARTE BOLAÑOS', '$2y$12$UDC/lctFwaYv0lt8qKsN8efj.yJZFjDZof0H1XfnyxGfdfLVTZMdm', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:21:07', '2026-03-21 01:21:07', '2026-03-21 01:21:07', NULL),
(223, '1062302422', 'ALEXANDRA LUCIA TROCHEZ GUZMAN', '$2y$12$KxiVuKhbbxxm0/VxlG41Becu7tmrEFi717eiRAHIOVoPyaR66LOWC', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:21:08', '2026-03-21 01:21:08', '2026-03-21 01:21:08', NULL),
(224, '1061432206', 'YOLANDAUL DAZA', '$2y$12$VkTHgFDvwBXbq7roFUB3E.E5whgL8js4YrZmzmjgcXHehlPub.wgq', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:21:08', '2026-03-21 01:21:08', '2026-03-21 01:21:08', NULL),
(225, '1062321911', 'MARIA CAMILA USURIAGA MORENO', '$2y$12$9RlxgnHB6mrC6h8xDXTrE.Yk8om8icUSPVhjeGILCixPM7cKgSQqi', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:21:08', '2026-03-21 01:21:08', '2026-03-21 01:21:08', NULL),
(226, '1061438581', 'ALDAIR VALENCIA MERA', '$2y$12$b512q14OSw8naE085p0QeO6ke7BLqctuzU7M5d3ESUPGLB2GKtbHe', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:21:09', '2026-03-21 01:21:09', '2026-03-21 01:21:09', NULL),
(227, '1062284612', 'LUIS GUILLER MOVANEGAS BOLAÑOS', '$2y$12$xLttZqHTpjf.27LF.Q9ZDO/Yk2xKPp9Mqu4ynZp/bbxn3pnuJeAKq', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:21:09', '2026-03-21 01:21:09', '2026-03-21 01:21:09', NULL),
(228, '10741038', 'DIRLEY VASQUEZ DIAZ', '$2y$12$OWcCKyfMkjNNhm7VNSrwIO.Sn.znf93nWJdEBczMx8kVo8B9Fh2kO', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:21:09', '2026-03-21 01:21:09', '2026-03-21 01:21:09', NULL),
(229, '76140326', 'VICTOR JAVIERVAS QUEZ MEJIA', '$2y$12$PFdNkNDojUP2dXm7PMafNu3ab0XHaAvGggellvizzzCAcS07GA31.', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:21:10', '2026-03-21 01:21:10', '2026-03-21 01:21:10', NULL),
(230, '1006398659', 'YONIER ALEJANDRO VELEZ DURAN', '$2y$12$clN/qchM6CCTS85s5DXJhuT5xgalBqFktcJyfpWR4HPogjX3BG70C', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:21:10', '2026-03-21 01:21:10', '2026-03-21 01:21:10', NULL),
(231, '1062313410', 'CRISTIAN VICTORIA CHACON', '$2y$12$AQTqTDbU.tqhdffK5FmfLuNWNx5ZSKjHQggLYm8qF5WlamsHAunum', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:21:10', '2026-03-21 01:21:10', '2026-03-21 01:21:10', NULL),
(232, '1059699655', 'WBEIMAR VINASCO TANGARIFE', '$2y$12$s9y/3DJLSFavOGSHzRxiZepUa.eKilcCyZV3pW8p4bzEFdEIpKgvu', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:21:11', '2026-03-21 01:21:11', '2026-03-21 01:21:11', NULL),
(233, '1007145941', 'JUAN CAMILO YELA GARCIA', '$2y$12$RtkcxhZRmMjt3YxniTG0Duc0jkmGlwfW0k/t7q.6X6J7U99zz3sBW', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:21:11', '2026-03-21 01:21:11', '2026-03-21 01:21:11', NULL),
(234, '1007147993', 'JUAN CAMILO YULE SANTACRUZ', '$2y$12$02dTmVGTO1JvP9YT5WsoNO6unNTIhPN1cuFkyKnPmL7H.W/da5jJ6', 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-20 20:21:12', '2026-03-21 01:21:12', '2026-03-21 01:21:12', NULL),
(235, '1061783624', 'IVAN ALONSO MOLINA LONDOÑO', '$2y$12$PRl067M3.lim3Db4JSSy1.ZmRfDLi5hyLulwVNW9NT0M8.hP9Q/qi', 1, 4, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-24 19:15:16', '2026-03-25 00:15:16', '2026-03-25 00:15:16', NULL),
(236, '1143982056', 'JESUS DAVIS PALACIOS', '$2y$12$YGI0CcP45rT3sq/Z3VXJ/.rfRWvQ5GYkEaZ2fW1TPyw4yxEzuz7ra', 1, 4, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-24 19:15:45', '2026-03-25 00:15:45', '2026-03-25 00:15:45', NULL),
(237, '1144095410', 'NATHALIA RISO ACOSTA', '$2y$12$o5zpxI1ZncIBw92asAOnbuTi8XM736s1RyQxs28pN38nhottoLLu6', 1, 4, NULL, 2, 1, 1, NULL, NULL, NULL, 1, '2026-03-24 19:16:11', '2026-03-25 00:16:11', '2026-03-25 00:16:11', NULL),
(238, '1061437655', 'CHRISTIAN EDUARDO SANCLEMENTE', '$2y$12$.R.I.LrJ0.YnZnN7FvxGeOQaA39AHlAsil/xOj6pPi86bQ.46O/6q', 1, 4, NULL, 1, 1, 1, NULL, NULL, NULL, 1, '2026-03-24 19:17:06', '2026-03-25 00:17:06', '2026-03-25 00:17:06', NULL),
(239, '1062321615', 'JOHAN ALEXIS BALANTA CARABALÍ', '$2y$12$SX66fQI91a8tH8DyxVJwWe6DquV0aAuWmVnfqZAqLsBNeiE6AwYpS', 1, 3, NULL, 1, 1, 2, 2, NULL, NULL, 1, '2026-03-24 19:27:23', '2026-03-25 00:27:23', '2026-03-25 00:30:56', NULL),
(240, '1007146200', 'DEISY LILIANA TEGUE MONTOYA', '$2y$12$4GItDH3Mk/fYjIYHZUtwt.FJnBxq7HJFDdvCoZ43C51GrrBDb4O7i', 1, 3, NULL, 1, 1, 2, 2, NULL, NULL, 1, '2026-03-24 19:30:38', '2026-03-25 00:30:38', '2026-03-25 00:30:38', NULL),
(241, '1062297262', 'YOVAN MANUEL PERDOMO PIL', '$2y$12$emuQMZEMv/uK.uTp10YOleYA7jvPTzYS82EGdstMuaBelAerjJwZy', 1, 3, NULL, 1, 1, 2, 2, NULL, NULL, 1, '2026-03-24 19:31:49', '2026-03-25 00:31:49', '2026-03-25 00:31:49', NULL),
(242, '76143236', 'GUSTAVO ADOLFO HOLGUIN', '$2y$12$/mF63k4U/2L57BLMAUdjKOTxYNHrsC299SQFlatXBBM0WdRTGiX.G', 1, 3, NULL, 1, 1, 2, 2, NULL, NULL, 1, '2026-03-24 19:32:21', '2026-03-25 00:32:21', '2026-03-25 00:32:21', NULL),
(243, '1062322382', 'EDWIN ANDRES SOLARTE', '$2y$12$KuInq02DS2/1K8rf9xHyke.rt0RRsrkN6IuhvV4Y0CrUmSeg0x3FO', 1, 3, NULL, 1, 1, 2, 2, NULL, NULL, 1, '2026-03-24 19:33:28', '2026-03-25 00:33:28', '2026-03-25 00:33:28', NULL),
(244, '1062323796', 'STEFANIA FERNANDEZ MUÑOZ', '$2y$12$KynxUX1wGkZoaoYPZ3N7wuA1VmNNKkl5kDUypZLJiSKI6W1PDlVVO', 1, 3, NULL, 1, 1, 2, 2, NULL, NULL, 1, '2026-03-24 19:34:37', '2026-03-25 00:34:37', '2026-03-25 00:34:37', NULL),
(245, '1002947385', 'GEOVANY LUCUMI MINA', '$2y$12$67NKc6UIu0DwLy.fiAESS.bdJ3FnjSz2/vaYpSP9io8jTYxYHYWW6', 1, 3, NULL, 1, 1, 2, 2, NULL, NULL, 1, '2026-03-24 19:35:27', '2026-03-25 00:35:27', '2026-03-25 00:35:27', NULL),
(246, '94531601', 'LUIS FERNANDO RIVAS LARA', '$2y$12$ZPd8g7ZzfKytTkAA6u98OOKNREbnAFoxvQGvckMV3HBKcJMJx2FNC', 1, 3, NULL, 1, 1, 2, 2, NULL, NULL, 1, '2026-03-24 19:36:06', '2026-03-25 00:36:06', '2026-03-25 00:36:06', NULL),
(248, '1054998310', 'JOSE EWLISER RODRIGUEZ', '$2y$12$TBB8rO9Fk6gJsq1XNBxJYOzB5JAQynuym3G5raWbB5jdLJyiviYmy', 1, 1, NULL, 3, 1, 5, NULL, 1, NULL, 1, '2026-03-25 16:04:03', '2026-03-25 21:04:03', '2026-03-25 21:04:03', NULL),
(249, '1053828773', 'HAROLD AMADOR MANJARES', '$2y$12$XFlvLQVUHfq8Js0gX35j.uT5sHbjOYG6bE32ZuiCtqwCxs/4B03wS', 1, 1, NULL, 3, 1, 5, NULL, 1, NULL, 1, '2026-03-25 16:04:58', '2026-03-25 21:04:58', '2026-03-25 21:04:58', NULL),
(250, '1053784819', 'ALEXIDES MOLINA GIRALDO', '$2y$12$UO18hBt.FNQpQLaobsUje.l1hp6.POGAhfXwO5BqJwvwxAk6LLB.G', 1, 1, NULL, 3, 1, 5, NULL, 1, NULL, 1, '2026-03-25 16:05:35', '2026-03-25 21:05:35', '2026-03-25 21:05:35', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `visitantes`
--

CREATE TABLE `visitantes` (
  `id_visitante` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(120) NOT NULL,
  `documento` varchar(30) DEFAULT NULL,
  `empresa_visita` varchar(120) DEFAULT NULL,
  `area_visita` varchar(120) DEFAULT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `visitantes`
--

INSERT INTO `visitantes` (`id_visitante`, `nombre`, `documento`, `empresa_visita`, `area_visita`, `fecha_registro`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'PEPITO PEREZ', '45875669', 'IBC', 'TIC', '2026-02-17 20:15:05', '2026-02-18 01:15:05', '2026-02-18 01:15:05', NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `area_visita`
--
ALTER TABLE `area_visita`
  ADD PRIMARY KEY (`id_area_visita`);

--
-- Indices de la tabla `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indices de la tabla `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indices de la tabla `casinos`
--
ALTER TABLE `casinos`
  ADD PRIMARY KEY (`id_casino`),
  ADD UNIQUE KEY `casinos_nit_unique` (`NIT`),
  ADD KEY `casinos_id_empresa_foreign` (`id_empresa`),
  ADD KEY `casinos_activo_id_empresa_index` (`activo`,`id_empresa`),
  ADD KEY `casinos_id_sede_foreign` (`id_sede`);

--
-- Indices de la tabla `casino_empresa`
--
ALTER TABLE `casino_empresa`
  ADD PRIMARY KEY (`id_casino`,`id_empresa`),
  ADD KEY `casino_empresa_id_empresa_foreign` (`id_empresa`);

--
-- Indices de la tabla `cuentas_cobro`
--
ALTER TABLE `cuentas_cobro`
  ADD PRIMARY KEY (`id_cuenta`),
  ADD KEY `cuentas_cobro_id_casino_fecha_inicio_fecha_fin_index` (`id_casino`,`fecha_inicio`,`fecha_fin`);

--
-- Indices de la tabla `empresas`
--
ALTER TABLE `empresas`
  ADD PRIMARY KEY (`id_empresa`),
  ADD UNIQUE KEY `empresas_nit_unique` (`NIT`);

--
-- Indices de la tabla `empresas_contratistas`
--
ALTER TABLE `empresas_contratistas`
  ADD PRIMARY KEY (`id_empresa_contratista`),
  ADD UNIQUE KEY `empresas_contratistas_nit_unique` (`nit`);

--
-- Indices de la tabla `empresas_temporales`
--
ALTER TABLE `empresas_temporales`
  ADD PRIMARY KEY (`id_empresa_temporal`);

--
-- Indices de la tabla `empresa_usuario`
--
ALTER TABLE `empresa_usuario`
  ADD PRIMARY KEY (`id_usuario`,`id_empresa`),
  ADD KEY `empresa_usuario_id_empresa_foreign` (`id_empresa`);

--
-- Indices de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indices de la tabla `horarios_consumo`
--
ALTER TABLE `horarios_consumo`
  ADD PRIMARY KEY (`id_horario`);

--
-- Indices de la tabla `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indices de la tabla `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indices de la tabla `precios`
--
ALTER TABLE `precios`
  ADD PRIMARY KEY (`id_precio`),
  ADD UNIQUE KEY `precios_id_horario_id_casino_unique` (`id_horario`,`id_casino`),
  ADD KEY `precios_id_casino_foreign` (`id_casino`);

--
-- Indices de la tabla `registro_consumos`
--
ALTER TABLE `registro_consumos`
  ADD PRIMARY KEY (`id_consumo`),
  ADD UNIQUE KEY `uk_consumo_usuario` (`id_usuario`,`fecha_consumo`,`id_horario`),
  ADD KEY `registro_consumos_id_visitante_foreign` (`id_visitante`),
  ADD KEY `registro_consumos_id_empresa_foreign` (`id_empresa`),
  ADD KEY `registro_consumos_id_casino_foreign` (`id_casino`),
  ADD KEY `registro_consumos_id_horario_foreign` (`id_horario`),
  ADD KEY `registro_consumos_registrado_por_foreign` (`registrado_por`),
  ADD KEY `registro_consumos_fecha_consumo_index` (`fecha_consumo`),
  ADD KEY `registro_consumos_id_area_visita_foreign` (`id_area_visita`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id_rol`),
  ADD UNIQUE KEY `roles_nombre_unique` (`nombre`);

--
-- Indices de la tabla `sedes`
--
ALTER TABLE `sedes`
  ADD PRIMARY KEY (`id_sede`);

--
-- Indices de la tabla `sede_usuario`
--
ALTER TABLE `sede_usuario`
  ADD PRIMARY KEY (`id_usuario`,`id_sede`),
  ADD KEY `sede_usuario_id_sede_foreign` (`id_sede`);

--
-- Indices de la tabla `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indices de la tabla `tipos_usuario`
--
ALTER TABLE `tipos_usuario`
  ADD PRIMARY KEY (`id_tipo_usuario`),
  ADD UNIQUE KEY `tipos_usuario_nombre_unique` (`nombre`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `usuarios_documento_unique` (`documento`),
  ADD KEY `usuarios_id_empresa_foreign` (`id_empresa`),
  ADD KEY `usuarios_id_tipo_usuario_foreign` (`id_tipo_usuario`),
  ADD KEY `usuarios_activo_id_empresa_index` (`activo`,`id_empresa`),
  ADD KEY `usuarios_id_rol_index` (`id_rol`),
  ADD KEY `usuarios_id_casino_asignado_foreign` (`id_casino_asignado`),
  ADD KEY `usuarios_id_sede_principal_foreign` (`id_sede_principal`),
  ADD KEY `usuarios_id_empresa_temporal_foreign` (`id_empresa_temporal`),
  ADD KEY `usuarios_id_empresa_contratista_foreign` (`id_empresa_contratista`);

--
-- Indices de la tabla `visitantes`
--
ALTER TABLE `visitantes`
  ADD PRIMARY KEY (`id_visitante`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `area_visita`
--
ALTER TABLE `area_visita`
  MODIFY `id_area_visita` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `casinos`
--
ALTER TABLE `casinos`
  MODIFY `id_casino` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `cuentas_cobro`
--
ALTER TABLE `cuentas_cobro`
  MODIFY `id_cuenta` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `empresas`
--
ALTER TABLE `empresas`
  MODIFY `id_empresa` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `empresas_contratistas`
--
ALTER TABLE `empresas_contratistas`
  MODIFY `id_empresa_contratista` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `empresas_temporales`
--
ALTER TABLE `empresas_temporales`
  MODIFY `id_empresa_temporal` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `horarios_consumo`
--
ALTER TABLE `horarios_consumo`
  MODIFY `id_horario` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT de la tabla `precios`
--
ALTER TABLE `precios`
  MODIFY `id_precio` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de la tabla `registro_consumos`
--
ALTER TABLE `registro_consumos`
  MODIFY `id_consumo` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id_rol` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `sedes`
--
ALTER TABLE `sedes`
  MODIFY `id_sede` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `tipos_usuario`
--
ALTER TABLE `tipos_usuario`
  MODIFY `id_tipo_usuario` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=251;

--
-- AUTO_INCREMENT de la tabla `visitantes`
--
ALTER TABLE `visitantes`
  MODIFY `id_visitante` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `casinos`
--
ALTER TABLE `casinos`
  ADD CONSTRAINT `casinos_id_empresa_foreign` FOREIGN KEY (`id_empresa`) REFERENCES `empresas` (`id_empresa`) ON DELETE CASCADE,
  ADD CONSTRAINT `casinos_id_sede_foreign` FOREIGN KEY (`id_sede`) REFERENCES `sedes` (`id_sede`) ON DELETE SET NULL;

--
-- Filtros para la tabla `casino_empresa`
--
ALTER TABLE `casino_empresa`
  ADD CONSTRAINT `casino_empresa_id_casino_foreign` FOREIGN KEY (`id_casino`) REFERENCES `casinos` (`id_casino`) ON DELETE CASCADE,
  ADD CONSTRAINT `casino_empresa_id_empresa_foreign` FOREIGN KEY (`id_empresa`) REFERENCES `empresas` (`id_empresa`) ON DELETE CASCADE;

--
-- Filtros para la tabla `cuentas_cobro`
--
ALTER TABLE `cuentas_cobro`
  ADD CONSTRAINT `cuentas_cobro_id_casino_foreign` FOREIGN KEY (`id_casino`) REFERENCES `casinos` (`id_casino`) ON DELETE CASCADE;

--
-- Filtros para la tabla `empresa_usuario`
--
ALTER TABLE `empresa_usuario`
  ADD CONSTRAINT `empresa_usuario_id_empresa_foreign` FOREIGN KEY (`id_empresa`) REFERENCES `empresas` (`id_empresa`) ON DELETE CASCADE,
  ADD CONSTRAINT `empresa_usuario_id_usuario_foreign` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `precios`
--
ALTER TABLE `precios`
  ADD CONSTRAINT `precios_id_casino_foreign` FOREIGN KEY (`id_casino`) REFERENCES `casinos` (`id_casino`) ON DELETE CASCADE,
  ADD CONSTRAINT `precios_id_horario_foreign` FOREIGN KEY (`id_horario`) REFERENCES `horarios_consumo` (`id_horario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `registro_consumos`
--
ALTER TABLE `registro_consumos`
  ADD CONSTRAINT `registro_consumos_id_area_visita_foreign` FOREIGN KEY (`id_area_visita`) REFERENCES `area_visita` (`id_area_visita`) ON DELETE SET NULL,
  ADD CONSTRAINT `registro_consumos_id_casino_foreign` FOREIGN KEY (`id_casino`) REFERENCES `casinos` (`id_casino`) ON DELETE CASCADE,
  ADD CONSTRAINT `registro_consumos_id_empresa_foreign` FOREIGN KEY (`id_empresa`) REFERENCES `empresas` (`id_empresa`) ON DELETE CASCADE,
  ADD CONSTRAINT `registro_consumos_id_horario_foreign` FOREIGN KEY (`id_horario`) REFERENCES `horarios_consumo` (`id_horario`) ON DELETE CASCADE,
  ADD CONSTRAINT `registro_consumos_id_usuario_foreign` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE SET NULL,
  ADD CONSTRAINT `registro_consumos_id_visitante_foreign` FOREIGN KEY (`id_visitante`) REFERENCES `visitantes` (`id_visitante`) ON DELETE SET NULL,
  ADD CONSTRAINT `registro_consumos_registrado_por_foreign` FOREIGN KEY (`registrado_por`) REFERENCES `usuarios` (`id_usuario`) ON DELETE SET NULL;

--
-- Filtros para la tabla `sede_usuario`
--
ALTER TABLE `sede_usuario`
  ADD CONSTRAINT `sede_usuario_id_sede_foreign` FOREIGN KEY (`id_sede`) REFERENCES `sedes` (`id_sede`) ON DELETE CASCADE,
  ADD CONSTRAINT `sede_usuario_id_usuario_foreign` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_id_casino_asignado_foreign` FOREIGN KEY (`id_casino_asignado`) REFERENCES `casinos` (`id_casino`) ON DELETE SET NULL,
  ADD CONSTRAINT `usuarios_id_empresa_contratista_foreign` FOREIGN KEY (`id_empresa_contratista`) REFERENCES `empresas_contratistas` (`id_empresa_contratista`) ON DELETE SET NULL,
  ADD CONSTRAINT `usuarios_id_empresa_foreign` FOREIGN KEY (`id_empresa`) REFERENCES `empresas` (`id_empresa`) ON DELETE CASCADE,
  ADD CONSTRAINT `usuarios_id_empresa_temporal_foreign` FOREIGN KEY (`id_empresa_temporal`) REFERENCES `empresas_temporales` (`id_empresa_temporal`) ON DELETE SET NULL,
  ADD CONSTRAINT `usuarios_id_rol_foreign` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`) ON DELETE CASCADE,
  ADD CONSTRAINT `usuarios_id_sede_principal_foreign` FOREIGN KEY (`id_sede_principal`) REFERENCES `sedes` (`id_sede`) ON DELETE SET NULL,
  ADD CONSTRAINT `usuarios_id_tipo_usuario_foreign` FOREIGN KEY (`id_tipo_usuario`) REFERENCES `tipos_usuario` (`id_tipo_usuario`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
