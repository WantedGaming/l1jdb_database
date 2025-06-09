-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 09, 2025 at 09:48 AM
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
-- Table structure for table `mapids`
--

CREATE TABLE `mapids` (
  `mapid` int(10) NOT NULL DEFAULT 0,
  `locationname` varchar(45) DEFAULT NULL,
  `desc_kr` varchar(45) NOT NULL,
  `startX` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `endX` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `startY` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `endY` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `monster_amount` float UNSIGNED NOT NULL DEFAULT 0,
  `drop_rate` float UNSIGNED NOT NULL DEFAULT 0,
  `underwater` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `markable` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `teleportable` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `escapable` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `resurrection` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `painwand` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `penalty` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `take_pets` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `recall_pets` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `usable_item` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `usable_skill` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `dungeon` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `dmgModiPc2Npc` int(3) NOT NULL DEFAULT 0,
  `dmgModiNpc2Pc` int(3) NOT NULL DEFAULT 0,
  `decreaseHp` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `dominationTeleport` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `beginZone` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `redKnightZone` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `ruunCastleZone` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `interWarZone` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `geradBuffZone` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `growBuffZone` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `interKind` int(3) NOT NULL DEFAULT 0,
  `script` varchar(50) DEFAULT NULL,
  `cloneStart` int(6) NOT NULL DEFAULT 0,
  `cloneEnd` int(6) NOT NULL DEFAULT 0,
  `pngId` int(11) DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=euckr COLLATE=euckr_korean_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `mapids`
--
ALTER TABLE `mapids`
  ADD PRIMARY KEY (`mapid`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
