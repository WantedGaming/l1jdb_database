-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 09, 2025 at 11:33 PM
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
-- Table structure for table `bin_companion_class_common`
--

CREATE TABLE `bin_companion_class_common` (
  `classId` int(6) NOT NULL DEFAULT 0,
  `class` varchar(100) DEFAULT NULL,
  `category` enum('DOG_FIGHT(5)','WILD(4)','PET(3)','DEVINE_BEAST(2)','FIERCE_ANIMAL(1)') NOT NULL DEFAULT 'FIERCE_ANIMAL(1)',
  `element` enum('LIGHT(5)','EARTH(4)','AIR(3)','WATER(2)','FIRE(1)','NONE(0)') NOT NULL DEFAULT 'NONE(0)',
  `skill` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=euckr COLLATE=euckr_korean_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bin_companion_class_common`
--
ALTER TABLE `bin_companion_class_common`
  ADD PRIMARY KEY (`classId`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
