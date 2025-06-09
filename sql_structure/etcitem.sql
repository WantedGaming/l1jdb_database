-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 09, 2025 at 12:11 AM
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
-- Table structure for table `etcitem`
--

CREATE TABLE `etcitem` (
  `item_id` int(10) UNSIGNED NOT NULL,
  `item_name_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `desc_kr` varchar(45) NOT NULL DEFAULT '',
  `desc_en` varchar(100) NOT NULL,
  `desc_powerbook` varchar(100) NOT NULL,
  `note` text NOT NULL,
  `desc_id` varchar(45) NOT NULL DEFAULT '',
  `itemGrade` enum('ONLY','MYTH','LEGEND','HERO','RARE','ADVANC','NORMAL') NOT NULL DEFAULT 'NORMAL',
  `item_type` enum('ARROW','WAND','LIGHT','GEM','TOTEM','FIRE_CRACKER','POTION','FOOD','SCROLL','QUEST_ITEM','SPELL_BOOK','PET_ITEM','OTHER','MATERIAL','EVENT','STING','TREASURE_BOX') NOT NULL DEFAULT 'OTHER',
  `use_type` enum('NONE','NORMAL','WAND1','WAND','SPELL_LONG','NTELE','IDENTIFY','RES','TELEPORT','INVISABLE','LETTER','LETTER_W','CHOICE','INSTRUMENT','SOSC','SPELL_SHORT','T_SHIRT','CLOAK','GLOVE','BOOTS','HELMET','RING','AMULET','SHIELD','GARDER','DAI','ZEL','BLANK','BTELE','SPELL_BUFF','CCARD','CCARD_W','VCARD','VCARD_W','WCARD','WCARD_W','BELT','SPELL_LONG2','EARRING','FISHING_ROD','RON','RON_2','ACCZEL','PAIR','HEALING','SHOULDER','BADGE','POTENTIAL_SCROLL','SPELLMELT','ELIXER_RON','INVENTORY_BONUS','TAM_FRUIT','RACE_TICKET','PAIR_2','MAGICDOLL','SENTENCE','SHOULDER_2','BADGE_2','PET_POTION','GARDER_2','DOMINATION_POLY','PENDANT','SHOVEL','LEV_100_POLY','SMELTING','PURIFY','CHARGED_MAP_TIME') NOT NULL DEFAULT 'NONE',
  `material` enum('NONE(-)','LIQUID(액체)','WAX(밀랍)','VEGGY(식물성)','FLESH(동물성)','PAPER(종이)','CLOTH(천)','LEATHER(가죽)','WOOD(나무)','BONE(뼈)','DRAGON_HIDE(용비늘)','IRON(철)','METAL(금속)','COPPER(구리)','SILVER(은)','GOLD(금)','PLATINUM(백금)','MITHRIL(미스릴)','PLASTIC(블랙미스릴)','GLASS(유리)','GEMSTONE(보석)','MINERAL(광석)','ORIHARUKON(오리하루콘)','DRANIUM(드라니움)') NOT NULL DEFAULT 'NONE(-)',
  `weight` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `iconId` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `spriteId` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `merge` enum('true','false') NOT NULL DEFAULT 'false',
  `max_charge_count` int(6) UNSIGNED NOT NULL DEFAULT 0,
  `dmg_small` int(6) UNSIGNED NOT NULL DEFAULT 0,
  `dmg_large` int(6) UNSIGNED NOT NULL DEFAULT 0,
  `ac_bonus` int(3) NOT NULL DEFAULT 0,
  `shortHit` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `shortDmg` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `longHit` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `longDmg` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `add_str` int(2) NOT NULL DEFAULT 0,
  `add_con` int(2) NOT NULL DEFAULT 0,
  `add_dex` int(2) NOT NULL DEFAULT 0,
  `add_int` int(2) NOT NULL DEFAULT 0,
  `add_wis` int(2) NOT NULL DEFAULT 0,
  `add_cha` int(2) NOT NULL DEFAULT 0,
  `add_hp` int(6) NOT NULL DEFAULT 0,
  `add_mp` int(6) NOT NULL DEFAULT 0,
  `add_hpr` int(6) NOT NULL DEFAULT 0,
  `add_mpr` int(6) NOT NULL DEFAULT 0,
  `add_sp` int(2) NOT NULL DEFAULT 0,
  `min_lvl` int(3) UNSIGNED NOT NULL DEFAULT 0,
  `max_lvl` int(3) UNSIGNED NOT NULL DEFAULT 0,
  `m_def` int(2) NOT NULL DEFAULT 0,
  `carryBonus` int(4) NOT NULL DEFAULT 0,
  `defense_water` int(2) NOT NULL DEFAULT 0,
  `defense_wind` int(2) NOT NULL DEFAULT 0,
  `defense_fire` int(2) NOT NULL DEFAULT 0,
  `defense_earth` int(2) NOT NULL DEFAULT 0,
  `attr_all` int(2) NOT NULL DEFAULT 0,
  `regist_stone` int(2) NOT NULL DEFAULT 0,
  `regist_sleep` int(2) NOT NULL DEFAULT 0,
  `regist_freeze` int(2) NOT NULL DEFAULT 0,
  `regist_blind` int(2) NOT NULL DEFAULT 0,
  `regist_skill` int(2) NOT NULL DEFAULT 0,
  `regist_spirit` int(2) NOT NULL DEFAULT 0,
  `regist_dragon` int(2) NOT NULL DEFAULT 0,
  `regist_fear` int(2) NOT NULL DEFAULT 0,
  `regist_all` int(2) NOT NULL DEFAULT 0,
  `hitup_skill` int(2) NOT NULL DEFAULT 0,
  `hitup_spirit` int(2) NOT NULL DEFAULT 0,
  `hitup_dragon` int(2) NOT NULL DEFAULT 0,
  `hitup_fear` int(2) NOT NULL DEFAULT 0,
  `hitup_all` int(2) NOT NULL DEFAULT 0,
  `hitup_magic` int(2) NOT NULL DEFAULT 0,
  `damage_reduction` int(2) NOT NULL DEFAULT 0,
  `MagicDamageReduction` int(2) NOT NULL DEFAULT 0,
  `reductionEgnor` int(2) NOT NULL DEFAULT 0,
  `reductionPercent` int(2) NOT NULL DEFAULT 0,
  `PVPDamage` int(2) NOT NULL DEFAULT 0,
  `PVPDamageReduction` int(2) NOT NULL DEFAULT 0,
  `PVPDamageReductionPercent` int(2) NOT NULL DEFAULT 0,
  `PVPMagicDamageReduction` int(2) NOT NULL DEFAULT 0,
  `PVPReductionEgnor` int(2) NOT NULL DEFAULT 0,
  `PVPMagicDamageReductionEgnor` int(2) NOT NULL DEFAULT 0,
  `abnormalStatusDamageReduction` int(2) NOT NULL DEFAULT 0,
  `abnormalStatusPVPDamageReduction` int(2) NOT NULL DEFAULT 0,
  `PVPDamagePercent` int(2) NOT NULL DEFAULT 0,
  `expBonus` int(3) NOT NULL DEFAULT 0,
  `rest_exp_reduce_efficiency` int(3) NOT NULL DEFAULT 0,
  `shortCritical` int(2) NOT NULL DEFAULT 0,
  `longCritical` int(2) NOT NULL DEFAULT 0,
  `magicCritical` int(2) NOT NULL DEFAULT 0,
  `addDg` int(2) NOT NULL DEFAULT 0,
  `addEr` int(2) NOT NULL DEFAULT 0,
  `addMe` int(2) NOT NULL DEFAULT 0,
  `poisonRegist` enum('false','true') NOT NULL DEFAULT 'false',
  `imunEgnor` int(3) NOT NULL DEFAULT 0,
  `stunDuration` int(2) NOT NULL DEFAULT 0,
  `tripleArrowStun` int(2) NOT NULL DEFAULT 0,
  `strangeTimeIncrease` int(4) NOT NULL DEFAULT 0,
  `strangeTimeDecrease` int(4) NOT NULL DEFAULT 0,
  `potionRegist` int(2) NOT NULL DEFAULT 0,
  `potionPercent` int(2) NOT NULL DEFAULT 0,
  `potionValue` int(2) NOT NULL DEFAULT 0,
  `hprAbsol32Second` int(2) NOT NULL DEFAULT 0,
  `mprAbsol64Second` int(2) NOT NULL DEFAULT 0,
  `mprAbsol16Second` int(2) NOT NULL DEFAULT 0,
  `hpPotionDelayDecrease` int(4) NOT NULL DEFAULT 0,
  `hpPotionCriticalProb` int(4) NOT NULL DEFAULT 0,
  `increaseArmorSkillProb` int(4) NOT NULL DEFAULT 0,
  `attackSpeedDelayRate` int(3) NOT NULL DEFAULT 0,
  `moveSpeedDelayRate` int(3) NOT NULL DEFAULT 0,
  `buffDurationSecond` int(8) NOT NULL DEFAULT 0,
  `locx` int(6) UNSIGNED NOT NULL DEFAULT 0,
  `locy` int(6) UNSIGNED NOT NULL DEFAULT 0,
  `mapid` int(6) UNSIGNED NOT NULL DEFAULT 0,
  `bless` int(2) UNSIGNED NOT NULL DEFAULT 1,
  `trade` int(2) UNSIGNED NOT NULL DEFAULT 0,
  `retrieve` int(2) UNSIGNED NOT NULL DEFAULT 0,
  `specialretrieve` int(2) UNSIGNED NOT NULL DEFAULT 0,
  `cant_delete` int(2) UNSIGNED NOT NULL DEFAULT 0,
  `cant_sell` int(2) UNSIGNED NOT NULL DEFAULT 0,
  `delay_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `delay_time` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `delay_effect` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `food_volume` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `save_at_once` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `Magic_name` varchar(20) DEFAULT NULL,
  `level` int(3) UNSIGNED NOT NULL DEFAULT 0,
  `attr` enum('EARTH','AIR','WATER','FIRE','NONE') NOT NULL DEFAULT 'NONE',
  `alignment` enum('CAOTIC','NEUTRAL','LAWFUL','NONE') NOT NULL DEFAULT 'NONE',
  `use_royal` int(2) UNSIGNED NOT NULL DEFAULT 0,
  `use_knight` int(2) UNSIGNED NOT NULL DEFAULT 0,
  `use_mage` int(2) UNSIGNED NOT NULL DEFAULT 0,
  `use_elf` int(2) UNSIGNED NOT NULL DEFAULT 0,
  `use_darkelf` int(2) UNSIGNED NOT NULL DEFAULT 0,
  `use_dragonknight` int(2) UNSIGNED NOT NULL DEFAULT 0,
  `use_illusionist` int(2) UNSIGNED NOT NULL DEFAULT 0,
  `use_warrior` int(2) UNSIGNED NOT NULL DEFAULT 0,
  `use_fencer` int(2) UNSIGNED NOT NULL DEFAULT 0,
  `use_lancer` int(2) UNSIGNED NOT NULL DEFAULT 0,
  `skill_type` enum('passive','active','none') NOT NULL DEFAULT 'none',
  `etc_value` int(10) NOT NULL DEFAULT 0,
  `limit_type` enum('WORLD_WAR','BEGIN_ZONE','NONE') NOT NULL DEFAULT 'NONE',
  `prob` int(3) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=euckr COLLATE=euckr_korean_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `etcitem`
--
ALTER TABLE `etcitem`
  ADD PRIMARY KEY (`item_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `etcitem`
--
ALTER TABLE `etcitem`
  MODIFY `item_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
