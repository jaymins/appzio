-- phpMyAdmin SQL Dump
-- version 4.7.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 13, 2019 at 09:19 PM
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
-- Table structure for table `ae_ext_purchase`
--

CREATE TABLE `ae_ext_purchase` (
                                   `id` int(11) UNSIGNED NOT NULL,
                                   `app_id` int(11) UNSIGNED NOT NULL,
                                   `play_id` int(11) UNSIGNED NOT NULL,
                                   `product_id` int(11) UNSIGNED NOT NULL,
                                   `price` int(6) NOT NULL,
                                   `currency` varchar(5) NOT NULL,
                                   `type` varchar(255) NOT NULL,
                                   `date` datetime NOT NULL,
                                   `store_id` varchar(255) NOT NULL,
                                   `receipt` text NOT NULL,
                                   `subject` varchar(255) NOT NULL,
                                   `subscription` tinyint(1) NOT NULL,
                                   `yearly` tinyint(1) NOT NULL,
                                   `monthly` tinyint(1) NOT NULL,
                                   `expiry` int(11) NOT NULL,
                                   `email` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_purchase_product`
--

CREATE TABLE `ae_ext_purchase_product` (
                                           `id` int(10) UNSIGNED NOT NULL,
                                           `app_id` int(11) UNSIGNED NOT NULL,
                                           `name` varchar(255) NOT NULL,
                                           `code` varchar(255) NOT NULL,
                                           `type` varchar(255) NOT NULL,
                                           `price` decimal(6,2) NOT NULL,
                                           `currency` varchar(5) NOT NULL,
                                           `code_ios` varchar(255) NOT NULL,
                                           `code_android` varchar(255) NOT NULL,
                                           `description` text NOT NULL,
                                           `image` varchar(255) NOT NULL,
                                           `icon` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ae_ext_purchase`
--
ALTER TABLE `ae_ext_purchase`
    ADD PRIMARY KEY (`id`),
    ADD KEY `app_id` (`app_id`),
    ADD KEY `play_id` (`play_id`),
    ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `ae_ext_purchase_product`
--
ALTER TABLE `ae_ext_purchase_product`
    ADD PRIMARY KEY (`id`),
    ADD KEY `app_id` (`app_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ae_ext_purchase`
--
ALTER TABLE `ae_ext_purchase`
    MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ae_ext_purchase_product`
--
ALTER TABLE `ae_ext_purchase_product`
    MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `ae_ext_purchase`
--
ALTER TABLE `ae_ext_purchase`
    ADD CONSTRAINT `ae_ext_purchase_ibfk_1` FOREIGN KEY (`app_id`) REFERENCES `ae_game` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
    ADD CONSTRAINT `ae_ext_purchase_ibfk_2` FOREIGN KEY (`play_id`) REFERENCES `ae_game_play` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
    ADD CONSTRAINT `ae_ext_purchase_ibfk_3` FOREIGN KEY (`product_id`) REFERENCES `ae_ext_purchase_product` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `ae_ext_purchase_product`
--
ALTER TABLE `ae_ext_purchase_product`
    ADD CONSTRAINT `ae_ext_purchase_product_ibfk_1` FOREIGN KEY (`app_id`) REFERENCES `ae_game` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
