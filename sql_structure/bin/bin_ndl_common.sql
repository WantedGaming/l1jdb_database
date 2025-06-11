-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 11, 2025 at 10:08 AM
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
-- Table structure for table `bin_ndl_common`
--

CREATE TABLE `bin_ndl_common` (
  `map_number` int(6) NOT NULL DEFAULT 0,
  `npc_classId` int(6) NOT NULL DEFAULT 0,
  `npc_desc_kr` varchar(100) DEFAULT NULL,
  `territory_startXY` int(10) NOT NULL DEFAULT 0,
  `territory_endXY` int(10) NOT NULL DEFAULT 0,
  `territory_location_desc` int(6) NOT NULL DEFAULT 0,
  `territory_average_npc_value` int(10) NOT NULL DEFAULT 0,
  `territory_average_ac` int(10) NOT NULL DEFAULT 0,
  `territory_average_level` int(10) NOT NULL DEFAULT 0,
  `territory_average_wis` int(10) NOT NULL DEFAULT 0,
  `territory_average_mr` int(10) NOT NULL DEFAULT 0,
  `territory_average_magic_barrier` int(10) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=euckr COLLATE=euckr_korean_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bin_ndl_common`
--
ALTER TABLE `bin_ndl_common`
  ADD PRIMARY KEY (`map_number`,`npc_classId`,`territory_startXY`,`territory_endXY`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
