-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 10, 2025 at 09:02 PM
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
-- Table structure for table `bin_craft_common`
--

CREATE TABLE `bin_craft_common` (
  `craft_id` int(6) NOT NULL DEFAULT 0,
  `desc_id` int(6) NOT NULL DEFAULT 0,
  `desc_kr` varchar(100) DEFAULT NULL,
  `min_level` int(3) NOT NULL DEFAULT 0,
  `max_level` int(3) NOT NULL DEFAULT 0,
  `required_gender` int(2) NOT NULL DEFAULT 0,
  `min_align` int(6) NOT NULL DEFAULT 0,
  `max_align` int(6) NOT NULL DEFAULT 0,
  `min_karma` int(10) NOT NULL DEFAULT 0,
  `max_karma` int(10) NOT NULL DEFAULT 0,
  `max_count` int(6) NOT NULL DEFAULT 0,
  `is_show` enum('true','false') NOT NULL DEFAULT 'false',
  `PCCafeOnly` enum('true','false') NOT NULL DEFAULT 'false',
  `bmProbOpen` enum('true','false') NOT NULL DEFAULT 'false',
  `required_classes` int(6) NOT NULL DEFAULT 0,
  `required_quests` text DEFAULT NULL,
  `required_sprites` text DEFAULT NULL,
  `required_items` text DEFAULT NULL,
  `inputs_arr_input_item` text DEFAULT NULL,
  `inputs_arr_option_item` text DEFAULT NULL,
  `outputs_success` text DEFAULT NULL,
  `outputs_failure` text DEFAULT NULL,
  `outputs_success_prob_by_million` int(10) NOT NULL DEFAULT 0,
  `batch_delay_sec` int(10) NOT NULL DEFAULT 0,
  `period_list` text DEFAULT NULL,
  `cur_successcount` int(10) NOT NULL DEFAULT 0,
  `max_successcount` int(10) NOT NULL DEFAULT 0,
  `except_npc` enum('true','false') NOT NULL DEFAULT 'false',
  `SuccessCountType` enum('World(0)','Account(1)','Character(2)','AllServers(3)') NOT NULL DEFAULT 'World(0)'
) ENGINE=InnoDB DEFAULT CHARSET=euckr COLLATE=euckr_korean_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bin_craft_common`
--
ALTER TABLE `bin_craft_common`
  ADD PRIMARY KEY (`craft_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
