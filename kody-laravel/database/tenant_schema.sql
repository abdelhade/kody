-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: kody26
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `acc_groups`
--

DROP TABLE IF EXISTS `acc_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `acc_groups` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `aname` varchar(40) NOT NULL,
  `acc_type` int(1) NOT NULL,
  `parent_id` int(1) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `code` varchar(30) DEFAULT NULL,
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `aname` (`aname`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `acc_head`
--

DROP TABLE IF EXISTS `acc_head`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `acc_head` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `code` varchar(20) NOT NULL,
  `deletable` int(11) DEFAULT 1,
  `editable` int(1) NOT NULL DEFAULT 1,
  `aname` varchar(50) NOT NULL,
  `phone` varchar(200) DEFAULT NULL,
  `address` varchar(200) DEFAULT NULL,
  `e_mail` varchar(100) DEFAULT NULL,
  `constant` int(11) NOT NULL DEFAULT 0,
  `is_stock` int(1) NOT NULL DEFAULT 0,
  `is_fund` int(11) DEFAULT 0,
  `rentable` int(11) DEFAULT NULL,
  `parent_id` int(1) NOT NULL,
  `nature` int(1) DEFAULT NULL,
  `kind` int(1) DEFAULT NULL,
  `is_basic` int(1) NOT NULL,
  `start_balance` decimal(3,0) NOT NULL DEFAULT 0,
  `credit` decimal(3,0) NOT NULL DEFAULT 0,
  `debit` decimal(3,0) NOT NULL DEFAULT 0,
  `balance` decimal(12,3) NOT NULL DEFAULT 0.000,
  `secret` int(1) NOT NULL DEFAULT 0,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `info` varchar(250) DEFAULT NULL,
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  UNIQUE KEY `aname` (`aname`)
) ENGINE=InnoDB AUTO_INCREMENT=284 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `allowances`
--

DROP TABLE IF EXISTS `allowances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `allowances` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `info` varchar(250) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `tybe` int(1) NOT NULL,
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `analisys`
--

