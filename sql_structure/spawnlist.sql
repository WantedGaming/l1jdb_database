-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 09, 2025 at 09:51 AM
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
-- Table structure for table `spawnlist`
--

CREATE TABLE `spawnlist` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(45) NOT NULL DEFAULT '',
  `count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `npc_templateid` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `group_id` int(6) UNSIGNED NOT NULL DEFAULT 0,
  `locx` int(6) UNSIGNED NOT NULL DEFAULT 0,
  `locy` int(6) UNSIGNED NOT NULL DEFAULT 0,
  `randomx` int(3) UNSIGNED NOT NULL DEFAULT 0,
  `randomy` int(3) UNSIGNED NOT NULL DEFAULT 0,
  `locx1` int(6) UNSIGNED NOT NULL DEFAULT 0,
  `locy1` int(6) UNSIGNED NOT NULL DEFAULT 0,
  `locx2` int(6) UNSIGNED NOT NULL DEFAULT 0,
  `locy2` int(6) UNSIGNED NOT NULL DEFAULT 0,
  `heading` int(2) UNSIGNED NOT NULL DEFAULT 0,
  `min_respawn_delay` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `max_respawn_delay` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `mapid` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `respawn_screen` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `movement_distance` int(3) UNSIGNED NOT NULL DEFAULT 0,
  `rest` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `near_spawn` tinyint(1) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=euckr COLLATE=euckr_korean_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `spawnlist`
--
ALTER TABLE `spawnlist`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `spawnlist`
--
ALTER TABLE `spawnlist`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
