-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 09, 2025 at 11:31 PM
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
-- Table structure for table `bin_catalyst_common`
--

CREATE TABLE `bin_catalyst_common` (
  `nameId` int(6) NOT NULL DEFAULT 0,
  `nameId_kr` varchar(100) DEFAULT NULL,
  `input` int(6) NOT NULL DEFAULT 0,
  `input_kr` varchar(100) DEFAULT NULL,
  `output` int(6) NOT NULL DEFAULT 0,
  `output_kr` varchar(100) DEFAULT NULL,
  `successProb` int(3) NOT NULL DEFAULT 0,
  `rewardCount` int(6) NOT NULL DEFAULT 0,
  `preserveProb` int(3) NOT NULL DEFAULT 0,
  `failOutput` int(6) NOT NULL DEFAULT 0,
  `failOutput_kr` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=euckr COLLATE=euckr_korean_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bin_catalyst_common`
--
ALTER TABLE `bin_catalyst_common`
  ADD PRIMARY KEY (`nameId`,`input`,`output`,`failOutput`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
