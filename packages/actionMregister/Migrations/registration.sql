-- phpMyAdmin SQL Dump
-- version 4.7.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 23, 2019 at 04:11 PM
-- Server version: 5.6.35
-- PHP Version: 7.1.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `swiss8au`
--

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_mregister_companies`
--

CREATE TABLE `ae_ext_mregister_companies` (
                                              `id` int(11) UNSIGNED NOT NULL,
                                              `app_id` int(11) UNSIGNED NOT NULL,
                                              `name` varchar(255) NOT NULL,
                                              `subscription_active` tinyint(1) NOT NULL,
                                              `subscription_expires` varchar(255) NOT NULL,
                                              `user_limit` int(6) NOT NULL,
                                              `notes` text NOT NULL,
                                              `admit_by_domain` tinyint(1) NOT NULL,
                                              `domain` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `ae_ext_mregister_companies`
--

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_mregister_companies_users`
--

CREATE TABLE `ae_ext_mregister_companies_users` (
                                                    `id` int(11) UNSIGNED NOT NULL,
                                                    `app_id` int(11) UNSIGNED NOT NULL,
                                                    `company_id` int(11) UNSIGNED DEFAULT NULL,
                                                    `play_id` int(11) UNSIGNED DEFAULT NULL,
                                                    `registered` tinyint(1) NOT NULL,
                                                    `firstname` varchar(255) NOT NULL,
                                                    `lastname` varchar(255) NOT NULL,
                                                    `department` varchar(255) NOT NULL,
                                                    `email` varchar(255) NOT NULL,
                                                    `phone` varchar(255) NOT NULL,
                                                    `registered_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `ae_ext_mregister_companies_users`
--

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ae_ext_mregister_companies`
--
ALTER TABLE `ae_ext_mregister_companies`
    ADD PRIMARY KEY (`id`),
    ADD KEY `app_id` (`app_id`);

--
-- Indexes for table `ae_ext_mregister_companies_users`
--
ALTER TABLE `ae_ext_mregister_companies_users`
    ADD PRIMARY KEY (`id`),
    ADD KEY `ae_ext_mregister_companies_users_ibfk_2` (`company_id`),
    ADD KEY `app_id` (`app_id`),
    ADD KEY `play_id` (`play_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ae_ext_mregister_companies`
--
ALTER TABLE `ae_ext_mregister_companies`
    MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `ae_ext_mregister_companies_users`
--
ALTER TABLE `ae_ext_mregister_companies_users`
    MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `ae_ext_mregister_companies`
--
ALTER TABLE `ae_ext_mregister_companies`
    ADD CONSTRAINT `ae_ext_mregister_companies_ibfk_1` FOREIGN KEY (`app_id`) REFERENCES `ae_game` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_ext_mregister_companies_users`
--
ALTER TABLE `ae_ext_mregister_companies_users`
    ADD CONSTRAINT `ae_ext_mregister_companies_users_ibfk_2` FOREIGN KEY (`company_id`) REFERENCES `ae_ext_mregister_companies` (`id`) ON DELETE CASCADE ON UPDATE SET NULL,
    ADD CONSTRAINT `ae_ext_mregister_companies_users_ibfk_3` FOREIGN KEY (`app_id`) REFERENCES `ae_game` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
    ADD CONSTRAINT `ae_ext_mregister_companies_users_ibfk_4` FOREIGN KEY (`play_id`) REFERENCES `ae_game_play` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
