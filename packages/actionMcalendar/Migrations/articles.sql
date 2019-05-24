-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 10.132.0.2
-- Generation Time: Feb 01, 2019 at 03:16 PM
-- Server version: 10.3.1-MariaDB-log
-- PHP Version: 7.1.17

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

INSERT INTO `ae_ext_article` (`id`, `app_id`, `category_id`, `play_id`, `title`, `header`, `content`, `link`, `rating`, `featured`, `article_date`) VALUES
(35, {appid}, NULL, NULL, 'Header Title', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.', '[{\"type\":\"text\",\"content\":\"Subtitle goes here\",\"styles\":{\"color\":\"#101010\",\"font-size\":\"23\",\"background-color\":\"#ffffff\",\"padding\":\"20 20 0 20\",\"font-android\":\"OpenSans-ExtraBold\"}},{\"type\":\"text\",\"content\":\"Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.\",\"styles\":{\"color\":\"#333333\",\"font-size\":\"17\",\"background-color\":\"#ffffff\",\"padding\":\"10 20 40 20\"}},{\"type\":\"gallery\",\"ref\":\"gal1\"}]', '', 0, 0, '2018-10-24 07:06:23'),
(36, {appid}, NULL, NULL, 'Video Article', '', '[{\"type\":\"text\",\"content\":\"Some example video content\",\"styles\":{\"margin\":\"0 0 0 0\",\"padding\":\"10 0 10 0\",\"color\":\"#ffffff\"}},{\"type\":\"video\",\"video_link\":\"https:\\/\\/appziomedia.blob.core.windows.net\\/asset-1fc906bd-4394-4c5d-af1e-bfe6450a808a\\/CI Cycle Video.mp4?sv=2015-07-08&sr=c&si=d7cc0767-cefe-4991-8c0b-f4cf6c9b9aa0&sig=C1UVUaKJHESbWdhaRPPPZt5mtQD3pw9bNWWgKvUm1rQ%3D&st=2018-02-08T08%3A47%3A45Z&se=2118-02-09T08%3A47%3A45Z\"}]', '', 0, 0, '2018-10-31 10:29:30');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