DROP TABLE IF EXISTS `analisys`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `analisys` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client` int(11) NOT NULL,
  `lap` int(11) NOT NULL,
  `name` varchar(250) DEFAULT NULL,
  `comment` varchar(250) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `img` varchar(250) DEFAULT NULL,
  `info` varchar(250) DEFAULT NULL,
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `attandance`
--

DROP TABLE IF EXISTS `attandance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `attandance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee` int(11) NOT NULL,
  `fptybe` int(1) NOT NULL,
  `fpdate` date NOT NULL DEFAULT current_timestamp(),
  `time` time NOT NULL DEFAULT current_timestamp(),
  `user` int(1) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `isdeleted` tinyint(1) NOT NULL DEFAULT 0,
  `fromwhere` varchar(10) DEFAULT NULL,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `employee` (`employee`),
  CONSTRAINT `attandance_ibfk_1` FOREIGN KEY (`employee`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8111 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `attdocs`
--

DROP TABLE IF EXISTS `attdocs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `attdocs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `empid` int(11) NOT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `fromdate` date DEFAULT NULL,
  `todate` date NOT NULL,
  `alldays` double NOT NULL,
  `workdays` double NOT NULL,
  `exphours` double NOT NULL,
  `accualhours` double NOT NULL,
  `attdays` int(11) NOT NULL,
  `absdays` int(11) NOT NULL,
  `holidays` int(11) NOT NULL,
  `earlyminits` double NOT NULL,
  `entitle` double NOT NULL DEFAULT 0,
  `bonus` double NOT NULL DEFAULT 0,
  `insurance` double NOT NULL DEFAULT 0,
  `tax` double NOT NULL DEFAULT 0,
  `deduction` double NOT NULL DEFAULT 0,
  `net_pay` double NOT NULL DEFAULT 0,
  `info` varchar(250) DEFAULT NULL,
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=520 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `attlog`
--

DROP TABLE IF EXISTS `attlog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `attlog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee` int(1) NOT NULL,
  `day` date NOT NULL,
  `starttime` time DEFAULT NULL,
  `endtime` time DEFAULT NULL,
  `fpin` time DEFAULT NULL,
  `fpout` time DEFAULT NULL,
  `defhours` double DEFAULT NULL,
  `curhours` double DEFAULT NULL,
  `dueforhour` double DEFAULT NULL,
  `realdue` double DEFAULT NULL,
  `statue` int(1) NOT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `info` int(11) DEFAULT NULL,
  `attdoc` int(1) DEFAULT NULL,
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2594 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `barcodes`
--

DROP TABLE IF EXISTS `barcodes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `barcodes` (
  `id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `barcode` varchar(25) NOT NULL,
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `barcode` (`barcode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `book_tybes`
--

DROP TABLE IF EXISTS `book_tybes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `book_tybes` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `name` varchar(25) NOT NULL,
  `value` double DEFAULT NULL,
  `qty` int(1) NOT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `booking_cards`
--

DROP TABLE IF EXISTS `booking_cards`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `booking_cards` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client` varchar(100) NOT NULL,
  `barcode` varchar(50) NOT NULL,
  `rtybe` varchar(20) NOT NULL,
  `rcost` int(11) NOT NULL,
  `qty` int(11) NOT NULL,
  `remain` int(11) NOT NULL,
  `bcase` int(11) NOT NULL,
  `fromdate` date NOT NULL,
  `todate` date NOT NULL,
  `crtime` datetime NOT NULL DEFAULT current_timestamp(),
  `isdeleted` int(1) NOT NULL DEFAULT 0,
  `user` int(1) NOT NULL DEFAULT 0,
  `bransh` int(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `calls`
--

DROP TABLE IF EXISTS `calls`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `calls` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subject` varchar(100) NOT NULL,
  `call_type` int(1) NOT NULL DEFAULT 1,
  `call_date` date NOT NULL DEFAULT current_timestamp(),
  `call_time` time NOT NULL DEFAULT current_timestamp(),
  `duration` varchar(100) DEFAULT NULL,
  `client_id` int(11) NOT NULL DEFAULT 1,
  `emp_comment` varchar(250) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `next_date` date NOT NULL DEFAULT current_timestamp(),
  `next_time` time NOT NULL DEFAULT current_timestamp(),
  `mod_comment` varchar(250) DEFAULT NULL,
  `mod_rate` int(1) NOT NULL DEFAULT 5,
  `user_id` int(11) NOT NULL DEFAULT 1,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cases`
--

DROP TABLE IF EXISTS `cases`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cases` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `cname` varchar(50) NOT NULL,
  `info` varchar(100) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chances`
--

DROP TABLE IF EXISTS `chances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chances` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `client` varchar(50) DEFAULT NULL,
  `cname` varchar(50) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `cdate` date DEFAULT NULL,
  `important` int(1) DEFAULT NULL,
  `expected` double NOT NULL DEFAULT 0,
  `tybe` int(1) NOT NULL DEFAULT 1,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chances_tybes`
--

DROP TABLE IF EXISTS `chances_tybes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chances_tybes` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `cname` varchar(50) NOT NULL,
  `info` varchar(50) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cname` (`cname`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cities`
--

DROP TABLE IF EXISTS `cities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cities` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `cname` varchar(150) NOT NULL,
  `info` varchar(150) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user` int(1) NOT NULL DEFAULT 1,
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cname` (`cname`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `clients`
--

DROP TABLE IF EXISTS `clients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `clients` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `phone2` varchar(150) DEFAULT NULL,
  `address` varchar(250) DEFAULT NULL,
  `address2` varchar(150) DEFAULT NULL,
  `address3` varchar(150) DEFAULT NULL,
  `city` int(11) DEFAULT NULL,
  `height` double DEFAULT NULL,
  `weight` double DEFAULT NULL,
  `dateofbirth` date DEFAULT NULL,
  `ref` varchar(20) DEFAULT NULL,
  `diseses` varchar(200) DEFAULT NULL,
  `info` varchar(200) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `imgs` varchar(250) DEFAULT NULL,
  `jop` varchar(50) DEFAULT NULL,
  `gender` int(1) DEFAULT NULL,
  `drugs` varchar(250) DEFAULT NULL,
  `seriousdes` varchar(250) DEFAULT NULL,
  `familydes` varchar(250) DEFAULT NULL,
  `allergy` varchar(250) DEFAULT NULL,
  `temp` varchar(9) DEFAULT NULL,
  `pressure` varchar(9) DEFAULT NULL,
  `diabetes` varchar(9) DEFAULT NULL,
  `brate` varchar(9) DEFAULT NULL,
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `closed_orders`
--

DROP TABLE IF EXISTS `closed_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `closed_orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shift` varchar(10) NOT NULL,
  `user` varchar(10) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `strttime` datetime DEFAULT NULL,
  `endtime` time DEFAULT NULL,
  `total_sales` double NOT NULL DEFAULT 0,
  `delevery` double NOT NULL DEFAULT 0,
  `tables` double NOT NULL DEFAULT 0,
  `takeaway` double NOT NULL DEFAULT 0,
  `expenses` double NOT NULL DEFAULT 0,
  `fund_before` double NOT NULL DEFAULT 0,
  `fund_after` double NOT NULL DEFAULT 0,
  `exp_notes` varchar(30) DEFAULT NULL,
  `cash` double NOT NULL DEFAULT 0,
  `info` varchar(50) DEFAULT NULL,
  `crtime` datetime NOT NULL DEFAULT current_timestamp(),
  `mdtime` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `info2` varchar(20) DEFAULT NULL,
  `tenant` int(11) NOT NULL DEFAULT 1,
  `branch` int(11) NOT NULL DEFAULT 1,
  `total_cash` decimal(10,2) DEFAULT 0.00,
  `total_visa` decimal(10,2) DEFAULT 0.00,
  `total_discount` decimal(10,2) DEFAULT 0.00,
  `total_returns` decimal(10,2) DEFAULT 0.00,
  `start_cash` decimal(10,2) DEFAULT 0.00,
  `actual_cash` decimal(10,2) DEFAULT 0.00,
  `actual_visa` decimal(10,2) DEFAULT 0.00,
  `deficit` decimal(10,2) DEFAULT 0.00,
  `status` tinyint(1) DEFAULT 1,
  `json_details` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cost_centers`
--

DROP TABLE IF EXISTS `cost_centers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cost_centers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cname` varchar(100) NOT NULL,
  `info` varchar(200) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cname` (`cname`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `criminals`
--

DROP TABLE IF EXISTS `criminals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `criminals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cname` varchar(200) NOT NULL,
  `nickname` varchar(200) NOT NULL,
  `dateofbirth` date DEFAULT NULL,
  `jop` varchar(200) NOT NULL,
  `station` varchar(111) DEFAULT NULL,
  `mname` varchar(200) NOT NULL,
  `crmaddress` varchar(200) NOT NULL,
  `idcardnum` varchar(200) NOT NULL,
  `scar` int(11) NOT NULL,
  `otherdetails` varchar(200) NOT NULL,
  `prtnrs` varchar(200) NOT NULL,
  `crmstyle` int(11) DEFAULT NULL,
  `dngrs` int(11) DEFAULT NULL,
  `fesh` int(11) DEFAULT NULL,
  `karta` int(11) DEFAULT NULL,
  `entry` int(11) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NULL DEFAULT current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `crm_style`
--

DROP TABLE IF EXISTS `crm_style`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `crm_style` (
  `id` int(11) NOT NULL,
  `cname` varchar(200) NOT NULL,
  `info` varchar(200) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NULL DEFAULT current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ctp`
--

DROP TABLE IF EXISTS `ctp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ctp` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `cname` varchar(50) NOT NULL,
  `type` varchar(100) DEFAULT NULL,
  `number` varchar(100) DEFAULT NULL,
  `info` varchar(100) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cvs`
--

DROP TABLE IF EXISTS `cvs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cvs` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `userid` int(1) NOT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `name` varchar(50) NOT NULL,
  `degree` varchar(50) DEFAULT NULL,
  `address` varchar(100) DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(50) NOT NULL,
  `skills` text DEFAULT NULL,
  `exp1` varchar(250) DEFAULT NULL,
  `exp2` varchar(250) DEFAULT NULL,
  `exp3` varchar(250) DEFAULT NULL,
  `lastsalary` varchar(50) NOT NULL,
  `expsalary` varchar(50) NOT NULL DEFAULT '0',
  `referances` text DEFAULT NULL,
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`,`email`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `delivery_clients`
--

DROP TABLE IF EXISTS `delivery_clients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `delivery_clients` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_name` varchar(255) NOT NULL COMMENT 'Customer name',
  `phone` varchar(20) NOT NULL COMMENT 'Phone number',
  `address` text NOT NULL COMMENT 'Address',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Created date',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Updated date',
  `isdeleted` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Is deleted',
  PRIMARY KEY (`id`),
  UNIQUE KEY `phone` (`phone`),
  KEY `isdeleted` (`isdeleted`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Delivery clients table';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `departments`
--

DROP TABLE IF EXISTS `departments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `departments` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `info` varchar(250) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `drugs`
--

DROP TABLE IF EXISTS `drugs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `drugs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(40) NOT NULL,
  `purpose` varchar(200) DEFAULT NULL,
  `effectivematerial` varchar(200) DEFAULT NULL,
  `sideeffects` text DEFAULT NULL,
  `info` varchar(250) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user` int(11) NOT NULL DEFAULT 1,
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `emp_allowences`
--

DROP TABLE IF EXISTS `emp_allowences`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `emp_allowences` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `empid` int(1) NOT NULL,
  `allowid` int(1) NOT NULL,
  `value` double NOT NULL,
  `info` int(1) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `emp_kbis`
--

DROP TABLE IF EXISTS `emp_kbis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `emp_kbis` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `emp_id` int(1) NOT NULL,
  `kbi_id` int(1) DEFAULT NULL,
  `kbi_weight` decimal(10,2) DEFAULT NULL,
  `kbi_rate` decimal(10,2) DEFAULT NULL,
  `kbi_sum` decimal(10,2) DEFAULT NULL,
  `user` int(11) DEFAULT 1,
  `crtime` datetime DEFAULT current_timestamp(),
  `mdtime` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `isdeleted` int(1) DEFAULT 0,
  `tenant` int(1) DEFAULT 0,
  `branch` int(1) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `emplog`
--

DROP TABLE IF EXISTS `emplog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `emplog` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `employee` int(1) NOT NULL,
  `date` date NOT NULL,
  `chkin` time DEFAULT NULL,
  `chkout` time DEFAULT NULL,
  `addin` time DEFAULT NULL,
  `addout` time DEFAULT NULL,
  `latecost` double DEFAULT NULL,
  `earlcost` double DEFAULT NULL,
  `absent` int(11) DEFAULT NULL,
  `holiday` int(11) DEFAULT NULL,
  `deducation` double DEFAULT NULL,
  `additional` double DEFAULT NULL,
  `user` int(11) NOT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `employee_evaluations`
--

DROP TABLE IF EXISTS `employee_evaluations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employee_evaluations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `emp_id` int(11) NOT NULL,
  `evaluation_type` varchar(100) NOT NULL,
  `points_type` enum('plus','minus') NOT NULL,
  `points` int(11) NOT NULL DEFAULT 1,
  `notes` text DEFAULT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `emp_id` (`emp_id`),
  CONSTRAINT `employee_evaluations_ibfk_1` FOREIGN KEY (`emp_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `employee_operations`
--

DROP TABLE IF EXISTS `employee_operations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employee_operations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `operation_id` int(11) NOT NULL,
  `status` varchar(50) DEFAULT 'assigned',
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `completed_at` timestamp NULL DEFAULT NULL,
  `notes` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `operation_id` (`operation_id`),
  CONSTRAINT `employee_operations_ibfk_1` FOREIGN KEY (`operation_id`) REFERENCES `hr_operations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `employees`
--

DROP TABLE IF EXISTS `employees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employees` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `basma_id` int(11) DEFAULT NULL,
  `basma_name` varchar(50) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `info` varchar(250) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `imgs` varchar(250) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `number` varchar(13) DEFAULT NULL,
  `active` int(1) NOT NULL DEFAULT 1,
  `dateofbirth` date DEFAULT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `country` int(1) DEFAULT NULL,
  `address` varchar(250) DEFAULT NULL,
  `address2` varchar(250) DEFAULT NULL,
  `town` int(1) DEFAULT NULL,
  `jop` int(1) DEFAULT NULL,
  `department` int(1) DEFAULT NULL,
  `joptybe` int(1) DEFAULT NULL,
  `joplevel` int(1) DEFAULT NULL,
  `dateofhire` date DEFAULT NULL,
  `dateofend` date DEFAULT NULL,
  `shift` int(1) DEFAULT NULL,
  `vacancy` int(1) DEFAULT NULL,
  `holiday` int(1) DEFAULT NULL,
  `salary` decimal(11,2) DEFAULT NULL,
  `password` varchar(250) DEFAULT NULL,
  `education` varchar(100) DEFAULT NULL,
  `skills` varchar(200) DEFAULT NULL,
  `hour_extra` decimal(10,2) NOT NULL DEFAULT 0.00,
  `day_extra` decimal(10,2) NOT NULL DEFAULT 0.00,
  `ent_tybe` int(11) NOT NULL DEFAULT 1,
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  `calc_type` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=101 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `entitles`
--

DROP TABLE IF EXISTS `entitles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `entitles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tybe` varchar(50) NOT NULL,
  `info` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `extras`
--

DROP TABLE IF EXISTS `extras`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `extras` (
  `id` int(11) NOT NULL,
  `empid` int(1) NOT NULL,
  `info` varchar(100) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `val` double NOT NULL,
  `tybe` int(1) NOT NULL,
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `fat_details`
--

DROP TABLE IF EXISTS `fat_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fat_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pro_tybe` int(11) DEFAULT NULL,
  `det_store` int(11) DEFAULT 1,
  `pro_id` int(11) DEFAULT NULL,
  `item_id` int(11) DEFAULT 0,
  `u_val` decimal(10,3) NOT NULL DEFAULT 1.000,
  `qty_in` double DEFAULT 0,
  `qty_out` double DEFAULT 0,
  `price` double DEFAULT 0,
  `cost_price` double(12,2) DEFAULT NULL,
  `stock_value` double(12,2) DEFAULT NULL,
  `discount` double DEFAULT NULL,
  `disc_pct` decimal(10,2) DEFAULT 0.00,
  `plus` double DEFAULT NULL,
  `det_value` double DEFAULT 0,
  `profit` float NOT NULL DEFAULT 0,
  `fatid` int(11) DEFAULT NULL,
  `fat_tybe` int(11) NOT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`),
  CONSTRAINT `fat_details_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `myitems` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=146 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `update_balance_trigger` AFTER INSERT ON `fat_details` FOR EACH ROW BEGIN UPDATE myitems SET itmqty = ( SELECT COALESCE(SUM(qty_in), 0)  - COALESCE(SUM(qty_out), 0) FROM fat_details WHERE item_id = NEW.item_id  AND isdeleted = 0 ) WHERE id = NEW.item_id ; END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `update_after_update` AFTER UPDATE ON `fat_details` FOR EACH ROW BEGIN UPDATE myitems SET itmqty = ( SELECT COALESCE(SUM(qty_in), 0) - COALESCE(SUM(qty_out), 0) FROM fat_details WHERE item_id = NEW.item_id AND isdeleted = 0 ) WHERE id = NEW.item_id AND isdeleted = 0 ; END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `fat_tybes`
--

DROP TABLE IF EXISTS `fat_tybes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fat_tybes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fname` varchar(200) NOT NULL,
  `info` varchar(200) DEFAULT NULL,
  `crttime` timestamp NOT NULL DEFAULT current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `fats`
--

DROP TABLE IF EXISTS `fats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fat_id` int(11) NOT NULL,
  `zanka_id` int(11) NOT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1012 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `fptybes`
--

DROP TABLE IF EXISTS `fptybes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fptybes` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hiringcontracts`
--

DROP TABLE IF EXISTS `hiringcontracts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hiringcontracts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `employee` int(11) NOT NULL,
  `jop` int(11) NOT NULL,
  `jopdescription` varchar(250) DEFAULT NULL,
  `joprule1` text DEFAULT NULL,
  `joprule2` text DEFAULT NULL,
  `joprule3` text DEFAULT NULL,
  `joprule4` text DEFAULT NULL,
  `workhours` int(11) DEFAULT NULL,
  `inorderhours` int(11) DEFAULT NULL,
  `workdaysoff` int(11) DEFAULT NULL,
  `salary` int(11) DEFAULT NULL,
  `salaryraise` int(11) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `user` int(11) NOT NULL,
  `info` varchar(250) DEFAULT NULL,
  `startcontract` date DEFAULT NULL,
  `endcontract` date DEFAULT NULL,
  `type` int(11) NOT NULL,
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `holidays`
--

DROP TABLE IF EXISTS `holidays`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `holidays` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(10) NOT NULL,
  `info` text DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hr_operation_steps`
--

DROP TABLE IF EXISTS `hr_operation_steps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hr_operation_steps` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `operation_id` int(11) NOT NULL,
  `description` text NOT NULL,
  `step_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `operation_id` (`operation_id`),
  CONSTRAINT `hr_operation_steps_ibfk_1` FOREIGN KEY (`operation_id`) REFERENCES `hr_operations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hr_operations`
--

DROP TABLE IF EXISTS `hr_operations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hr_operations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`),
  CONSTRAINT `hr_operations_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `hr_operations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `imgs`
--

DROP TABLE IF EXISTS `imgs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `imgs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `iname` text NOT NULL,
  `cname` int(11) DEFAULT NULL,
  `itemid` int(11) NOT NULL,
  `size` int(11) NOT NULL,
  `clprofile` int(11) DEFAULT NULL,
  `img_date` date DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `imporfplog`
--

DROP TABLE IF EXISTS `imporfplog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `imporfplog` (
  `id` int(1) DEFAULT NULL,
  `basma_id` int(11) NOT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `item_group`
--

DROP TABLE IF EXISTS `item_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `item_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gname` varchar(100) NOT NULL,
  `info` varchar(200) DEFAULT NULL,
  `parent` int(1) DEFAULT 0,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user` int(1) NOT NULL DEFAULT 0,
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `gname` (`gname`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `item_group2`
--

DROP TABLE IF EXISTS `item_group2`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `item_group2` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gname` varchar(100) NOT NULL,
  `info` varchar(100) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user` int(11) NOT NULL DEFAULT 0,
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `gname` (`gname`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `item_group3`
--

DROP TABLE IF EXISTS `item_group3`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `item_group3` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gname` varchar(100) NOT NULL,
  `info` varchar(200) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user` int(1) NOT NULL DEFAULT 0,
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `gname` (`gname`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `item_units`
--

DROP TABLE IF EXISTS `item_units`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `item_units` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL,
  `unit_id` int(11) NOT NULL,
  `u_val` decimal(10,3) NOT NULL,
  `def_sale` int(11) NOT NULL DEFAULT 0,
  `def_buy` int(11) NOT NULL DEFAULT 0,
  `def_stock` int(11) NOT NULL DEFAULT 0,
  `cost_price` int(11) NOT NULL,
  `price1` decimal(10,3) NOT NULL DEFAULT 0.000,
  `price2` decimal(10,3) NOT NULL DEFAULT 0.000,
  `price3` decimal(10,3) NOT NULL DEFAULT 0.000,
  `price4` decimal(10,3) DEFAULT 0.000,
  `unit_barcode` varchar(20) NOT NULL,
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `joplevels`
--

DROP TABLE IF EXISTS `joplevels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `joplevels` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `info` varchar(250) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `joprules`
--

DROP TABLE IF EXISTS `joprules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `joprules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `info` varchar(250) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `jops`
--

DROP TABLE IF EXISTS `jops`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jops` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `info` varchar(250) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `joptybes`
--

DROP TABLE IF EXISTS `joptybes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `joptybes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `info` text DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `journal_entries`
--

DROP TABLE IF EXISTS `journal_entries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `journal_entries` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `journal_id` int(1) NOT NULL,
  `account_id` int(1) NOT NULL,
  `debit` int(11) NOT NULL DEFAULT 0,
  `credit` int(11) NOT NULL DEFAULT 0,
  `tybe` int(1) NOT NULL,
  `info` varchar(150) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `op2` int(11) DEFAULT 0,
  `op_id` int(11) DEFAULT 0,
  `isdeleted` tinyint(1) DEFAULT 0,
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `idx_je_journal_id` (`journal_id`),
  KEY `idx_je_account_id` (`account_id`),
  CONSTRAINT `journal_entries_ibfk_1` FOREIGN KEY (`account_id`) REFERENCES `acc_head` (`id`),
  CONSTRAINT `journal_entries_ibfk_2` FOREIGN KEY (`journal_id`) REFERENCES `journal_heads` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=219 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `balance_after_insert` BEFORE INSERT ON `journal_entries` FOR EACH ROW BEGIN UPDATE acc_head SET balance = ( SELECT SUM(debit) - SUM(credit) FROM journal_entries WHERE account_id = NEW.account_id AND isdeleted != 1 ) WHERE id = NEW.account_id ; END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `update_all_ins` AFTER INSERT ON `journal_entries` FOR EACH ROW BEGIN DECLARE debit_sum DECIMAL(18,2); DECLARE credit_sum DECIMAL(18,2); SELECT COALESCE(SUM(debit), 0), COALESCE(SUM(credit), 0) INTO debit_sum, credit_sum FROM journal_entries WHERE account_id = NEW.account_id; UPDATE acc_head SET balance = balance + NEW.debit - NEW.credit WHERE id = NEW.account_id; END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `balance_after_update` AFTER UPDATE ON `journal_entries` FOR EACH ROW BEGIN UPDATE acc_head SET balance = ( SELECT COALESCE(SUM(debit), 0) - COALESCE(SUM(credit), 0) FROM journal_entries WHERE account_id = NEW.account_id AND isdeleted = 0 ) WHERE id = NEW.account_id; END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `balance_after_delete` AFTER DELETE ON `journal_entries` FOR EACH ROW BEGIN UPDATE acc_head SET balance = ( SELECT COALESCE(SUM(debit), 0) - COALESCE(SUM(credit), 0) FROM journal_entries WHERE account_id = OLD.account_id AND isdeleted = 0 ) WHERE id = OLD.account_id; END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `journal_heads`
--

DROP TABLE IF EXISTS `journal_heads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `journal_heads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `journal_id` int(11) NOT NULL,
  `total` double NOT NULL,
  `jdate` date NOT NULL DEFAULT current_timestamp(),
  `op_id` int(11) DEFAULT NULL,
  `pro_tybe` int(11) DEFAULT NULL,
  `details` varchar(250) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `op2` int(11) DEFAULT 0,
  `isdeleted` tinyint(1) DEFAULT 0,
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user` int(1) DEFAULT NULL,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `journal_heads_ibfk_1` (`pro_tybe`)
) ENGINE=InnoDB AUTO_INCREMENT=110 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `journal_tybes`
--

DROP TABLE IF EXISTS `journal_tybes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `journal_tybes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `journal_id` int(11) DEFAULT NULL,
  `jname` varchar(222) DEFAULT NULL,
  `jtext` varchar(222) DEFAULT NULL,
  `info` varchar(222) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `karta`
--

DROP TABLE IF EXISTS `karta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `karta` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kname` varchar(200) NOT NULL,
  `info` varchar(200) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NULL DEFAULT current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `kbis`
--

DROP TABLE IF EXISTS `kbis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kbis` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kname` varchar(100) NOT NULL,
  `info` varchar(100) DEFAULT NULL,
  `user` int(11) DEFAULT 1,
  `isdeleted` tinyint(1) DEFAULT 0,
  `crtime` datetime DEFAULT current_timestamp(),
  `mdtime` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `ktybe` varchar(100) DEFAULT NULL,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `my_news`
--

DROP TABLE IF EXISTS `my_news`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `my_news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `img` varchar(250) DEFAULT NULL,
  `tags` varchar(250) DEFAULT NULL,
  `content` text NOT NULL,
  `user` int(11) NOT NULL DEFAULT 1,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `myinstallments`
--

DROP TABLE IF EXISTS `myinstallments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `myinstallments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cl_id` int(11) NOT NULL,
  `rent_id` int(11) NOT NULL,
  `contract` int(11) DEFAULT 0,
  `ins_value` double NOT NULL DEFAULT 0,
  `ins_date` date NOT NULL,
  `ins_case` int(11) NOT NULL,
  `ins_paid` double NOT NULL,
  `voucher` int(11) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `info` varchar(250) DEFAULT NULL,
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `cl_id` (`cl_id`),
  KEY `rent_id` (`rent_id`),
  KEY `contract` (`contract`),
  CONSTRAINT `myinstallments_ibfk_1` FOREIGN KEY (`cl_id`) REFERENCES `acc_head` (`id`),
  CONSTRAINT `myinstallments_ibfk_2` FOREIGN KEY (`rent_id`) REFERENCES `acc_head` (`id`),
  CONSTRAINT `myinstallments_ibfk_3` FOREIGN KEY (`contract`) REFERENCES `myrents` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `myitems`
--

DROP TABLE IF EXISTS `myitems`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `myitems` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `iname` varchar(200) NOT NULL,
  `name2` varchar(200) DEFAULT NULL,
  `code` int(11) DEFAULT NULL,
  `salesqty` double DEFAULT 1,
  `barcode` varchar(25) DEFAULT NULL,
  `itmqty` double NOT NULL DEFAULT 0,
  `info` varchar(250) DEFAULT NULL,
  `market_price` float NOT NULL DEFAULT 0,
  `cost_price` float NOT NULL DEFAULT 0,
  `last_price` int(11) NOT NULL DEFAULT 0,
  `price1` float NOT NULL DEFAULT 0,
  `price2` float NOT NULL DEFAULT 0,
  `price3` float NOT NULL,
  `group1` int(11) NOT NULL DEFAULT 0,
  `group2` int(11) NOT NULL DEFAULT 0,
  `group3` int(11) NOT NULL DEFAULT 0,
  `isdeleted` tinyint(1) DEFAULT 0,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user` int(11) NOT NULL DEFAULT 1,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  `manual_price_edit` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `iname` (`iname`),
  KEY `idx_myitems_iname` (`iname`),
  KEY `idx_myitems_name2` (`name2`),
  KEY `idx_myitems_barcode` (`barcode`),
  KEY `idx_myitems_isdeleted` (`isdeleted`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `myoper_det`
--

DROP TABLE IF EXISTS `myoper_det`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `myoper_det` (
  `oper_det_id` int(11) NOT NULL,
  `int_oper_det_date` int(11) DEFAULT NULL,
  `oper_head_id` int(11) DEFAULT NULL,
  `comp_id` int(11) DEFAULT NULL,
  `debit` decimal(26,4) DEFAULT NULL,
  `credit` decimal(26,4) DEFAULT NULL,
  `eng_debit` decimal(26,4) DEFAULT NULL,
  `eng_credit` decimal(26,4) DEFAULT NULL,
  `model_val` decimal(26,4) DEFAULT NULL,
  `def_val` decimal(26,4) DEFAULT NULL,
  `acc_id` int(11) DEFAULT NULL,
  `stor_id` int(11) DEFAULT NULL,
  `group_id` int(11) DEFAULT NULL,
  `man_id` int(11) DEFAULT NULL,
  `cost_center_id` int(11) DEFAULT NULL,
  `has_costed_link` tinyint(4) DEFAULT NULL,
  `is_not_active` tinyint(4) DEFAULT NULL,
  `notes` varchar(50) DEFAULT NULL,
  `mst_no` varchar(20) DEFAULT NULL,
  `mst_date` varchar(12) DEFAULT NULL,
  `balance_befor` decimal(26,4) DEFAULT NULL,
  `balance_after` decimal(26,4) DEFAULT NULL,
  `det_Currency_id` int(11) DEFAULT NULL,
  `det_Currency_unit_convert` decimal(12,6) DEFAULT NULL,
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `myoptions`
--

DROP TABLE IF EXISTS `myoptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `myoptions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `oname` varchar(30) NOT NULL,
  `info` varchar(250) DEFAULT NULL,
  `def_value` int(11) NOT NULL DEFAULT 0,
  `cur_value` int(11) NOT NULL DEFAULT 0,
  `op_tybe` int(11) NOT NULL DEFAULT 0,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mypatterns`
--

DROP TABLE IF EXISTS `mypatterns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mypatterns` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pname` varchar(100) NOT NULL,
  `ptext` varchar(100) NOT NULL,
  `is_def` int(11) NOT NULL DEFAULT 0,
  `is_basic` int(11) NOT NULL DEFAULT 0,
  `ptybe` int(11) NOT NULL DEFAULT 4,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `info` varchar(100) DEFAULT NULL,
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mypowers`
--

DROP TABLE IF EXISTS `mypowers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mypowers` (
  `power_id` int(11) NOT NULL,
  `section_type_no` int(11) DEFAULT NULL,
  `power_name` varchar(100) DEFAULT NULL,
  `eng_power_name` varchar(100) DEFAULT NULL,
  `is_hide_in_casher` int(11) DEFAULT NULL,
  `level_no` tinyint(4) DEFAULT NULL,
  `is_for_view_only` tinyint(4) DEFAULT NULL,
  `power_code` varchar(100) DEFAULT NULL,
  `power_class` tinyint(4) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `col_index` int(11) DEFAULT NULL,
  `stoped` tinyint(4) DEFAULT NULL,
  `tmp_state_no` tinyint(4) DEFAULT NULL,
  `power_type` tinyint(4) DEFAULT NULL,
  `menu_type` tinyint(4) DEFAULT NULL,
  `def_state` varchar(20) DEFAULT NULL,
  `user_1` varchar(20) DEFAULT NULL,
  `kind` varchar(10) DEFAULT NULL,
  `is_on_my_thread` tinyint(4) DEFAULT NULL,
  `is_calling_from_main` tinyint(4) DEFAULT NULL,
  `calling_from` varchar(10) DEFAULT NULL,
  `edit_mode` tinyint(4) DEFAULT NULL,
  `frist_shown_id` varchar(10) DEFAULT NULL,
  `is_casher_from` tinyint(4) DEFAULT NULL,
  `is_op_paper` tinyint(4) DEFAULT NULL,
  `is_hiddin` tinyint(4) DEFAULT NULL,
  `prog_id` tinyint(4) DEFAULT NULL,
  `is_pure_kitchen` tinyint(4) DEFAULT NULL,
  `is_for_api` tinyint(4) DEFAULT NULL,
  `t_stamp` timestamp NULL DEFAULT NULL,
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `myrents`
--

DROP TABLE IF EXISTS `myrents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `myrents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cl_id` int(11) NOT NULL,
  `rent_id` int(11) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `idintity` varchar(50) DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `pay_tybe` int(11) NOT NULL DEFAULT 1,
  `r_value` double NOT NULL DEFAULT 0,
  `bnd1` varchar(250) DEFAULT NULL,
  `bnd2` varchar(250) DEFAULT NULL,
  `bnd3` varchar(250) DEFAULT NULL,
  `bnd4` varchar(250) DEFAULT NULL,
  `info` varchar(250) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `cl_id` (`cl_id`),
  KEY `rent_id` (`rent_id`),
  CONSTRAINT `myrents_ibfk_1` FOREIGN KEY (`cl_id`) REFERENCES `acc_head` (`id`),
  CONSTRAINT `myrents_ibfk_2` FOREIGN KEY (`rent_id`) REFERENCES `acc_head` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `myunits`
--

DROP TABLE IF EXISTS `myunits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `myunits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uname` varchar(60) NOT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `myvouchers`
--

DROP TABLE IF EXISTS `myvouchers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `myvouchers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `vdate` date NOT NULL DEFAULT current_timestamp(),
  `tybe` int(1) NOT NULL,
  `val` double DEFAULT NULL,
  `account` int(1) NOT NULL,
  `fund_account` int(1) NOT NULL,
  `voucher_id` varchar(15) NOT NULL,
  `serial_number` varchar(20) DEFAULT NULL,
  `cost_center` int(1) DEFAULT NULL,
  `info` varchar(200) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user` int(11) NOT NULL DEFAULT 1,
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `notes`
--

DROP TABLE IF EXISTS `notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `n1` varchar(100) DEFAULT '0',
  `n2` varchar(100) DEFAULT '0',
  `n3` varchar(100) DEFAULT '0',
  `n4` varchar(100) DEFAULT '0',
  `n5` varchar(100) DEFAULT '0',
  `n6` varchar(100) DEFAULT '0',
  `n7` varchar(100) DEFAULT '0',
  `n8` varchar(100) DEFAULT '0',
  `n9` varchar(100) DEFAULT '0',
  `n10` varchar(100) DEFAULT '0',
  `n11` varchar(100) DEFAULT '0',
  `n12` varchar(100) DEFAULT '0',
  `n13` varchar(100) DEFAULT '0',
  `n14` varchar(100) DEFAULT '0',
  `n15` varchar(100) DEFAULT '0',
  `n16` varchar(100) DEFAULT '0',
  `n17` varchar(100) DEFAULT '0',
  `n18` varchar(100) DEFAULT '0',
  `n19` varchar(100) DEFAULT '0',
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `oppatterns`
--

DROP TABLE IF EXISTS `oppatterns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `oppatterns` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pame` varchar(100) DEFAULT NULL,
  `ptext` varchar(100) DEFAULT NULL,
  `def_width` int(11) NOT NULL DEFAULT 50,
  `cur_width` int(11) NOT NULL DEFAULT 50,
  `shown` int(11) NOT NULL DEFAULT 1,
  `is_edit` int(11) NOT NULL DEFAULT 1,
  `is_print` int(11) NOT NULL DEFAULT 1,
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `order_status`
--

DROP TABLE IF EXISTS `order_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `user` int(1) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `order_types`
--

DROP TABLE IF EXISTS `order_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `info` varchar(100) DEFAULT NULL,
  `user` int(1) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tybe` int(11) DEFAULT NULL,
  `employee` int(11) DEFAULT NULL,
  `status` int(11) NOT NULL,
  `applyingdate` date NOT NULL,
  `curdate` date NOT NULL,
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `employee_id` (`employee`),
  KEY `tybe` (`tybe`),
  KEY `status` (`status`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`employee`) REFERENCES `employees` (`id`),
  CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`tybe`) REFERENCES `order_types` (`id`),
  CONSTRAINT `orders_ibfk_3` FOREIGN KEY (`status`) REFERENCES `order_status` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ot_head`
--

DROP TABLE IF EXISTS `ot_head`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ot_head` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pro_id` int(11) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `table_id` int(11) DEFAULT NULL COMMENT '╪▒┘é┘à ╪º┘ä╪╖╪º┘ê┘ä╪⌐',
  `order_type` enum('dine_in','takeaway','delivery','table') DEFAULT 'takeaway',
  `pro_tybe` int(1) DEFAULT NULL,
  `is_stock` int(11) DEFAULT NULL,
  `is_finance` int(11) DEFAULT NULL,
  `is_manager` int(11) DEFAULT NULL,
  `is_journal` int(11) DEFAULT NULL,
  `journal_tybe` int(11) DEFAULT NULL,
  `info` varchar(200) DEFAULT NULL,
  `start_time` time DEFAULT current_timestamp(),
  `end_time` time DEFAULT current_timestamp(),
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `pro_date` date DEFAULT NULL,
  `accural_date` date DEFAULT NULL,
  `pro_pattren` int(11) DEFAULT NULL,
  `pro_num` varchar(50) DEFAULT NULL,
  `receipt_number` varchar(50) DEFAULT NULL,
  `pro_serial` varchar(50) DEFAULT NULL,
  `tax_num` varchar(50) DEFAULT NULL,
  `price_list` int(11) DEFAULT NULL,
  `store_id` int(11) DEFAULT NULL,
  `emp_id` int(11) DEFAULT NULL,
  `emp2_id` int(11) DEFAULT NULL,
  `acc1` int(11) DEFAULT NULL,
  `acc1_before` double DEFAULT NULL,
  `acc1_after` double DEFAULT NULL,
  `acc2` int(11) DEFAULT NULL,
  `acc2_before` double DEFAULT NULL,
  `acc2_after` double DEFAULT NULL,
  `pro_value` double DEFAULT NULL,
  `fat_cost` double DEFAULT NULL,
  `cost_center` int(11) DEFAULT NULL,
  `profit` double DEFAULT NULL,
  `fat_total` double DEFAULT NULL,
  `fat_net` double DEFAULT 0,
  `fat_disc` double DEFAULT NULL,
  `fat_disc_per` double DEFAULT NULL,
  `fat_plus` double DEFAULT NULL,
  `fat_plus_per` double DEFAULT NULL,
  `fat_tax` double DEFAULT NULL,
  `fat_tax_per` double DEFAULT NULL,
  `paid_amount` decimal(15,2) DEFAULT 0.00,
  `remaining_amount` decimal(15,2) DEFAULT 0.00,
  `payment_status` enum('unpaid','partial','paid','refunded') DEFAULT 'unpaid',
  `invoice_status` enum('draft','completed','cancelled') DEFAULT 'completed',
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `acc_fund` int(1) DEFAULT 0,
  `op2` int(11) DEFAULT 0,
  `isdeleted` tinyint(1) DEFAULT 0,
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user` int(11) NOT NULL DEFAULT 1,
  `waiter_id` int(11) DEFAULT NULL COMMENT '┘à╪╣╪▒┘ü ╪º┘ä┘ê┘è╪¬╪▒',
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  `closed` int(1) DEFAULT 0,
  `order_status` enum('draft','active','completed','cancelled') DEFAULT 'active' COMMENT '╪¡╪º┘ä╪⌐ ╪º┘ä╪╖┘ä╪¿',
  `payment_method` varchar(20) DEFAULT 'cash' COMMENT '╪╖╪▒┘è┘é╪⌐ ╪º┘ä╪»┘ü╪╣',
  `payment_notes` text DEFAULT NULL COMMENT '┘à┘ä╪º╪¡╪╕╪º╪¬ ╪º┘ä╪»┘ü╪╣',
  `payment_date` datetime DEFAULT NULL COMMENT '╪¬╪º╪▒┘è╪« ╪º┘ä╪»┘ü╪╣',
  `jal_name` varchar(255) DEFAULT NULL,
  `jal_notes` text DEFAULT NULL,
  `jal_amount` decimal(10,2) DEFAULT 0.00,
  PRIMARY KEY (`id`),
  UNIQUE KEY `receipt_number` (`receipt_number`),
  KEY `acc1` (`acc1`),
  KEY `acc2` (`acc2`),
  KEY `emp2_id` (`emp2_id`),
  KEY `emp_id` (`emp_id`),
  KEY `journal_tybe` (`journal_tybe`),
  KEY `user` (`user`),
  KEY `cost_center` (`cost_center`),
  KEY `store_id` (`store_id`),
  KEY `price_list` (`price_list`),
  KEY `idx_table` (`table_id`),
  KEY `idx_order_type` (`order_type`),
  KEY `idx_payment_status` (`payment_status`),
  KEY `idx_isdeleted` (`isdeleted`),
  KEY `waiter_id` (`waiter_id`)
) ENGINE=InnoDB AUTO_INCREMENT=115 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `paper_types`
--

DROP TABLE IF EXISTS `paper_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `paper_types` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `pname` varchar(50) NOT NULL,
  `info` varchar(100) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `patt_cols`
--

DROP TABLE IF EXISTS `patt_cols`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `patt_cols` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cname` varchar(100) NOT NULL,
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `payroll_calcs`
--

DROP TABLE IF EXISTS `payroll_calcs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payroll_calcs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `snd_id` int(11) NOT NULL,
  `calc_tybe` tinyint(4) NOT NULL DEFAULT 1,
  `date` date NOT NULL,
  `emp_id` int(11) DEFAULT NULL,
  `emp_name` varchar(100) DEFAULT NULL,
  `amount` double NOT NULL DEFAULT 0,
  `percent` double NOT NULL DEFAULT 0,
  `info` varchar(250) DEFAULT NULL,
  `info2` varchar(250) DEFAULT NULL,
  `user` varchar(50) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `snd_id` (`snd_id`),
  KEY `emp_date` (`emp_id`,`date`)
) ENGINE=InnoDB AUTO_INCREMENT=179 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `permits`
--

DROP TABLE IF EXISTS `permits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `empid` int(1) NOT NULL,
  `info` varchar(100) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `curdate` date NOT NULL,
  `startdate` date NOT NULL,
  `enddate` date NOT NULL,
  `val` double DEFAULT NULL,
  `statue` int(1) NOT NULL,
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `prescdetails`
--

DROP TABLE IF EXISTS `prescdetails`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `prescdetails` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `prescid` int(11) DEFAULT NULL,
  `drug` int(11) DEFAULT NULL,
  `dose` varchar(200) NOT NULL,
  `info` varchar(200) NOT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `prescs`
--

DROP TABLE IF EXISTS `prescs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `prescs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client` int(11) DEFAULT NULL,
  `visit` int(11) DEFAULT NULL,
  `analayses` varchar(250) DEFAULT NULL,
  `info` varchar(200) NOT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `price_lists`
--

DROP TABLE IF EXISTS `price_lists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `price_lists` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pname` varchar(100) NOT NULL,
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `print`
--

DROP TABLE IF EXISTS `print`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `print` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `pname` varchar(50) NOT NULL,
  `type` varchar(11) NOT NULL,
  `number` varchar(11) NOT NULL,
  `info` varchar(100) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pro_tybes`
--

DROP TABLE IF EXISTS `pro_tybes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pro_tybes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pname` varchar(200) DEFAULT NULL,
  `ptext` varchar(200) DEFAULT NULL,
  `ptybe` int(11) DEFAULT NULL,
  `info` varchar(200) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `process`
--

DROP TABLE IF EXISTS `process`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `process` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(255) NOT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=586 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `prods`
--

DROP TABLE IF EXISTS `prods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `prods` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `pname` varchar(50) NOT NULL,
  `info` varchar(100) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `productions`
--

DROP TABLE IF EXISTS `productions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `productions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `snd_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `emp_name` varchar(100) NOT NULL,
  `qty` double NOT NULL,
  `price` double NOT NULL,
  `value` double NOT NULL,
  `info` varchar(150) DEFAULT NULL,
  `info2` varchar(150) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user` int(11) NOT NULL DEFAULT 1,
  `isdeleted` int(11) NOT NULL DEFAULT 0,
  `tenant` int(11) NOT NULL DEFAULT 0,
  `branch` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pst_activities`
--

DROP TABLE IF EXISTS `pst_activities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pst_activities` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `aname` varchar(111) NOT NULL,
  `info` varchar(111) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user` int(1) DEFAULT 0,
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pst_criminals`
--

DROP TABLE IF EXISTS `pst_criminals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pst_criminals` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `cname` varchar(150) NOT NULL,
  `otherdetails` varchar(200) DEFAULT NULL,
  `karta` int(1) DEFAULT NULL,
  `dngrs` int(1) DEFAULT NULL,
  `fesh` int(1) DEFAULT NULL,
  `prtnrs` varchar(150) DEFAULT NULL,
  `crmaddress` varchar(150) DEFAULT NULL,
  `idcardnum` varchar(150) DEFAULT NULL,
  `scar` varchar(150) DEFAULT NULL,
  `mname` varchar(150) DEFAULT NULL,
  `nickname` varchar(150) DEFAULT NULL,
  `tybe` varchar(150) DEFAULT NULL,
  `danger_factor` int(1) NOT NULL DEFAULT 1,
  `info` varchar(150) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user` int(1) NOT NULL,
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pst_crmstyles`
--

DROP TABLE IF EXISTS `pst_crmstyles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pst_crmstyles` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `cname` varchar(150) NOT NULL,
  `info` varchar(150) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user` int(1) NOT NULL,
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pst_gangs`
--

DROP TABLE IF EXISTS `pst_gangs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pst_gangs` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `gname` varchar(150) NOT NULL,
  `info` varchar(150) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user` int(1) NOT NULL,
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pst_issues`
--

DROP TABLE IF EXISTS `pst_issues`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pst_issues` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `iname` varchar(150) NOT NULL,
  `issue_tybe` int(1) NOT NULL DEFAULT 1,
  `info` varchar(150) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user` int(1) NOT NULL,
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pulse_logs`
--

DROP TABLE IF EXISTS `pulse_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pulse_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL,
  `category` enum('positive','negative') NOT NULL,
  `rating` int(11) DEFAULT 5,
  `notes` text DEFAULT NULL,
  `recorded_by` int(11) NOT NULL,
  `recorded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_employee` (`employee_id`),
  KEY `idx_type` (`type_id`),
  KEY `idx_recorded_at` (`recorded_at`),
  KEY `idx_category` (`category`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pulse_types`
--

DROP TABLE IF EXISTS `pulse_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pulse_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `category` enum('positive','negative') NOT NULL DEFAULT 'positive',
  `icon` varchar(50) DEFAULT 'fas fa-star',
  `points` int(11) DEFAULT 1,
  `isdeleted` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rays`
--

DROP TABLE IF EXISTS `rays`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rays` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client` int(11) NOT NULL,
  `lap` int(11) NOT NULL,
  `name` varchar(250) NOT NULL,
  `comment` varchar(250) DEFAULT NULL,
  `img` varchar(250) DEFAULT NULL,
  `crtime` timestamp NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `info` varchar(250) DEFAULT NULL,
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `reservations`
--

DROP TABLE IF EXISTS `reservations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reservations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client` int(11) NOT NULL,
  `diseses` varchar(250) DEFAULT '0',
  `phone` varchar(15) DEFAULT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `duration` double DEFAULT NULL,
  `visittybe` int(11) NOT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `paid` int(11) DEFAULT 0,
  `deserved` int(11) DEFAULT 0,
  `rest` int(11) DEFAULT 0,
  `done` int(11) DEFAULT 0,
  `info` varchar(250) DEFAULT NULL,
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `salaries`
--

DROP TABLE IF EXISTS `salaries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `salaries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `empid` int(1) NOT NULL,
  `info` varchar(100) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `starttime` time NOT NULL,
  `endtime` time NOT NULL,
  `salary` double DEFAULT 0,
  `extra` double DEFAULT 0,
  `disc` double DEFAULT 0,
  `allow` double DEFAULT 0,
  `dedu` double DEFAULT 0,
  `total` double NOT NULL DEFAULT 0,
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `services`
--

DROP TABLE IF EXISTS `services`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sname` varchar(50) NOT NULL,
  `info` varchar(100) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `session_time`
--

DROP TABLE IF EXISTS `session_time`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `session_time` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `user` int(1) NOT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=82 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `settings` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `company_name` varchar(200) DEFAULT NULL,
  `company_add` varchar(200) DEFAULT NULL,
  `company_email` varchar(50) DEFAULT NULL,
  `company_tel` varchar(200) DEFAULT NULL,
  `edit_pass` varchar(50) DEFAULT NULL,
  `lic` varchar(250) DEFAULT NULL,
  `updateline` text DEFAULT NULL,
  `acc_rent` int(11) DEFAULT 0,
  `startdate` date DEFAULT NULL,
  `enddate` date DEFAULT NULL,
  `lang` varchar(20) DEFAULT 'ar',
  `bodycolor` varchar(50) DEFAULT NULL,
  `showhr` int(1) NOT NULL DEFAULT 1,
  `showclinc` int(1) NOT NULL DEFAULT 1,
  `showatt` int(11) NOT NULL DEFAULT 1,
  `showpayroll` int(11) NOT NULL DEFAULT 1,
  `showrent` int(11) NOT NULL DEFAULT 1,
  `showpay` int(11) DEFAULT 1,
  `showtsk` int(11) NOT NULL DEFAULT 1,
  `def_pos_client` int(1) DEFAULT NULL,
  `def_pos_store` int(1) DEFAULT NULL,
  `def_pos_employee` int(1) DEFAULT NULL,
  `def_pos_fund` int(1) DEFAULT NULL,
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  `logo` varchar(255) DEFAULT NULL,
  `show_all_tasks` int(1) DEFAULT NULL,
  `pos_type` varchar(20) DEFAULT 'barcode' COMMENT '┘å┘ê╪╣ ┘å╪╕╪º┘à POS: barcode ╪ú┘ê clothes',
  `pos_has_password` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1 = POS ┘à╪¡┘à┘è ╪¿╪¿╪º╪▒┘â┘ê╪»╪î 0 = POS ┘à┘ü╪¬┘ê╪¡',
  `showpulse` int(11) DEFAULT 1,
  `emp_commission` double NOT NULL DEFAULT 0,
  `user_commission` double NOT NULL DEFAULT 0,
  `receipt_show_logo` tinyint(1) NOT NULL DEFAULT 1,
  `receipt_font_size` int(11) NOT NULL DEFAULT 14,
  `receipt_paper_width` varchar(20) NOT NULL DEFAULT '78mm',
  `receipt_footer_text` varchar(255) NOT NULL DEFAULT 'Γ¥ñ perfect place to grow',
  `receipt_show_client` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shifts`
--

DROP TABLE IF EXISTS `shifts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shifts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `info` text DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `shiftstart` time DEFAULT NULL,
  `shiftend` time DEFAULT NULL,
  `hours` double(11,2) DEFAULT NULL,
  `instart` time DEFAULT NULL,
  `inend` time DEFAULT NULL,
  `outstart` time DEFAULT NULL,
  `outend` time DEFAULT NULL,
  `latelimit` int(1) DEFAULT NULL,
  `earlylimit` int(11) DEFAULT NULL,
  `ignore_early_in` tinyint(1) DEFAULT 0,
  `ignore_late_out` tinyint(1) DEFAULT 0,
  `workingdays` varchar(20) DEFAULT NULL,
  `single_fp_rule` varchar(20) NOT NULL DEFAULT 'half',
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shw_optns`
--

DROP TABLE IF EXISTS `shw_optns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shw_optns` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sname` varchar(100) NOT NULL,
  `is_show` int(11) NOT NULL DEFAULT 0,
  `def_width` int(11) NOT NULL DEFAULT 50,
  `cur_width` int(11) NOT NULL DEFAULT 50,
  `info` varchar(150) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sitting_items`
--

DROP TABLE IF EXISTS `sitting_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sitting_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `iname` varchar(250) NOT NULL,
  `item_value` int(1) NOT NULL DEFAULT 0,
  `item_description` varchar(250) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `skills`
--

DROP TABLE IF EXISTS `skills`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `skills` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sname` varchar(200) NOT NULL,
  `icon` varchar(100) DEFAULT NULL,
  `info` varchar(200) DEFAULT NULL,
  `scolor` varchar(100) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user` int(11) NOT NULL DEFAULT 1,
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `system_logs`
--

DROP TABLE IF EXISTS `system_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `timestamp` datetime NOT NULL,
  `level` varchar(20) NOT NULL,
  `message` text NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `username` varchar(100) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `request_uri` varchar(500) DEFAULT NULL,
  `context` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tables`
--

DROP TABLE IF EXISTS `tables`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tables` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tname` varchar(255) NOT NULL,
  `table_case` int(11) NOT NULL DEFAULT 0,
  `crtime` datetime DEFAULT current_timestamp(),
  `mdtime` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `isdeleted` tinyint(1) NOT NULL DEFAULT 0,
  `branch` varchar(255) DEFAULT NULL,
  `tatnet` varchar(255) DEFAULT NULL,
  `parent_table_id` int(11) DEFAULT NULL,
  `is_merged` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tasks`
--

DROP TABLE IF EXISTS `tasks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tasks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `ch_tybe` int(11) NOT NULL,
  `info` text DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `user` int(1) NOT NULL,
  `tasktybe` int(1) NOT NULL,
  `important` int(1) NOT NULL,
  `urgent` int(1) NOT NULL,
  `emp_comment` varchar(200) DEFAULT NULL,
  `cl_comment` varchar(200) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tasktybes`
--

DROP TABLE IF EXISTS `tasktybes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tasktybes` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `name` varchar(25) NOT NULL,
  `info` text DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tenants`
--

DROP TABLE IF EXISTS `tenants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tenants` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `domain` varchar(255) DEFAULT NULL,
  `code` varchar(255) DEFAULT NULL,
  `database` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`settings`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tenants_domain_unique` (`domain`),
  UNIQUE KEY `tenants_code_unique` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `test`
--

DROP TABLE IF EXISTS `test`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `test` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `name` varchar(50) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `test` varchar(1) DEFAULT NULL,
  `time` time DEFAULT NULL,
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `towns`
--

DROP TABLE IF EXISTS `towns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `towns` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `info` text DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `transactions`
--

DROP TABLE IF EXISTS `transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tdate` date NOT NULL,
  `details` varchar(200) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `uname` varchar(20) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `usertype` int(11) NOT NULL,
  `userrole` int(11) NOT NULL DEFAULT 1,
  `is_waiter` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1 = ┘ê┘è╪¬╪▒╪î 0 = ┘à╪│╪¬╪«╪»┘à ╪╣╪º╪»┘è',
  `img` varchar(255) NOT NULL,
  `def_client` int(11) DEFAULT NULL,
  `def_fund` int(11) DEFAULT NULL,
  `def_store` int(11) DEFAULT NULL,
  `def_prod` int(11) DEFAULT NULL,
  `def_emp` int(11) DEFAULT NULL,
  `tasksindex` int(11) DEFAULT NULL,
  `tasksadd` int(11) DEFAULT NULL,
  `tasksedit` int(11) DEFAULT NULL,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `is_waiter` (`is_waiter`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `usr_pwrs`
--

DROP TABLE IF EXISTS `usr_pwrs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usr_pwrs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rollname` varchar(50) DEFAULT NULL,
  `is_active` int(11) DEFAULT 1,
  `is_fav_users` int(11) DEFAULT 0,
  `show_users` int(11) DEFAULT 1,
  `add_users` int(11) DEFAULT 1,
  `edit_users` int(11) DEFAULT 1,
  `delete_users` int(11) DEFAULT 1,
  `is_fav_general_entrys` int(11) DEFAULT 0,
  `show_general_entrys` int(11) DEFAULT 1,
  `add_general_entrys` int(11) DEFAULT 1,
  `edit_general_entrys` int(11) DEFAULT 1,
  `delete_general_entrys` int(11) DEFAULT 1,
  `is_fav_clients` int(11) DEFAULT 0,
  `show_clients` int(11) DEFAULT 1,
  `add_clients` int(11) DEFAULT 1,
  `edit_clients` int(11) DEFAULT 1,
  `is_fav_suppliers` int(11) DEFAULT 0,
  `delete_clients` int(11) DEFAULT 1,
  `show_suppliers` int(11) DEFAULT 1,
  `add_suppliers` int(11) DEFAULT 1,
  `edit_suppliers` int(11) DEFAULT 1,
  `delete_suppliers` int(11) DEFAULT 1,
  `is_fav_funds` int(11) DEFAULT 0,
  `show_funds` int(11) DEFAULT 1,
  `add_funds` int(11) DEFAULT 1,
  `edit_funds` int(11) DEFAULT 1,
  `delete_funds` int(11) DEFAULT 1,
  `is_fav_banks` int(11) DEFAULT 0,
  `show_banks` int(11) DEFAULT 1,
  `add_banks` int(11) DEFAULT 1,
  `edit_banks` int(11) DEFAULT 1,
  `delete_banks` int(11) DEFAULT 1,
  `is_fav_stock` int(11) DEFAULT 0,
  `show_stock` int(11) DEFAULT 1,
  `add_stock` int(11) DEFAULT 1,
  `edit_stock` int(11) DEFAULT 1,
  `delete_stock` int(11) DEFAULT 1,
  `is_fav_expenses` int(11) DEFAULT 0,
  `show_expenses` int(11) DEFAULT 1,
  `add_expenses` int(11) DEFAULT 1,
  `edit_expenses` int(11) DEFAULT 1,
  `delete_expenses` int(11) DEFAULT 1,
  `is_fav_revenuses` int(11) DEFAULT 0,
  `show_revenuses` int(11) DEFAULT 1,
  `add_revenuses` int(11) DEFAULT 1,
  `edit_revenuses` int(11) DEFAULT 1,
  `delete_revenuses` int(11) DEFAULT 1,
  `is_fav_credits` int(11) DEFAULT 0,
  `show_credits` int(11) DEFAULT 1,
  `add_credits` int(11) DEFAULT 1,
  `edit_credits` int(11) DEFAULT 1,
  `delete_credits` int(11) DEFAULT 1,
  `is_fav_depits` int(11) DEFAULT 0,
  `show_depits` int(11) DEFAULT 1,
  `add_depits` int(11) DEFAULT 1,
  `edit_depits` int(11) DEFAULT 1,
  `delete_depits` int(11) DEFAULT 1,
  `is_fav_delivery` int(11) DEFAULT 0,
  `show_delivery` int(11) DEFAULT 1,
  `add_delivery` int(11) DEFAULT 1,
  `edit_delivery` int(11) DEFAULT 1,
  `delete_delivery` int(11) DEFAULT 1,
  `is_fav_partners` int(11) DEFAULT 0,
  `show_partners` int(11) DEFAULT 1,
  `add_partners` int(11) DEFAULT 1,
  `edit_partners` int(11) DEFAULT 1,
  `delete_partners` int(11) DEFAULT 1,
  `is_fav_assets` int(11) DEFAULT 0,
  `show_assets` int(11) DEFAULT 1,
  `add_assets` int(11) DEFAULT 1,
  `edit_assets` int(11) DEFAULT 1,
  `delete_assets` int(11) DEFAULT 1,
  `is_fav_rentables` int(11) DEFAULT 0,
  `show_rentables` int(11) DEFAULT 1,
  `add_rentables` int(11) DEFAULT 1,
  `edit_rentables` int(11) DEFAULT 1,
  `delete_rentables` int(11) DEFAULT 1,
  `is_fav_employees` int(11) DEFAULT 0,
  `show_employees` int(11) DEFAULT 1,
  `add_employees` int(11) DEFAULT 1,
  `edit_employees` int(11) DEFAULT 1,
  `delete_employees` int(11) DEFAULT 1,
  `is_fav_items` int(11) DEFAULT 0,
  `show_items` int(11) DEFAULT 1,
  `add_items` int(11) DEFAULT 1,
  `edit_items` int(11) DEFAULT 1,
  `delete_items` int(11) DEFAULT 1,
  `is_fav_item_groups` int(11) DEFAULT 0,
  `show_item_groups` int(11) DEFAULT 1,
  `add_item_groups` int(11) DEFAULT 1,
  `edit_item_groups` int(11) DEFAULT 1,
  `delete_item_groups` int(11) DEFAULT 1,
  `is_fav_sales` int(11) DEFAULT 0,
  `show_sales` int(11) DEFAULT 1,
  `add_sales` int(11) DEFAULT 1,
  `edit_sales` int(11) DEFAULT 1,
  `delete_sales` int(11) DEFAULT 1,
  `is_fav_resale` int(11) DEFAULT 0,
  `show_resale` int(11) DEFAULT 1,
  `add_resale` int(11) DEFAULT 1,
  `edit_resale` int(11) DEFAULT 1,
  `delete_resale` int(11) DEFAULT 1,
  `is_fav_purcases` int(11) DEFAULT 0,
  `show_purchases` int(11) DEFAULT 1,
  `add_purchases` int(11) DEFAULT 1,
  `edit_purchases` int(11) DEFAULT 1,
  `delete_purchases` int(11) DEFAULT 1,
  `is_fav_repurchases` int(11) DEFAULT 0,
  `show_repurchases` int(11) DEFAULT 1,
  `add_repurchases` int(11) DEFAULT 1,
  `edit_repurchases` int(11) DEFAULT 1,
  `delete_repurchases` int(11) DEFAULT 1,
  `is_fav_recive` int(11) DEFAULT 0,
  `show_recive` int(11) DEFAULT 1,
  `add_recive` int(11) DEFAULT 1,
  `edit_recive` int(11) DEFAULT 1,
  `delete_recive` int(11) DEFAULT 1,
  `show_payment` int(11) DEFAULT 1,
  `is_fav_payment` int(11) DEFAULT 0,
  `add_payment` int(11) DEFAULT 1,
  `edit_payment` int(11) DEFAULT 1,
  `delete_payment` int(11) DEFAULT 1,
  `is_fav_clinic_clients` int(11) DEFAULT 0,
  `show_clinic_clients` int(11) DEFAULT 1,
  `add_clinic_clients` int(11) DEFAULT 1,
  `edit_clinic_clients` int(11) DEFAULT 1,
  `delete_clinic_clients` int(11) DEFAULT 1,
  `is_fav_reservations` int(11) DEFAULT 0,
  `show_reservations` int(11) DEFAULT 1,
  `add_reservations` int(11) DEFAULT 1,
  `edit_reservations` int(11) DEFAULT 1,
  `delete_reservations` int(11) DEFAULT 1,
  `is_fav_drugs` int(11) DEFAULT 0,
  `show_drugs` int(11) DEFAULT 1,
  `add_drugs` int(11) DEFAULT 1,
  `edit_drugs` int(11) DEFAULT 1,
  `is_fav_attandance` int(11) DEFAULT 1,
  `delete_attandance` int(11) DEFAULT 1,
  `edit_attandance` int(11) DEFAULT 1,
  `show_attandance` int(11) DEFAULT 1,
  `add_attandance` int(11) DEFAULT 1,
  `delete_drugs` int(11) DEFAULT 1,
  `is_fav_client_profile` int(11) DEFAULT 0,
  `show_client_profile` int(11) DEFAULT 1,
  `add_client_profile` int(11) DEFAULT 1,
  `edit_client_profile` int(11) DEFAULT 1,
  `delete_client_profile` int(11) DEFAULT 1,
  `is_fav_advanced_clients` int(11) DEFAULT 0,
  `show_advanced_clients` int(11) DEFAULT 1,
  `add_advanced_clients` int(11) DEFAULT 1,
  `edit_advanced_clients` int(11) DEFAULT 1,
  `delete_advanced_clients` int(11) DEFAULT 1,
  `is_fav_chances` int(11) DEFAULT 0,
  `show_chances` int(11) DEFAULT 1,
  `add_chances` int(11) DEFAULT 1,
  `edit_chances` int(11) DEFAULT 1,
  `delete_chances` int(11) DEFAULT 1,
  `is_fav_calls` int(11) DEFAULT 0,
  `show_calls` int(11) DEFAULT 1,
  `add_calls` int(11) DEFAULT 1,
  `edit_calls` int(11) DEFAULT 1,
  `delete_calls` int(11) DEFAULT 1,
  `is_fav_journals` int(11) DEFAULT 0,
  `show_journals` int(11) DEFAULT 1,
  `add_journals` int(11) DEFAULT 1,
  `edit_journals` int(11) DEFAULT 1,
  `delete_journals` int(11) DEFAULT 1,
  `show_gl_reports` int(11) DEFAULT 1,
  `show_clinic_reports` int(11) DEFAULT 1,
  `show_rent_reports` int(11) DEFAULT 1,
  `show_payroll_report` int(11) DEFAULT 1,
  `show_hr_report` int(11) DEFAULT 1,
  `sid_entry` int(11) DEFAULT 1,
  `sid_stock` int(11) DEFAULT 1,
  `sid_sales` int(11) DEFAULT 1,
  `sid_purchases` int(11) DEFAULT 1,
  `sid_vouchers` int(11) DEFAULT 1,
  `sid_clinics` int(11) DEFAULT 1,
  `sid_crm` int(11) DEFAULT 1,
  `sid_accounts` int(11) DEFAULT 1,
  `sid_assets` int(11) DEFAULT 1,
  `sid_reports` int(11) DEFAULT 1,
  `sid_hr` int(11) DEFAULT 1,
  `sid_payroll` int(11) DEFAULT 1,
  `sid_rents` int(11) NOT NULL DEFAULT 1,
  `sid_cards` int(11) NOT NULL DEFAULT 1,
  `edit_user_passwords` int(11) NOT NULL DEFAULT 0,
  `show_total_reservation` int(11) DEFAULT 1,
  `show_ended_reservation` int(11) DEFAULT 1,
  `info` varchar(250) DEFAULT NULL,
  `isdeleted` tinyint(1) DEFAULT 0,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  `show_all_tasks` int(1) DEFAULT NULL,
  `show_main_cards` tinyint(1) NOT NULL DEFAULT 1,
  `show_main_elements` tinyint(1) NOT NULL DEFAULT 1,
  `show_main_tables` tinyint(1) NOT NULL DEFAULT 1,
  `sid_pulse` int(11) DEFAULT 1,
  `sid_visits` int(11) DEFAULT 1,
  `show_main_hr` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `rollname` (`rollname`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vacancies`
--

DROP TABLE IF EXISTS `vacancies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vacancies` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `info` text DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `visits`
--

DROP TABLE IF EXISTS `visits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `visits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gender` enum('male','female') NOT NULL,
  `age_group` enum('under18','18_25','25_40','over40') NOT NULL,
  `mode` enum('solo','group') NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `order_value` enum('under60','over60') NOT NULL,
  `type` enum('new','returning','regular') NOT NULL,
  `created_by` int(10) unsigned NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `client` int(11) NOT NULL,
  `complaint` varchar(250) DEFAULT NULL,
  `diagnosis` varchar(250) DEFAULT NULL,
  `recommendation` varchar(250) DEFAULT NULL,
  `prescription` varchar(250) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `info` varchar(250) NOT NULL,
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `visittybes`
--

DROP TABLE IF EXISTS `visittybes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `visittybes` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `name` varchar(25) NOT NULL,
  `value` double DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `zankat`
--

DROP TABLE IF EXISTS `zankat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `zankat` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `zname` varchar(100) NOT NULL,
  `colors` int(1) NOT NULL DEFAULT 1,
  `ctp` int(1) DEFAULT NULL,
  `zncase` int(1) DEFAULT NULL,
  `print` int(1) DEFAULT NULL,
  `ptype` int(1) DEFAULT NULL,
  `service` int(1) DEFAULT NULL,
  `prod` int(1) DEFAULT NULL,
  `measure` int(1) NOT NULL,
  `draw` int(1) NOT NULL,
  `farkh` int(1) NOT NULL,
  `info` varchar(255) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `date` varchar(20) NOT NULL,
  `user` varchar(20) NOT NULL,
  `fatid` int(11) DEFAULT 0,
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-09 22:39:53
