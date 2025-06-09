-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 09, 2025 at 03:59 AM
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
-- Table structure for table `droplist`
--

CREATE TABLE `droplist` (
  `mobId` int(6) NOT NULL DEFAULT 0,
  `mobname_kr` varchar(100) NOT NULL,
  `mobname_en` varchar(100) NOT NULL,
  `moblevel` int(10) NOT NULL DEFAULT 0,
  `itemId` int(6) NOT NULL DEFAULT 0,
  `itemname_kr` varchar(50) NOT NULL,
  `itemname_en` varchar(100) NOT NULL,
  `min` int(4) NOT NULL DEFAULT 0,
  `max` int(4) NOT NULL DEFAULT 0,
  `chance` int(8) NOT NULL DEFAULT 0,
  `Enchant` int(10) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=euckr COLLATE=euckr_korean_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `droplist`
--
ALTER TABLE `droplist`
  ADD PRIMARY KEY (`mobId`,`itemId`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
