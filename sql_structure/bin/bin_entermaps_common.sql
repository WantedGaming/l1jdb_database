-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 09, 2025 at 11:35 PM
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
-- Table structure for table `bin_entermaps_common`
--

CREATE TABLE `bin_entermaps_common` (
  `id` int(6) NOT NULL DEFAULT 0,
  `action_name` varchar(50) NOT NULL DEFAULT '',
  `number_id` int(6) NOT NULL DEFAULT 0,
  `loc_x` int(6) NOT NULL DEFAULT 0,
  `loc_y` int(6) NOT NULL DEFAULT 0,
  `loc_range` int(3) NOT NULL DEFAULT 0,
  `priority_id` int(2) NOT NULL DEFAULT 0,
  `maxUser` int(3) NOT NULL DEFAULT 0,
  `conditions` text DEFAULT NULL,
  `destinations` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=euckr COLLATE=euckr_korean_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bin_entermaps_common`
--
ALTER TABLE `bin_entermaps_common`
  ADD PRIMARY KEY (`id`,`action_name`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
