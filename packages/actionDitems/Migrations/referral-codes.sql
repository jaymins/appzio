-- phpMyAdmin SQL Dump
-- version 4.8.4
-- https://www.phpmyadmin.net/
--
-- Host: appziodb
-- Generation Time: Dec 19, 2018 at 10:17 AM
-- Server version: 10.3.11-MariaDB-1:10.3.11+maria~bionic
-- PHP Version: 7.2.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `appziodb`
--

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_referral_codes`
--

DROP TABLE IF EXISTS `ae_ext_referral_codes`;
CREATE TABLE `ae_ext_referral_codes` (
  `id` int(11) UNSIGNED NOT NULL,
  `play_id` int(11) UNSIGNED NOT NULL,
  `code` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_referral_codes_use`
--

DROP TABLE IF EXISTS `ae_ext_referral_codes_use`;
CREATE TABLE `ae_ext_referral_codes_use` (
  `id` int(11) UNSIGNED NOT NULL,
  `play_id` int(11) UNSIGNED NOT NULL,
  `referral_code_id` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ae_ext_referral_codes`
--
ALTER TABLE `ae_ext_referral_codes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `play_id` (`play_id`);

--
-- Indexes for table `ae_ext_referral_codes_use`
--
ALTER TABLE `ae_ext_referral_codes_use`
  ADD PRIMARY KEY (`id`),
  ADD KEY `play_id` (`play_id`),
  ADD KEY `ae_ext_referral_codes_use_referral_codes` (`referral_code_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ae_ext_referral_codes`
--
ALTER TABLE `ae_ext_referral_codes`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ae_ext_referral_codes_use`
--
ALTER TABLE `ae_ext_referral_codes_use`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `ae_ext_referral_codes`
--
ALTER TABLE `ae_ext_referral_codes`
  ADD CONSTRAINT `ae_ext_referral_codes_play` FOREIGN KEY (`play_id`) REFERENCES `ae_game_play` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_ext_referral_codes_use`
--
ALTER TABLE `ae_ext_referral_codes_use`
  ADD CONSTRAINT `ae_ext_referral_codes_use_play` FOREIGN KEY (`play_id`) REFERENCES `ae_game_play` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_ext_referral_codes_use_referral_codes` FOREIGN KEY (`referral_code_id`) REFERENCES `ae_ext_referral_codes` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;
COMMIT;
