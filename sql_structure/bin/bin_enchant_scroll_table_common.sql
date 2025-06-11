-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 10, 2025 at 09:17 PM
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
-- Database: `l1j_remastered`
--

-- --------------------------------------------------------

--
-- Table structure for table `bin_enchant_scroll_table_common`
--

CREATE TABLE `bin_enchant_scroll_table_common` (
  `enchantType` int(3) NOT NULL DEFAULT 0,
  `nameid` int(6) NOT NULL DEFAULT 0,
  `desc_kr` varchar(100) DEFAULT NULL,
  `targetEnchant` int(3) NOT NULL DEFAULT 0,
  `noTargetMaterialList` text DEFAULT NULL,
  `target_category` enum('NONE(0)','WEAPON(1)','ARMOR(2)','ACCESSORY(3)','ELEMENT(4)') NOT NULL DEFAULT 'NONE(0)',
  `isBmEnchantScroll` enum('false','true') NOT NULL DEFAULT 'false',
  `elementalType` int(2) NOT NULL DEFAULT 0,
  `useBlesscodeScroll` int(2) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=euckr COLLATE=euckr_korean_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bin_enchant_scroll_table_common`
--
ALTER TABLE `bin_enchant_scroll_table_common`
  ADD PRIMARY KEY (`enchantType`,`nameid`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
