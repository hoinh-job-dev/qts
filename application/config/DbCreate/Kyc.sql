-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 06, 2016 at 10:33 PM
-- Server version: 5.5.52-0ubuntu0.14.04.1
-- PHP Version: 5.5.9-1ubuntu4.20

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `Kyc`
--

-- --------------------------------------------------------

--
-- Table structure for table `m_system`
--

DROP TABLE IF EXISTS `m_system`;
CREATE TABLE IF NOT EXISTS `m_system` (
  `key` tinyint(3) NOT NULL,
  `value` varchar(30) COLLATE utf8_bin NOT NULL,
  `create_by` int(10) NOT NULL,
  `create_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_by` int(10) DEFAULT NULL,
  `update_at` timestamp NULL DEFAULT NULL,
  `delete_flag` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `t_database_tracking`
--

DROP TABLE IF EXISTS `t_database_tracking`;
CREATE TABLE IF NOT EXISTS `t_database_tracking` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `hash` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `hash` (`hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `t_imgfile`
--

DROP TABLE IF EXISTS `t_imgfile`;
CREATE TABLE IF NOT EXISTS `t_imgfile` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `system_key` tinyint(3) NOT NULL,
  `personal_id` int(10) NOT NULL,
  `imgfile` varchar(40) NOT NULL DEFAULT '',
  `create_by` int(10) NOT NULL DEFAULT '1',
  `create_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_by` int(10) DEFAULT NULL,
  `update_at` timestamp NULL DEFAULT NULL,
  `delete_flag` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `t_personal`
--

DROP TABLE IF EXISTS `t_personal`;
CREATE TABLE IF NOT EXISTS `t_personal` (
  `personal_id` int(10) NOT NULL AUTO_INCREMENT,
  `system_key` tinyint(3) NOT NULL,
  `family_name` varchar(20) COLLATE utf8_bin NOT NULL DEFAULT '',
  `first_name` varchar(20) COLLATE utf8_bin NOT NULL DEFAULT '',
  `company_name` varchar(40) COLLATE utf8_bin DEFAULT NULL,
  `family_name_kana` varchar(20) COLLATE utf8_bin NOT NULL DEFAULT '',
  `first_name_kana` varchar(20) COLLATE utf8_bin NOT NULL DEFAULT '',
  `company_name_kana` varchar(40) COLLATE utf8_bin DEFAULT NULL,
  `birthday` date NOT NULL,
  `email` varchar(50) COLLATE utf8_bin NOT NULL,
  `tel` varchar(15) COLLATE utf8_bin NOT NULL,
  `zip_code` varchar(10) COLLATE utf8_bin NOT NULL,
  `country` varchar(10) COLLATE utf8_bin NOT NULL,
  `prefecture` varchar(10) COLLATE utf8_bin NOT NULL,
  `city` varchar(30) COLLATE utf8_bin NOT NULL DEFAULT '',
  `building` varchar(40) COLLATE utf8_bin NOT NULL DEFAULT '',
  `personal_agreement` tinyint(1) NOT NULL DEFAULT '1',
  `create_by` int(10) NOT NULL,
  `create_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_by` int(10) DEFAULT NULL,
  `update_at` timestamp NULL DEFAULT NULL,
  `delete_flag` tinyint(1) NOT NULL,
  PRIMARY KEY (`personal_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
