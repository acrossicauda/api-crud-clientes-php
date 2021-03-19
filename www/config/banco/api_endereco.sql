-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: 19-Mar-2021 às 18:03
-- Versão do servidor: 5.7.19
-- PHP Version: 7.1.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `api_endereco`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `cidades`
--

DROP TABLE IF EXISTS `cidades`;
CREATE TABLE IF NOT EXISTS `cidades` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `cidade` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `idCidade_estado` bigint(20) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `cidades_idcidade_estado_foreign` (`idCidade_estado`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- --------------------------------------------------------

--
-- Estrutura da tabela `enderecos`
--

DROP TABLE IF EXISTS `enderecos`;
CREATE TABLE IF NOT EXISTS `enderecos` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `tipoLogradouro` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `logradouro` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cep` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `numero` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `idEndereco_cidade` bigint(20) UNSIGNED NOT NULL,
  `idEndereco_estado` bigint(20) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `enderecos_idendereco_cidade_foreign` (`idEndereco_cidade`),
  KEY `enderecos_idendereco_estado_foreign` (`idEndereco_estado`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- --------------------------------------------------------

--
-- Estrutura da tabela `estados`
--

DROP TABLE IF EXISTS `estados`;
CREATE TABLE IF NOT EXISTS `estados` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `estado` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `uf` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `estados_estado_unique` (`estado`),
  UNIQUE KEY `estados_uf_unique` (`uf`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- --------------------------------------------------------

--
-- Estrutura da tabela `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



-- --------------------------------------------------------

--
-- Estrutura da tabela `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `login` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `senha` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `idEndereco` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `usuarios_login_unique` (`login`),
  KEY `usuarios_idendereco_foreign` (`idEndereco`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
