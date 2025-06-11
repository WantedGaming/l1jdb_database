-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 11, 2025 at 09:46 AM
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
-- Table structure for table `bin_spell_common`
--

CREATE TABLE `bin_spell_common` (
  `spell_id` int(10) NOT NULL DEFAULT 0,
  `spell_category` enum('COMPANION_SPELL_BUFF(2)','SPELL_BUFF(1)','SPELL(0)') NOT NULL DEFAULT 'SPELL(0)',
  `on_icon_id` int(6) NOT NULL DEFAULT 0,
  `off_icon_id` int(6) NOT NULL DEFAULT 0,
  `duration` int(10) NOT NULL DEFAULT 0,
  `tooltip_str_id` int(6) NOT NULL DEFAULT 0,
  `tooltip_str_kr` varchar(200) DEFAULT NULL,
  `spell_bonus_list` text DEFAULT NULL,
  `companion_on_icon_id` int(6) NOT NULL DEFAULT 0,
  `companion_off_icon_id` int(6) NOT NULL DEFAULT 0,
  `companion_icon_priority` int(3) NOT NULL DEFAULT 0,
  `companion_tooltip_str_id` int(6) NOT NULL DEFAULT 0,
  `companion_new_str_id` int(6) NOT NULL DEFAULT 0,
  `companion_end_str_id` int(6) NOT NULL DEFAULT 0,
  `companion_is_good` int(3) NOT NULL DEFAULT 0,
  `companion_duration_show_type` int(3) NOT NULL DEFAULT 0,
  `delay_group_id` int(2) NOT NULL DEFAULT 0,
  `extract_item_name_id` int(6) NOT NULL DEFAULT 0,
  `extract_item_count` int(6) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=euckr COLLATE=euckr_korean_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bin_spell_common`
--
ALTER TABLE `bin_spell_common`
  ADD PRIMARY KEY (`spell_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
