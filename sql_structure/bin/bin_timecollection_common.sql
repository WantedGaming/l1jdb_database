-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 09, 2025 at 11:38 PM
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
-- Table structure for table `bin_timecollection_common`
--

CREATE TABLE `bin_timecollection_common` (
  `buffSelect` text DEFAULT NULL,
  `rewardList` text DEFAULT NULL,
  `enchantSection` text DEFAULT NULL,
  `group_id` int(3) NOT NULL DEFAULT 0,
  `group_desc` int(6) NOT NULL DEFAULT 0,
  `group_desc_kr` varchar(100) DEFAULT NULL,
  `group_level_min` int(3) NOT NULL DEFAULT 0,
  `group_level_max` int(3) NOT NULL DEFAULT 0,
  `group_period_StartDate` varchar(100) DEFAULT NULL,
  `group_period_EndDate` varchar(100) DEFAULT NULL,
  `group_set_id` int(3) NOT NULL DEFAULT 0,
  `group_set_desc` int(6) NOT NULL DEFAULT 0,
  `group_set_desc_kr` varchar(100) DEFAULT NULL,
  `group_set_defaultTime` varchar(100) DEFAULT NULL,
  `group_set_recycle` int(3) NOT NULL DEFAULT 0,
  `group_set_itemSlot` text DEFAULT NULL,
  `group_set_BuffType` text DEFAULT NULL,
  `group_set_endBonus` enum('true','false') NOT NULL DEFAULT 'false',
  `group_set_ExtraTimeId` int(10) NOT NULL DEFAULT 0,
  `group_set_SetType` enum('NONE(-1)','TC_INFINITY(0)','TC_LIMITED(1)','TC_BONUS_INFINITY(2)','TC_BONUS_LIMITED(3)','TC_ADENA_REFILL(4)','TC_ADENA_REFILL_ERROR(5)','TC_BONUS_ADENA_REFILL(6)','TC_BONUS_ADENA_REFILL_ERROR(7)') NOT NULL DEFAULT 'NONE(-1)',
  `ExtraTimeSection` text DEFAULT NULL,
  `NPCDialogInfo` text DEFAULT NULL,
  `AlarmSetting` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=euckr COLLATE=euckr_korean_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bin_timecollection_common`
--
ALTER TABLE `bin_timecollection_common`
  ADD PRIMARY KEY (`group_id`,`group_set_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
