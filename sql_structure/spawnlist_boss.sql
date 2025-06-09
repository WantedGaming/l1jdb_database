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
-- Table structure for table `spawnlist_boss`
--

CREATE TABLE `spawnlist_boss` (
  `id` int(10) UNSIGNED NOT NULL,
  `spawn_group_id` int(11) NOT NULL DEFAULT 0,
  `name` varchar(45) CHARACTER SET euckr COLLATE euckr_korean_ci DEFAULT '',
  `desc_kr` varchar(45) CHARACTER SET euckr COLLATE euckr_korean_ci DEFAULT '',
  `npcid` int(10) NOT NULL DEFAULT 0,
  `spawnDay` varchar(100) CHARACTER SET euckr COLLATE euckr_korean_ci DEFAULT NULL,
  `spawnTime` text CHARACTER SET euckr COLLATE euckr_korean_ci DEFAULT NULL,
  `spawnX` int(5) NOT NULL DEFAULT 0,
  `spawnY` int(5) NOT NULL DEFAULT 0,
  `spawnMapId` int(5) NOT NULL DEFAULT 0,
  `rndMinut` int(6) NOT NULL DEFAULT 0,
  `rndRange` int(10) NOT NULL DEFAULT 0,
  `heading` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `groupid` int(10) NOT NULL DEFAULT 0,
  `movementDistance` int(3) NOT NULL DEFAULT 0,
  `isYN` enum('true','false') CHARACTER SET euckr COLLATE euckr_korean_ci NOT NULL DEFAULT 'false',
  `mentType` enum('NONE','WORLD','MAP','SCREEN') NOT NULL,
  `ment` varchar(100) CHARACTER SET euckr COLLATE euckr_korean_ci DEFAULT '',
  `percent` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `aliveSecond` int(10) NOT NULL DEFAULT 0,
  `spawnType` enum('NORMAL','DOMINATION_TOWER','DRAGON_RAID','POISON_FEILD') CHARACTER SET euckr COLLATE euckr_korean_ci NOT NULL DEFAULT 'NORMAL'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `spawnlist_boss`
--
ALTER TABLE `spawnlist_boss`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `spawnlist_boss`
--
ALTER TABLE `spawnlist_boss`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
