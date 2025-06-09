-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 09, 2025 at 11:30 AM
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
-- Table structure for table `magicdoll_potential`
--

CREATE TABLE `magicdoll_potential` (
  `bonusId` int(3) NOT NULL DEFAULT 0,
  `name` varchar(70) DEFAULT NULL,
  `desc_kr` varchar(45) NOT NULL,
  `isUse` enum('false','true') DEFAULT 'true',
  `ac_bonus` int(2) NOT NULL DEFAULT 0,
  `str` int(2) NOT NULL DEFAULT 0,
  `con` int(2) NOT NULL DEFAULT 0,
  `dex` int(2) NOT NULL DEFAULT 0,
  `int` int(2) NOT NULL DEFAULT 0,
  `wis` int(2) NOT NULL DEFAULT 0,
  `cha` int(2) NOT NULL DEFAULT 0,
  `allStatus` int(2) NOT NULL DEFAULT 0,
  `shortDamage` int(2) NOT NULL DEFAULT 0,
  `shortHit` int(2) NOT NULL DEFAULT 0,
  `shortCritical` int(2) NOT NULL DEFAULT 0,
  `longDamage` int(2) NOT NULL DEFAULT 0,
  `longHit` int(2) NOT NULL DEFAULT 0,
  `longCritical` int(2) NOT NULL DEFAULT 0,
  `spellpower` int(2) NOT NULL DEFAULT 0,
  `magicHit` int(2) NOT NULL DEFAULT 0,
  `magicCritical` int(2) NOT NULL DEFAULT 0,
  `hp` int(3) NOT NULL DEFAULT 0,
  `mp` int(3) NOT NULL DEFAULT 0,
  `hpr` int(2) NOT NULL DEFAULT 0,
  `mpr` int(2) NOT NULL DEFAULT 0,
  `hpStill` int(2) NOT NULL DEFAULT 0,
  `mpStill` int(2) NOT NULL DEFAULT 0,
  `stillChance` int(3) NOT NULL DEFAULT 0,
  `hprAbsol` int(2) NOT NULL DEFAULT 0,
  `mprAbsol` int(2) NOT NULL DEFAULT 0,
  `attrFire` int(2) NOT NULL DEFAULT 0,
  `attrWater` int(2) NOT NULL DEFAULT 0,
  `attrWind` int(2) NOT NULL DEFAULT 0,
  `attrEarth` int(2) NOT NULL DEFAULT 0,
  `attrAll` int(2) NOT NULL DEFAULT 0,
  `mr` int(2) NOT NULL DEFAULT 0,
  `expBonus` int(3) NOT NULL DEFAULT 0,
  `carryBonus` int(3) NOT NULL DEFAULT 0,
  `dg` int(2) NOT NULL DEFAULT 0,
  `er` int(2) NOT NULL DEFAULT 0,
  `me` int(2) NOT NULL DEFAULT 0,
  `reduction` int(2) NOT NULL DEFAULT 0,
  `reductionEgnor` int(2) NOT NULL DEFAULT 0,
  `reductionMagic` int(2) NOT NULL DEFAULT 0,
  `reductionPercent` int(2) NOT NULL DEFAULT 0,
  `PVPDamage` int(2) NOT NULL DEFAULT 0,
  `PVPReduction` int(2) NOT NULL DEFAULT 0,
  `PVPReductionEgnor` int(2) NOT NULL DEFAULT 0,
  `PVPReductionMagic` int(2) NOT NULL DEFAULT 0,
  `PVPReductionMagicEgnor` int(2) NOT NULL DEFAULT 0,
  `toleranceSkill` int(2) NOT NULL DEFAULT 0,
  `toleranceSpirit` int(2) NOT NULL DEFAULT 0,
  `toleranceDragon` int(2) NOT NULL DEFAULT 0,
  `toleranceFear` int(2) NOT NULL DEFAULT 0,
  `toleranceAll` int(2) NOT NULL DEFAULT 0,
  `hitupSkill` int(2) NOT NULL DEFAULT 0,
  `hitupSpirit` int(2) NOT NULL DEFAULT 0,
  `hitupDragon` int(2) NOT NULL DEFAULT 0,
  `hitupFear` int(2) NOT NULL DEFAULT 0,
  `hitupAll` int(2) NOT NULL DEFAULT 0,
  `imunEgnor` int(2) NOT NULL DEFAULT 0,
  `strangeTimeIncrease` int(4) NOT NULL DEFAULT 0,
  `firstSpeed` enum('true','false') NOT NULL DEFAULT 'false',
  `secondSpeed` enum('true','false') NOT NULL DEFAULT 'false',
  `thirdSpeed` enum('true','false') NOT NULL DEFAULT 'false',
  `forthSpeed` enum('true','false') NOT NULL DEFAULT 'false',
  `skilId` int(9) NOT NULL DEFAULT -1,
  `skillChance` int(3) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=euckr COLLATE=euckr_korean_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `magicdoll_potential`
--
ALTER TABLE `magicdoll_potential`
  ADD PRIMARY KEY (`bonusId`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
