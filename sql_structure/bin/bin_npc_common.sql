-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 11, 2025 at 10:04 AM
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
-- Table structure for table `bin_npc_common`
--

CREATE TABLE `bin_npc_common` (
  `class_id` int(6) NOT NULL DEFAULT 0,
  `npc_id` int(11) NOT NULL,
  `with_bin_spawn` tinyint(1) NOT NULL,
  `sprite_id` int(6) NOT NULL DEFAULT 0,
  `desc_id` varchar(100) DEFAULT NULL,
  `desc_kr` varchar(100) DEFAULT NULL,
  `level` int(3) NOT NULL DEFAULT 0,
  `hp` int(9) NOT NULL DEFAULT 0,
  `mp` int(9) NOT NULL DEFAULT 0,
  `ac` int(3) NOT NULL DEFAULT 0,
  `str` int(3) NOT NULL DEFAULT 0,
  `con` int(3) NOT NULL DEFAULT 0,
  `dex` int(3) NOT NULL DEFAULT 0,
  `wis` int(3) NOT NULL DEFAULT 0,
  `inti` int(3) NOT NULL DEFAULT 0,
  `cha` int(3) NOT NULL DEFAULT 0,
  `mr` int(3) NOT NULL DEFAULT 0,
  `magic_level` int(3) NOT NULL DEFAULT 0,
  `magic_bonus` int(3) NOT NULL DEFAULT 0,
  `magic_evasion` int(3) NOT NULL DEFAULT 0,
  `resistance_fire` int(3) NOT NULL DEFAULT 0,
  `resistance_water` int(3) NOT NULL DEFAULT 0,
  `resistance_air` int(3) NOT NULL DEFAULT 0,
  `resistance_earth` int(3) NOT NULL DEFAULT 0,
  `alignment` int(6) NOT NULL DEFAULT 0,
  `big` enum('true','false') NOT NULL DEFAULT 'false',
  `drop_items` text DEFAULT NULL,
  `tendency` enum('AGGRESSIVE(2)','PASSIVE(1)','NEUTRAL(0)') NOT NULL DEFAULT 'NEUTRAL(0)',
  `category` int(10) NOT NULL DEFAULT 0,
  `is_bossmonster` enum('true','false') NOT NULL DEFAULT 'false',
  `can_turnundead` enum('true','false') NOT NULL DEFAULT 'false'
) ENGINE=InnoDB DEFAULT CHARSET=euckr COLLATE=euckr_korean_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bin_npc_common`
--
ALTER TABLE `bin_npc_common`
  ADD PRIMARY KEY (`class_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
