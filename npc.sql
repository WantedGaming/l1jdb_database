-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 09, 2025 at 03:54 AM
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
-- Table structure for table `npc`
--

CREATE TABLE `npc` (
  `npcid` int(10) UNSIGNED NOT NULL,
  `classId` int(6) NOT NULL DEFAULT 0,
  `desc_en` varchar(100) NOT NULL,
  `desc_powerbook` varchar(100) NOT NULL,
  `desc_kr` varchar(45) NOT NULL DEFAULT '',
  `desc_id` varchar(45) NOT NULL DEFAULT '',
  `note` varchar(45) NOT NULL DEFAULT '',
  `impl` varchar(45) NOT NULL DEFAULT '',
  `spriteId` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `lvl` int(3) UNSIGNED NOT NULL DEFAULT 0,
  `hp` int(6) UNSIGNED NOT NULL DEFAULT 0,
  `mp` int(6) UNSIGNED NOT NULL DEFAULT 0,
  `ac` int(3) NOT NULL DEFAULT 0,
  `str` int(3) NOT NULL DEFAULT 0,
  `con` int(3) NOT NULL DEFAULT 0,
  `dex` int(3) NOT NULL DEFAULT 0,
  `wis` int(3) NOT NULL DEFAULT 0,
  `intel` int(3) NOT NULL DEFAULT 0,
  `mr` int(3) NOT NULL DEFAULT 0,
  `exp` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `alignment` int(10) NOT NULL DEFAULT 0,
  `big` enum('true','false') NOT NULL DEFAULT 'false',
  `weakAttr` enum('NONE','EARTH','FIRE','WATER','WIND') NOT NULL DEFAULT 'NONE',
  `ranged` int(3) UNSIGNED NOT NULL DEFAULT 0,
  `is_taming` enum('true','false') NOT NULL DEFAULT 'false',
  `passispeed` int(6) UNSIGNED NOT NULL DEFAULT 0,
  `atkspeed` int(6) UNSIGNED NOT NULL DEFAULT 0,
  `atk_magic_speed` int(6) UNSIGNED NOT NULL DEFAULT 0,
  `sub_magic_speed` int(6) UNSIGNED NOT NULL DEFAULT 0,
  `undead` enum('NONE','UNDEAD','DEMON','UNDEAD_BOSS','DRANIUM') NOT NULL DEFAULT 'NONE',
  `poison_atk` enum('NONE','DAMAGE','PARALYSIS','SILENCE') NOT NULL DEFAULT 'NONE',
  `is_agro` enum('false','true') NOT NULL DEFAULT 'false',
  `is_agro_poly` enum('false','true') NOT NULL DEFAULT 'false',
  `is_agro_invis` enum('false','true') NOT NULL DEFAULT 'false',
  `family` varchar(20) NOT NULL DEFAULT '',
  `agrofamily` int(1) UNSIGNED NOT NULL DEFAULT 0,
  `agrogfxid1` int(10) NOT NULL DEFAULT -1,
  `agrogfxid2` int(10) NOT NULL DEFAULT -1,
  `is_picupitem` enum('false','true') NOT NULL DEFAULT 'false',
  `digestitem` int(1) UNSIGNED NOT NULL DEFAULT 0,
  `is_bravespeed` enum('false','true') NOT NULL DEFAULT 'false',
  `hprinterval` int(6) UNSIGNED NOT NULL DEFAULT 0,
  `hpr` int(5) UNSIGNED NOT NULL DEFAULT 0,
  `mprinterval` int(6) UNSIGNED NOT NULL DEFAULT 0,
  `mpr` int(5) UNSIGNED NOT NULL DEFAULT 0,
  `is_teleport` enum('true','false') NOT NULL DEFAULT 'false',
  `randomlevel` int(3) UNSIGNED NOT NULL DEFAULT 0,
  `randomhp` int(5) UNSIGNED NOT NULL DEFAULT 0,
  `randommp` int(5) UNSIGNED NOT NULL DEFAULT 0,
  `randomac` int(3) NOT NULL DEFAULT 0,
  `randomexp` int(5) UNSIGNED NOT NULL DEFAULT 0,
  `randomAlign` int(5) NOT NULL DEFAULT 0,
  `damage_reduction` int(5) UNSIGNED NOT NULL DEFAULT 0,
  `is_hard` enum('true','false') NOT NULL DEFAULT 'false',
  `is_bossmonster` enum('true','false') NOT NULL DEFAULT 'false',
  `can_turnundead` enum('true','false') NOT NULL DEFAULT 'false',
  `bowSpritetId` int(5) UNSIGNED NOT NULL DEFAULT 0,
  `karma` int(10) NOT NULL DEFAULT 0,
  `transform_id` int(10) NOT NULL DEFAULT -1,
  `transform_gfxid` int(10) NOT NULL DEFAULT 0,
  `light_size` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `is_amount_fixed` enum('false','true') NOT NULL DEFAULT 'false',
  `is_change_head` enum('false','true') NOT NULL DEFAULT 'false',
  `spawnlist_door` int(10) NOT NULL DEFAULT 0,
  `count_map` int(10) NOT NULL DEFAULT 0,
  `cant_resurrect` enum('false','true') NOT NULL DEFAULT 'false',
  `isHide` enum('true','false') NOT NULL DEFAULT 'false'
) ENGINE=InnoDB DEFAULT CHARSET=euckr COLLATE=euckr_korean_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `npc`
--
ALTER TABLE `npc`
  ADD PRIMARY KEY (`npcid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `npc`
--
ALTER TABLE `npc`
  MODIFY `npcid` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
