-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 16, 2025 at 11:29 PM
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
-- Table structure for table `beginner`
--

CREATE TABLE `beginner` (
  `id` int(10) NOT NULL,
  `item_id` int(6) NOT NULL DEFAULT 0,
  `count` int(10) NOT NULL DEFAULT 0,
  `charge_count` int(10) NOT NULL DEFAULT 0,
  `enchantlvl` int(6) NOT NULL DEFAULT 0,
  `item_name` varchar(50) NOT NULL DEFAULT '',
  `desc_kr` varchar(50) NOT NULL,
  `activate` enum('A','P','K','E','W','D','T','B','J','F','L') NOT NULL DEFAULT 'A'
) ENGINE=InnoDB DEFAULT CHARSET=euckr COLLATE=euckr_korean_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `beginner`
--
ALTER TABLE `beginner`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `beginner`
--
ALTER TABLE `beginner`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
