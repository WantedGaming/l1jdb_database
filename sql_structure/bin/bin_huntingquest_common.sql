-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 09, 2025 at 11:36 PM
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
-- Table structure for table `bin_huntingquest_common`
--

CREATE TABLE `bin_huntingquest_common` (
  `maxQuestCount` int(3) NOT NULL DEFAULT 0,
  `goalKillCount` int(3) NOT NULL DEFAULT 0,
  `reset_HourOfDay` int(2) NOT NULL DEFAULT -1,
  `reward_normal_ConditionalRewards` text DEFAULT NULL,
  `reward_normal_UsedItemID` int(6) NOT NULL,
  `reward_normal_UsedAmount` int(6) NOT NULL DEFAULT 0,
  `reward_dragon_ConditionalRewards` text DEFAULT NULL,
  `reward_dragon_UsedItemID` int(6) NOT NULL DEFAULT 0,
  `reward_dragon_UsedAmount` int(6) NOT NULL DEFAULT 0,
  `reward_hightdragon_ConditionalRewards` text DEFAULT NULL,
  `reward_hightdragon_UsedItemID` int(6) NOT NULL DEFAULT 0,
  `reward_hightdragon_UsedAmount` int(6) NOT NULL DEFAULT 0,
  `requiredCondition_MinLevel` int(3) NOT NULL DEFAULT 0,
  `requiredCondition_MaxLevel` int(3) NOT NULL DEFAULT 0,
  `requiredCondition_Map` int(6) NOT NULL DEFAULT 0,
  `requiredCondition_LocationDesc` int(6) NOT NULL DEFAULT 0,
  `enterMapID` int(6) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=euckr COLLATE=euckr_korean_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bin_huntingquest_common`
--
ALTER TABLE `bin_huntingquest_common`
  ADD PRIMARY KEY (`requiredCondition_Map`,`requiredCondition_LocationDesc`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
