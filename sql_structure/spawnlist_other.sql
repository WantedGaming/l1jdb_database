-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 09, 2025 at 09:50 AM
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
-- Table structure for table `spawnlist_other`
--

CREATE TABLE `spawnlist_other` (
  `id` int(5) UNSIGNED NOT NULL,
  `type` int(5) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'with type 999: manually created not linked with this table.',
  `name` varchar(45) NOT NULL DEFAULT '',
  `npc_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `locx` int(6) UNSIGNED NOT NULL DEFAULT 0,
  `locy` int(6) UNSIGNED NOT NULL DEFAULT 0,
  `heading` int(2) NOT NULL DEFAULT 0,
  `randomRange` int(3) NOT NULL DEFAULT 0,
  `timeMillisToDelete` int(6) NOT NULL DEFAULT 0,
  `gfxId` int(6) NOT NULL DEFAULT 0,
  `actionStatus` int(3) NOT NULL DEFAULT 4,
  `leftEdge` int(6) NOT NULL DEFAULT 0,
  `rightEdge` int(6) NOT NULL DEFAULT 0,
  `direction` int(3) NOT NULL DEFAULT 0,
  `targetPlayer` tinyint(1) NOT NULL DEFAULT 0,
  `paralysisTime` int(3) NOT NULL DEFAULT 0,
  `count` int(3) NOT NULL DEFAULT 1,
  `mapId` int(10) NOT NULL,
  `location` varchar(150) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=euckr COLLATE=euckr_korean_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `spawnlist_other`
--
ALTER TABLE `spawnlist_other`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `spawnlist_other`
--
ALTER TABLE `spawnlist_other`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
