-- phpMyAdmin SQL Dump
-- version 4.7.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Dec 01, 2018 at 05:47 PM
-- Server version: 5.6.35
-- PHP Version: 7.1.1

CREATE DATABASE IF NOT EXISTS appziodb;
USE appziodb;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `appziodb`
--

-- --------------------------------------------------------

--
-- Table structure for table `aco`
--

CREATE TABLE `aco` (
  `id` mediumint(11) NOT NULL,
  `collection_id` mediumint(11) NOT NULL,
  `path` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Table structure for table `aco_collection`
--

CREATE TABLE `aco_collection` (
  `id` mediumint(11) NOT NULL,
  `alias` varchar(20) NOT NULL,
  `model` varchar(15) NOT NULL,
  `foreign_key` mediumint(11) NOT NULL,
  `created` mediumint(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ae_access_tokens`
--

CREATE TABLE `ae_access_tokens` (
  `id` int(11) UNSIGNED NOT NULL,
  `play_id` int(11) UNSIGNED NOT NULL,
  `id_user` int(11) UNSIGNED NOT NULL,
  `token` varchar(32) NOT NULL,
  `expires` int(11) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `api_key` varchar(16) NOT NULL,
  `deviceparams` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ae_api_errorlog`
--

CREATE TABLE `ae_api_errorlog` (
  `id` int(11) UNSIGNED NOT NULL,
  `api_key` varchar(16) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `msg` varchar(255) NOT NULL,
  `api_call` varchar(255) NOT NULL,
  `api_params` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ae_api_filelookup`
--

CREATE TABLE `ae_api_filelookup` (
  `id` int(11) UNSIGNED NOT NULL,
  `game_id` int(11) UNSIGNED NOT NULL,
  `original` varchar(255) NOT NULL,
  `md5` varchar(60) NOT NULL,
  `cachefile` varchar(255) NOT NULL,
  `priority` tinyint(1) NOT NULL DEFAULT '2',
  `branch_id` int(11) NOT NULL,
  `action_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ae_api_key`
--

CREATE TABLE `ae_api_key` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `id_user` int(11) UNSIGNED NOT NULL,
  `key` varchar(255) NOT NULL,
  `private_key` varchar(255) NOT NULL,
  `ratelimit` int(11) NOT NULL,
  `calls` int(20) NOT NULL,
  `last_call` date NOT NULL,
  `callback_url` varchar(255) NOT NULL,
  `note` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ae_app_menus`
--

CREATE TABLE `ae_app_menus` (
  `id` int(11) UNSIGNED NOT NULL,
  `app_id` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `safe_name` varchar(255) NOT NULL,
  `state` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ae_app_menus_user_state`
--

CREATE TABLE `ae_app_menus_user_state` (
  `id` int(11) NOT NULL,
  `menu_id` int(11) UNSIGNED NOT NULL,
  `menu_item_id` int(11) UNSIGNED NOT NULL,
  `action_id` int(11) UNSIGNED NOT NULL,
  `branch_id` int(11) UNSIGNED NOT NULL,
  `play_id` int(11) UNSIGNED NOT NULL,
  `current_state` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ae_app_menu_items`
--

CREATE TABLE `ae_app_menu_items` (
  `id` int(11) UNSIGNED NOT NULL,
  `menu_id` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `safe_name` varchar(255) NOT NULL,
  `item_order` int(11) NOT NULL,
  `state` varchar(255) NOT NULL,
  `action` varchar(255) NOT NULL,
  `action_config` varchar(255) NOT NULL,
  `icon` varchar(255) NOT NULL,
  `icon_active` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `fallback_id` varchar(255) NOT NULL,
  `call_backend` tinyint(1) NOT NULL,
  `open_popup` tinyint(1) NOT NULL,
  `refresh_action` int(11) NOT NULL,
  `refresh_action_open` tinyint(1) NOT NULL,
  `refresh_action_close` tinyint(1) NOT NULL,
  `action_tab` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ae_category`
--

CREATE TABLE `ae_category` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `visible` smallint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `ae_category`
--

INSERT INTO `ae_category` (`id`, `name`, `visible`) VALUES
(1, 'Languages', 1),
(2, 'Software', 1),
(3, 'K12', 1),
(4, 'Higher Education', 1),
(5, 'Corporate Learning', 1),
(6, 'Life Hacking', 1),
(7, 'Other', 1);

-- --------------------------------------------------------

--
-- Table structure for table `ae_channel`
--

CREATE TABLE `ae_channel` (
  `id` int(11) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `icon` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `active` int(1) NOT NULL DEFAULT '0',
  `hide_from_user` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `ae_channel`
--

INSERT INTO `ae_channel` (`id`, `title`, `icon`, `description`, `active`, `hide_from_user`) VALUES
(1, 'email', 'email.png', '', 1, 0),
(2, 'skype', 'skype.png', '', 0, 0),
(3, 'twitter', 'twitter.png', '', 0, 0),
(4, 'sms', 'sms.png', '', 1, 0),
(5, 'calls', 'calls.png', '', 0, 0),
(6, 'Web only', 'email.png', '', 1, 0),
(7, 'push', 'sms.png', 'Push notifications to mobile devices', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `ae_channel_sent_sms`
--

CREATE TABLE `ae_channel_sent_sms` (
  `id` int(11) UNSIGNED NOT NULL,
  `playtask_id` int(11) UNSIGNED NOT NULL,
  `relay` varchar(255) NOT NULL,
  `msgid` varchar(255) NOT NULL,
  `statuscode` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ae_channel_setting`
--

CREATE TABLE `ae_channel_setting` (
  `id` int(11) UNSIGNED NOT NULL,
  `id_channel` int(11) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `type` varchar(255) DEFAULT 'text',
  `maxlength` smallint(4) NOT NULL DEFAULT '32',
  `options` text NOT NULL,
  `missing_msg` text NOT NULL,
  `default` text NOT NULL,
  `hint` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `ae_channel_setting`
--

INSERT INTO `ae_channel_setting` (`id`, `id_channel`, `title`, `type`, `maxlength`, `options`, `missing_msg`, `default`, `hint`) VALUES
(1, 1, 'email', 'text', 32, '', '', '', 'This is the email address where you receive game tasks & notifications. '),
(2, 1, 'format', 'listbox', 32, 'HTML;Text', '', 'HTML', 'We strongly recommend HTML!'),
(3, 2, 'skypeid', 'text', 32, '', '', '', ''),
(4, 3, 'twitterid', 'text', 32, '', '', '', ''),
(5, 3, 'tweet', 'checkbox', 32, '', '', '1', 'Do you want games to send you tweets?'),
(6, 3, 'message', 'checkbox', 32, '', '', '1', 'Do you want games to send you messages via Twitter?'),
(7, 4, 'smsphone', 'text', 32, '', '', '', ''),
(8, 5, 'callphone', 'text', 32, '', '', '', ''),
(9, 7, 'device_id', 'uneditable', 255, '', '', '', ''),
(10, 7, 'device_platform', 'dropdownlist', 32, 'iOS;Android;Web', '', '', ''),
(11, 7, 'active', 'checkbox', 32, '', '', '', 'Are push messages active or not?');

-- --------------------------------------------------------

--
-- Table structure for table `ae_channel_setting_user`
--

CREATE TABLE `ae_channel_setting_user` (
  `id` int(11) UNSIGNED NOT NULL,
  `id_user` int(11) UNSIGNED NOT NULL,
  `id_setting` int(11) UNSIGNED NOT NULL,
  `value` text NOT NULL,
  `ratelimit` int(3) NOT NULL DEFAULT '4',
  `lastcomm` int(8) NOT NULL,
  `lastcommday` int(8) NOT NULL,
  `commstoday` int(3) NOT NULL,
  `totalcomms` int(8) NOT NULL,
  `verified` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ae_channel_user`
--

CREATE TABLE `ae_channel_user` (
  `id_channel` int(11) UNSIGNED NOT NULL,
  `id_user` int(11) UNSIGNED NOT NULL,
  `settings` text NOT NULL,
  `status` int(1) NOT NULL,
  `alert` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `ae_channel_user`
--

INSERT INTO `ae_channel_user` (`id_channel`, `id_user`, `settings`, `status`, `alert`) VALUES
(1, 1749, '', 1, ''),
(1, 1750, '', 1, ''),
(4, 1749, '', 1, ''),
(4, 1750, '', 1, '');

-- --------------------------------------------------------

--
-- Table structure for table `ae_chat`
--

CREATE TABLE `ae_chat` (
  `id` int(11) UNSIGNED NOT NULL,
  `context_key` varchar(255) NOT NULL,
  `game_id` int(11) UNSIGNED NOT NULL,
  `owner_play_id` int(11) UNSIGNED DEFAULT NULL,
  `chat_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `type` varchar(40) NOT NULL DEFAULT 'default',
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `blocked` tinyint(1) NOT NULL DEFAULT '0',
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `category` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `tags` text NOT NULL,
  `can_invite` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ae_chat_attachments`
--

CREATE TABLE `ae_chat_attachments` (
  `id` int(11) UNSIGNED NOT NULL,
  `chat_message_id` int(11) UNSIGNED NOT NULL,
  `chat_attachment_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `chat_attachment_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ae_chat_messages`
--

CREATE TABLE `ae_chat_messages` (
  `id` int(11) UNSIGNED NOT NULL,
  `chat_id` int(11) UNSIGNED NOT NULL,
  `author_play_id` int(11) UNSIGNED DEFAULT NULL,
  `chat_message_text` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `chat_message_is_read` tinyint(4) NOT NULL,
  `chat_message_read_time` timestamp NULL DEFAULT NULL,
  `chat_message_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ae_chat_messages_likes`
--

CREATE TABLE `ae_chat_messages_likes` (
  `id` int(11) UNSIGNED NOT NULL,
  `play_id` int(11) UNSIGNED NOT NULL,
  `message_id` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ae_chat_users`
--

CREATE TABLE `ae_chat_users` (
  `id` int(11) UNSIGNED NOT NULL,
  `chat_id` int(11) UNSIGNED NOT NULL,
  `chat_user_play_id` int(11) UNSIGNED NOT NULL,
  `context` varchar(255) NOT NULL,
  `context_key` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ae_crons`
--

CREATE TABLE `ae_crons` (
  `id` int(11) UNSIGNED NOT NULL,
  `game_id` int(11) UNSIGNED NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `modelpath` varchar(255) NOT NULL,
  `model_name` varchar(255) NOT NULL,
  `function` varchar(255) NOT NULL,
  `frequency` varchar(255) NOT NULL DEFAULT 'minute',
  `last_run` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ae_crons_logs`
--

CREATE TABLE `ae_crons_logs` (
  `id` int(11) UNSIGNED NOT NULL,
  `cron_id` int(11) UNSIGNED NOT NULL,
  `timestamp` int(11) NOT NULL,
  `result` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_article`
--

CREATE TABLE `ae_ext_article` (
  `id` int(11) UNSIGNED NOT NULL,
  `app_id` int(11) UNSIGNED NOT NULL,
  `category_id` int(11) UNSIGNED DEFAULT NULL,
  `play_id` int(11) UNSIGNED DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `header` text NOT NULL,
  `content` text NOT NULL,
  `link` text NOT NULL,
  `rating` tinyint(1) NOT NULL,
  `featured` tinyint(1) NOT NULL DEFAULT '0',
  `article_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_article_bookmarks`
--

CREATE TABLE `ae_ext_article_bookmarks` (
  `id` int(11) UNSIGNED NOT NULL,
  `play_id` int(11) UNSIGNED NOT NULL,
  `article_id` int(11) UNSIGNED NOT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'bookmark'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_article_categories`
--

CREATE TABLE `ae_ext_article_categories` (
  `id` int(11) UNSIGNED NOT NULL,
  `app_id` int(14) UNSIGNED NOT NULL,
  `parent_id` int(11) UNSIGNED NOT NULL,
  `sorting` tinyint(3) NOT NULL,
  `title` varchar(255) NOT NULL,
  `headertext` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `picture` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_article_comments`
--

CREATE TABLE `ae_ext_article_comments` (
  `id` int(11) UNSIGNED NOT NULL,
  `play_id` int(11) UNSIGNED NOT NULL,
  `rating` tinyint(1) NOT NULL,
  `comment` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_article_photos`
--

CREATE TABLE `ae_ext_article_photos` (
  `id` int(11) UNSIGNED NOT NULL,
  `article_id` int(11) UNSIGNED NOT NULL,
  `photo` varchar(255) NOT NULL,
  `position` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_article_tags`
--

CREATE TABLE `ae_ext_article_tags` (
  `id` int(1) UNSIGNED NOT NULL,
  `app_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_bbs`
--

CREATE TABLE `ae_ext_bbs` (
  `id` int(11) UNSIGNED NOT NULL,
  `playtask_id` int(11) UNSIGNED NOT NULL,
  `date` datetime NOT NULL,
  `title` varchar(255) CHARACTER SET utf8 NOT NULL,
  `msg` text CHARACTER SET utf8 NOT NULL,
  `comment` text CHARACTER SET utf8 NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `parent_id` int(11) UNSIGNED NOT NULL,
  `admin_comment` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_bid_items`
--

CREATE TABLE `ae_ext_bid_items` (
  `id` int(11) UNSIGNED NOT NULL,
  `play_id` int(11) UNSIGNED NOT NULL,
  `title` varchar(50) NOT NULL,
  `description` varchar(255) NOT NULL,
  `styles` text,
  `valid_date` int(11) UNSIGNED NOT NULL,
  `status` varchar(10) NOT NULL DEFAULT 'open',
  `lat` decimal(11,8) NOT NULL,
  `lon` decimal(11,8) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_bookings`
--

CREATE TABLE `ae_ext_bookings` (
  `id` int(11) UNSIGNED NOT NULL,
  `play_id` int(11) UNSIGNED DEFAULT NULL,
  `assignee_play_id` int(11) UNSIGNED DEFAULT NULL,
  `item_id` int(11) UNSIGNED DEFAULT NULL,
  `date` int(11) NOT NULL,
  `length` int(11) NOT NULL,
  `notes` mediumtext NOT NULL,
  `status` varchar(60) NOT NULL,
  `price` int(11) NOT NULL,
  `lat` decimal(11,8) NOT NULL,
  `lon` decimal(11,8) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_calendar_entry`
--

CREATE TABLE `ae_ext_calendar_entry` (
  `id` int(11) UNSIGNED NOT NULL,
  `play_id` int(11) UNSIGNED NOT NULL,
  `type_id` int(11) UNSIGNED DEFAULT NULL,
  `exercise_id` int(11) UNSIGNED DEFAULT NULL,
  `program_id` int(11) UNSIGNED DEFAULT NULL,
  `recipe_id` int(11) UNSIGNED DEFAULT NULL,
  `notes` mediumint(9) DEFAULT NULL,
  `points` int(11) DEFAULT NULL,
  `time` int(11) UNSIGNED NOT NULL,
  `completion` smallint(3) DEFAULT NULL,
  `is_completed` tinyint(1) DEFAULT NULL,
  `completed_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_classifieds_categories`
--

CREATE TABLE `ae_ext_classifieds_categories` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET latin1 DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_classifieds_categories_items`
--

CREATE TABLE `ae_ext_classifieds_categories_items` (
  `id` int(11) UNSIGNED NOT NULL,
  `category_id` int(11) UNSIGNED NOT NULL,
  `item_id` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_classifieds_favourite_items`
--

CREATE TABLE `ae_ext_classifieds_favourite_items` (
  `id` int(10) UNSIGNED NOT NULL,
  `play_id` int(10) UNSIGNED NOT NULL,
  `item_id` int(10) UNSIGNED NOT NULL,
  `favourite` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_classifieds_filter`
--

CREATE TABLE `ae_ext_classifieds_filter` (
  `id` int(11) NOT NULL,
  `play_id` int(11) UNSIGNED NOT NULL,
  `categories` text COLLATE utf8mb4_unicode_ci,
  `distance` int(11) DEFAULT NULL,
  `price_min` int(11) DEFAULT NULL,
  `price_max` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_classifieds_items`
--

CREATE TABLE `ae_ext_classifieds_items` (
  `id` int(11) UNSIGNED NOT NULL,
  `category` varchar(100) CHARACTER SET latin1 NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `country` varchar(100) CHARACTER SET latin1 NOT NULL,
  `city` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` int(11) NOT NULL,
  `creator` int(11) NOT NULL,
  `pictures` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_classifieds_items_meta`
--

CREATE TABLE `ae_ext_classifieds_items_meta` (
  `id` int(11) UNSIGNED NOT NULL,
  `item_id` int(11) UNSIGNED NOT NULL,
  `key` varchar(100) CHARACTER SET latin1 NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_club_news`
--

CREATE TABLE `ae_ext_club_news` (
  `id` int(11) UNSIGNED NOT NULL,
  `club_id` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `photo` varchar(255) NOT NULL,
  `link_url` varchar(255) NOT NULL,
  `timestamp` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_deals_logs`
--

CREATE TABLE `ae_ext_deals_logs` (
  `id` int(11) UNSIGNED NOT NULL,
  `log_date` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `app_id` int(11) UNSIGNED NOT NULL,
  `total_deals_added` int(11) UNSIGNED NOT NULL,
  `added_deals_batch` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_deals_batch` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `deals_ids` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `comp_ids` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `updated_deals_data` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `log_data_sent` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_diary`
--

CREATE TABLE `ae_ext_diary` (
  `id` int(11) UNSIGNED NOT NULL,
  `playtask_id` int(11) UNSIGNED NOT NULL,
  `date` datetime NOT NULL,
  `title` varchar(255) CHARACTER SET utf8 NOT NULL,
  `msg` text CHARACTER SET utf8 NOT NULL,
  `comment` text CHARACTER SET utf8 NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `parent_id` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_fit_exercise`
--

CREATE TABLE `ae_ext_fit_exercise` (
  `id` int(11) UNSIGNED NOT NULL,
  `app_id` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8_bin NOT NULL,
  `article_id` int(11) UNSIGNED DEFAULT NULL,
  `points` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_fit_exercise_movement`
--

CREATE TABLE `ae_ext_fit_exercise_movement` (
  `id` int(11) UNSIGNED NOT NULL,
  `exercise_id` int(11) UNSIGNED NOT NULL,
  `movement_id` int(11) UNSIGNED NOT NULL,
  `movement_category_id` int(11) UNSIGNED NOT NULL,
  `weight` decimal(6,2) DEFAULT NULL,
  `reps` smallint(3) DEFAULT NULL,
  `rounds` smallint(3) DEFAULT NULL,
  `rest` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `points` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_fit_movement`
--

CREATE TABLE `ae_ext_fit_movement` (
  `id` int(11) UNSIGNED NOT NULL,
  `app_id` int(11) UNSIGNED NOT NULL,
  `article_id` int(11) UNSIGNED DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_fit_movement_category`
--

CREATE TABLE `ae_ext_fit_movement_category` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8_bin NOT NULL,
  `timer_type` varchar(255) COLLATE utf8_bin NOT NULL,
  `background_image` varchar(255) COLLATE utf8_bin DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `ae_ext_fit_movement_category`
--

INSERT INTO `ae_ext_fit_movement_category` (`id`, `name`, `timer_type`, `background_image`) VALUES
(1, 'Warmup', 'countdown', 'swiss8-exercise-bg-1.jpg'),
(2, 'Power', 'rest', 'swiss8-exercise-bg-2.jpg'),
(3, 'Conditioning', 'wod', 'swiss8-exercise-bg-3.jpg'),
(4, 'Test', 'wod', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_fit_pr`
--

CREATE TABLE `ae_ext_fit_pr` (
  `id` int(11) UNSIGNED NOT NULL,
  `app_id` int(11) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `unit` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_fit_program`
--

CREATE TABLE `ae_ext_fit_program` (
  `id` int(11) UNSIGNED NOT NULL,
  `app_id` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8_bin NOT NULL,
  `category_id` int(11) UNSIGNED DEFAULT NULL,
  `subcategory_id` int(11) UNSIGNED DEFAULT NULL,
  `article_id` int(11) UNSIGNED DEFAULT NULL,
  `program_type` varchar(255) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_fit_program_category`
--

CREATE TABLE `ae_ext_fit_program_category` (
  `id` int(11) UNSIGNED NOT NULL,
  `app_id` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8_bin NOT NULL,
  `icon` varchar(255) COLLATE utf8_bin NOT NULL,
  `color` char(7) COLLATE utf8_bin NOT NULL,
  `category_order` smallint(2) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_fit_program_exercise`
--

CREATE TABLE `ae_ext_fit_program_exercise` (
  `id` int(11) UNSIGNED NOT NULL,
  `program_id` int(11) UNSIGNED NOT NULL,
  `exercise_id` int(11) UNSIGNED NOT NULL,
  `week` smallint(3) DEFAULT NULL,
  `day` smallint(3) DEFAULT NULL,
  `priority` smallint(2) NOT NULL DEFAULT '0',
  `time` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `repeat_days` varchar(255) COLLATE utf8_bin DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_fit_program_recipe`
--

CREATE TABLE `ae_ext_fit_program_recipe` (
  `id` int(11) UNSIGNED NOT NULL,
  `program_id` int(11) UNSIGNED NOT NULL,
  `recipe_id` int(11) UNSIGNED NOT NULL,
  `week` smallint(3) DEFAULT NULL,
  `recipe_order` smallint(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_fit_program_selection`
--

CREATE TABLE `ae_ext_fit_program_selection` (
  `id` int(11) UNSIGNED NOT NULL,
  `play_id` int(11) UNSIGNED NOT NULL,
  `program_id` int(11) UNSIGNED NOT NULL,
  `program_type` varchar(255) NOT NULL,
  `start_time` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_fit_program_subcategory`
--

CREATE TABLE `ae_ext_fit_program_subcategory` (
  `id` int(11) UNSIGNED NOT NULL,
  `app_id` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `category_order` smallint(2) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_fit_pr_user`
--

CREATE TABLE `ae_ext_fit_pr_user` (
  `id` int(11) UNSIGNED NOT NULL,
  `play_id` int(11) UNSIGNED NOT NULL,
  `pr_id` int(11) UNSIGNED NOT NULL,
  `value` int(11) NOT NULL,
  `date` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_food_custom_ingredient`
--

CREATE TABLE `ae_ext_food_custom_ingredient` (
  `id` int(11) NOT NULL,
  `play_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `ingredient_category` int(11) UNSIGNED DEFAULT NULL,
  `date` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_food_ingredient`
--

CREATE TABLE `ae_ext_food_ingredient` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_bin NOT NULL,
  `unit` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `category_id` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `ae_ext_food_ingredient`
--

INSERT INTO `ae_ext_food_ingredient` (`id`, `name`, `unit`, `category_id`) VALUES
(1, 'Milk', 'l', 1),
(2, 'Lemon', 'kg', 2),
(3, 'Meat', 'kg', 3),
(4, 'Bread', NULL, 4),
(5, 'Tomatoes', 'kg', 2),
(6, 'Protein', 'g', 4),
(7, 'fresh fruit', 'kg', 3),
(8, 'vegetables', 'kg', 2),
(9, 'packed item', NULL, 4);

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_food_ingredient_category`
--

CREATE TABLE `ae_ext_food_ingredient_category` (
  `id` int(11) UNSIGNED NOT NULL,
  `app_id` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8_bin NOT NULL,
  `icon` varchar(255) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `ae_ext_food_ingredient_category`
--

INSERT INTO `ae_ext_food_ingredient_category` (`id`, `app_id`, `name`, `icon`) VALUES
(1, 8, 'Fridge', 'theme-icon-fridge.png'),
(2, 8, 'Fresh', 'theme-icon-plum.png'),
(3, 8, 'Frozen', 'theme-icon-snow.png'),
(4, 8, 'Dry', 'theme-icon-dry.png');

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_food_recipe`
--

CREATE TABLE `ae_ext_food_recipe` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8_bin NOT NULL,
  `difficult` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `serve` int(11) DEFAULT NULL,
  `type_id` int(11) NOT NULL,
  `photo` varchar(255) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_food_recipe_ingredient`
--

CREATE TABLE `ae_ext_food_recipe_ingredient` (
  `id` int(11) NOT NULL,
  `recipe_id` int(11) UNSIGNED NOT NULL,
  `ingredient_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_food_recipe_step`
--

CREATE TABLE `ae_ext_food_recipe_step` (
  `id` int(11) UNSIGNED NOT NULL,
  `recipe_id` int(11) UNSIGNED NOT NULL,
  `time` int(11) DEFAULT NULL,
  `description` text COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_food_recipe_type`
--

CREATE TABLE `ae_ext_food_recipe_type` (
  `id` int(11) NOT NULL,
  `app_id` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8_bin NOT NULL,
  `time_start` datetime NOT NULL,
  `time_end` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_food_shopping_list`
--

CREATE TABLE `ae_ext_food_shopping_list` (
  `id` int(11) NOT NULL,
  `play_id` int(11) UNSIGNED NOT NULL,
  `date_from` int(11) NOT NULL,
  `date_to` int(11) NOT NULL,
  `ingredient_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_gallery_images`
--

CREATE TABLE `ae_ext_gallery_images` (
  `id` int(11) UNSIGNED NOT NULL,
  `play_id` int(11) UNSIGNED NOT NULL,
  `image` varchar(255) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_golf_hole`
--

CREATE TABLE `ae_ext_golf_hole` (
  `id` int(11) UNSIGNED NOT NULL,
  `place_id` int(11) UNSIGNED DEFAULT NULL,
  `number` tinyint(2) NOT NULL,
  `par` tinyint(1) NOT NULL,
  `hcp` tinyint(2) NOT NULL,
  `type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tee_lat` decimal(11,8) NOT NULL,
  `tee_lon` decimal(11,8) NOT NULL,
  `flag_lat` decimal(11,8) NOT NULL,
  `flag_lon` decimal(11,8) NOT NULL,
  `beacon_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `length_pro` smallint(3) NOT NULL,
  `length_men` smallint(3) NOT NULL,
  `length_women` smallint(3) NOT NULL,
  `length_junior` smallint(3) NOT NULL,
  `map` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `map_approach` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `comments` text COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_golf_hole_user`
--

CREATE TABLE `ae_ext_golf_hole_user` (
  `id` int(11) UNSIGNED NOT NULL,
  `hole_id` int(11) UNSIGNED DEFAULT NULL,
  `event_id` int(11) UNSIGNED DEFAULT NULL,
  `play_id` int(11) UNSIGNED NOT NULL,
  `strokes` tinyint(2) NOT NULL,
  `comments` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `done` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_items`
--

CREATE TABLE `ae_ext_items` (
  `id` int(11) UNSIGNED NOT NULL,
  `play_id` int(11) UNSIGNED NOT NULL,
  `game_id` int(11) UNSIGNED NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `place_id` int(11) NOT NULL,
  `type` mediumtext NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `time` varchar(255) NOT NULL,
  `images` longtext NOT NULL,
  `date_added` int(11) NOT NULL,
  `status` varchar(255) NOT NULL,
  `featured` tinyint(1) NOT NULL DEFAULT '0',
  `external` tinyint(1) NOT NULL DEFAULT '0',
  `city` varchar(255) NOT NULL,
  `country` varchar(255) NOT NULL,
  `zip` varchar(255) NOT NULL,
  `lat` decimal(11,8) NOT NULL,
  `lon` decimal(11,8) NOT NULL,
  `buyer_play_id` int(11) UNSIGNED DEFAULT NULL,
  `source` varchar(255) NOT NULL,
  `importa_date` int(11) NOT NULL,
  `external_id` varchar(255) NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `extra_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_items_categories`
--

CREATE TABLE `ae_ext_items_categories` (
  `id` int(11) NOT NULL,
  `app_id` int(11) UNSIGNED NOT NULL,
  `parent_id` int(11) UNSIGNED NOT NULL,
  `picture` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` mediumtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_items_category_item`
--

CREATE TABLE `ae_ext_items_category_item` (
  `id` int(11) UNSIGNED NOT NULL,
  `item_id` int(11) UNSIGNED DEFAULT NULL,
  `category_id` int(11) NOT NULL,
  `description` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_items_filters`
--

CREATE TABLE `ae_ext_items_filters` (
  `id` int(11) UNSIGNED NOT NULL,
  `play_id` int(11) UNSIGNED NOT NULL,
  `category_id` int(11) UNSIGNED NOT NULL,
  `price_from` int(11) NOT NULL,
  `price_to` int(11) NOT NULL,
  `tags` longtext NOT NULL,
  `category` int(11) UNSIGNED NOT NULL,
  `categories` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_items_images`
--

CREATE TABLE `ae_ext_items_images` (
  `id` int(11) UNSIGNED NOT NULL,
  `item_id` int(11) UNSIGNED NOT NULL,
  `date` int(11) NOT NULL,
  `image` varchar(255) NOT NULL,
  `featured` tinyint(1) NOT NULL DEFAULT '0',
  `image_order` smallint(2) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_items_likes`
--

CREATE TABLE `ae_ext_items_likes` (
  `id` int(11) NOT NULL,
  `play_id` int(11) UNSIGNED NOT NULL,
  `item_id` int(11) UNSIGNED NOT NULL,
  `status` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_items_reminders`
--

CREATE TABLE `ae_ext_items_reminders` (
  `id` int(11) UNSIGNED NOT NULL,
  `item_id` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `message` varchar(255) NOT NULL,
  `type` varchar(100) NOT NULL,
  `date` int(11) NOT NULL,
  `recurring` tinyint(1) NOT NULL DEFAULT '0',
  `frequency` int(11) NOT NULL,
  `notification_sent` tinyint(1) NOT NULL,
  `to_calendar` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_items_reports`
--

CREATE TABLE `ae_ext_items_reports` (
  `id` int(11) UNSIGNED NOT NULL,
  `play_id` int(11) UNSIGNED NOT NULL,
  `item_id` int(11) UNSIGNED NOT NULL,
  `item_owner_id` int(11) UNSIGNED NOT NULL,
  `reason` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_items_tags`
--

CREATE TABLE `ae_ext_items_tags` (
  `id` int(11) UNSIGNED NOT NULL,
  `app_id` int(11) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_items_tag_item`
--

CREATE TABLE `ae_ext_items_tag_item` (
  `id` int(11) UNSIGNED NOT NULL,
  `item_id` int(11) UNSIGNED NOT NULL,
  `tag_id` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_meter`
--

CREATE TABLE `ae_ext_meter` (
  `id` int(10) UNSIGNED NOT NULL,
  `play_id` int(11) UNSIGNED NOT NULL,
  `meter_id` varchar(20) NOT NULL,
  `meter_description` varchar(255) NOT NULL,
  `lat` decimal(11,8) NOT NULL,
  `lon` decimal(11,8) NOT NULL,
  `online` tinyint(1) NOT NULL DEFAULT '0',
  `meter_name` varchar(255) NOT NULL,
  `capture_interval` int(8) NOT NULL,
  `current_usage` int(8) NOT NULL,
  `meter_type` varchar(255) NOT NULL,
  `last_update` int(11) NOT NULL,
  `current_usage_volts` int(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_meter_appliances`
--

CREATE TABLE `ae_ext_meter_appliances` (
  `id` int(11) UNSIGNED NOT NULL,
  `meter_id` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `manageable` tinyint(1) NOT NULL,
  `verified` tinyint(1) NOT NULL,
  `added` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_meter_data`
--

CREATE TABLE `ae_ext_meter_data` (
  `id` int(11) UNSIGNED NOT NULL,
  `meter_id` int(11) UNSIGNED NOT NULL,
  `capture_time` int(11) NOT NULL,
  `energy` decimal(5,2) NOT NULL,
  `voltage` int(5) NOT NULL,
  `power` int(5) NOT NULL,
  `current` int(11) NOT NULL,
  `capture_interval` int(8) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_mobilebeacons`
--

CREATE TABLE `ae_ext_mobilebeacons` (
  `id` int(11) UNSIGNED NOT NULL,
  `app_id` int(11) UNSIGNED NOT NULL,
  `place_id` int(11) UNSIGNED DEFAULT NULL,
  `identifier` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `region` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `minor` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `major` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lat` decimal(11,8) NOT NULL,
  `lon` decimal(11,8) NOT NULL,
  `brand` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `country` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `city` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `timezone` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `location_id` int(11) NOT NULL,
  `zipcode` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `state` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `state_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `street_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `street_number` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hardware_type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `color` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_mobileevents`
--

CREATE TABLE `ae_ext_mobileevents` (
  `id` int(11) UNSIGNED NOT NULL,
  `game_id` int(11) UNSIGNED NOT NULL,
  `play_id` int(11) UNSIGNED NOT NULL,
  `place_id` int(11) UNSIGNED DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `time_of_day` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notes` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `starting_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_mobileevents_participants`
--

CREATE TABLE `ae_ext_mobileevents_participants` (
  `id` int(11) UNSIGNED NOT NULL,
  `play_id` int(11) UNSIGNED NOT NULL,
  `event_id` int(11) UNSIGNED NOT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_mobilefeedbacktool`
--

CREATE TABLE `ae_ext_mobilefeedbacktool` (
  `id` int(11) UNSIGNED NOT NULL,
  `game_id` int(11) UNSIGNED NOT NULL,
  `author_id` int(11) UNSIGNED DEFAULT NULL,
  `recipient_id` int(11) UNSIGNED DEFAULT NULL,
  `requester_id` int(11) UNSIGNED NOT NULL,
  `department_id_sender` int(11) UNSIGNED NOT NULL,
  `department_id_recipient` int(11) UNSIGNED NOT NULL,
  `fundamentals_id` int(11) UNSIGNED NOT NULL,
  `pic` varchar(255) COLLATE utf32_swedish_ci NOT NULL,
  `subject` varchar(255) COLLATE utf32_swedish_ci NOT NULL,
  `message` text COLLATE utf32_swedish_ci NOT NULL,
  `excellent` text COLLATE utf32_swedish_ci NOT NULL,
  `to_maintain` text COLLATE utf32_swedish_ci NOT NULL,
  `to_change` text COLLATE utf32_swedish_ci NOT NULL,
  `rating` tinyint(2) NOT NULL,
  `comment` text COLLATE utf32_swedish_ci NOT NULL,
  `feedback_rating` tinyint(2) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `recipient_email` varchar(255) COLLATE utf32_swedish_ci NOT NULL,
  `msg_read` tinyint(4) NOT NULL,
  `comment_read` tinyint(4) NOT NULL,
  `request_comment_read` tinyint(1) NOT NULL DEFAULT '0',
  `pending_username` varchar(255) COLLATE utf32_swedish_ci NOT NULL,
  `pending_author_username` varchar(255) COLLATE utf32_swedish_ci NOT NULL,
  `is_request` tinyint(1) NOT NULL,
  `request_message` text COLLATE utf32_swedish_ci NOT NULL,
  `request_subject` varchar(255) COLLATE utf32_swedish_ci NOT NULL,
  `msg_archived` tinyint(1) NOT NULL DEFAULT '0',
  `comment_archived` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf32 COLLATE=utf32_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_mobilefeedbacktool_departments`
--

CREATE TABLE `ae_ext_mobilefeedbacktool_departments` (
  `id` int(11) UNSIGNED NOT NULL,
  `game_id` int(11) UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_mobilefeedbacktool_fundamentals`
--

CREATE TABLE `ae_ext_mobilefeedbacktool_fundamentals` (
  `id` int(11) UNSIGNED NOT NULL,
  `sorting` tinyint(2) NOT NULL,
  `game_id` int(11) UNSIGNED NOT NULL,
  `team_id` int(11) UNSIGNED DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_mobilefeedbacktool_teams`
--

CREATE TABLE `ae_ext_mobilefeedbacktool_teams` (
  `id` int(11) UNSIGNED NOT NULL,
  `game_id` int(11) UNSIGNED NOT NULL,
  `owner_id` int(11) UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `license` int(11) NOT NULL DEFAULT '8'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_mobilefeedbacktool_teams_members`
--

CREATE TABLE `ae_ext_mobilefeedbacktool_teams_members` (
  `id` int(11) UNSIGNED NOT NULL,
  `team_id` int(11) UNSIGNED NOT NULL,
  `play_id` int(11) UNSIGNED DEFAULT NULL,
  `role_type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `first_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'invited',
  `invite_code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_mobilematching`
--

CREATE TABLE `ae_ext_mobilematching` (
  `id` int(11) UNSIGNED NOT NULL,
  `game_id` int(11) UNSIGNED NOT NULL,
  `play_id` int(11) UNSIGNED NOT NULL,
  `lat` decimal(11,8) NOT NULL,
  `lon` decimal(11,8) NOT NULL,
  `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `gender` varchar(50) NOT NULL,
  `match_always` tinyint(1) NOT NULL DEFAULT '0',
  `score` tinyint(2) NOT NULL,
  `flag` tinyint(2) NOT NULL,
  `education` tinyint(2) NOT NULL,
  `hindu_caste` varchar(50) NOT NULL,
  `role` varchar(50) NOT NULL,
  `is_boosted` tinyint(1) NOT NULL,
  `boosted_timestamp` int(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_mobilematching_meta`
--

CREATE TABLE `ae_ext_mobilematching_meta` (
  `id` int(11) UNSIGNED NOT NULL,
  `play_id` int(11) UNSIGNED NOT NULL,
  `meta_key` text NOT NULL,
  `meta_value` text NOT NULL,
  `meta_limit` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_mobileplaces`
--

CREATE TABLE `ae_ext_mobileplaces` (
  `id` int(11) UNSIGNED NOT NULL,
  `game_id` int(11) UNSIGNED NOT NULL,
  `playid` int(11) NOT NULL,
  `lat` decimal(11,8) NOT NULL,
  `lon` decimal(11,8) NOT NULL,
  `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `type` varchar(122) NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `zip` int(11) NOT NULL,
  `city` varchar(255) NOT NULL,
  `county` varchar(255) NOT NULL DEFAULT 'FR',
  `country` varchar(255) NOT NULL,
  `info` text NOT NULL,
  `logo` varchar(255) NOT NULL DEFAULT 'dummylogo.png',
  `images` text NOT NULL,
  `premium` tinyint(1) NOT NULL,
  `headerimage1` varchar(255) NOT NULL,
  `headerimage2` varchar(255) NOT NULL,
  `headerimage3` varchar(255) NOT NULL,
  `import_date` int(11) NOT NULL,
  `code` varchar(255) NOT NULL,
  `hex_color` varchar(7) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_mobileproperty`
--

CREATE TABLE `ae_ext_mobileproperty` (
  `id` int(11) UNSIGNED NOT NULL,
  `game_id` int(11) UNSIGNED NOT NULL,
  `play_id` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `district` varchar(255) NOT NULL,
  `full_address` varchar(255) NOT NULL,
  `district_lat` decimal(11,8) NOT NULL,
  `district_lng` decimal(11,8) NOT NULL,
  `square_ft` int(11) NOT NULL,
  `square_meters` int(11) NOT NULL,
  `price_per_month` int(11) NOT NULL,
  `price_per_week` int(11) NOT NULL,
  `offer_code` varchar(255) NOT NULL,
  `description` mediumtext NOT NULL,
  `num_bedrooms` tinyint(4) NOT NULL,
  `num_bathrooms` tinyint(4) NOT NULL,
  `full_management` tinyint(1) NOT NULL,
  `tenancy_agreement` tinyint(1) NOT NULL,
  `reference_check` tinyint(1) NOT NULL,
  `active` tinyint(1) NOT NULL,
  `balcony_type` varchar(255) NOT NULL,
  `tenant_fee` int(11) NOT NULL DEFAULT '0',
  `images` text NOT NULL,
  `available_date` date NOT NULL,
  `feature_pets_allowed` tinyint(1) NOT NULL,
  `feature_lift` tinyint(1) NOT NULL,
  `feature_concierge` tinyint(1) NOT NULL,
  `feature_private_parking` tinyint(1) NOT NULL,
  `feature_students_welcome` tinyint(1) NOT NULL,
  `feature_bills_included` tinyint(1) NOT NULL,
  `feature_dish_washer` tinyint(1) NOT NULL,
  `feature_air_conditioner` tinyint(1) NOT NULL,
  `feature_washing_machine` tinyint(1) NOT NULL,
  `feature_unfurnished` tinyint(1) NOT NULL,
  `available` tinyint(1) NOT NULL DEFAULT '1',
  `furnished` tinyint(1) NOT NULL,
  `property_type` varchar(255) NOT NULL,
  `balcony_type_garden` tinyint(1) NOT NULL,
  `balcony_type_terrace` tinyint(1) NOT NULL,
  `balcony_type_balcony` tinyint(1) NOT NULL,
  `balcony_type_patio` tinyint(1) NOT NULL,
  `tenancy_option_shortlet` tinyint(1) NOT NULL,
  `tenancy_option_longlet` tinyint(1) NOT NULL,
  `xml_imported` tinyint(1) NOT NULL,
  `do_advertising` tinyint(4) NOT NULL,
  `is_premium` tinyint(4) NOT NULL,
  `feature_furnished` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_mobileproperty_bookmark`
--

CREATE TABLE `ae_ext_mobileproperty_bookmark` (
  `id` int(11) UNSIGNED NOT NULL,
  `play_id` int(11) UNSIGNED NOT NULL,
  `game_id` int(11) UNSIGNED NOT NULL,
  `mobileproperty_id` int(11) UNSIGNED NOT NULL,
  `status` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_mobileproperty_settings`
--

CREATE TABLE `ae_ext_mobileproperty_settings` (
  `id` int(11) NOT NULL,
  `play_id` int(11) UNSIGNED NOT NULL,
  `game_id` int(11) UNSIGNED NOT NULL,
  `from_num_bedrooms` tinyint(4) NOT NULL,
  `to_num_bedrooms` tinyint(4) NOT NULL,
  `from_price_per_month` int(11) NOT NULL,
  `to_price_per_month` int(11) NOT NULL,
  `property_type` varchar(255) DEFAULT NULL,
  `type_house` tinyint(1) NOT NULL,
  `type_flat` tinyint(1) NOT NULL,
  `type_room` tinyint(1) NOT NULL,
  `furnished` tinyint(1) DEFAULT NULL,
  `options_pets_allowed` tinyint(1) NOT NULL,
  `options_outside_spaces` int(1) NOT NULL,
  `districts` text NOT NULL,
  `filter_sq_ft` varchar(255) NOT NULL,
  `filter_price_per_week` varchar(255) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_mobileproperty_users`
--

CREATE TABLE `ae_ext_mobileproperty_users` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `agent_id` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_mtasks`
--

CREATE TABLE `ae_ext_mtasks` (
  `id` int(14) UNSIGNED NOT NULL,
  `owner_id` int(14) UNSIGNED NOT NULL,
  `invitation_id` int(11) UNSIGNED NOT NULL,
  `assignee_id` int(14) UNSIGNED NOT NULL,
  `category_id` int(14) UNSIGNED NOT NULL,
  `category_name` varchar(255) NOT NULL,
  `created_time` int(11) NOT NULL,
  `start_time` int(11) NOT NULL,
  `deadline` int(11) NOT NULL,
  `repeat_frequency` int(11) NOT NULL,
  `times_frequency` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `picture` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `completion` tinyint(1) NOT NULL,
  `comments` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_mtasks_invitations`
--

CREATE TABLE `ae_ext_mtasks_invitations` (
  `id` int(11) UNSIGNED NOT NULL,
  `play_id` int(11) UNSIGNED NOT NULL,
  `invited_play_id` int(11) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `nickname` varchar(255) NOT NULL,
  `primary_contact` tinyint(1) NOT NULL DEFAULT '0',
  `status` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_mtasks_proof`
--

CREATE TABLE `ae_ext_mtasks_proof` (
  `id` int(11) UNSIGNED NOT NULL,
  `task_id` int(11) UNSIGNED NOT NULL,
  `created_date` int(11) NOT NULL,
  `description` text NOT NULL,
  `comment` text NOT NULL,
  `status` varchar(100) NOT NULL DEFAULT 'proposed',
  `photo` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_notifications`
--

CREATE TABLE `ae_ext_notifications` (
  `id` int(11) UNSIGNED NOT NULL,
  `app_id` int(11) UNSIGNED NOT NULL,
  `task_id` int(11) UNSIGNED DEFAULT NULL,
  `notification_id` int(11) UNSIGNED DEFAULT NULL,
  `play_id_from` int(11) UNSIGNED DEFAULT NULL,
  `play_id_to` int(11) UNSIGNED DEFAULT NULL,
  `temporary_email` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `msg` text NOT NULL,
  `status` varchar(100) NOT NULL,
  `push` tinyint(1) NOT NULL,
  `email` tinyint(1) NOT NULL,
  `sms` tinyint(1) NOT NULL,
  `created_date` int(11) NOT NULL,
  `read_date` int(11) NOT NULL,
  `icon` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `action_id` int(11) UNSIGNED NOT NULL,
  `action_param` varchar(255) NOT NULL,
  `shorturl` varchar(255) NOT NULL,
  `type` varchar(80) NOT NULL,
  `item_id` int(11) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_photostream`
--

CREATE TABLE `ae_ext_photostream` (
  `id` int(11) UNSIGNED NOT NULL,
  `playtask_id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `parent_id` int(11) UNSIGNED NOT NULL,
  `date` datetime NOT NULL,
  `title` varchar(255) NOT NULL,
  `msg` text NOT NULL,
  `comment` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_photostream_post`
--

CREATE TABLE `ae_ext_photostream_post` (
  `id` int(11) UNSIGNED NOT NULL,
  `photostream_id` int(11) UNSIGNED NOT NULL,
  `parent_id` int(11) UNSIGNED NOT NULL,
  `date` datetime NOT NULL,
  `title` varchar(255) NOT NULL,
  `msg` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_products`
--

CREATE TABLE `ae_ext_products` (
  `id` int(11) UNSIGNED NOT NULL,
  `app_id` int(11) UNSIGNED NOT NULL,
  `category_id` int(11) UNSIGNED DEFAULT NULL,
  `play_id` int(11) UNSIGNED DEFAULT NULL,
  `amazon_product_id` varchar(255) NOT NULL,
  `photo` varchar(255) NOT NULL,
  `additional_photos` text NOT NULL,
  `title` varchar(255) NOT NULL,
  `header` text NOT NULL,
  `description` text NOT NULL,
  `link` text NOT NULL,
  `rating` tinyint(1) NOT NULL,
  `price` decimal(15,2) NOT NULL,
  `points_value` int(4) NOT NULL,
  `featured` tinyint(1) NOT NULL DEFAULT '0',
  `sorting` tinyint(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_products_bookmarks`
--

CREATE TABLE `ae_ext_products_bookmarks` (
  `id` int(11) UNSIGNED NOT NULL,
  `play_id` int(11) UNSIGNED NOT NULL,
  `product_id` int(11) UNSIGNED NOT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'bookmark'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_products_carts`
--

CREATE TABLE `ae_ext_products_carts` (
  `id` int(11) UNSIGNED NOT NULL,
  `play_id` int(11) UNSIGNED NOT NULL,
  `product_id` int(11) UNSIGNED NOT NULL,
  `task_id` int(11) UNSIGNED DEFAULT NULL,
  `date_added` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `cart_status` varchar(100) NOT NULL DEFAULT 'cart'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_products_categories`
--

CREATE TABLE `ae_ext_products_categories` (
  `id` int(11) UNSIGNED NOT NULL,
  `app_id` int(14) UNSIGNED NOT NULL,
  `sorting` tinyint(3) NOT NULL,
  `title` varchar(255) NOT NULL,
  `headertext` varchar(255) NOT NULL,
  `icon` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_products_photos`
--

CREATE TABLE `ae_ext_products_photos` (
  `id` int(11) UNSIGNED NOT NULL,
  `product_id` int(11) UNSIGNED NOT NULL,
  `photo` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_products_purchases`
--

CREATE TABLE `ae_ext_products_purchases` (
  `id` int(14) UNSIGNED NOT NULL,
  `play_id` int(14) UNSIGNED NOT NULL,
  `product_id` int(14) UNSIGNED NOT NULL,
  `date` int(11) UNSIGNED NOT NULL,
  `price` decimal(15,2) NOT NULL,
  `status` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_products_reviews`
--

CREATE TABLE `ae_ext_products_reviews` (
  `id` int(11) UNSIGNED NOT NULL,
  `play_id` int(11) UNSIGNED NOT NULL,
  `rating` tinyint(1) NOT NULL,
  `comment` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_products_tags`
--

CREATE TABLE `ae_ext_products_tags` (
  `id` int(1) UNSIGNED NOT NULL,
  `app_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_products_tags_products`
--

CREATE TABLE `ae_ext_products_tags_products` (
  `tag_id` int(14) UNSIGNED NOT NULL,
  `product_id` int(14) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_quiz`
--

CREATE TABLE `ae_ext_quiz` (
  `id` int(11) UNSIGNED NOT NULL,
  `app_id` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf16_unicode_ci NOT NULL,
  `description` mediumtext COLLATE utf16_unicode_ci NOT NULL,
  `valid_from` int(11) NOT NULL DEFAULT '0',
  `valid_to` int(11) NOT NULL DEFAULT '0',
  `active` tinyint(4) NOT NULL DEFAULT '1',
  `show_in_list` tinyint(1) NOT NULL DEFAULT '1',
  `image` varchar(255) COLLATE utf16_unicode_ci NOT NULL,
  `save_to_database` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf16 COLLATE=utf16_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_quiz_question`
--

CREATE TABLE `ae_ext_quiz_question` (
  `id` int(11) UNSIGNED NOT NULL,
  `app_id` int(11) UNSIGNED NOT NULL,
  `variable_name` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `question` mediumtext NOT NULL,
  `active` tinyint(1) NOT NULL,
  `picture` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'multiselect',
  `allow_multiple` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_quiz_question_answer`
--

CREATE TABLE `ae_ext_quiz_question_answer` (
  `id` int(11) UNSIGNED NOT NULL,
  `question_id` int(11) UNSIGNED NOT NULL,
  `answer_id` int(11) UNSIGNED NOT NULL,
  `answer` mediumtext COLLATE utf16_unicode_ci NOT NULL,
  `play_id` int(11) UNSIGNED NOT NULL,
  `comment` mediumtext COLLATE utf16_unicode_ci NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf16 COLLATE=utf16_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_quiz_question_option`
--

CREATE TABLE `ae_ext_quiz_question_option` (
  `id` int(11) UNSIGNED NOT NULL,
  `question_id` int(11) UNSIGNED NOT NULL,
  `answer` mediumtext COLLATE utf16_unicode_ci NOT NULL,
  `answer_order` int(4) NOT NULL,
  `is_correct` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf16 COLLATE=utf16_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_quiz_sets`
--

CREATE TABLE `ae_ext_quiz_sets` (
  `id` int(11) UNSIGNED NOT NULL,
  `app_id` int(11) UNSIGNED NOT NULL,
  `quiz_id` int(11) UNSIGNED NOT NULL,
  `question_id` int(11) UNSIGNED NOT NULL,
  `sorting` smallint(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_requests`
--

CREATE TABLE `ae_ext_requests` (
  `id` int(11) UNSIGNED NOT NULL,
  `requester_playid` int(11) UNSIGNED NOT NULL,
  `activity` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `location` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `tip` int(11) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `respondents` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `confirmed_respondent` int(11) DEFAULT NULL,
  `chat_initiated` int(11) NOT NULL,
  `accepter_action` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `accepter_rating` int(11) DEFAULT NULL,
  `requester_action` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `requester_rating` int(11) DEFAULT NULL,
  `sent_pushes` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `lat` decimal(11,8) NOT NULL,
  `lon` decimal(11,8) NOT NULL,
  `row_last_updated` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_request_payments`
--

CREATE TABLE `ae_ext_request_payments` (
  `id` int(11) UNSIGNED NOT NULL,
  `request_id` int(11) UNSIGNED NOT NULL,
  `sum_requester_before` int(11) NOT NULL,
  `sum_requester_after` int(11) NOT NULL,
  `sum_accepter_before` int(11) NOT NULL,
  `sum_accepter_after` int(11) NOT NULL,
  `sum_admin` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_tattoos`
--

CREATE TABLE `ae_ext_tattoos` (
  `id` int(11) UNSIGNED NOT NULL,
  `play_id` int(11) UNSIGNED NOT NULL,
  `game_id` int(11) UNSIGNED NOT NULL,
  `category_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `price` varchar(255) NOT NULL,
  `time` varchar(255) NOT NULL,
  `images` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_tattoos_bookings`
--

CREATE TABLE `ae_ext_tattoos_bookings` (
  `id` int(11) NOT NULL,
  `tattoo_id` int(11) NOT NULL,
  `tattooist_id` int(11) NOT NULL,
  `buyer_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `time` varchar(255) NOT NULL,
  `notes` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_tattoos_categories`
--

CREATE TABLE `ae_ext_tattoos_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_tattoos_likes`
--

CREATE TABLE `ae_ext_tattoos_likes` (
  `id` int(11) NOT NULL,
  `play_id` int(11) UNSIGNED NOT NULL,
  `tattoo_id` int(11) UNSIGNED NOT NULL,
  `status` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_tattoos_tags`
--

CREATE TABLE `ae_ext_tattoos_tags` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_tattoos_tag_tattoo`
--

CREATE TABLE `ae_ext_tattoos_tag_tattoo` (
  `id` int(11) UNSIGNED NOT NULL,
  `tattoo_id` int(11) UNSIGNED NOT NULL,
  `tag_id` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_user_bids`
--

CREATE TABLE `ae_ext_user_bids` (
  `id` int(11) UNSIGNED NOT NULL,
  `bid_item_id` int(11) UNSIGNED NOT NULL,
  `play_id` int(11) UNSIGNED NOT NULL,
  `price` decimal(10,0) NOT NULL,
  `message` varchar(255) NOT NULL,
  `created_date` int(11) NOT NULL,
  `status` varchar(10) NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_user_bid_item_images`
--

CREATE TABLE `ae_ext_user_bid_item_images` (
  `id` int(11) UNSIGNED NOT NULL,
  `bid_item_id` int(11) UNSIGNED NOT NULL,
  `image` varchar(255) NOT NULL,
  `weight` int(11) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_wallet`
--

CREATE TABLE `ae_ext_wallet` (
  `id` int(11) UNSIGNED NOT NULL,
  `play_id` int(11) UNSIGNED DEFAULT NULL,
  `funds_raw` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ae_ext_wallet_logs`
--

CREATE TABLE `ae_ext_wallet_logs` (
  `id` int(11) UNSIGNED NOT NULL,
  `play_id` int(11) UNSIGNED DEFAULT NULL,
  `funds` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `payed` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ae_fbfriend`
--

CREATE TABLE `ae_fbfriend` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `fbid` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ae_fbfriend_user`
--

CREATE TABLE `ae_fbfriend_user` (
  `user_id` int(11) UNSIGNED NOT NULL,
  `ae_fbfriend_id` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ae_fb_invite`
--

CREATE TABLE `ae_fb_invite` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `fb_request` varchar(255) NOT NULL,
  `fb_invites` text NOT NULL,
  `playtask_id` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ae_fieldtype`
--

CREATE TABLE `ae_fieldtype` (
  `fieldtype` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `ae_fieldtype`
--

INSERT INTO `ae_fieldtype` (`fieldtype`) VALUES
('appendtext'),
('captcha'),
('checkbox'),
('checkboxinline'),
('checkboxlist'),
('date'),
('daterange'),
('disabledcheckbox'),
('dropdownlist'),
('email'),
('file'),
('hidden'),
('listbox'),
('markdowneditor'),
('password'),
('prependtext'),
('radio'),
('radiolist'),
('redactor'),
('select2'),
('selectlist'),
('terms'),
('text'),
('textarea'),
('uneditable'),
('url');

-- --------------------------------------------------------

--
-- Table structure for table `ae_filenames`
--

CREATE TABLE `ae_filenames` (
  `id` int(14) NOT NULL,
  `game_id` int(11) UNSIGNED NOT NULL,
  `action_id` int(10) UNSIGNED NOT NULL,
  `sha1` varchar(255) CHARACTER SET utf8 COLLATE utf8_swedish_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_swedish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ae_game`
--

CREATE TABLE `ae_game` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED DEFAULT NULL,
  `category_id` int(11) UNSIGNED DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `active` int(11) DEFAULT NULL,
  `icon` varchar(255) NOT NULL DEFAULT 'new.png',
  `headboard_portrait` varchar(255) NOT NULL DEFAULT 'changeme.jpg',
  `headboard_landscape` varchar(255) NOT NULL DEFAULT 'changeme.jpg',
  `background_image_landscape` varchar(255) NOT NULL,
  `background_image_portrait` varchar(255) NOT NULL,
  `logo` varchar(255) NOT NULL,
  `length` varchar(255) NOT NULL,
  `timelimit` int(11) DEFAULT NULL,
  `description` text NOT NULL,
  `levels` smallint(2) NOT NULL,
  `alert` varchar(255) NOT NULL,
  `featured` tinyint(1) NOT NULL DEFAULT '0',
  `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `max_actions` int(5) NOT NULL DEFAULT '9999',
  `custom_css` varchar(255) NOT NULL,
  `show_toplist` tinyint(1) NOT NULL DEFAULT '0',
  `register_email` tinyint(1) NOT NULL DEFAULT '1',
  `register_sms` tinyint(1) NOT NULL DEFAULT '1',
  `start_without_registration` tinyint(1) NOT NULL DEFAULT '1',
  `show_homepage` tinyint(1) NOT NULL DEFAULT '0',
  `choose_playername` tinyint(1) NOT NULL DEFAULT '0',
  `choose_avatar` tinyint(1) NOT NULL DEFAULT '0',
  `shorturl` varchar(255) NOT NULL,
  `custom_domain` varchar(255) NOT NULL,
  `home_instructions` text NOT NULL,
  `notifyme` int(11) DEFAULT NULL,
  `skin` varchar(255) NOT NULL DEFAULT 'modern',
  `show_logo` tinyint(1) NOT NULL DEFAULT '0',
  `show_social` tinyint(1) NOT NULL DEFAULT '0',
  `social_share_url` varchar(255) NOT NULL,
  `social_share_description` varchar(255) NOT NULL,
  `social_force_to_canvas_url` varchar(255) NOT NULL,
  `app_fb_hash` varchar(255) NOT NULL,
  `custom_colors` int(1) NOT NULL DEFAULT '0',
  `colors` text NOT NULL,
  `show_branches` tinyint(1) NOT NULL DEFAULT '1',
  `api_key` varchar(255) NOT NULL,
  `api_secret_key` varchar(255) NOT NULL,
  `api_callback_url` varchar(255) NOT NULL,
  `api_application_id` varchar(255) NOT NULL,
  `api_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `keen_api_enabled` tinyint(1) NOT NULL,
  `keen_api_master_key` varchar(255) NOT NULL,
  `keen_api_write_key` text NOT NULL,
  `keen_api_read_key` text NOT NULL,
  `keen_api_config` text NOT NULL,
  `google_api_enabled` tinyint(1) NOT NULL,
  `google_api_code` varchar(255) NOT NULL,
  `google_api_config` text NOT NULL,
  `fb_api_enabled` tinyint(1) NOT NULL,
  `fb_api_id` varchar(255) NOT NULL,
  `fb_api_secret` varchar(255) NOT NULL,
  `fb_invite_points` int(10) UNSIGNED NOT NULL,
  `hide_points` tinyint(1) NOT NULL DEFAULT '0',
  `game_password` varchar(120) NOT NULL,
  `nickname_variable_id` int(11) UNSIGNED NOT NULL,
  `show_toplist_points` int(11) UNSIGNED NOT NULL,
  `show_toplist_entries` int(11) UNSIGNED NOT NULL,
  `profilepic_variable_id` int(11) UNSIGNED NOT NULL,
  `notifications_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `cookie_lifetime` int(11) NOT NULL DEFAULT '0',
  `notification_config` text NOT NULL,
  `perm_can_reset` tinyint(1) NOT NULL DEFAULT '1',
  `perm_can_delete` tinyint(1) NOT NULL DEFAULT '1',
  `lang_show` tinyint(1) NOT NULL DEFAULT '0',
  `lang_default` varchar(2) NOT NULL DEFAULT 'en',
  `secondary_points` tinyint(1) NOT NULL,
  `secondary_points_title` varchar(255) NOT NULL,
  `tertiary_points` tinyint(1) NOT NULL,
  `tertiary_points_title` varchar(255) NOT NULL,
  `primary_points_shortname` varchar(255) NOT NULL,
  `primary_points_title` varchar(255) NOT NULL,
  `secondary_points_shortname` varchar(255) NOT NULL,
  `tertiary_points_shortname` varchar(255) NOT NULL,
  `icon_primary_points` varchar(255) NOT NULL DEFAULT 'star.png',
  `icon_secondary_points` varchar(255) NOT NULL DEFAULT 'xp.png',
  `icon_tertiary_points` varchar(255) NOT NULL DEFAULT 'heart.png',
  `template` tinyint(1) NOT NULL,
  `game_wide` tinyint(1) NOT NULL DEFAULT '0',
  `asset_migration` tinyint(1) NOT NULL DEFAULT '0',
  `thirdparty_api_config` text NOT NULL,
  `auth_config` text NOT NULL,
  `visual_config` text NOT NULL,
  `visual_config_params` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ae_game_badge`
--

CREATE TABLE `ae_game_badge` (
  `id` int(11) UNSIGNED NOT NULL,
  `game_id` int(11) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `color` varchar(255) NOT NULL,
  `icon` varchar(255) NOT NULL,
  `trigger_id` int(11) NOT NULL,
  `trigger_value` varchar(255) NOT NULL,
  `trigger_config` text NOT NULL,
  `show_initially` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ae_game_branch`
--

CREATE TABLE `ae_game_branch` (
  `id` int(11) UNSIGNED NOT NULL,
  `game_id` int(11) UNSIGNED NOT NULL,
  `trigger_id` int(11) UNSIGNED DEFAULT '11',
  `channel_id` int(11) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `order` smallint(3) NOT NULL DEFAULT '0',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `template` tinyint(1) NOT NULL,
  `trigger_value` text NOT NULL,
  `exclude_from_listing` tinyint(1) NOT NULL DEFAULT '0',
  `agressive_loading` tinyint(1) NOT NULL DEFAULT '0',
  `trigger_config` text NOT NULL,
  `description` text NOT NULL,
  `reset_other_actions` tinyint(1) NOT NULL DEFAULT '0',
  `timelimit` int(11) NOT NULL,
  `branch_id_aftertime` int(11) UNSIGNED NOT NULL,
  `visibility_in_listing` varchar(255) NOT NULL,
  `icon` varchar(255) NOT NULL,
  `image_portrait` varchar(255) NOT NULL,
  `background_image_portrait` varchar(255) NOT NULL,
  `image_landscape` varchar(255) NOT NULL,
  `background_image_landscape` varchar(255) NOT NULL,
  `menu_image` varchar(255) NOT NULL,
  `action_list` int(1) NOT NULL,
  `custom_colors` int(1) NOT NULL,
  `colors` text NOT NULL,
  `import_id` int(14) NOT NULL,
  `infinite` tinyint(1) NOT NULL DEFAULT '0',
  `random_order` tinyint(1) NOT NULL DEFAULT '0',
  `hide_menubar` tinyint(1) NOT NULL,
  `hide_menubar_mainmenu` tinyint(1) NOT NULL,
  `config` text NOT NULL,
  `asset_loading` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ae_game_branch_action`
--

CREATE TABLE `ae_game_branch_action` (
  `id` int(10) UNSIGNED NOT NULL,
  `branch_id` int(11) UNSIGNED NOT NULL,
  `channel_id` int(11) UNSIGNED DEFAULT NULL,
  `parent_id` int(11) UNSIGNED DEFAULT NULL,
  `trigger_id` int(11) UNSIGNED DEFAULT '7',
  `type_id` int(11) UNSIGNED DEFAULT NULL,
  `role_id` int(11) UNSIGNED DEFAULT NULL,
  `pointsystem_id` int(11) UNSIGNED NOT NULL,
  `secondary_points` int(11) NOT NULL DEFAULT '0',
  `tertiary_points` smallint(6) NOT NULL,
  `timelimit` time NOT NULL DEFAULT '00:00:00',
  `trigger_time` time NOT NULL DEFAULT '00:00:00',
  `trigger_config` text NOT NULL,
  `component_config` text NOT NULL,
  `order` smallint(3) UNSIGNED NOT NULL DEFAULT '1',
  `name` varchar(255) CHARACTER SET utf8 NOT NULL,
  `permaname` varchar(255) NOT NULL,
  `config` longtext NOT NULL,
  `points` smallint(6) NOT NULL DEFAULT '5',
  `negative_points` smallint(6) NOT NULL DEFAULT '0',
  `requires_points` int(11) DEFAULT '0',
  `requires_role` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `active` int(1) NOT NULL DEFAULT '0',
  `introtext` text NOT NULL,
  `trigger_value` text NOT NULL,
  `timebomb` time NOT NULL DEFAULT '00:00:00',
  `repeat` varchar(255) NOT NULL DEFAULT '0',
  `repeat_value` int(11) NOT NULL DEFAULT '1',
  `activate_branch_id` varchar(255) NOT NULL,
  `tomenu` int(11) UNSIGNED NOT NULL,
  `buttontxt` varchar(255) CHARACTER SET utf8 NOT NULL,
  `remains_visible` tinyint(1) NOT NULL DEFAULT '0',
  `editable` tinyint(1) NOT NULL DEFAULT '0',
  `pill_name` varchar(255) NOT NULL,
  `commenting` tinyint(1) NOT NULL DEFAULT '0',
  `commenting_brief` text NOT NULL,
  `add_time` smallint(6) NOT NULL,
  `subtract_time` smallint(6) NOT NULL,
  `custom_colors` tinyint(1) NOT NULL DEFAULT '0',
  `colors` text NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ae_game_branch_action_type`
--

CREATE TABLE `ae_game_branch_action_type` (
  `id` int(11) UNSIGNED NOT NULL,
  `title` text NOT NULL,
  `icon` varchar(255) NOT NULL DEFAULT 'new.png',
  `shortname` varchar(255) NOT NULL,
  `id_user` int(11) UNSIGNED DEFAULT NULL,
  `id_sshkey` int(11) UNSIGNED NOT NULL,
  `description` text NOT NULL,
  `version` varchar(4) NOT NULL,
  `channels` text NOT NULL,
  `uiformat` varchar(255) NOT NULL,
  `active` tinyint(1) NOT NULL,
  `global` tinyint(1) NOT NULL,
  `githubrepo` text NOT NULL,
  `adminfeedback` text NOT NULL,
  `requestupdate` tinyint(1) NOT NULL,
  `uses_table` tinyint(1) NOT NULL,
  `has_statistics` tinyint(1) NOT NULL,
  `has_export` tinyint(1) NOT NULL,
  `invisible` tinyint(1) NOT NULL,
  `hide_from_api` tinyint(1) NOT NULL,
  `ios_supports` tinyint(1) NOT NULL DEFAULT '0',
  `android_supports` tinyint(1) NOT NULL DEFAULT '0',
  `web_supports` tinyint(1) NOT NULL DEFAULT '0',
  `article_view` tinyint(1) NOT NULL DEFAULT '0',
  `library` varchar(255) NOT NULL DEFAULT 'PHP2'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `ae_game_branch_action_type`
--

INSERT INTO `ae_game_branch_action_type` (`id`, `title`, `icon`, `shortname`, `id_user`, `id_sshkey`, `description`, `version`, `channels`, `uiformat`, `active`, `global`, `githubrepo`, `adminfeedback`, `requestupdate`, `uses_table`, `has_statistics`, `has_export`, `invisible`, `hide_from_api`, `ios_supports`, `android_supports`, `web_supports`, `article_view`, `library`) VALUES
(2, 'Mobile & Web Multiselect', 'accordion.png', 'multiselect', 1, 0, '<p></p><p></p><p>Multiselect action allow you to prompt the user to answer a multiple choice question. Answer can be images and you can have multiple correct asnwers giving different points</p>\r\n', '0.8', '', 'HTML 5', 1, 1, 'https://github.com/activationengine/actionextension-message', '', 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 'builtin'),
(7, 'Web Feeling', 'emo4.png', 'feeling', 1, 0, '<p></p><p>The Feeling Meeter action is Likert Scale . With it users can indicate their feelings, satisfaction and other things</p>\r\n', '0.1', '', 'HTML 5', 1, 1, '', '', 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 'PHP1'),
(8, 'Mobile & Web Static message', 'align_middle.png', 'staticmsg', 1, 0, '<p></p><p>This works same way as message, except that there is no way to complete this task (you can make it expire though). Also the background of the task is different.</p>\r\n', '0.1', '', 'HTML 5', 1, 1, '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 'builtin'),
(10, 'Web GPS Tracker', 'new.png', 'gpstracker', 1, 0, '<p></p><p></p><p>Uses phone\'s location to define either certain GPS target coordinates or certain distance that needs to be moved.</p>\r\n', '0.1', '', 'HTML 5', 1, 1, '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 'PHP1'),
(11, 'Web Essay', 'barchart.png', 'essay', 1, 0, '<p></p><p>Essay action will allow user to input long text with rich media inside it. Later this text (essay) can be reviewed by admin and awarded certain points. </p>\r\n', '0.1', '', 'HTML 5', 0, 1, '', '', 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 'PHP1'),
(12, 'Forward', 'forward.png', 'forward', 1, 0, '<p>Forward action is kind of as JUMP TO functionality. It will transport the user to some other branch or action, not requiring him to complete any previous actions.</p>', '0.1', '', '{%HTML_five%}', 1, 1, '', '', 0, 0, 0, 0, 1, 0, 1, 1, 1, 0, 'PHP1'),
(13, 'Web Achievement', 'barchart.png', 'achievement', 1, 0, '<p>This action is shown as a \"diploma\" in right column and have possibility to share it to Facebook & Twitter. </p>', '0.1', '', 'HTML 5', 1, 1, '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 'PHP1'),
(15, 'Mobile & Web URL', 'application_link.png', 'url', 1, 0, '<p>Forwards user directly to another url.&nbsp;</p>', '0.1', '', 'HTML 5', 1, 1, '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 'builtin'),
(16, 'Web Diary', 'application_form.png', 'diary', 1, 0, '<p></p><p>Let\'s you add a diary component.</p>\r\n', '0.1', '', 'HTML 5', 1, 1, '', '', 0, 0, 1, 1, 0, 0, 0, 0, 0, 0, 'PHP1'),
(17, 'Web BBS (Bulletin Board)', 'comments.png', 'bbs', 1, 0, '<p></p><p>Bulletin Board action is a discussion board. All players can participate there if the action is active for them.&nbsp;</p>\r\n', '0.1', '', 'HTML 5', 1, 1, '', '', 0, 0, 1, 1, 0, 0, 0, 0, 0, 0, 'PHP1'),
(18, 'Web Profile Collector', 'administrator.png', 'profile', 1, 0, '<p>This action can collect users profile information, including name, email (login), password, phone, time zone, profile picture and two custom defined variables (text field or file).&nbsp;</p>', '0.1', '', 'HTML 5', 1, 1, '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 'PHP1'),
(21, 'Web Logic Game 2048', 'board_game.png', 'numbersgame', 1, 0, '<p>This is a simple but highly addictive numbers based logic game called 2048.&nbsp;</p>', '0.1', '', 'HTML 5', 1, 1, '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 'PHP1'),
(27, 'Mobile & Web Multiselect with images', 'accordion.png', 'multiselectimg', 1, 0, '<p></p><p>Description</p> ', '0.8', '', 'HTML 5', 1, 1, 'https://github.com/activationengine/actionextension-message', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 'builtin'),
(30, 'Logic Reset App', 'alarm_bell.png', 'resetgame', 1, 0, '<p><b>Careful with this one!</b> If this action triggers, it will empty the whole game from the player and move player to the beginning of the game. You have been warned.</p>', '0.1', '', 'HTML 5', 1, 1, '', '', 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 'builtin'),
(31, 'Web Photostream', 'comments.png', 'photostream', 1, 0, '<p></p><p></p><p>Create a shared photostream with a possibility of sending individual photos to a predefined address.</p>\r\n', '0.1', '', 'HTML 5', 1, 1, '', '', 0, 0, 1, 1, 0, 0, 0, 0, 0, 0, 'PHP1'),
(33, 'Feeling Meter Slider', 'emo4.png', 'feelingslider', 1, 0, '<p>This will ask for users feeling with a slider. Answer gets saved to a game variable .</p>', '0.1', '', 'HTML 5', 1, 1, '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 'PHP1'),
(34, 'Json List', 'key_l.png', 'jsonlist', 1, 0, '<p></p><p>This action will import any json feed &amp; parse it into a nice looking list. Item page can include description, image, sound, video. Configuration is slightly complicated.</p>\r\n', '1', '', 'HTML 5', 0, 1, '', '', 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 'builtin'),
(41, 'Mobile Raw JSON (article)', 'accordion.png', 'articleview', 1, 0, '<p>This is a multi-layout action. Needs manual configuration.</p>', '0.1', '', '{%HTML_five%}', 1, 1, '', '', 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 'PHP1'),
(42, 'Mobile chat', 'group.png', 'mobilechat', 1, 0, '<p>This adds a mobile chat to your app. Control the visibility using action triggers. Everyone seeing the chat will join the same discussion. Set variables for name and photo. No web support for this.</p>', '0.1', '', '{%HTML_five%}', 1, 1, '', '', 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 'PHP1'),
(43, 'Mobile Recipe', 'calendar_view_day.png', 'recipe', 1, 0, '<p>This is a native component, visualising food recipes.</p>', '0.1', '', '{%HTML_five%}', 1, 1, '', '', 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 'PHP1'),
(45, 'Mobile Registration', 'calendar_view_day.png', 'mobileregister', 1, 0, '<p>This is a native component, adding a registration option.</p>', '0.1', '', '{%HTML_five%}', 0, 1, '', '', 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 'PHP1'),
(47, 'Mobile Gallery', 'calendar_view_day.png', 'mobilegallery', 1, 0, '<p>This is a native component, allowing user to submit image with a comment. An action will be created of the user submission and placed inside a branch of your choosing. Action has three modes, one for submitting, one for the gallery and one for showing the picture.</p>', '0.1', '', '{%HTML_five%}', 1, 1, '', '', 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 'PHP1'),
(48, 'Mobile Profile', 'calendar_view_day.png', 'mobileprofile', 1, 0, '<p>This is a native component, adding a profile page.</p>', '0.1', '', '{%HTML_five%}', 1, 1, '', '', 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 'PHP1'),
(50, 'Mobile Image Viewer', 'calendar_view_day.png', 'mobileimageview', 1, 0, '<p>This is a native component, showing a gallery entry dynamically.</p>', '0.1', '', '{%HTML_five%}', 1, 1, '', '', 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 'PHP1'),
(51, 'Mobile Instagram Gallery', 'picture.png', 'mobileinstagramgallery', 1, 0, '<p>A very nice gallery action.</p>', '0.1', '', '{%HTML_five%}', 1, 0, '', '', 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 'PHP1'),
(52, 'Mobile List Appointments', 'application_form_edit.png', 'listappointments', 1, 0, '<p>An action for listing future appointments.</p>', '0.1', '', '{%HTML_five%}', 1, 1, '', '', 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 'PHP1'),
(54, 'Mobile Preferences', 'node-tree.png', 'mobilepreferences', 1, 0, '<p>This is a general preferences action for mobile. You can define&nbsp;which variables can be set.&nbsp;</p>', '0.1', '', '{%HTML_five%}', 1, 1, '', '', 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 'PHP1'),
(55, 'Mobile Cookify Home', 'key_h.png', 'cookifyhome', 1, 0, '<p>This is a special home screen action for Cookify applications.&nbsp;</p>', '0.1', '', '{%HTML_five%}', 1, 1, '', '', 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 'PHP1'),
(56, 'Mobile Registration', 'application_form_edit.png', 'mobileregister2', 1, 0, '<p>An action for listing future appointments.</p>', '0.1', '', '{%HTML_five%}', 1, 1, '', '', 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 'PHP1'),
(57, 'Mobile Booking', 'application_form_edit.png', 'mobilebooking', 1, 0, '<p>An action for creating a booking.</p>', '0.1', '', '{%HTML_five%}', 1, 1, '', '', 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 'PHP1'),
(58, 'Mobile Login', 'application_form.png', 'mobilelogin', 1, 0, '<p>This will provide login option for your app.</p>', '0.1', '', '{%HTML_five%}', 1, 1, '', '', 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 'PHP1'),
(59, 'Mobile User Profile', 'administrator.png', 'mobileuserprofile', 1, 0, '<p>Show user information.</p>', '0.1', '', '{%HTML_five%}', 1, 1, '', '', 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 'PHP1'),
(60, 'Mobile Matching', 'arrow_switch.png', 'mobilematching', 1, 0, '<p>Mobile matching of users / items.</p>', '0.1', '', '{%HTML_five%}', 1, 1, '', '', 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 'PHP1'),
(61, 'Mobile Location', 'yelp.png', 'mobilelocation', 1, 0, '<p>Define / update users location information.</p>', '0.1', '', '{%HTML_five%}', 1, 1, '', '', 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 'PHP1'),
(62, 'Mobile User List', 'client_account_template.png', 'mobileuserlist', 1, 0, '<p>Shows a list of users (can be based on criteria)</p>', '0.1', '', '{%HTML_five%}', 1, 1, '', '', 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 'PHP1'),
(63, 'Mobile Invite Friends', 'email_to_friend.png', 'mobileinvite', 1, 0, '<p>Invite friends</p>', '0.1', '', '{%HTML_five%}', 1, 1, '', '', 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 'PHP1'),
(64, 'Mobile Deal Viewer', 'text_align_justity.png', 'mobiledealviewer', 1, 0, '<p>Deal Dataholder</p>', '0.1', '', '{%HTML_five%}', 1, 1, '', '', 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 'PHP1'),
(65, 'Mobile Deals', 'text_list_numbers.png', 'mobiledeals', 1, 0, '<p>An action, used for displaying deals</p>', '0.1', '', '{%HTML_five%}', 1, 1, '', '', 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 'PHP1'),
(66, 'Mobile Point Status', 'ruby_go.png', 'mobilepointstatus', 1, 0, '<p>Shows users points & game progress.</p>', '0.1', '', '{%HTML_five%}', 1, 1, '', '', 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 'PHP1'),
(69, 'Mobile Deals Categories', 'text_list_numbers.png', 'mobiledealscategories', 1, 0, '<p>An action, which allows syncing with a mobile deals provider</p>', '0.1', '', '{%HTML_five%}', 1, 1, '', '', 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 'PHP1'),
(70, 'Mobile Quiz', 'dopplr.png', 'mobilequiz', 1, 0, '<p>This is a main menu for mobile quiz app.</p>', '0.1', '', '{%HTML_five%}', 1, 1, '', '', 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 'PHP1'),
(71, 'Mobile Gatekeeper', 'virus_protection.png', 'mobilegate', 1, 0, '<p>You can set a \"gate\" to branch, which will require users to have a certain number of points before they can continue.</p>', '0.1', '', '{%HTML_five%}', 1, 1, '', '', 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 'PHP1'),
(72, 'Mobile Rewarding', 'card_gold.png', 'mobilerewarding', 1, 0, '<p>You can give user points with several different ways.&nbsp;</p>', '0.1', '', '{%HTML_five%}', 1, 1, '', '', 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 'PHP1'),
(73, 'Mobile Webview', 'align_center.png', 'webview', 1, 0, '<p>Webview. Can be either simple html or an url to include inside the app. Notice that webview load slower than native components and can\'t be cached.&nbsp;</p>', '1', '', '{%HTML_five%}', 1, 1, '', '', 0, 0, 0, 0, 0, 0, 1, 1, 0, 0, 'PHP1'),
(74, 'Mobile Feedback', 'node-tree.png', 'mobilefeedback', 1, 0, '<p>This is an action, which would allow users to send feedback to an email address.</p>', '0.1', '', '{%HTML_five%}', 1, 1, '', '', 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 'PHP1'),
(75, 'Mobile Form', 'application_form_edit.png', 'mobileform', 1, 0, '<p>Simple form builder.</p>', '0.1', '', '{%HTML_five%}', 1, 1, '', '', 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 'PHP1'),
(77, 'Mobile Debug', 'advanced_data_grid.png', 'mobiledebug', 1, 0, '<p>This is a very simple action showing debug information. Used for development purposes.</p>', '0.1', '', '{%HTML_five%}', 1, 1, '', '', 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 'PHP1'),
(78, 'Mobile Dates', 'email_to_friend.png', 'mobiledates', 1, 0, '<p>Manage your dates</p>', '0.1', '', '{%HTML_five%}', 1, 1, '', '', 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 'PHP1'),
(79, 'Mobile Language Selector', 'application_cascade.png', 'mobilelanguage', 1, 0, '<p>This will show a language selector and set the language for the client. Configure the languages from the mobile settings.&nbsp;</p>', '0.1', '', '{%HTML_five%}', 1, 1, '', '', 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 'PHP1'),
(80, 'Mobile Rate App', 'email_to_friend.png', 'mobilerateapp', 1, 0, '<p>Rate the current app</p>', '0.1', '', '{%HTML_five%}', 1, 1, '', '', 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 'PHP1'),
(81, 'Mobile Admin', 'change_password.png', 'mobileadmin', 1, 0, '<p>This adds administration functionalities that are usable through app. Search for users, see statistics etc.&nbsp;</p>', '0.1', '', '{%HTML_five%}', 1, 1, '', '', 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 'PHP1'),
(82, 'Mobile App Settings', 'application_form_edit.png', 'mobileappsettings', 1, 0, '<p>Provides a different ( theme based ) app settings.</p>', '0.1', '', '{%HTML_five%}', 1, 1, '', '', 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 'PHP1'),
(83, 'Mobile Instagram Login', 'camera_small.png', 'mobileinstagramlogin', 1, 0, '<p>Provides a login for instagram.</p>', '0.1', '', '{%HTML_five%}', 1, 1, '', '', 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 'PHP1'),
(84, 'Mobile Beacon Admin', 'bluetooth.png', 'mobilebeaconadmin', 1, 0, '<p>This action will let you administer your beacons and create geographic regions which the app is monitoring.</p>', '0.1', '', '{%HTML_five%}', 1, 1, '', '', 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 'PHP1'),
(85, 'Mobile Availability', 'application_view_columns.png', 'mobileavailability', 1, 0, '<p>Adds a simple availability calendar.</p>', '0.1', '', '{%HTML_five%}', 1, 1, '', '', 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 'PHP1'),
(86, 'Mobile Places', 'check_box.png', 'mobileplaces', 1, 0, '<p>Adds a place search where user can also save her own places.&nbsp;</p>', '0.1', '', '{%HTML_five%}', 1, 1, '', '', 0, 0, 1, 0, 0, 0, 1, 1, 0, 1, 'PHP1'),
(87, 'Mobile oAuth Connector', 'brick_link.png', 'mobileoauth', 1, 0, '<p>This will allow user to login & register using another oAuth provider, including another Appzio app.</p>', '0.1', '', '{%HTML_five%}', 1, 1, '', '', 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 'PHP1'),
(88, 'Mobile Events', 'date_add.png', 'mobileevents', 1, 0, '<p>This allows to create events where to invite people to.</p>', '0.1', '', '{%HTML_five%}', 1, 1, '', '', 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 'PHP1'),
(89, 'Mobile Golf', 'flag_yellow.png', 'mobilegolf', 1, 0, '<p>Lets you record golf rounds.</p>', '0.1', '', '{%HTML_five%}', 1, 1, '', '', 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 'PHP1'),
(90, 'Mobile Missing Details', 'application_form_add.png', 'mobilemissingdetails', 1, 0, '<p>A simple action, which would ask for certain missing variables</p>', '0.1', '', '{%HTML_five%}', 1, 1, '', '', 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 'PHP1'),
(91, 'Mobile Feedback Tool', '3d_glasses.png', 'mobilefeedbacktool', 1, 0, '<p>This is a comprehensive home action for a feedback system with some gamification elements.</p>', '0.1', '', '{%HTML_five%}', 1, 1, '', '', 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 'PHP1'),
(92, 'Mobile Feedback Reporting', 'chart_curve.png', 'mobilefeedbackreports', 1, 0, '<p>Reporting module for feedbacks.&nbsp;</p>', '0.1', '', '{%HTML_five%}', 1, 0, '', '', 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 'PHP1'),
(93, 'Mobile Search', 'magnifier.png', 'mobilesearch', 1, 0, '<p>Search for different content types. Currently supports users and chats.</p>', '0.1', '', '{%HTML_five%}', 1, 1, '', '', 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 'PHP1'),
(94, 'Mobile Category Search', 'camera_lens.png', 'mobilecategorysearch', 1, 0, '<p>This is to be coupled with mobile matching to provide a search.</p>', '0.1', '', '{%HTML_five%}', 1, 1, '', '', 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 'PHP1'),
(95, 'Mobile OauthConnector', 'door_in.png', 'oauthconnector', 1, 0, '<p>This is a web based oAuth provider.</p>', '0.1', '', '{%HTML_five%}', 1, 1, '', '', 0, 0, 0, 0, 0, 0, 1, 1, 1, 0, 'PHP1'),
(96, 'Mobile Properties', 'bank.png', 'mobileproperties', 1, 0, '<p>Properties listing and CRUD operations</p>', '0.1', '', '{%HTML_five%}', 1, 1, '', '', 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 'PHP1'),
(97, 'Mobile Team Admin', 'client_account_template.png', 'mobileteamadmin', 1, 0, '<p>Team administration (add, invite, manage)</p>', '0.1', '', '{%HTML_five%}', 1, 1, '', '', 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 'PHP1'),
(98, 'Mobile Intro', 'application_go.png', 'mobileintro', 1, 0, '<p>Simple intro action with a swipable content.&nbsp;Image on top (recommended size is 750x480) and text on the bottom.&nbsp;</p>', '0.1', '', '{%HTML_five%}', 1, 1, '', '', 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 'PHP1'),
(99, 'Mobile Helpers', 'zoom.png', 'mobilehelpers', 1, 0, '<p>A multipurpose action, designed to show different helpers in a popup.</p>', '0.1', '', '{%HTML_five%}', 1, 1, '', '', 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 'PHP1'),
(100, 'Mobile Menus', 'application_side_tree.png', 'mobilemenus', 1, 0, '<p>A multipurpose action, designed to handle the custom app menus.</p>', '0.1', '', '{%HTML_five%}', 1, 1, '', '', 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 'PHP1'),
(101, 'Mobile Version Updater', 'brick_add.png', 'mobileversionupdate', 1, 0, '<p>This will show a prompt for updating the version.</p>', '0.1', '', '{%HTML_five%}', 1, 1, '', '', 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 'PHP1'),
(102, 'Mobile Register 2', 'new.png', 'mregister', 1, 0, '<p>New bootstrap 2 using mobile register</p>', '1', '', 'native', 1, 1, '', '', 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 'PHP2'),
(103, 'Mobile Notifications', 'tag_blue_add.png', 'mnotifications', 1, 0, '<p>This handles showing notifications inside the app. Also provides helpers for sending notifications between users, sending invitations etc.</p>', '1', '', 'native', 1, 1, '', '', 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 'PHP2'),
(104, 'Mobile User Settings 2', 'user.png', 'musersettings', 1, 0, '<p>Settings for user giving access to editing selected variables. Editable variables should be defined as a list in the action\'s config. This is using the Appzio&nbsp;PHP Library 2.</p>', '1', '', 'native', 1, 1, '', '', 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 'PHP2'),
(105, 'Mobile Rating', 'new.png', 'mobilerating', 1, 0, '<p>New bootstrap 2 app rating action</p>', '1', '', 'native', 1, 1, '', '', 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 'PHP2'),
(106, 'Mobile Subscription', 'card_apple.png', 'msubscription', 1, 0, '<p>Adds an in-app subscription module.</p>', '1', '', 'native', 1, 1, '', '', 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 'PHP2'),
(107, 'Mobile Tattoos', 'new.png', 'mtattoos', 1, 0, '<p>New bootstrap 2 tattoos action</p>', '1', '', 'native', 1, 1, '', '', 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 'PHP2'),
(108, 'Mobile Swipematch', 'angel.png', 'mswipematch', 1, 0, '<p>Bootstrap 2 matching action</p>', '0.1', '', '{%HTML_five%}', 1, 1, '', '', 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 'PHP2'),
(109, 'Mobile Classfieds', 'application_view_columns.png', 'mobileclassifieds', 1, 0, '<p>Classfieds action</p>', '1', '', 'native', 1, 1, '', '', 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 'PHP2'),
(110, 'Mobile Items', 'new.png', 'mitems', 1, 0, '<p>Generic items action for Bootstrap2</p>', '1', '', 'native', 1, 1, '', '', 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 'PHP2'),
(111, 'Mobile Booking', 'new.png', 'mbooking', 1, 0, '<p>Bootstrap 2 booking action</p>', '1', '', 'native', 1, 1, '', '', 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 'PHP2'),
(112, 'Mobile Menus ( B2 )', 'application_form_edit.png', 'mmenus', 1, 0, '<p>Creates in-app navigation items</p>', '1', '', 'native', 1, 1, '', '', 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 'PHP2'),
(114, 'Mobile Articles', 'application_form_edit.png', 'marticles', 1, 0, '<p>Multi-purpose articles listing action</p>', '1', '', 'native', 1, 1, '', '', 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 'PHP2'),
(115, 'Mobile Maps', 'menu.png', 'mmaps', NULL, 0, 'Mobile maps action', '1', '', 'native', 1, 1, '', '', 0, 0, 0, 0, 0, 0, 1, 1, 0, 0, 'PHP2'),
(117, 'Mobile Login 2', 'group_key.png', 'mlogin', 1, 0, '<p>Bootstrap 2 mobile login action</p>', '1', '', 'native', 1, 1, '', '', 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 'PHP2'),
(120, 'Mobile Venues', 'menu.png', 'mvenues', NULL, 0, 'Venues', '1', '', 'native', 1, 1, '', '', 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 'PHP2'),
(121, 'Quizz', 'menu.png', 'Mquiz', NULL, 0, 'Quiz module', '1', '', 'native', 1, 1, '', '', 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 'PHP2'),
(122, 'Shopping', 'menu.png', 'mshopping', NULL, 0, 'General purpose shopping module', '1', '', 'native', 1, 1, '', '', 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 'PHP2'),
(123, 'Leaderboard', 'menu.png', 'mleaderboard', NULL, 0, 'Leaderboard', '1', '', 'native', 1, 1, '', '', 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 'PHP2'),
(124, 'Gallery', 'gallery.png', 'mgallery', NULL, 0, 'General purpose gallery module', '1', '', 'native', 1, 1, '', '', 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 'PHP2'),
(125, 'Mobile Messaging', 'messenger.png', 'mmessaging', NULL, 0, 'General purpose chat/messaging module', '1', '', 'native', 1, 1, '', '', 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 'PHP2'),
(126, 'Mobile GDPR', 'page_paste.png', 'mgdpr', NULL, 0, 'GDPR module allows user to email saved info about them & delete the user.', '1', '', 'native', 1, 1, '', '', 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 'PHP2'),
(127, 'Mobile Calendar', 'calendar_add.png', 'mcalendar', 1, 0, '<p>Calendar module</p>', '1', '', 'native', 1, 1, '', '', 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 'PHP2'),
(128, 'Mobile Example', 'new.png', 'mexample', 1, 0, '<p>Example module</p>', '1', '', 'native', 1, 1, '', '', 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 'PHP2'),
(129, 'Fitness Programs', 'menu.png', 'Mfitness', 1, 0, '<p>Fitness programs</p>', '1', '', 'native', 1, 1, '', '', 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 'PHP2'),
(130, 'Nexudus Connector', 'menu.png', 'Mnexudus', 1, 0, '<p>Action for connecting with Nexudus booking system.</p>', '1', '', 'native', 1, 1, '', '', 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 'PHP2'),
(131, 'Mobile Food', 'menu.png', 'mfood', NULL, 0, 'General purpose action for recipes and more', '1', '', 'native', 1, 1, '', '', 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 'PHP2'),
(132, 'Mobile Intro', 'menu.png', 'Mintro', 1, 0, '<p></p>', '1', '', 'native', 1, 1, '', '', 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 'PHP2');

-- --------------------------------------------------------

--
-- Table structure for table `ae_game_keyvaluestorage`
--

CREATE TABLE `ae_game_keyvaluestorage` (
  `id` int(11) UNSIGNED NOT NULL,
  `game_id` int(11) UNSIGNED NOT NULL,
  `key` varchar(100) NOT NULL,
  `value` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ae_game_level`
--

CREATE TABLE `ae_game_level` (
  `id` int(11) UNSIGNED NOT NULL,
  `game_id` int(11) UNSIGNED NOT NULL,
  `pointsystem_id` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `points_start` int(11) UNSIGNED NOT NULL,
  `points_finish` int(11) UNSIGNED NOT NULL,
  `description` text NOT NULL,
  `completion_msg` text NOT NULL,
  `icon` varchar(255) NOT NULL DEFAULT 'new.png'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ae_game_menu`
--

CREATE TABLE `ae_game_menu` (
  `id` int(11) UNSIGNED NOT NULL,
  `game_id` int(11) NOT NULL,
  `order` tinyint(3) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `config` text NOT NULL,
  `icon` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `menuname` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ae_game_play`
--

CREATE TABLE `ae_game_play` (
  `id` int(11) UNSIGNED NOT NULL,
  `role_id` int(11) UNSIGNED DEFAULT NULL,
  `game_id` int(11) UNSIGNED DEFAULT NULL,
  `user_id` int(11) UNSIGNED DEFAULT NULL,
  `last_update` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `progress` smallint(2) NOT NULL DEFAULT '0',
  `status` smallint(2) NOT NULL DEFAULT '1',
  `alert` varchar(255) NOT NULL,
  `level` smallint(2) NOT NULL DEFAULT '1',
  `current_level_id` int(10) UNSIGNED NOT NULL,
  `priority` tinyint(1) NOT NULL DEFAULT '1',
  `branch_starttime` int(11) NOT NULL,
  `last_action_update` int(11) NOT NULL,
  `autogenerated_styles` mediumtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ae_game_play_action`
--

CREATE TABLE `ae_game_play_action` (
  `id` int(11) UNSIGNED NOT NULL,
  `action_id` int(11) UNSIGNED NOT NULL,
  `play_id` int(11) UNSIGNED NOT NULL,
  `trigger_id` int(11) UNSIGNED DEFAULT NULL,
  `user_id` int(11) UNSIGNED DEFAULT NULL,
  `id_userchannelsetting` int(11) DEFAULT NULL,
  `added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `time_updated` int(15) NOT NULL,
  `time_lastcomm` int(15) NOT NULL,
  `time_opened` int(11) NOT NULL,
  `time_last_opened` int(11) NOT NULL,
  `time_showed_as_reached` int(11) NOT NULL,
  `status` int(1) NOT NULL DEFAULT '1',
  `status_processed` int(1) NOT NULL DEFAULT '0',
  `shorturl` varchar(255) NOT NULL,
  `points` smallint(6) NOT NULL,
  `secondary_points` smallint(6) NOT NULL,
  `tertiary_points` smallint(6) NOT NULL,
  `time_completed` int(15) NOT NULL,
  `global` int(1) NOT NULL DEFAULT '0',
  `repeat_count` int(11) NOT NULL DEFAULT '0',
  `expires` int(15) DEFAULT NULL,
  `essay_status` int(1) NOT NULL,
  `comment` text NOT NULL,
  `newcount` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ae_game_play_branch`
--

CREATE TABLE `ae_game_play_branch` (
  `play_id` int(11) UNSIGNED NOT NULL,
  `branch_id` int(11) UNSIGNED NOT NULL,
  `played` int(1) NOT NULL DEFAULT '0',
  `order` int(11) NOT NULL,
  `updated` int(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ae_game_play_datastorage`
--

CREATE TABLE `ae_game_play_datastorage` (
  `id` int(11) UNSIGNED NOT NULL,
  `play_id` int(11) UNSIGNED NOT NULL,
  `key` varchar(100) NOT NULL,
  `filename` varchar(100) NOT NULL,
  `label` varchar(100) NOT NULL,
  `value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ae_game_play_keyvaluestorage`
--

CREATE TABLE `ae_game_play_keyvaluestorage` (
  `id` int(11) UNSIGNED NOT NULL,
  `play_id` int(11) UNSIGNED NOT NULL,
  `key` varchar(100) NOT NULL,
  `value` int(11) UNSIGNED NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ae_game_play_role`
--

CREATE TABLE `ae_game_play_role` (
  `play_id` int(11) UNSIGNED NOT NULL,
  `role_id` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ae_game_play_user`
--

CREATE TABLE `ae_game_play_user` (
  `id` int(11) UNSIGNED NOT NULL,
  `id_user` int(11) UNSIGNED DEFAULT NULL,
  `id_play` int(11) UNSIGNED DEFAULT NULL,
  `id_role` int(11) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ae_game_play_variable`
--

CREATE TABLE `ae_game_play_variable` (
  `id` int(11) UNSIGNED NOT NULL,
  `play_id` int(11) UNSIGNED DEFAULT NULL,
  `variable_id` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `parameters` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ae_game_rate`
--

CREATE TABLE `ae_game_rate` (
  `id` int(11) UNSIGNED NOT NULL,
  `game_id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `rate` tinyint(3) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ae_game_role`
--

CREATE TABLE `ae_game_role` (
  `id` int(11) UNSIGNED NOT NULL,
  `game_id` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ae_game_score`
--

CREATE TABLE `ae_game_score` (
  `id` int(11) UNSIGNED NOT NULL,
  `game_id` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `icon` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ae_game_trigger`
--

CREATE TABLE `ae_game_trigger` (
  `id` int(11) UNSIGNED NOT NULL,
  `id_user` int(11) UNSIGNED DEFAULT NULL,
  `alert` tinyint(1) NOT NULL,
  `alert_text` text NOT NULL,
  `name` varchar(255) NOT NULL,
  `shortname` varchar(255) NOT NULL,
  `unit` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `context_action` tinyint(1) NOT NULL DEFAULT '0',
  `context_global` tinyint(1) NOT NULL DEFAULT '0',
  `context_level` tinyint(1) NOT NULL DEFAULT '0',
  `context_component` tinyint(1) NOT NULL DEFAULT '0',
  `config` text NOT NULL,
  `global` tinyint(1) NOT NULL,
  `request_update` tinyint(1) NOT NULL,
  `icon` varchar(255) NOT NULL DEFAULT 'new.png',
  `active` tinyint(4) NOT NULL DEFAULT '1',
  `description` text NOT NULL,
  `version` varchar(255) NOT NULL,
  `script` text NOT NULL,
  `value_hint` text NOT NULL,
  `setupfile` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `ae_game_trigger`
--

INSERT INTO `ae_game_trigger` (`id`, `id_user`, `alert`, `alert_text`, `name`, `shortname`, `unit`, `title`, `context_action`, `context_global`, `context_level`, `context_component`, `config`, `global`, `request_update`, `icon`, `active`, `description`, `version`, `script`, `value_hint`, `setupfile`) VALUES
(1, 1, 0, '', 'points', 'points', '{%points%}', 'Game points', 1, 1, 1, 1, '', 1, 0, 'cricket.png', 1, '<p></p><p>Trigger activates when user has a set number of points in the entire game.</p>\r\n', '0.1', '/* the following variables are available:\r\n   $this->created // creation time\r\n   $this->tasktimeleft\r\n   $this->gameplay->points\r\n   $this->gameplay->points\r\n*/\r\n\r\nif($this->gameplay->points > $this->trigger value){\r\n  return true;\r\n}', 'Define how many points user should have (in the entire game!)', '0'),
(2, 1, 0, '', 'time', 'starttime', 'min', 'Time-game-start', 1, 1, 0, 0, '', 1, 0, 'clock_15.png', 1, '<p>Time since the game has started.</p>', '0.1', 'if(time() > $this->created+$this->trigger){\r\n   return true;\r\n}\r\n', 'DD:HH:MM (days, hours, minutes since game has started)', '0'),
(7, 1, 0, '', '', 'previouscompleted', '', 'When previous action is completed', 1, 0, 0, 0, '', 1, 0, 'accept.png', 1, '<p></p><p></p><p>Choose a parent action &amp; set the time for possible delay (time is defined as HH:MM:SS for example to set five minutes, define 00:05:00 on the trigger value field.</p>\r\n', '0.1', '/* the following variables are available:\r\n   $this->created // creation time\r\n   $this->tasktimeleft\r\n   $this->gameplay->points\r\n   $this->gameplay->points\r\n*/\r\n\r\nif(time() > $this->created+$this->trigger){\r\n  return true;\r\n}\r\n', '(DD:HH:MM) time, after completing the parent task this trigger is activated.', '0'),
(8, 1, 0, '', '', 'noactiveactions', '', 'No active actions', 1, 1, 0, 0, '', 1, 0, 'new.png', 1, '<p>If player has no active actions, activate this.</p>', '0.1', '', '', '0'),
(9, 1, 0, '', '', 'levelpoints', '', 'Branch Points', 1, 0, 0, 0, '', 1, 0, 'numeric_stepper.png', 1, '<p></p><p>Activate if user has this number or more points WITHIN the active branch.</p>\r\n', '0.1', '', '', '0'),
(10, 1, 0, '', '', 'everyday', '', 'Every day', 0, 1, 0, 0, '', 1, 0, 'clock_add.png', 1, '<p>This trigger will launch every day</p>', '0.1', '', 'Specify the time of the day when this should launch (HH:MM, for example 22:59).', ''),
(11, 1, 0, '', '', 'previousbranchcomplete', '', 'When previous branch is completed', 0, 0, 1, 0, '', 1, 0, 'convert_gray_to_color.png', 1, '<p></p><p>This activates branch when previous branch is completed. NOTE: All actions from previous branch have to be completed!</p>\r\n', '0.1', '', '', '0'),
(13, 1, 0, '', '', 'variable', '', 'On Variable Value', 1, 1, 1, 1, '', 1, 0, 'new.png', 1, '<p>This trigger will launch based on global variable value.&nbsp;</p>', '0.1', '', '', '1'),
(14, 1, 0, '', '', 'parentcompleted', '', 'When parent task completes', 1, 0, 0, 1, '', 1, 0, 'new.png', 1, '<p>Triggers when parent task is completed.</p>', '0.1', '', 'DD:HH:MM (triggering with delay, days, hours, minutes since parent task is completed).', ''),
(15, 1, 0, '', '', 'inactiveplayer', '', 'Inactive player', 1, 1, 0, 0, '', 1, 0, 'new.png', 1, '<p></p><p>Trigger which launches If player has been inactive for some time (can be defined as minutes, hours or days as `DD:HH:MM` format)</p>', '0.1', '', 'DD:HH:MM (It launches If player has been inactive for some time: days, hours, minutes).', '0'),
(16, 1, 0, '', '', 'playercurrentlevel', '', 'Player current level', 1, 0, 0, 0, '', 0, 0, 'new.png', 1, '<p></p><p>Trigger based on players current level</p>', '0.1', '', 'Trigger based on players current level.', '0'),
(17, 1, 0, '', '', 'branchactive', '', 'On Branch Active', 1, 0, 0, 0, '', 1, 0, 'new.png', 1, '<p>This will trigger when the host branch in itself becomes active.&nbsp;</p>', '0.1', '', '', '0'),
(18, 1, 0, '', '', 'alwayson', '', 'Always on', 0, 0, 1, 1, '', 1, 0, 'emotion_adore.png', 1, '<p>Branch will be shown as long as there are active actions inside it.&nbsp;</p>', '0.1', '', '', '0'),
(19, 1, 0, '', '', 'fbconnected', '', 'Facebook connected', 0, 0, 1, 0, '', 0, 0, 'comment_facebook.png', 1, '<p>Triggers if user has connected with Facebook.</p>', '0.1', '', '', '0'),
(20, 1, 0, '', '', 'fbnotconnected', '', 'Facebook not connected', 0, 0, 1, 0, '', 0, 0, 'comments_facebook.png', 1, '<p>Triggers if user doesn\'t have Facebook connected.</p>', '0.1', '', '', '0'),
(21, 1, 0, '', '', 'fbconnected', '', 'Facebook connected', 0, 0, 1, 0, '', 0, 0, 'comment_facebook.png', 1, '<p>Triggers if user has connected with Facebook.</p>', '0.1', '', '', '0'),
(22, 1, 0, '', '', 'fbnotconnected', '', 'Facebook not connected', 0, 0, 1, 0, '', 0, 0, 'comments_facebook.png', 1, '<p>Triggers if user doesn\'t have Facebook connected.</p>', '0.1', '', '', '0'),
(23, 1, 0, '', '', 'alwaysoff', '', 'Always Off', 1, 0, 1, 0, '', 1, 0, 'delete.png', 1, '<p>Content should never be triggered. Useful for storage branches etc.</p>', '0.1', '', '', '0'),
(24, 1, 0, '', '', 'androidversion', '', 'Android version', 0, 0, 1, 0, '', 1, 0, 'ax.png', 1, '<p>This will trigger based on Android client\'s version. You can use this either force or suggest to update version. If you want client with version smaller than 1.7.1 to see this branch, set trigger value to 1.7. Ie. its smaller than and will trigger on smaller versions.</p>', '0.1', '', '', '0'),
(25, 1, 0, '', '', 'iosversion', '', 'iOS Version', 0, 0, 1, 0, '', 1, 0, 'events.png', 1, '<p>You can trigger based on iOS version. This will be useful for prompting version updates.&nbsp;If you want client with version smaller than 1.7.1 to see this branch, set trigger value to 1.7. Ie. its smaller than and will trigger on smaller versions.</p>', '0.1', '', '', '0'),
(26, 1, 0, '', '', 'secondarypoints', '', 'Game Secondary Points', 1, 0, 1, 0, '', 1, 0, 'http_status_permanent.png', 1, '<p>This triggers based on secondary points.</p>', '0.1', '', '', '0'),
(27, 1, 0, '', '', 'beacon', '', 'Beacon', 1, 0, 1, 0, '', 1, 0, 'bluetooth.png', 1, '<p>Triggers at a proximity of a beacon. This will launch the corresponding branch OR action. If you want to trigger actions, the actions need to be inside branch called \"Beacons\"&nbsp;and this branch need to be set to always on. On the trigger value, define either the beacon ID or areaid:beaconid.&nbsp;</p>', '0.1', '', '', '1');

-- --------------------------------------------------------

--
-- Table structure for table `ae_game_variable`
--

CREATE TABLE `ae_game_variable` (
  `id` int(11) UNSIGNED NOT NULL,
  `game_id` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 NOT NULL,
  `used_by_actions` text NOT NULL,
  `set_on_players` int(11) NOT NULL,
  `value_type` varbinary(255) NOT NULL DEFAULT 'text'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ae_invitation`
--

CREATE TABLE `ae_invitation` (
  `id` int(11) UNSIGNED NOT NULL,
  `username` varchar(255) NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `play_id` int(11) UNSIGNED NOT NULL,
  `game_id` int(11) UNSIGNED NOT NULL,
  `type` varchar(255) NOT NULL,
  `reminder_interval` int(11) NOT NULL,
  `reminder_repeat` int(2) NOT NULL,
  `msg` text NOT NULL,
  `msg_reminder` text NOT NULL,
  `variables` text NOT NULL,
  `invitation_channel` varchar(255) NOT NULL,
  `reminder_channel` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `invitation_sent` int(11) NOT NULL,
  `reminder_sent` int(8) NOT NULL,
  `reminder_count` int(2) NOT NULL,
  `status` varchar(255) NOT NULL,
  `shorturl` varchar(60) NOT NULL,
  `subject` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ae_location_log`
--

CREATE TABLE `ae_location_log` (
  `id` int(11) UNSIGNED NOT NULL,
  `game_id` int(11) UNSIGNED NOT NULL,
  `play_id` int(11) UNSIGNED NOT NULL,
  `beacon_id` int(11) UNSIGNED DEFAULT NULL,
  `place_id` int(11) UNSIGNED DEFAULT NULL,
  `lat` decimal(10,8) NOT NULL,
  `lon` decimal(10,8) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ae_locking`
--

CREATE TABLE `ae_locking` (
  `id` int(11) NOT NULL,
  `game_id` int(11) NOT NULL,
  `lock_name` varchar(255) NOT NULL,
  `time` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `ae_locking`
--

INSERT INTO `ae_locking` (`id`, `game_id`, `lock_name`, `time`) VALUES
(165550, 0, 'asyncnotifications', 1539701641);

-- --------------------------------------------------------

--
-- Table structure for table `ae_logic_transactions`
--

CREATE TABLE `ae_logic_transactions` (
  `id` int(11) UNSIGNED NOT NULL,
  `context` varchar(255) NOT NULL,
  `process_id` int(11) UNSIGNED NOT NULL,
  `startcount` int(11) UNSIGNED NOT NULL,
  `starttime` int(11) UNSIGNED NOT NULL,
  `finishtime` int(11) UNSIGNED NOT NULL,
  `executiontime` int(11) UNSIGNED NOT NULL,
  `flagged` tinyint(4) NOT NULL DEFAULT '0',
  `warning` tinyint(1) NOT NULL DEFAULT '0',
  `step` varchar(255) NOT NULL,
  `phase` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ae_mobile`
--

CREATE TABLE `ae_mobile` (
  `game_id` int(11) UNSIGNED NOT NULL,
  `ios_enabled` tinyint(1) NOT NULL,
  `android_enabled` tinyint(1) NOT NULL,
  `push_enabled` tinyint(1) NOT NULL,
  `appstore_url` text NOT NULL,
  `playstore_url` text NOT NULL,
  `amazonstore_url` text NOT NULL,
  `amazon_push_id` varchar(255) NOT NULL,
  `languages` varchar(255) NOT NULL DEFAULT 'en',
  `config_main` text NOT NULL,
  `lang_config` text CHARACTER SET latin1 NOT NULL,
  `lang_config_custom` text CHARACTER SET latin1 NOT NULL,
  `lang_config_en` text NOT NULL,
  `lang_config_fi` text NOT NULL,
  `lang_config_bg` text NOT NULL,
  `config_profilepage` text NOT NULL,
  `config_mainpage` text CHARACTER SET latin1 NOT NULL,
  `config_toplist` text NOT NULL,
  `config_mainmenu` text NOT NULL,
  `config_menu_sidemenu` text NOT NULL,
  `config_menu_mainmenu` int(11) NOT NULL,
  `assets` text NOT NULL,
  `assets_map` text NOT NULL,
  `clientassets` int(11) NOT NULL,
  `force_plaintext` int(1) NOT NULL DEFAULT '0',
  `google_project_id` varchar(255) NOT NULL DEFAULT '0',
  `apple_push_cert` text CHARACTER SET latin1 NOT NULL,
  `apple_push_p12` text NOT NULL,
  `autogenerated_styles` mediumtext NOT NULL,
  `build_info` text NOT NULL,
  `google_api_key` varchar(255) NOT NULL DEFAULT '0',
  `notification_config` text NOT NULL,
  `styles` text NOT NULL,
  `ios_build` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ae_notification`
--

CREATE TABLE `ae_notification` (
  `id` int(11) UNSIGNED NOT NULL,
  `id_user` int(11) UNSIGNED NOT NULL,
  `id_channel` int(11) UNSIGNED NOT NULL,
  `id_playaction` int(11) UNSIGNED NOT NULL,
  `app_id` int(11) UNSIGNED NOT NULL,
  `onesignal_msgid` varchar(255) NOT NULL,
  `action_id` int(11) UNSIGNED NOT NULL,
  `play_id` int(11) UNSIGNED NOT NULL,
  `menu_id` varchar(255) DEFAULT NULL,
  `menuid` varchar(255) NOT NULL,
  `shown_in_app` tinyint(1) NOT NULL DEFAULT '0',
  `read_in_app` tinyint(1) NOT NULL DEFAULT '0',
  `type` varchar(20) NOT NULL DEFAULT 'push',
  `subject` text CHARACTER SET utf8 NOT NULL,
  `message` text CHARACTER SET utf8 NOT NULL,
  `email_to` varchar(255) NOT NULL,
  `parameters` varchar(255) NOT NULL,
  `badge_count` int(11) UNSIGNED NOT NULL,
  `manual_config` text NOT NULL,
  `sendtime` int(11) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `repeated` tinyint(3) NOT NULL DEFAULT '0',
  `lastsent` int(11) NOT NULL,
  `expired` tinyint(1) NOT NULL DEFAULT '0',
  `debug` text NOT NULL,
  `os_success` tinyint(1) DEFAULT NULL,
  `os_failed` tinyint(1) DEFAULT NULL,
  `os_converted` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ae_packages`
--

CREATE TABLE `ae_packages` (
  `id` int(11) UNSIGNED NOT NULL,
  `game_id` int(11) UNSIGNED NOT NULL,
  `package_id` int(11) UNSIGNED NOT NULL,
  `current_version` varchar(255) NOT NULL,
  `auto_update` tinyint(1) NOT NULL DEFAULT '0',
  `version` varchar(255) NOT NULL,
  `local_actiontype_id` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ae_packages_themes`
--

CREATE TABLE `ae_packages_themes` (
  `id` int(11) UNSIGNED NOT NULL,
  `game_id` int(11) UNSIGNED NOT NULL,
  `package_id` int(11) UNSIGNED NOT NULL,
  `theme_id` int(11) UNSIGNED NOT NULL,
  `auto_update` tinyint(1) NOT NULL DEFAULT '1',
  `version` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ae_purchase`
--

CREATE TABLE `ae_purchase` (
  `id` int(11) NOT NULL,
  `game_id` int(11) UNSIGNED NOT NULL,
  `play_id` int(11) UNSIGNED DEFAULT NULL,
  `product_id` int(11) UNSIGNED DEFAULT NULL,
  `price` decimal(6,2) NOT NULL,
  `product_title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `platform` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ae_role`
--

CREATE TABLE `ae_role` (
  `id` int(11) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `ae_role`
--

INSERT INTO `ae_role` (`id`, `title`) VALUES
(1, 'admin'),
(2, 'player'),
(3, 'developer');

-- --------------------------------------------------------

--
-- Table structure for table `ae_role_user`
--

CREATE TABLE `ae_role_user` (
  `user_id` int(11) UNSIGNED NOT NULL,
  `role_id` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ae_settings`
--

CREATE TABLE `ae_settings` (
  `id` mediumint(11) NOT NULL,
  `title` varchar(90) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ae_task`
--

CREATE TABLE `ae_task` (
  `id` int(11) UNSIGNED NOT NULL,
  `play_id` int(11) UNSIGNED NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `context` varchar(128) NOT NULL,
  `task` text NOT NULL,
  `parameters` text NOT NULL,
  `result` text NOT NULL,
  `tries` tinyint(2) NOT NULL,
  `timetolive` int(10) NOT NULL,
  `launchtime` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `ae_task`
--

INSERT INTO `ae_task` (`id`, `play_id`, `status`, `context`, `task`, `parameters`, `result`, `tries`, `timetolive`, `launchtime`) VALUES
(262, 1759, 0, 'async', 'cache:listbranches', '', '', 0, 1543678545, 1543678425),
(263, 1759, 0, 'async', 'cache:listbranches', '', '', 0, 1543678709, 1543678589),
(264, 1759, 0, 'async', 'cache:listbranches', '', '', 0, 1543678741, 1543678621),
(265, 1759, 0, 'async', 'cache:listbranches', '', '', 0, 1543678770, 1543678653),
(266, 1759, 0, 'async', 'cache:listbranches', '', '', 0, 1543678862, 1543678742),
(267, 1759, 0, 'async', 'cache:listbranches', '', '', 0, 1543678869, 1543678749),
(268, 1759, 0, 'async', 'cache:listbranches', '', '', 0, 1543678874, 1543678754),
(269, 1760, 0, 'async', 'cache:listbranches', '', '', 0, 1543679028, 1543678908),
(270, 1760, 0, 'async', 'cache:listbranches', '', '', 0, 1543679055, 1543678938);

-- --------------------------------------------------------

--
-- Table structure for table `aro`
--

CREATE TABLE `aro` (
  `id` mediumint(11) NOT NULL,
  `collection_id` mediumint(11) NOT NULL,
  `path` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `aro_collection`
--

CREATE TABLE `aro_collection` (
  `id` mediumint(11) NOT NULL,
  `alias` varchar(20) NOT NULL,
  `model` varchar(15) NOT NULL,
  `foreign_key` mediumint(11) NOT NULL,
  `created` mediumint(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `dbadmin_queries`
--

CREATE TABLE `dbadmin_queries` (
  `id` int(11) NOT NULL,
  `hash` char(32) NOT NULL,
  `results_count` int(11) NOT NULL,
  `timestamp` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Table structure for table `ha_logins`
--

CREATE TABLE `ha_logins` (
  `id` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `loginProvider` varchar(50) NOT NULL,
  `loginProviderIdentifier` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `menu`
--

CREATE TABLE `menu` (
  `id` mediumint(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `parent_id` mediumint(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `menu`
--

INSERT INTO `menu` (`id`, `name`, `title`, `parent_id`) VALUES
(8, 'main-guest', 'Main', NULL),
(9, 'admin', 'Admin', NULL),
(10, 'main-loggedin', 'Main', NULL),
(11, 'user', 'User', NULL),
(12, 'dev', '', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `menu_item`
--

CREATE TABLE `menu_item` (
  `id` mediumint(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `menu_id` mediumint(11) DEFAULT NULL,
  `parent_id` mediumint(11) DEFAULT NULL,
  `role_id` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `path` varchar(255) NOT NULL,
  `order` tinyint(3) NOT NULL,
  `icon` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `menu_item`
--

INSERT INTO `menu_item` (`id`, `title`, `type`, `menu_id`, `parent_id`, `role_id`, `path`, `order`, `icon`) VALUES
(2, '{%menu_main%}', '', 8, NULL, 0, 'http://appzio.com/', 1, ''),
(5, '{%menu_register%}', '', 8, NULL, 0, 'http://appzio.com/register/?user-price=350', 2, ''),
(6, 'menu_login', '', 8, NULL, 0, 'userGroups', 3, ''),
(9, '{%menu_author_game%}', '', 10, NULL, 0, 'dashboard', 2, ''),
(11, '{%menu_developers%}', '', 10, NULL, 3, 'aedev', 3, ''),
(12, '{%menu_dev_main%}', '', 12, NULL, 0, 'aedev', 0, ''),
(14, '{%menu_dev_extensions%}', '', 12, NULL, 0, 'aedev/extension/index', 3, ''),
(15, '{%menu_dev_triggers%}', '', 12, NULL, 0, 'aedev/trigger/index', 4, ''),
(17, '{%ssh_keys%}', '', 12, NULL, 0, 'aedev/sshkeys/index', 5, '');

-- --------------------------------------------------------

--
-- Table structure for table `permission`
--

CREATE TABLE `permission` (
  `id` int(11) NOT NULL,
  `aco_id` int(11) NOT NULL,
  `aro_id` int(11) NOT NULL,
  `aco_path` varchar(11) NOT NULL,
  `aro_path` varchar(11) NOT NULL,
  `action_id` int(11) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ssh_keys`
--

CREATE TABLE `ssh_keys` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `context` varchar(255) NOT NULL,
  `sshkey` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_migration`
--

CREATE TABLE `tbl_migration` (
  `version` varchar(255) NOT NULL,
  `apply_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `usergroups_access`
--

CREATE TABLE `usergroups_access` (
  `id` bigint(20) NOT NULL,
  `element` int(3) NOT NULL,
  `element_id` bigint(20) NOT NULL,
  `module` varchar(140) NOT NULL,
  `controller` varchar(140) NOT NULL,
  `permission` varchar(7) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `usergroups_access`
--

INSERT INTO `usergroups_access` (`id`, `element`, `element_id`, `module`, `controller`, `permission`) VALUES
(1, 2, 3, 'dashboard', 'default', 'read'),
(2, 2, 3, 'dashboard', 'default', 'write');

-- --------------------------------------------------------

--
-- Table structure for table `usergroups_configuration`
--

CREATE TABLE `usergroups_configuration` (
  `id` bigint(20) NOT NULL,
  `rule` varchar(40) DEFAULT NULL,
  `value` varchar(20) DEFAULT NULL,
  `options` text,
  `description` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `usergroups_configuration`
--

INSERT INTO `usergroups_configuration` (`id`, `rule`, `value`, `options`, `description`) VALUES
(1, 'version', '1.8', 'CONST', 'userGroups version'),
(2, 'password_strength', '0', 'a:3:{i:0;s:4:\"weak\";i:1;s:6:\"medium\";i:2;s:6:\"strong\";}', 'password strength:<br/>weak: password of at least 5 characters, any character allowed.<br/>\r\n			medium: password of at least 5 characters, must contain at least 2 digits and 2 letters.<br/>\r\n			strong: password of at least 5 characters, must contain at least 2 digits, 2 letters and a special character.'),
(3, 'registration', 'TRUE', 'BOOL', 'allow user registration'),
(4, 'public_user_list', 'FALSE', 'BOOL', 'logged users can see the complete user list'),
(5, 'public_profiles', 'FALSE', 'BOOL', 'allow everyone, even guests, to see user profiles'),
(6, 'profile_privacy', 'TRUE', 'BOOL', 'logged user can see other users profiles'),
(7, 'personal_home', 'FALSE', 'BOOL', 'users can set their own home'),
(8, 'simple_password_reset', 'FALSE', 'BOOL', 'if true users just have to provide user and email to reset their password.<br/>Otherwise they will have to answer their custom question'),
(9, 'user_need_activation', 'TRUE', 'BOOL', 'if true when a user creates an account a mail with an activation code will be sent to his email address'),
(10, 'user_need_approval', 'FALSE', 'BOOL', 'if true when a user creates an account a user with user admin rights will have to approve the registration.<br/>If both this setting and user_need_activation are true the user will need to activate is account first and then will need the approval'),
(11, 'user_registration_group', '2', 'GROUP_LIST', 'the group new users automatically belong to'),
(12, 'dumb_admin', 'TRUE', 'BOOL', 'users with just admin write permissions won\'t see the Main Configuration and Cron Jobs panels'),
(13, 'super_admin', 'FALSE', 'BOOL', 'users with userGroups admin admin permission will have access to everything, just like root'),
(14, 'permission_cascade', 'TRUE', 'BOOL', 'if a user has on a controller admin permissions will have access to write and read pages. If he has write permissions will also have access to read pages'),
(15, 'server_executed_crons', 'FALSE', 'BOOL', 'if true crons must be executed from the server using a crontab');

-- --------------------------------------------------------

--
-- Table structure for table `usergroups_cron`
--

CREATE TABLE `usergroups_cron` (
  `id` bigint(20) NOT NULL,
  `name` varchar(40) DEFAULT NULL,
  `lapse` int(6) DEFAULT NULL,
  `last_occurrence` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `usergroups_group`
--

CREATE TABLE `usergroups_group` (
  `id` bigint(20) NOT NULL,
  `groupname` varchar(120) NOT NULL,
  `level` int(6) DEFAULT NULL,
  `home` varchar(120) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `usergroups_group`
--

INSERT INTO `usergroups_group` (`id`, `groupname`, `level`, `home`) VALUES
(1, 'root', 100, NULL),
(2, 'user', 1, '/userGroups'),
(3, 'player', 99, '/dashboard');

-- --------------------------------------------------------

--
-- Table structure for table `usergroups_lookup`
--

CREATE TABLE `usergroups_lookup` (
  `id` bigint(20) NOT NULL,
  `element` varchar(20) DEFAULT NULL,
  `value` int(5) DEFAULT NULL,
  `text` varchar(40) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `usergroups_lookup`
--

INSERT INTO `usergroups_lookup` (`id`, `element`, `value`, `text`) VALUES
(1, 'status', 0, 'banned'),
(2, 'status', 1, 'waiting activation'),
(3, 'status', 2, 'waiting approval'),
(4, 'status', 3, 'password change request'),
(5, 'status', 4, 'active');

-- --------------------------------------------------------

--
-- Table structure for table `usergroups_user`
--

CREATE TABLE `usergroups_user` (
  `id` int(11) UNSIGNED NOT NULL,
  `group_id` bigint(20) DEFAULT NULL,
  `language` varchar(2) NOT NULL DEFAULT 'en',
  `username` varchar(120) NOT NULL,
  `password` varchar(120) DEFAULT NULL,
  `email` varchar(120) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `home` varchar(120) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `question` text,
  `answer` text,
  `creation_date` datetime DEFAULT NULL,
  `activation_code` varchar(30) DEFAULT NULL,
  `activation_time` datetime DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `ban` datetime DEFAULT NULL,
  `ban_reason` text,
  `developer_phone` varchar(255) NOT NULL,
  `developer_verification` varchar(255) NOT NULL,
  `developer_karmapoints` int(11) NOT NULL DEFAULT '10',
  `alert` text NOT NULL,
  `phone` text NOT NULL,
  `timezone` varchar(10) NOT NULL,
  `twitter` varchar(255) NOT NULL,
  `skype` varchar(255) NOT NULL,
  `fbid` varchar(255) NOT NULL,
  `fbtoken` varchar(255) NOT NULL,
  `fbtoken_long` varchar(255) NOT NULL,
  `nickname` varchar(255) NOT NULL,
  `creator_api_key` varchar(16) NOT NULL,
  `temp_user` tinyint(1) NOT NULL,
  `source` varchar(255) NOT NULL,
  `last_push` int(11) NOT NULL,
  `play_id` int(11) UNSIGNED NOT NULL,
  `laratoken` varchar(255) NOT NULL,
  `active_app_id` varchar(255) NOT NULL,
  `sftp_username` varchar(255) NOT NULL,
  `terms_approved` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

--
-- Dumping data for table `usergroups_user`
--

INSERT INTO `usergroups_user` (`id`, `group_id`, `language`, `username`, `password`, `email`, `firstname`, `lastname`, `home`, `status`, `question`, `answer`, `creation_date`, `activation_code`, `activation_time`, `last_login`, `ban`, `ban_reason`, `developer_phone`, `developer_verification`, `developer_karmapoints`, `alert`, `phone`, `timezone`, `twitter`, `skype`, `fbid`, `fbtoken`, `fbtoken_long`, `nickname`, `creator_api_key`, `temp_user`, `source`, `last_push`, `play_id`, `laratoken`, `active_app_id`, `sftp_username`, `terms_approved`) VALUES
(1, 1, 'en', 'admin', '604167bb3bc55d73aadea5b07f30eef9', 'info@appzio.com', 'App', 'Admin', '0', 4, 'company name?', 'Appzio', '2012-11-25 23:17:05', '582d6f8087894', '2016-11-17 09:51:12', '2018-12-01 14:04:54', NULL, NULL, '+358505002232', '', 114, '', '4155143124', '+2', '', '', '', '', '', 'tester', '', 0, '', 0, 0, 'f9f96189fe82be493b4ba2fe0583f0e3', '', '', 1),
(1749, 3, 'en', '36991d4d8b6702309282f29374efcf1a', '01f429dea9026986f01237aeb3f47fa4', '', '', '', '/aeplay/home/gamehome?gid=8', 1, NULL, NULL, '2018-12-01 17:33:44', '5c02a9d865412', NULL, NULL, NULL, NULL, '', '', 10, '', '', '+2', '', '', '', '', '', '', '9816304ea1bd02d0', 0, 'client_iphone', 0, 1759, '', '', '', 0),
(1750, 3, 'en', '97edb599970c863805b85f4204b19538', 'c4f8e85c344453d8d1caa7c16d950aea', '', '', '', '/aeplay/home/gamehome?gid=7', 1, NULL, NULL, '2018-12-01 17:40:25', '5c02ab697af91', NULL, NULL, NULL, NULL, '', '', 10, '', '', '+2', '', '', '', '', '', '', '307096aa9c582ff0', 0, 'client_iphone', 0, 1760, '', '', '', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `aco`
--
ALTER TABLE `aco`
  ADD PRIMARY KEY (`id`),
  ADD KEY `aco_collection_id` (`collection_id`),
  ADD KEY `path` (`path`);

--
-- Indexes for table `aco_collection`
--
ALTER TABLE `aco_collection`
  ADD PRIMARY KEY (`id`),
  ADD KEY `alias` (`alias`);

--
-- Indexes for table `ae_access_tokens`
--
ALTER TABLE `ae_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `token` (`token`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `api_key` (`api_key`),
  ADD KEY `play_id` (`play_id`);

--
-- Indexes for table `ae_api_errorlog`
--
ALTER TABLE `ae_api_errorlog`
  ADD PRIMARY KEY (`id`),
  ADD KEY `api_key` (`api_key`);

--
-- Indexes for table `ae_api_filelookup`
--
ALTER TABLE `ae_api_filelookup`
  ADD PRIMARY KEY (`id`),
  ADD KEY `game_id` (`game_id`,`original`,`md5`),
  ADD KEY `original` (`original`),
  ADD KEY `md5` (`md5`),
  ADD KEY `cachefile` (`cachefile`),
  ADD KEY `prio` (`priority`);

--
-- Indexes for table `ae_api_key`
--
ALTER TABLE `ae_api_key`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`id_user`);

--
-- Indexes for table `ae_app_menus`
--
ALTER TABLE `ae_app_menus`
  ADD PRIMARY KEY (`id`),
  ADD KEY `app_id` (`app_id`),
  ADD KEY `id` (`id`),
  ADD KEY `safe_name` (`safe_name`);

--
-- Indexes for table `ae_app_menus_user_state`
--
ALTER TABLE `ae_app_menus_user_state`
  ADD PRIMARY KEY (`id`),
  ADD KEY `app_menu_id` (`menu_id`),
  ADD KEY `app_menu_item_id` (`menu_item_id`),
  ADD KEY `action_id` (`action_id`),
  ADD KEY `branch_id` (`branch_id`),
  ADD KEY `play_id` (`play_id`),
  ADD KEY `menu_id` (`menu_id`),
  ADD KEY `menu_item_id` (`menu_item_id`),
  ADD KEY `action_id_2` (`action_id`),
  ADD KEY `branch_id_2` (`branch_id`),
  ADD KEY `play_id_2` (`play_id`);

--
-- Indexes for table `ae_app_menu_items`
--
ALTER TABLE `ae_app_menu_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `app_menu_id` (`menu_id`),
  ADD KEY `menu_id` (`menu_id`),
  ADD KEY `menu_id_2` (`menu_id`),
  ADD KEY `menu_id_3` (`menu_id`);

--
-- Indexes for table `ae_category`
--
ALTER TABLE `ae_category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ae_channel`
--
ALTER TABLE `ae_channel`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ae_channel_sent_sms`
--
ALTER TABLE `ae_channel_sent_sms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `playtask_id` (`playtask_id`);

--
-- Indexes for table `ae_channel_setting`
--
ALTER TABLE `ae_channel_setting`
  ADD PRIMARY KEY (`id`),
  ADD KEY `channel_id` (`id_channel`),
  ADD KEY `type` (`type`);

--
-- Indexes for table `ae_channel_setting_user`
--
ALTER TABLE `ae_channel_setting_user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_setting` (`id_setting`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `ae_channel_user`
--
ALTER TABLE `ae_channel_user`
  ADD PRIMARY KEY (`id_channel`,`id_user`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `ae_chat`
--
ALTER TABLE `ae_chat`
  ADD PRIMARY KEY (`id`),
  ADD KEY `owner_play_id` (`owner_play_id`),
  ADD KEY `type` (`type`),
  ADD KEY `type_2` (`type`),
  ADD KEY `game_id` (`game_id`),
  ADD KEY `category` (`category`),
  ADD KEY `city` (`city`),
  ADD KEY `context_key` (`context_key`);

--
-- Indexes for table `ae_chat_attachments`
--
ALTER TABLE `ae_chat_attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `chat_message_id` (`chat_message_id`);

--
-- Indexes for table `ae_chat_messages`
--
ALTER TABLE `ae_chat_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `chat_id` (`chat_id`),
  ADD KEY `chat_message_is_read` (`chat_message_is_read`);

--
-- Indexes for table `ae_chat_messages_likes`
--
ALTER TABLE `ae_chat_messages_likes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `message_id` (`message_id`),
  ADD KEY `play_id` (`play_id`);

--
-- Indexes for table `ae_chat_users`
--
ALTER TABLE `ae_chat_users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `chat_id` (`chat_id`),
  ADD KEY `chat_user_play_id` (`chat_user_play_id`),
  ADD KEY `context` (`context`),
  ADD KEY `context_key` (`context_key`);

--
-- Indexes for table `ae_crons`
--
ALTER TABLE `ae_crons`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ae_crons_logs`
--
ALTER TABLE `ae_crons_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cron_id` (`cron_id`);

--
-- Indexes for table `ae_ext_article`
--
ALTER TABLE `ae_ext_article`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `app_id` (`app_id`),
  ADD KEY `ae_ext_article_ibfk_2` (`play_id`);

--
-- Indexes for table `ae_ext_article_bookmarks`
--
ALTER TABLE `ae_ext_article_bookmarks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `play_id` (`play_id`),
  ADD KEY `article_id` (`article_id`);

--
-- Indexes for table `ae_ext_article_categories`
--
ALTER TABLE `ae_ext_article_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `title` (`title`),
  ADD KEY `app_id` (`app_id`);

--
-- Indexes for table `ae_ext_article_comments`
--
ALTER TABLE `ae_ext_article_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `play_id` (`play_id`);

--
-- Indexes for table `ae_ext_article_photos`
--
ALTER TABLE `ae_ext_article_photos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `article_id` (`article_id`);

--
-- Indexes for table `ae_ext_article_tags`
--
ALTER TABLE `ae_ext_article_tags`
  ADD PRIMARY KEY (`id`),
  ADD KEY `app_id` (`app_id`);

--
-- Indexes for table `ae_ext_bbs`
--
ALTER TABLE `ae_ext_bbs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `playtask_id` (`playtask_id`),
  ADD KEY `date` (`date`),
  ADD KEY `user_id` (`user_id`,`parent_id`);

--
-- Indexes for table `ae_ext_bid_items`
--
ALTER TABLE `ae_ext_bid_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `play_id` (`play_id`);

--
-- Indexes for table `ae_ext_bookings`
--
ALTER TABLE `ae_ext_bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `play_id` (`play_id`),
  ADD KEY `assignee_play_id` (`assignee_play_id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `ae_ext_calendar_entry`
--
ALTER TABLE `ae_ext_calendar_entry`
  ADD PRIMARY KEY (`id`),
  ADD KEY `play_id` (`play_id`),
  ADD KEY `type_id` (`type_id`),
  ADD KEY `exercise_id` (`exercise_id`),
  ADD KEY `program_id` (`program_id`),
  ADD KEY `recipe_id` (`recipe_id`);

--
-- Indexes for table `ae_ext_classifieds_categories`
--
ALTER TABLE `ae_ext_classifieds_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ae_ext_classifieds_categories_items`
--
ALTER TABLE `ae_ext_classifieds_categories_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `ae_ext_classifieds_favourite_items`
--
ALTER TABLE `ae_ext_classifieds_favourite_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `play_id` (`play_id`);

--
-- Indexes for table `ae_ext_classifieds_filter`
--
ALTER TABLE `ae_ext_classifieds_filter`
  ADD PRIMARY KEY (`id`),
  ADD KEY `play_id` (`play_id`);

--
-- Indexes for table `ae_ext_classifieds_items`
--
ALTER TABLE `ae_ext_classifieds_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ae_ext_classifieds_items_meta`
--
ALTER TABLE `ae_ext_classifieds_items_meta`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `key` (`key`);

--
-- Indexes for table `ae_ext_club_news`
--
ALTER TABLE `ae_ext_club_news`
  ADD PRIMARY KEY (`id`),
  ADD KEY `club_id` (`club_id`);

--
-- Indexes for table `ae_ext_deals_logs`
--
ALTER TABLE `ae_ext_deals_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `app_id` (`app_id`);

--
-- Indexes for table `ae_ext_diary`
--
ALTER TABLE `ae_ext_diary`
  ADD PRIMARY KEY (`id`),
  ADD KEY `playtask_id` (`playtask_id`),
  ADD KEY `date` (`date`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `ae_ext_fit_exercise`
--
ALTER TABLE `ae_ext_fit_exercise`
  ADD PRIMARY KEY (`id`),
  ADD KEY `article_id` (`article_id`),
  ADD KEY `ae_ext_fit_exercise.app_id` (`app_id`);

--
-- Indexes for table `ae_ext_fit_exercise_movement`
--
ALTER TABLE `ae_ext_fit_exercise_movement`
  ADD PRIMARY KEY (`id`),
  ADD KEY `exercise_id` (`exercise_id`),
  ADD KEY `movement_id` (`movement_id`),
  ADD KEY `movement_category_id` (`movement_category_id`);

--
-- Indexes for table `ae_ext_fit_movement`
--
ALTER TABLE `ae_ext_fit_movement`
  ADD PRIMARY KEY (`id`),
  ADD KEY `article_id` (`article_id`),
  ADD KEY `ae_ext_fit_movement.app_id` (`app_id`);

--
-- Indexes for table `ae_ext_fit_movement_category`
--
ALTER TABLE `ae_ext_fit_movement_category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ae_ext_fit_pr`
--
ALTER TABLE `ae_ext_fit_pr`
  ADD PRIMARY KEY (`id`),
  ADD KEY `app_id` (`app_id`);

--
-- Indexes for table `ae_ext_fit_program`
--
ALTER TABLE `ae_ext_fit_program`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `article_id` (`article_id`),
  ADD KEY `ae_ext_fit_program_app_id.app_id` (`app_id`),
  ADD KEY `subcategory_id` (`subcategory_id`);

--
-- Indexes for table `ae_ext_fit_program_category`
--
ALTER TABLE `ae_ext_fit_program_category`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ae_ext_fit_program_category.app_id` (`app_id`);

--
-- Indexes for table `ae_ext_fit_program_exercise`
--
ALTER TABLE `ae_ext_fit_program_exercise`
  ADD PRIMARY KEY (`id`),
  ADD KEY `program_id` (`program_id`),
  ADD KEY `exercise_id` (`exercise_id`);

--
-- Indexes for table `ae_ext_fit_program_recipe`
--
ALTER TABLE `ae_ext_fit_program_recipe`
  ADD PRIMARY KEY (`id`),
  ADD KEY `program_id` (`program_id`),
  ADD KEY `recipe_id` (`recipe_id`);

--
-- Indexes for table `ae_ext_fit_program_selection`
--
ALTER TABLE `ae_ext_fit_program_selection`
  ADD PRIMARY KEY (`id`),
  ADD KEY `play_id` (`play_id`),
  ADD KEY `program_id` (`program_id`);

--
-- Indexes for table `ae_ext_fit_program_subcategory`
--
ALTER TABLE `ae_ext_fit_program_subcategory`
  ADD PRIMARY KEY (`id`),
  ADD KEY `app_id` (`app_id`);

--
-- Indexes for table `ae_ext_fit_pr_user`
--
ALTER TABLE `ae_ext_fit_pr_user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `play_id` (`play_id`),
  ADD KEY `pr_id` (`pr_id`);

--
-- Indexes for table `ae_ext_food_custom_ingredient`
--
ALTER TABLE `ae_ext_food_custom_ingredient`
  ADD PRIMARY KEY (`id`),
  ADD KEY `play_id` (`play_id`),
  ADD KEY `ingredient_category` (`ingredient_category`);

--
-- Indexes for table `ae_ext_food_ingredient`
--
ALTER TABLE `ae_ext_food_ingredient`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category` (`category_id`);

--
-- Indexes for table `ae_ext_food_ingredient_category`
--
ALTER TABLE `ae_ext_food_ingredient_category`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id` (`id`);

--
-- Indexes for table `ae_ext_food_recipe`
--
ALTER TABLE `ae_ext_food_recipe`
  ADD PRIMARY KEY (`id`),
  ADD KEY `recipe_type` (`type_id`);

--
-- Indexes for table `ae_ext_food_recipe_ingredient`
--
ALTER TABLE `ae_ext_food_recipe_ingredient`
  ADD PRIMARY KEY (`id`),
  ADD KEY `recipe_id` (`recipe_id`),
  ADD KEY `ingredient_id` (`ingredient_id`);

--
-- Indexes for table `ae_ext_food_recipe_step`
--
ALTER TABLE `ae_ext_food_recipe_step`
  ADD PRIMARY KEY (`id`),
  ADD KEY `recipe` (`recipe_id`);

--
-- Indexes for table `ae_ext_food_recipe_type`
--
ALTER TABLE `ae_ext_food_recipe_type`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ae_ext_food_recipe_type.app_id` (`app_id`);

--
-- Indexes for table `ae_ext_food_shopping_list`
--
ALTER TABLE `ae_ext_food_shopping_list`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ingredient_id` (`ingredient_id`),
  ADD KEY `play_id` (`play_id`);

--
-- Indexes for table `ae_ext_gallery_images`
--
ALTER TABLE `ae_ext_gallery_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `play_id` (`play_id`);

--
-- Indexes for table `ae_ext_golf_hole`
--
ALTER TABLE `ae_ext_golf_hole`
  ADD PRIMARY KEY (`id`),
  ADD KEY `place_id` (`place_id`);

--
-- Indexes for table `ae_ext_golf_hole_user`
--
ALTER TABLE `ae_ext_golf_hole_user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hole_id` (`hole_id`),
  ADD KEY `play_id` (`play_id`),
  ADD KEY `event_id` (`event_id`);

--
-- Indexes for table `ae_ext_items`
--
ALTER TABLE `ae_ext_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `item-slug` (`slug`),
  ADD KEY `play_id` (`play_id`),
  ADD KEY `game_id` (`game_id`),
  ADD KEY `ae_ext_items_ibfk_3` (`category_id`),
  ADD KEY `featured` (`featured`),
  ADD KEY `external` (`external`);

--
-- Indexes for table `ae_ext_items_categories`
--
ALTER TABLE `ae_ext_items_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `app_id` (`app_id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Indexes for table `ae_ext_items_category_item`
--
ALTER TABLE `ae_ext_items_category_item`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ae_ext_items_category_item_ibfk_1` (`item_id`),
  ADD KEY `ae_ext_items_category_item_ibfk_2` (`category_id`);

--
-- Indexes for table `ae_ext_items_filters`
--
ALTER TABLE `ae_ext_items_filters`
  ADD PRIMARY KEY (`id`),
  ADD KEY `play_id` (`play_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `ae_ext_items_images`
--
ALTER TABLE `ae_ext_items_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `ae_ext_items_likes`
--
ALTER TABLE `ae_ext_items_likes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `play_id` (`play_id`),
  ADD KEY `ae_ext_items_likes_ibfk_2` (`item_id`);

--
-- Indexes for table `ae_ext_items_reminders`
--
ALTER TABLE `ae_ext_items_reminders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `type` (`type`),
  ADD KEY `date` (`date`);

--
-- Indexes for table `ae_ext_items_reports`
--
ALTER TABLE `ae_ext_items_reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `play_id` (`play_id`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `item_owner_id` (`item_owner_id`);

--
-- Indexes for table `ae_ext_items_tags`
--
ALTER TABLE `ae_ext_items_tags`
  ADD PRIMARY KEY (`id`),
  ADD KEY `app_id` (`app_id`);

--
-- Indexes for table `ae_ext_items_tag_item`
--
ALTER TABLE `ae_ext_items_tag_item`
  ADD PRIMARY KEY (`id`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `tag_id` (`tag_id`);

--
-- Indexes for table `ae_ext_meter`
--
ALTER TABLE `ae_ext_meter`
  ADD PRIMARY KEY (`id`),
  ADD KEY `play_id` (`play_id`),
  ADD KEY `meter_id` (`meter_id`),
  ADD KEY `last_update` (`last_update`);

--
-- Indexes for table `ae_ext_meter_appliances`
--
ALTER TABLE `ae_ext_meter_appliances`
  ADD PRIMARY KEY (`id`),
  ADD KEY `meter_id` (`meter_id`);

--
-- Indexes for table `ae_ext_meter_data`
--
ALTER TABLE `ae_ext_meter_data`
  ADD PRIMARY KEY (`id`),
  ADD KEY `meter_id` (`meter_id`),
  ADD KEY `capture_time` (`capture_time`),
  ADD KEY `energy` (`energy`);

--
-- Indexes for table `ae_ext_mobilebeacons`
--
ALTER TABLE `ae_ext_mobilebeacons`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lat` (`lat`),
  ADD KEY `lon` (`lon`),
  ADD KEY `region` (`region`),
  ADD KEY `app_id` (`app_id`),
  ADD KEY `place_id` (`place_id`);

--
-- Indexes for table `ae_ext_mobileevents`
--
ALTER TABLE `ae_ext_mobileevents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `game_id` (`game_id`),
  ADD KEY `play_id` (`play_id`),
  ADD KEY `place_id` (`place_id`);

--
-- Indexes for table `ae_ext_mobileevents_participants`
--
ALTER TABLE `ae_ext_mobileevents_participants`
  ADD PRIMARY KEY (`id`),
  ADD KEY `play_id` (`play_id`),
  ADD KEY `event_id` (`event_id`);

--
-- Indexes for table `ae_ext_mobilefeedbacktool`
--
ALTER TABLE `ae_ext_mobilefeedbacktool`
  ADD PRIMARY KEY (`id`),
  ADD KEY `game_id` (`game_id`),
  ADD KEY `author_id` (`author_id`),
  ADD KEY `recipient_id` (`recipient_id`),
  ADD KEY `department_id_sender` (`department_id_sender`),
  ADD KEY `department_id_recipient` (`department_id_recipient`),
  ADD KEY `fundamentals_id` (`fundamentals_id`),
  ADD KEY `requester_id` (`requester_id`),
  ADD KEY `requester_id_2` (`requester_id`);

--
-- Indexes for table `ae_ext_mobilefeedbacktool_departments`
--
ALTER TABLE `ae_ext_mobilefeedbacktool_departments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `game_id` (`game_id`);

--
-- Indexes for table `ae_ext_mobilefeedbacktool_fundamentals`
--
ALTER TABLE `ae_ext_mobilefeedbacktool_fundamentals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `game_id` (`game_id`);

--
-- Indexes for table `ae_ext_mobilefeedbacktool_teams`
--
ALTER TABLE `ae_ext_mobilefeedbacktool_teams`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `game_id` (`game_id`),
  ADD KEY `owner_id` (`owner_id`);

--
-- Indexes for table `ae_ext_mobilefeedbacktool_teams_members`
--
ALTER TABLE `ae_ext_mobilefeedbacktool_teams_members`
  ADD PRIMARY KEY (`id`),
  ADD KEY `team_id` (`team_id`),
  ADD KEY `play_id` (`play_id`),
  ADD KEY `role_type` (`role_type`);

--
-- Indexes for table `ae_ext_mobilematching`
--
ALTER TABLE `ae_ext_mobilematching`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `game_id` (`game_id`),
  ADD KEY `play_id` (`play_id`),
  ADD KEY `hindu_caste` (`hindu_caste`),
  ADD KEY `role` (`role`);

--
-- Indexes for table `ae_ext_mobilematching_meta`
--
ALTER TABLE `ae_ext_mobilematching_meta`
  ADD PRIMARY KEY (`id`),
  ADD KEY `play_id` (`play_id`);

--
-- Indexes for table `ae_ext_mobileplaces`
--
ALTER TABLE `ae_ext_mobileplaces`
  ADD PRIMARY KEY (`id`),
  ADD KEY `game_id` (`game_id`),
  ADD KEY `lat` (`lat`),
  ADD KEY `lon` (`lon`),
  ADD KEY `zip` (`zip`),
  ADD KEY `country` (`country`),
  ADD KEY `premium` (`premium`),
  ADD KEY `code` (`code`),
  ADD KEY `type` (`type`);

--
-- Indexes for table `ae_ext_mobileproperty`
--
ALTER TABLE `ae_ext_mobileproperty`
  ADD PRIMARY KEY (`id`),
  ADD KEY `game_id` (`game_id`),
  ADD KEY `play_id` (`play_id`);

--
-- Indexes for table `ae_ext_mobileproperty_bookmark`
--
ALTER TABLE `ae_ext_mobileproperty_bookmark`
  ADD PRIMARY KEY (`id`),
  ADD KEY `play_id` (`play_id`),
  ADD KEY `mobileproperty_id` (`mobileproperty_id`),
  ADD KEY `game_id` (`game_id`);

--
-- Indexes for table `ae_ext_mobileproperty_settings`
--
ALTER TABLE `ae_ext_mobileproperty_settings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `play_id` (`play_id`),
  ADD KEY `game_id` (`game_id`),
  ADD KEY `from_num_bedrooms` (`from_num_bedrooms`),
  ADD KEY `to_num_bedrooms` (`to_num_bedrooms`),
  ADD KEY `from_price_per_month` (`from_price_per_month`),
  ADD KEY `to_price_per_month` (`to_price_per_month`),
  ADD KEY `type_house` (`type_house`),
  ADD KEY `type_flat` (`type_flat`),
  ADD KEY `type_room` (`type_room`),
  ADD KEY `furnished` (`furnished`);

--
-- Indexes for table `ae_ext_mobileproperty_users`
--
ALTER TABLE `ae_ext_mobileproperty_users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `agent_id` (`agent_id`);

--
-- Indexes for table `ae_ext_mtasks`
--
ALTER TABLE `ae_ext_mtasks`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `created_time` (`created_time`),
  ADD KEY `deadline` (`deadline`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `owner_id` (`owner_id`),
  ADD KEY `assignee_id` (`assignee_id`);

--
-- Indexes for table `ae_ext_mtasks_invitations`
--
ALTER TABLE `ae_ext_mtasks_invitations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `play_id` (`play_id`),
  ADD KEY `invited_play_id` (`invited_play_id`);

--
-- Indexes for table `ae_ext_mtasks_proof`
--
ALTER TABLE `ae_ext_mtasks_proof`
  ADD PRIMARY KEY (`id`),
  ADD KEY `task_id` (`task_id`);

--
-- Indexes for table `ae_ext_notifications`
--
ALTER TABLE `ae_ext_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `play_id_from` (`play_id_from`),
  ADD KEY `play_id_to` (`play_id_to`),
  ADD KEY `app_id` (`app_id`),
  ADD KEY `notification_id` (`notification_id`),
  ADD KEY `shorturl` (`shorturl`),
  ADD KEY `temporary_email` (`temporary_email`),
  ADD KEY `task_id` (`task_id`);

--
-- Indexes for table `ae_ext_photostream`
--
ALTER TABLE `ae_ext_photostream`
  ADD PRIMARY KEY (`id`),
  ADD KEY `playtask_id` (`playtask_id`),
  ADD KEY `date` (`date`);

--
-- Indexes for table `ae_ext_photostream_post`
--
ALTER TABLE `ae_ext_photostream_post`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bbs_id` (`photostream_id`),
  ADD KEY `parent_id` (`parent_id`),
  ADD KEY `date` (`date`);

--
-- Indexes for table `ae_ext_products`
--
ALTER TABLE `ae_ext_products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `app_id` (`app_id`),
  ADD KEY `ae_ext_products_ibfk_2` (`play_id`);

--
-- Indexes for table `ae_ext_products_bookmarks`
--
ALTER TABLE `ae_ext_products_bookmarks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `play_id` (`play_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `ae_ext_products_carts`
--
ALTER TABLE `ae_ext_products_carts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `play_id` (`play_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `task_id` (`task_id`),
  ADD KEY `cart_status` (`cart_status`);

--
-- Indexes for table `ae_ext_products_categories`
--
ALTER TABLE `ae_ext_products_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `title` (`title`),
  ADD KEY `app_id` (`app_id`);

--
-- Indexes for table `ae_ext_products_photos`
--
ALTER TABLE `ae_ext_products_photos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `ae_ext_products_purchases`
--
ALTER TABLE `ae_ext_products_purchases`
  ADD PRIMARY KEY (`id`),
  ADD KEY `play_id` (`play_id`),
  ADD KEY `date` (`date`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `ae_ext_products_reviews`
--
ALTER TABLE `ae_ext_products_reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `play_id` (`play_id`);

--
-- Indexes for table `ae_ext_products_tags`
--
ALTER TABLE `ae_ext_products_tags`
  ADD PRIMARY KEY (`id`),
  ADD KEY `app_id` (`app_id`);

--
-- Indexes for table `ae_ext_products_tags_products`
--
ALTER TABLE `ae_ext_products_tags_products`
  ADD UNIQUE KEY `tag_id_2` (`tag_id`,`product_id`),
  ADD KEY `tag_id` (`tag_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `ae_ext_quiz`
--
ALTER TABLE `ae_ext_quiz`
  ADD PRIMARY KEY (`id`),
  ADD KEY `app_id` (`app_id`);

--
-- Indexes for table `ae_ext_quiz_question`
--
ALTER TABLE `ae_ext_quiz_question`
  ADD PRIMARY KEY (`id`),
  ADD KEY `variable_id` (`variable_name`),
  ADD KEY `ae_ext_quiz_question_ibfk_1` (`app_id`);

--
-- Indexes for table `ae_ext_quiz_question_option`
--
ALTER TABLE `ae_ext_quiz_question_option`
  ADD PRIMARY KEY (`id`),
  ADD KEY `question_id` (`question_id`);

--
-- Indexes for table `ae_ext_quiz_sets`
--
ALTER TABLE `ae_ext_quiz_sets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quiz_id` (`quiz_id`),
  ADD KEY `app_id` (`app_id`),
  ADD KEY `question_id` (`question_id`);

--
-- Indexes for table `ae_ext_requests`
--
ALTER TABLE `ae_ext_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `requester_playid` (`requester_playid`);

--
-- Indexes for table `ae_ext_request_payments`
--
ALTER TABLE `ae_ext_request_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `request_id` (`request_id`);

--
-- Indexes for table `ae_ext_tattoos`
--
ALTER TABLE `ae_ext_tattoos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `play_id` (`play_id`),
  ADD KEY `game_id` (`game_id`),
  ADD KEY `ae_ext_tattoos_ibfk_3` (`category_id`);

--
-- Indexes for table `ae_ext_tattoos_bookings`
--
ALTER TABLE `ae_ext_tattoos_bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tattoo_id` (`tattoo_id`),
  ADD KEY `tattooist_id` (`tattooist_id`),
  ADD KEY `buyer_id` (`buyer_id`);

--
-- Indexes for table `ae_ext_tattoos_categories`
--
ALTER TABLE `ae_ext_tattoos_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ae_ext_tattoos_likes`
--
ALTER TABLE `ae_ext_tattoos_likes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `play_id` (`play_id`),
  ADD KEY `ae_ext_tattoos_likes_ibfk_2` (`tattoo_id`);

--
-- Indexes for table `ae_ext_tattoos_tags`
--
ALTER TABLE `ae_ext_tattoos_tags`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ae_ext_tattoos_tag_tattoo`
--
ALTER TABLE `ae_ext_tattoos_tag_tattoo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tattoo_id` (`tattoo_id`),
  ADD KEY `tag_id` (`tag_id`);

--
-- Indexes for table `ae_ext_user_bids`
--
ALTER TABLE `ae_ext_user_bids`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bid_item_id` (`bid_item_id`),
  ADD KEY `play_id` (`play_id`);

--
-- Indexes for table `ae_ext_user_bid_item_images`
--
ALTER TABLE `ae_ext_user_bid_item_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bid_item_id` (`bid_item_id`);

--
-- Indexes for table `ae_ext_wallet`
--
ALTER TABLE `ae_ext_wallet`
  ADD PRIMARY KEY (`id`),
  ADD KEY `play_id` (`play_id`);

--
-- Indexes for table `ae_ext_wallet_logs`
--
ALTER TABLE `ae_ext_wallet_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `play_id` (`play_id`);

--
-- Indexes for table `ae_fbfriend`
--
ALTER TABLE `ae_fbfriend`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `fbid` (`fbid`);

--
-- Indexes for table `ae_fbfriend_user`
--
ALTER TABLE `ae_fbfriend_user`
  ADD PRIMARY KEY (`user_id`,`ae_fbfriend_id`),
  ADD KEY `ae_fbfriend_id` (`ae_fbfriend_id`);

--
-- Indexes for table `ae_fb_invite`
--
ALTER TABLE `ae_fb_invite`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `playtask_id` (`playtask_id`);

--
-- Indexes for table `ae_fieldtype`
--
ALTER TABLE `ae_fieldtype`
  ADD PRIMARY KEY (`fieldtype`);

--
-- Indexes for table `ae_filenames`
--
ALTER TABLE `ae_filenames`
  ADD PRIMARY KEY (`id`),
  ADD KEY `game_id` (`game_id`),
  ADD KEY `action_id` (`action_id`),
  ADD KEY `sha1` (`sha1`);

--
-- Indexes for table `ae_game`
--
ALTER TABLE `ae_game`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `secondary_points` (`secondary_points`,`tertiary_points`);

--
-- Indexes for table `ae_game_badge`
--
ALTER TABLE `ae_game_badge`
  ADD PRIMARY KEY (`id`),
  ADD KEY `game_id` (`game_id`),
  ADD KEY `game_id_2` (`game_id`);

--
-- Indexes for table `ae_game_branch`
--
ALTER TABLE `ae_game_branch`
  ADD PRIMARY KEY (`id`),
  ADD KEY `game_id` (`game_id`),
  ADD KEY `trigger_id` (`trigger_id`),
  ADD KEY `channel_id` (`channel_id`),
  ADD KEY `order` (`order`),
  ADD KEY `active` (`active`),
  ADD KEY `infinite` (`infinite`),
  ADD KEY `order_2` (`order`),
  ADD KEY `active_2` (`active`),
  ADD KEY `infinite_2` (`infinite`),
  ADD KEY `order_3` (`order`),
  ADD KEY `active_3` (`active`),
  ADD KEY `infinite_3` (`infinite`);

--
-- Indexes for table `ae_game_branch_action`
--
ALTER TABLE `ae_game_branch_action`
  ADD PRIMARY KEY (`id`),
  ADD KEY `channel_id` (`channel_id`),
  ADD KEY `parent_id` (`parent_id`),
  ADD KEY `order` (`order`),
  ADD KEY `trigger_id` (`trigger_id`),
  ADD KEY `level_id` (`branch_id`),
  ADD KEY `type_id` (`type_id`),
  ADD KEY `role_id` (`role_id`),
  ADD KEY `activate_branch_id` (`activate_branch_id`),
  ADD KEY `pointsystem_id` (`pointsystem_id`),
  ADD KEY `active` (`active`),
  ADD KEY `active_2` (`active`),
  ADD KEY `active_3` (`active`),
  ADD KEY `permaname` (`permaname`);

--
-- Indexes for table `ae_game_branch_action_type`
--
ALTER TABLE `ae_game_branch_action_type`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `library` (`library`),
  ADD KEY `id_sshkey` (`id_sshkey`);

--
-- Indexes for table `ae_game_keyvaluestorage`
--
ALTER TABLE `ae_game_keyvaluestorage`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `game_id_2` (`game_id`,`key`),
  ADD KEY `game_id` (`game_id`),
  ADD KEY `key` (`key`);

--
-- Indexes for table `ae_game_level`
--
ALTER TABLE `ae_game_level`
  ADD PRIMARY KEY (`id`),
  ADD KEY `game_id` (`game_id`),
  ADD KEY `pointsystem_id` (`pointsystem_id`);

--
-- Indexes for table `ae_game_menu`
--
ALTER TABLE `ae_game_menu`
  ADD PRIMARY KEY (`id`),
  ADD KEY `game_id` (`game_id`,`name`);

--
-- Indexes for table `ae_game_play`
--
ALTER TABLE `ae_game_play`
  ADD PRIMARY KEY (`id`),
  ADD KEY `role_id` (`role_id`),
  ADD KEY `game_id` (`game_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `current_branch_id` (`current_level_id`),
  ADD KEY `priority` (`priority`),
  ADD KEY `last_update` (`last_update`),
  ADD KEY `created` (`created`),
  ADD KEY `status` (`status`),
  ADD KEY `level` (`level`),
  ADD KEY `last_action_update` (`last_action_update`),
  ADD KEY `level_2` (`level`),
  ADD KEY `status_2` (`status`),
  ADD KEY `status_3` (`status`),
  ADD KEY `status_4` (`status`),
  ADD KEY `game_id_2` (`game_id`,`user_id`);

--
-- Indexes for table `ae_game_play_action`
--
ALTER TABLE `ae_game_play_action`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_task` (`action_id`,`play_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `play_id` (`play_id`),
  ADD KEY `trigger_id` (`trigger_id`),
  ADD KEY `shorturl` (`shorturl`),
  ADD KEY `added` (`added`),
  ADD KEY `status` (`status`),
  ADD KEY `action_id` (`action_id`),
  ADD KEY `id_userchannelsetting` (`id_userchannelsetting`),
  ADD KEY `status_2` (`status`),
  ADD KEY `points` (`points`),
  ADD KEY `secondary_points` (`secondary_points`),
  ADD KEY `secondary_points_2` (`secondary_points`),
  ADD KEY `action_id_2` (`action_id`),
  ADD KEY `id_userchannelsetting_2` (`id_userchannelsetting`),
  ADD KEY `status_3` (`status`),
  ADD KEY `points_2` (`points`),
  ADD KEY `secondary_points_3` (`secondary_points`),
  ADD KEY `secondary_points_4` (`secondary_points`),
  ADD KEY `action_id_3` (`action_id`),
  ADD KEY `id_userchannelsetting_3` (`id_userchannelsetting`),
  ADD KEY `status_4` (`status`),
  ADD KEY `points_3` (`points`),
  ADD KEY `secondary_points_5` (`secondary_points`),
  ADD KEY `secondary_points_6` (`secondary_points`);

--
-- Indexes for table `ae_game_play_branch`
--
ALTER TABLE `ae_game_play_branch`
  ADD UNIQUE KEY `play_id` (`play_id`,`branch_id`),
  ADD KEY `branch_id` (`branch_id`);

--
-- Indexes for table `ae_game_play_datastorage`
--
ALTER TABLE `ae_game_play_datastorage`
  ADD PRIMARY KEY (`id`),
  ADD KEY `play_id` (`play_id`),
  ADD KEY `key` (`key`),
  ADD KEY `label` (`label`);

--
-- Indexes for table `ae_game_play_keyvaluestorage`
--
ALTER TABLE `ae_game_play_keyvaluestorage`
  ADD PRIMARY KEY (`id`),
  ADD KEY `play_id` (`play_id`),
  ADD KEY `key` (`key`),
  ADD KEY `value` (`value`);

--
-- Indexes for table `ae_game_play_role`
--
ALTER TABLE `ae_game_play_role`
  ADD PRIMARY KEY (`play_id`),
  ADD KEY `role_id` (`role_id`);

--
-- Indexes for table `ae_game_play_user`
--
ALTER TABLE `ae_game_play_user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_game` (`id_play`),
  ADD KEY `id_role` (`id_role`);

--
-- Indexes for table `ae_game_play_variable`
--
ALTER TABLE `ae_game_play_variable`
  ADD PRIMARY KEY (`id`),
  ADD KEY `play_id` (`play_id`),
  ADD KEY `variable_id` (`variable_id`),
  ADD KEY `play_id_2` (`play_id`,`variable_id`);

--
-- Indexes for table `ae_game_rate`
--
ALTER TABLE `ae_game_rate`
  ADD PRIMARY KEY (`id`),
  ADD KEY `game_id` (`game_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `ae_game_role`
--
ALTER TABLE `ae_game_role`
  ADD PRIMARY KEY (`id`),
  ADD KEY `game_id` (`game_id`);

--
-- Indexes for table `ae_game_score`
--
ALTER TABLE `ae_game_score`
  ADD PRIMARY KEY (`id`),
  ADD KEY `game_id` (`game_id`);

--
-- Indexes for table `ae_game_trigger`
--
ALTER TABLE `ae_game_trigger`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `shortname` (`shortname`),
  ADD KEY `shortname_2` (`shortname`),
  ADD KEY `shortname_3` (`shortname`);

--
-- Indexes for table `ae_game_variable`
--
ALTER TABLE `ae_game_variable`
  ADD PRIMARY KEY (`id`),
  ADD KEY `game_id` (`game_id`),
  ADD KEY `name` (`name`);

--
-- Indexes for table `ae_invitation`
--
ALTER TABLE `ae_invitation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `game_id` (`game_id`);

--
-- Indexes for table `ae_location_log`
--
ALTER TABLE `ae_location_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `game_id` (`game_id`),
  ADD KEY `beacon_id` (`beacon_id`),
  ADD KEY `place_id` (`place_id`),
  ADD KEY `play_id` (`play_id`);

--
-- Indexes for table `ae_locking`
--
ALTER TABLE `ae_locking`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lock_name` (`lock_name`),
  ADD KEY `time` (`time`),
  ADD KEY `game_id` (`game_id`);

--
-- Indexes for table `ae_logic_transactions`
--
ALTER TABLE `ae_logic_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `context` (`context`,`process_id`,`startcount`,`starttime`,`finishtime`,`executiontime`,`flagged`),
  ADD KEY `process_id` (`process_id`),
  ADD KEY `context_2` (`context`),
  ADD KEY `phase` (`phase`);

--
-- Indexes for table `ae_mobile`
--
ALTER TABLE `ae_mobile`
  ADD UNIQUE KEY `game_id` (`game_id`);

--
-- Indexes for table `ae_notification`
--
ALTER TABLE `ae_notification`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_channel` (`id_channel`),
  ADD KEY `id_playaction` (`id_playaction`),
  ADD KEY `play_id` (`play_id`),
  ADD KEY `expired` (`expired`),
  ADD KEY `app_id` (`app_id`),
  ADD KEY `os_success` (`os_success`),
  ADD KEY `os_failed` (`os_failed`),
  ADD KEY `os_converted` (`os_converted`);

--
-- Indexes for table `ae_packages`
--
ALTER TABLE `ae_packages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `local_action_id` (`local_actiontype_id`),
  ADD KEY `game_id` (`game_id`),
  ADD KEY `package_id` (`package_id`);

--
-- Indexes for table `ae_packages_themes`
--
ALTER TABLE `ae_packages_themes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `game_id` (`game_id`),
  ADD KEY `package_id` (`package_id`),
  ADD KEY `theme_id` (`theme_id`);

--
-- Indexes for table `ae_purchase`
--
ALTER TABLE `ae_purchase`
  ADD PRIMARY KEY (`id`),
  ADD KEY `game_id` (`game_id`),
  ADD KEY `play_id` (`play_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `ae_role`
--
ALTER TABLE `ae_role`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ae_role_user`
--
ALTER TABLE `ae_role_user`
  ADD KEY `user_id` (`user_id`),
  ADD KEY `role_id` (`role_id`);

--
-- Indexes for table `ae_settings`
--
ALTER TABLE `ae_settings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `title` (`title`);

--
-- Indexes for table `ae_task`
--
ALTER TABLE `ae_task`
  ADD PRIMARY KEY (`id`),
  ADD KEY `play_id` (`play_id`);

--
-- Indexes for table `aro`
--
ALTER TABLE `aro`
  ADD PRIMARY KEY (`id`),
  ADD KEY `aco_collection_id` (`collection_id`),
  ADD KEY `path` (`path`);

--
-- Indexes for table `aro_collection`
--
ALTER TABLE `aro_collection`
  ADD PRIMARY KEY (`id`),
  ADD KEY `alias` (`alias`);

--
-- Indexes for table `dbadmin_queries`
--
ALTER TABLE `dbadmin_queries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hash` (`hash`);

--
-- Indexes for table `ha_logins`
--
ALTER TABLE `ha_logins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `loginProvider_2` (`loginProvider`,`loginProviderIdentifier`),
  ADD KEY `loginProvider` (`loginProvider`),
  ADD KEY `loginProviderIdentifier` (`loginProviderIdentifier`),
  ADD KEY `userId` (`userId`),
  ADD KEY `id` (`id`);

--
-- Indexes for table `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Indexes for table `menu_item`
--
ALTER TABLE `menu_item`
  ADD PRIMARY KEY (`id`),
  ADD KEY `menu_id` (`menu_id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Indexes for table `permission`
--
ALTER TABLE `permission`
  ADD PRIMARY KEY (`id`),
  ADD KEY `aco_id` (`aco_id`,`aro_id`,`aco_path`,`aro_path`),
  ADD KEY `action_id` (`action_id`),
  ADD KEY `created` (`created`);

--
-- Indexes for table `ssh_keys`
--
ALTER TABLE `ssh_keys`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `tbl_migration`
--
ALTER TABLE `tbl_migration`
  ADD PRIMARY KEY (`version`);

--
-- Indexes for table `usergroups_access`
--
ALTER TABLE `usergroups_access`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `usergroups_configuration`
--
ALTER TABLE `usergroups_configuration`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `usergroups_cron`
--
ALTER TABLE `usergroups_cron`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `usergroups_group`
--
ALTER TABLE `usergroups_group`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `groupname` (`groupname`);

--
-- Indexes for table `usergroups_lookup`
--
ALTER TABLE `usergroups_lookup`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `usergroups_user`
--
ALTER TABLE `usergroups_user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `group_id_idxfk` (`group_id`),
  ADD KEY `email` (`email`),
  ADD KEY `creator_api_key` (`creator_api_key`),
  ADD KEY `id` (`id`,`creator_api_key`),
  ADD KEY `username_2` (`username`,`creator_api_key`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `aco`
--
ALTER TABLE `aco`
  MODIFY `id` mediumint(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `aco_collection`
--
ALTER TABLE `aco_collection`
  MODIFY `id` mediumint(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ae_access_tokens`
--
ALTER TABLE `ae_access_tokens`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1161;
--
-- AUTO_INCREMENT for table `ae_api_errorlog`
--
ALTER TABLE `ae_api_errorlog`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `ae_api_filelookup`
--
ALTER TABLE `ae_api_filelookup`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1576;
--
-- AUTO_INCREMENT for table `ae_api_key`
--
ALTER TABLE `ae_api_key`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ae_app_menus`
--
ALTER TABLE `ae_app_menus`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;
--
-- AUTO_INCREMENT for table `ae_app_menus_user_state`
--
ALTER TABLE `ae_app_menus_user_state`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ae_app_menu_items`
--
ALTER TABLE `ae_app_menu_items`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;
--
-- AUTO_INCREMENT for table `ae_category`
--
ALTER TABLE `ae_category`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `ae_channel`
--
ALTER TABLE `ae_channel`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `ae_channel_sent_sms`
--
ALTER TABLE `ae_channel_sent_sms`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ae_channel_setting`
--
ALTER TABLE `ae_channel_setting`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
--
-- AUTO_INCREMENT for table `ae_channel_setting_user`
--
ALTER TABLE `ae_channel_setting_user`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1986;
--
-- AUTO_INCREMENT for table `ae_channel_user`
--
ALTER TABLE `ae_channel_user`
  MODIFY `id_channel` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `ae_chat`
--
ALTER TABLE `ae_chat`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;
--
-- AUTO_INCREMENT for table `ae_chat_attachments`
--
ALTER TABLE `ae_chat_attachments`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `ae_chat_messages`
--
ALTER TABLE `ae_chat_messages`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
--
-- AUTO_INCREMENT for table `ae_chat_messages_likes`
--
ALTER TABLE `ae_chat_messages_likes`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
--
-- AUTO_INCREMENT for table `ae_chat_users`
--
ALTER TABLE `ae_chat_users`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;
--
-- AUTO_INCREMENT for table `ae_crons`
--
ALTER TABLE `ae_crons`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ae_crons_logs`
--
ALTER TABLE `ae_crons_logs`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ae_ext_article`
--
ALTER TABLE `ae_ext_article`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;
--
-- AUTO_INCREMENT for table `ae_ext_article_bookmarks`
--
ALTER TABLE `ae_ext_article_bookmarks`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ae_ext_article_categories`
--
ALTER TABLE `ae_ext_article_categories`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT for table `ae_ext_article_comments`
--
ALTER TABLE `ae_ext_article_comments`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ae_ext_article_photos`
--
ALTER TABLE `ae_ext_article_photos`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;
--
-- AUTO_INCREMENT for table `ae_ext_article_tags`
--
ALTER TABLE `ae_ext_article_tags`
  MODIFY `id` int(1) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ae_ext_bbs`
--
ALTER TABLE `ae_ext_bbs`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ae_ext_bid_items`
--
ALTER TABLE `ae_ext_bid_items`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
--
-- AUTO_INCREMENT for table `ae_ext_bookings`
--
ALTER TABLE `ae_ext_bookings`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT for table `ae_ext_calendar_entry`
--
ALTER TABLE `ae_ext_calendar_entry`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;
--
-- AUTO_INCREMENT for table `ae_ext_classifieds_categories`
--
ALTER TABLE `ae_ext_classifieds_categories`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
--
-- AUTO_INCREMENT for table `ae_ext_classifieds_categories_items`
--
ALTER TABLE `ae_ext_classifieds_categories_items`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ae_ext_classifieds_favourite_items`
--
ALTER TABLE `ae_ext_classifieds_favourite_items`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT for table `ae_ext_classifieds_filter`
--
ALTER TABLE `ae_ext_classifieds_filter`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `ae_ext_classifieds_items`
--
ALTER TABLE `ae_ext_classifieds_items`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;
--
-- AUTO_INCREMENT for table `ae_ext_classifieds_items_meta`
--
ALTER TABLE `ae_ext_classifieds_items_meta`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ae_ext_club_news`
--
ALTER TABLE `ae_ext_club_news`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ae_ext_deals_logs`
--
ALTER TABLE `ae_ext_deals_logs`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ae_ext_diary`
--
ALTER TABLE `ae_ext_diary`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ae_ext_fit_exercise`
--
ALTER TABLE `ae_ext_fit_exercise`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT for table `ae_ext_fit_exercise_movement`
--
ALTER TABLE `ae_ext_fit_exercise_movement`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `ae_ext_fit_movement`
--
ALTER TABLE `ae_ext_fit_movement`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `ae_ext_fit_movement_category`
--
ALTER TABLE `ae_ext_fit_movement_category`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `ae_ext_fit_pr`
--
ALTER TABLE `ae_ext_fit_pr`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT for table `ae_ext_fit_program`
--
ALTER TABLE `ae_ext_fit_program`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `ae_ext_fit_program_category`
--
ALTER TABLE `ae_ext_fit_program_category`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT for table `ae_ext_fit_program_exercise`
--
ALTER TABLE `ae_ext_fit_program_exercise`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
--
-- AUTO_INCREMENT for table `ae_ext_fit_program_recipe`
--
ALTER TABLE `ae_ext_fit_program_recipe`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ae_ext_fit_program_selection`
--
ALTER TABLE `ae_ext_fit_program_selection`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `ae_ext_fit_program_subcategory`
--
ALTER TABLE `ae_ext_fit_program_subcategory`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `ae_ext_fit_pr_user`
--
ALTER TABLE `ae_ext_fit_pr_user`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
--
-- AUTO_INCREMENT for table `ae_ext_food_custom_ingredient`
--
ALTER TABLE `ae_ext_food_custom_ingredient`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `ae_ext_food_ingredient`
--
ALTER TABLE `ae_ext_food_ingredient`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT for table `ae_ext_food_ingredient_category`
--
ALTER TABLE `ae_ext_food_ingredient_category`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `ae_ext_food_recipe`
--
ALTER TABLE `ae_ext_food_recipe`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `ae_ext_food_recipe_ingredient`
--
ALTER TABLE `ae_ext_food_recipe_ingredient`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
--
-- AUTO_INCREMENT for table `ae_ext_food_recipe_step`
--
ALTER TABLE `ae_ext_food_recipe_step`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
--
-- AUTO_INCREMENT for table `ae_ext_food_recipe_type`
--
ALTER TABLE `ae_ext_food_recipe_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `ae_ext_food_shopping_list`
--
ALTER TABLE `ae_ext_food_shopping_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT for table `ae_ext_gallery_images`
--
ALTER TABLE `ae_ext_gallery_images`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ae_ext_golf_hole`
--
ALTER TABLE `ae_ext_golf_hole`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ae_ext_golf_hole_user`
--
ALTER TABLE `ae_ext_golf_hole_user`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ae_ext_items`
--
ALTER TABLE `ae_ext_items`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=195;
--
-- AUTO_INCREMENT for table `ae_ext_items_categories`
--
ALTER TABLE `ae_ext_items_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;
--
-- AUTO_INCREMENT for table `ae_ext_items_category_item`
--
ALTER TABLE `ae_ext_items_category_item`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
--
-- AUTO_INCREMENT for table `ae_ext_items_filters`
--
ALTER TABLE `ae_ext_items_filters`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
--
-- AUTO_INCREMENT for table `ae_ext_items_images`
--
ALTER TABLE `ae_ext_items_images`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT for table `ae_ext_items_likes`
--
ALTER TABLE `ae_ext_items_likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=260;
--
-- AUTO_INCREMENT for table `ae_ext_items_reminders`
--
ALTER TABLE `ae_ext_items_reminders`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ae_ext_items_reports`
--
ALTER TABLE `ae_ext_items_reports`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `ae_ext_items_tags`
--
ALTER TABLE `ae_ext_items_tags`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;
--
-- AUTO_INCREMENT for table `ae_ext_items_tag_item`
--
ALTER TABLE `ae_ext_items_tag_item`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=161;
--
-- AUTO_INCREMENT for table `ae_ext_meter`
--
ALTER TABLE `ae_ext_meter`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `ae_ext_meter_appliances`
--
ALTER TABLE `ae_ext_meter_appliances`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ae_ext_meter_data`
--
ALTER TABLE `ae_ext_meter_data`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=827;
--
-- AUTO_INCREMENT for table `ae_ext_mobilebeacons`
--
ALTER TABLE `ae_ext_mobilebeacons`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ae_ext_mobileevents`
--
ALTER TABLE `ae_ext_mobileevents`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ae_ext_mobileevents_participants`
--
ALTER TABLE `ae_ext_mobileevents_participants`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ae_ext_mobilefeedbacktool`
--
ALTER TABLE `ae_ext_mobilefeedbacktool`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ae_ext_mobilefeedbacktool_departments`
--
ALTER TABLE `ae_ext_mobilefeedbacktool_departments`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ae_ext_mobilefeedbacktool_fundamentals`
--
ALTER TABLE `ae_ext_mobilefeedbacktool_fundamentals`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ae_ext_mobilefeedbacktool_teams`
--
ALTER TABLE `ae_ext_mobilefeedbacktool_teams`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ae_ext_mobilefeedbacktool_teams_members`
--
ALTER TABLE `ae_ext_mobilefeedbacktool_teams_members`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ae_ext_mobilematching`
--
ALTER TABLE `ae_ext_mobilematching`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=579;
--
-- AUTO_INCREMENT for table `ae_ext_mobilematching_meta`
--
ALTER TABLE `ae_ext_mobilematching_meta`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ae_ext_mobileplaces`
--
ALTER TABLE `ae_ext_mobileplaces`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `ae_ext_mobileproperty`
--
ALTER TABLE `ae_ext_mobileproperty`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ae_ext_mobileproperty_bookmark`
--
ALTER TABLE `ae_ext_mobileproperty_bookmark`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ae_ext_mobileproperty_settings`
--
ALTER TABLE `ae_ext_mobileproperty_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ae_ext_mobileproperty_users`
--
ALTER TABLE `ae_ext_mobileproperty_users`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ae_ext_mtasks`
--
ALTER TABLE `ae_ext_mtasks`
  MODIFY `id` int(14) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;
--
-- AUTO_INCREMENT for table `ae_ext_mtasks_invitations`
--
ALTER TABLE `ae_ext_mtasks_invitations`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;
--
-- AUTO_INCREMENT for table `ae_ext_mtasks_proof`
--
ALTER TABLE `ae_ext_mtasks_proof`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;
--
-- AUTO_INCREMENT for table `ae_ext_notifications`
--
ALTER TABLE `ae_ext_notifications`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=199;
--
-- AUTO_INCREMENT for table `ae_ext_photostream`
--
ALTER TABLE `ae_ext_photostream`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ae_ext_photostream_post`
--
ALTER TABLE `ae_ext_photostream_post`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ae_ext_products`
--
ALTER TABLE `ae_ext_products`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `ae_ext_products_bookmarks`
--
ALTER TABLE `ae_ext_products_bookmarks`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ae_ext_products_carts`
--
ALTER TABLE `ae_ext_products_carts`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;
--
-- AUTO_INCREMENT for table `ae_ext_products_categories`
--
ALTER TABLE `ae_ext_products_categories`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `ae_ext_products_photos`
--
ALTER TABLE `ae_ext_products_photos`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ae_ext_products_reviews`
--
ALTER TABLE `ae_ext_products_reviews`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ae_ext_products_tags`
--
ALTER TABLE `ae_ext_products_tags`
  MODIFY `id` int(1) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ae_ext_quiz`
--
ALTER TABLE `ae_ext_quiz`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `ae_ext_quiz_question`
--
ALTER TABLE `ae_ext_quiz_question`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
--
-- AUTO_INCREMENT for table `ae_ext_quiz_question_option`
--
ALTER TABLE `ae_ext_quiz_question_option`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=146;
--
-- AUTO_INCREMENT for table `ae_ext_quiz_sets`
--
ALTER TABLE `ae_ext_quiz_sets`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;
--
-- AUTO_INCREMENT for table `ae_ext_requests`
--
ALTER TABLE `ae_ext_requests`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ae_ext_request_payments`
--
ALTER TABLE `ae_ext_request_payments`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ae_ext_tattoos`
--
ALTER TABLE `ae_ext_tattoos`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;
--
-- AUTO_INCREMENT for table `ae_ext_tattoos_bookings`
--
ALTER TABLE `ae_ext_tattoos_bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;
--
-- AUTO_INCREMENT for table `ae_ext_tattoos_categories`
--
ALTER TABLE `ae_ext_tattoos_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `ae_ext_tattoos_likes`
--
ALTER TABLE `ae_ext_tattoos_likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=109;
--
-- AUTO_INCREMENT for table `ae_ext_tattoos_tags`
--
ALTER TABLE `ae_ext_tattoos_tags`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT for table `ae_ext_tattoos_tag_tattoo`
--
ALTER TABLE `ae_ext_tattoos_tag_tattoo`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
--
-- AUTO_INCREMENT for table `ae_ext_user_bids`
--
ALTER TABLE `ae_ext_user_bids`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
--
-- AUTO_INCREMENT for table `ae_ext_user_bid_item_images`
--
ALTER TABLE `ae_ext_user_bid_item_images`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
--
-- AUTO_INCREMENT for table `ae_ext_wallet`
--
ALTER TABLE `ae_ext_wallet`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ae_ext_wallet_logs`
--
ALTER TABLE `ae_ext_wallet_logs`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ae_fbfriend`
--
ALTER TABLE `ae_fbfriend`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ae_fb_invite`
--
ALTER TABLE `ae_fb_invite`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ae_filenames`
--
ALTER TABLE `ae_filenames`
  MODIFY `id` int(14) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
--
-- AUTO_INCREMENT for table `ae_game`
--
ALTER TABLE `ae_game`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT for table `ae_game_badge`
--
ALTER TABLE `ae_game_badge`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ae_game_branch`
--
ALTER TABLE `ae_game_branch`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=114;
--
-- AUTO_INCREMENT for table `ae_game_branch_action`
--
ALTER TABLE `ae_game_branch_action`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=152;
--
-- AUTO_INCREMENT for table `ae_game_branch_action_type`
--
ALTER TABLE `ae_game_branch_action_type`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=133;
--
-- AUTO_INCREMENT for table `ae_game_keyvaluestorage`
--
ALTER TABLE `ae_game_keyvaluestorage`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ae_game_level`
--
ALTER TABLE `ae_game_level`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ae_game_menu`
--
ALTER TABLE `ae_game_menu`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ae_game_play`
--
ALTER TABLE `ae_game_play`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1761;
--
-- AUTO_INCREMENT for table `ae_game_play_action`
--
ALTER TABLE `ae_game_play_action`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24806;
--
-- AUTO_INCREMENT for table `ae_game_play_datastorage`
--
ALTER TABLE `ae_game_play_datastorage`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ae_game_play_keyvaluestorage`
--
ALTER TABLE `ae_game_play_keyvaluestorage`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=475;
--
-- AUTO_INCREMENT for table `ae_game_play_role`
--
ALTER TABLE `ae_game_play_role`
  MODIFY `play_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ae_game_play_user`
--
ALTER TABLE `ae_game_play_user`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ae_game_play_variable`
--
ALTER TABLE `ae_game_play_variable`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20437;
--
-- AUTO_INCREMENT for table `ae_game_rate`
--
ALTER TABLE `ae_game_rate`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ae_game_role`
--
ALTER TABLE `ae_game_role`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ae_game_score`
--
ALTER TABLE `ae_game_score`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ae_game_trigger`
--
ALTER TABLE `ae_game_trigger`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;
--
-- AUTO_INCREMENT for table `ae_game_variable`
--
ALTER TABLE `ae_game_variable`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1147;
--
-- AUTO_INCREMENT for table `ae_invitation`
--
ALTER TABLE `ae_invitation`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ae_location_log`
--
ALTER TABLE `ae_location_log`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ae_locking`
--
ALTER TABLE `ae_locking`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=165551;
--
-- AUTO_INCREMENT for table `ae_logic_transactions`
--
ALTER TABLE `ae_logic_transactions`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ae_notification`
--
ALTER TABLE `ae_notification`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=218;
--
-- AUTO_INCREMENT for table `ae_packages`
--
ALTER TABLE `ae_packages`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ae_packages_themes`
--
ALTER TABLE `ae_packages_themes`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ae_purchase`
--
ALTER TABLE `ae_purchase`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ae_role`
--
ALTER TABLE `ae_role`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `ae_settings`
--
ALTER TABLE `ae_settings`
  MODIFY `id` mediumint(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ae_task`
--
ALTER TABLE `ae_task`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=271;
--
-- AUTO_INCREMENT for table `aro`
--
ALTER TABLE `aro`
  MODIFY `id` mediumint(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `aro_collection`
--
ALTER TABLE `aro_collection`
  MODIFY `id` mediumint(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `dbadmin_queries`
--
ALTER TABLE `dbadmin_queries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `ha_logins`
--
ALTER TABLE `ha_logins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `menu`
--
ALTER TABLE `menu`
  MODIFY `id` mediumint(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
--
-- AUTO_INCREMENT for table `menu_item`
--
ALTER TABLE `menu_item`
  MODIFY `id` mediumint(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
--
-- AUTO_INCREMENT for table `permission`
--
ALTER TABLE `permission`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ssh_keys`
--
ALTER TABLE `ssh_keys`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `usergroups_access`
--
ALTER TABLE `usergroups_access`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `usergroups_configuration`
--
ALTER TABLE `usergroups_configuration`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
--
-- AUTO_INCREMENT for table `usergroups_cron`
--
ALTER TABLE `usergroups_cron`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `usergroups_group`
--
ALTER TABLE `usergroups_group`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `usergroups_lookup`
--
ALTER TABLE `usergroups_lookup`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `usergroups_user`
--
ALTER TABLE `usergroups_user`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1751;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `ae_access_tokens`
--
ALTER TABLE `ae_access_tokens`
  ADD CONSTRAINT `ae_access_tokens_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `usergroups_user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_access_tokens_ibfk_3` FOREIGN KEY (`play_id`) REFERENCES `ae_game_play` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_api_filelookup`
--
ALTER TABLE `ae_api_filelookup`
  ADD CONSTRAINT `ae_api_filelookup_ibfk_1` FOREIGN KEY (`game_id`) REFERENCES `ae_game` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_app_menus`
--
ALTER TABLE `ae_app_menus`
  ADD CONSTRAINT `ae_app_menus_ibfk_1` FOREIGN KEY (`app_id`) REFERENCES `ae_game` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_app_menu_items`
--
ALTER TABLE `ae_app_menu_items`
  ADD CONSTRAINT `ae_app_menu_items_ibfk_1` FOREIGN KEY (`menu_id`) REFERENCES `ae_app_menus` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_app_menu_items_ibfk_2` FOREIGN KEY (`menu_id`) REFERENCES `ae_app_menus` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_channel_setting`
--
ALTER TABLE `ae_channel_setting`
  ADD CONSTRAINT `ae_channel_setting_ibfk_2` FOREIGN KEY (`id_channel`) REFERENCES `ae_channel` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_channel_setting_ibfk_3` FOREIGN KEY (`type`) REFERENCES `ae_fieldtype` (`fieldtype`) ON DELETE SET NULL ON UPDATE NO ACTION;

--
-- Constraints for table `ae_channel_setting_user`
--
ALTER TABLE `ae_channel_setting_user`
  ADD CONSTRAINT `ae_channel_setting_user_ibfk_3` FOREIGN KEY (`id_user`) REFERENCES `usergroups_user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_channel_setting_user_ibfk_4` FOREIGN KEY (`id_setting`) REFERENCES `ae_channel_setting` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_channel_user`
--
ALTER TABLE `ae_channel_user`
  ADD CONSTRAINT `ae_channel_user_ibfk_7` FOREIGN KEY (`id_channel`) REFERENCES `ae_channel` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_channel_user_ibfk_8` FOREIGN KEY (`id_user`) REFERENCES `usergroups_user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_chat`
--
ALTER TABLE `ae_chat`
  ADD CONSTRAINT `ae_chat_ibfk_1` FOREIGN KEY (`game_id`) REFERENCES `ae_game` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_game_play` FOREIGN KEY (`owner_play_id`) REFERENCES `ae_game_play` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION;

--
-- Constraints for table `ae_chat_attachments`
--
ALTER TABLE `ae_chat_attachments`
  ADD CONSTRAINT `ae_chat_messages` FOREIGN KEY (`chat_message_id`) REFERENCES `ae_chat_messages` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_chat_messages`
--
ALTER TABLE `ae_chat_messages`
  ADD CONSTRAINT `chat_id` FOREIGN KEY (`chat_id`) REFERENCES `ae_chat` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_chat_messages_likes`
--
ALTER TABLE `ae_chat_messages_likes`
  ADD CONSTRAINT `ae_chat_messages_likes_ibfk_1` FOREIGN KEY (`message_id`) REFERENCES `ae_chat_messages` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_chat_messages_likes_ibfk_2` FOREIGN KEY (`play_id`) REFERENCES `ae_game_play` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_chat_users`
--
ALTER TABLE `ae_chat_users`
  ADD CONSTRAINT `chat` FOREIGN KEY (`chat_id`) REFERENCES `ae_chat` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `chat_user_play_id` FOREIGN KEY (`chat_user_play_id`) REFERENCES `ae_game_play` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_ext_article`
--
ALTER TABLE `ae_ext_article`
  ADD CONSTRAINT `ae_ext_article_ibfk_2` FOREIGN KEY (`play_id`) REFERENCES `ae_game_play` (`id`) ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_ext_article_ibfk_3` FOREIGN KEY (`app_id`) REFERENCES `ae_game` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_ext_article_ibfk_4` FOREIGN KEY (`category_id`) REFERENCES `ae_ext_article_categories` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION;

--
-- Constraints for table `ae_ext_article_bookmarks`
--
ALTER TABLE `ae_ext_article_bookmarks`
  ADD CONSTRAINT `ae_ext_article_bookmarks_ibfk_1` FOREIGN KEY (`play_id`) REFERENCES `ae_game_play` (`id`),
  ADD CONSTRAINT `ae_ext_article_bookmarks_ibfk_2` FOREIGN KEY (`article_id`) REFERENCES `ae_ext_article` (`id`);

--
-- Constraints for table `ae_ext_article_categories`
--
ALTER TABLE `ae_ext_article_categories`
  ADD CONSTRAINT `ae_ext_article_categories_ibfk_1` FOREIGN KEY (`app_id`) REFERENCES `ae_game` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_ext_article_comments`
--
ALTER TABLE `ae_ext_article_comments`
  ADD CONSTRAINT `ae_ext_article_comments_ibfk_1` FOREIGN KEY (`play_id`) REFERENCES `ae_game_play` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_ext_article_photos`
--
ALTER TABLE `ae_ext_article_photos`
  ADD CONSTRAINT `ae_ext_article_photos_ibfk_1` FOREIGN KEY (`article_id`) REFERENCES `ae_ext_article` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_ext_article_tags`
--
ALTER TABLE `ae_ext_article_tags`
  ADD CONSTRAINT `ae_ext_article_tags_ibfk_1` FOREIGN KEY (`app_id`) REFERENCES `ae_game` (`id`);

--
-- Constraints for table `ae_ext_bbs`
--
ALTER TABLE `ae_ext_bbs`
  ADD CONSTRAINT `ae_ext_bbs_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `usergroups_user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_ext_bbs_ibfk_3` FOREIGN KEY (`playtask_id`) REFERENCES `ae_game_play_action` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_ext_bid_items`
--
ALTER TABLE `ae_ext_bid_items`
  ADD CONSTRAINT `ae_ext_bid_items_ibfk_1` FOREIGN KEY (`play_id`) REFERENCES `ae_game_play` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_ext_bookings`
--
ALTER TABLE `ae_ext_bookings`
  ADD CONSTRAINT `ae_ext_bookings_ibfk_1` FOREIGN KEY (`play_id`) REFERENCES `ae_game_play` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_ext_bookings_ibfk_2` FOREIGN KEY (`assignee_play_id`) REFERENCES `ae_game_play` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_ext_bookings_ibfk_3` FOREIGN KEY (`item_id`) REFERENCES `ae_ext_items` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION;

--
-- Constraints for table `ae_ext_calendar_entry`
--
ALTER TABLE `ae_ext_calendar_entry`
  ADD CONSTRAINT `ae_ext_calendar_entry_ibfk_1` FOREIGN KEY (`play_id`) REFERENCES `ae_game_play` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_ext_calendar_entry_ibfk_5` FOREIGN KEY (`recipe_id`) REFERENCES `ae_ext_food_recipe` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_ext_calendar_entry_ibfk_6` FOREIGN KEY (`exercise_id`) REFERENCES `ae_ext_fit_exercise` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_ext_calendar_entry_ibfk_7` FOREIGN KEY (`program_id`) REFERENCES `ae_ext_fit_program` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_ext_calendar_entry_ibfk_8` FOREIGN KEY (`type_id`) REFERENCES `ae_ext_fit_program_category` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_ext_classifieds_categories_items`
--
ALTER TABLE `ae_ext_classifieds_categories_items`
  ADD CONSTRAINT `ae_ext_classifieds_categories_items_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `ae_ext_classifieds_categories` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_ext_classifieds_categories_items_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `ae_ext_classifieds_items` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_ext_classifieds_favourite_items`
--
ALTER TABLE `ae_ext_classifieds_favourite_items`
  ADD CONSTRAINT `ae_ext_classifieds_favourite_items_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `ae_ext_classifieds_items` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_ext_classifieds_filter`
--
ALTER TABLE `ae_ext_classifieds_filter`
  ADD CONSTRAINT `ae_ext_classifieds_filter_ibfk_1` FOREIGN KEY (`play_id`) REFERENCES `ae_game_play` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_ext_club_news`
--
ALTER TABLE `ae_ext_club_news`
  ADD CONSTRAINT `ae_ext_club_news_ibfk_1` FOREIGN KEY (`club_id`) REFERENCES `ae_ext_mobileplaces` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_ext_deals_logs`
--
ALTER TABLE `ae_ext_deals_logs`
  ADD CONSTRAINT `ae_ext_deals_logs_ibfk_1` FOREIGN KEY (`app_id`) REFERENCES `ae_game` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ae_ext_diary`
--
ALTER TABLE `ae_ext_diary`
  ADD CONSTRAINT `ae_ext_diary_ibfk_1` FOREIGN KEY (`playtask_id`) REFERENCES `ae_game_play_action` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_ext_diary_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `usergroups_user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_ext_fit_exercise`
--
ALTER TABLE `ae_ext_fit_exercise`
  ADD CONSTRAINT `ae_ext_fit_exercise.app_id` FOREIGN KEY (`app_id`) REFERENCES `ae_game` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_ext_fit_exercise_ibfk_1` FOREIGN KEY (`article_id`) REFERENCES `ae_ext_article` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION;

--
-- Constraints for table `ae_ext_fit_exercise_movement`
--
ALTER TABLE `ae_ext_fit_exercise_movement`
  ADD CONSTRAINT `ae_ext_fit_exercise_movement_ibfk_1` FOREIGN KEY (`exercise_id`) REFERENCES `ae_ext_fit_exercise` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_ext_fit_exercise_movement_ibfk_2` FOREIGN KEY (`movement_id`) REFERENCES `ae_ext_fit_movement` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_ext_fit_exercise_movement_ibfk_3` FOREIGN KEY (`movement_category_id`) REFERENCES `ae_ext_fit_movement_category` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_ext_fit_movement`
--
ALTER TABLE `ae_ext_fit_movement`
  ADD CONSTRAINT `ae_ext_fit_movement.app_id` FOREIGN KEY (`app_id`) REFERENCES `ae_game` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_ext_fit_movement_ibfk_1` FOREIGN KEY (`article_id`) REFERENCES `ae_ext_article` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION;

--
-- Constraints for table `ae_ext_fit_pr`
--
ALTER TABLE `ae_ext_fit_pr`
  ADD CONSTRAINT `ae_ext_fit_pr_ibfk_1` FOREIGN KEY (`app_id`) REFERENCES `ae_game` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_ext_fit_program`
--
ALTER TABLE `ae_ext_fit_program`
  ADD CONSTRAINT `ae_ext_fit_program_app_id.app_id` FOREIGN KEY (`app_id`) REFERENCES `ae_game` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_ext_fit_program_ibfk_1` FOREIGN KEY (`article_id`) REFERENCES `ae_ext_article` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_ext_fit_program_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `ae_ext_fit_program_category` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_ext_fit_program_ibfk_3` FOREIGN KEY (`subcategory_id`) REFERENCES `ae_ext_fit_program_subcategory` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION;

--
-- Constraints for table `ae_ext_fit_program_category`
--
ALTER TABLE `ae_ext_fit_program_category`
  ADD CONSTRAINT `ae_ext_fit_program_category.app_id` FOREIGN KEY (`app_id`) REFERENCES `ae_game` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_ext_fit_program_exercise`
--
ALTER TABLE `ae_ext_fit_program_exercise`
  ADD CONSTRAINT `ae_ext_fit_program_exercise_ibfk_1` FOREIGN KEY (`exercise_id`) REFERENCES `ae_ext_fit_exercise` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_ext_fit_program_exercise_ibfk_2` FOREIGN KEY (`program_id`) REFERENCES `ae_ext_fit_program` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_ext_fit_program_recipe`
--
ALTER TABLE `ae_ext_fit_program_recipe`
  ADD CONSTRAINT `ae_ext_fit_program_recipe_ibfk_1` FOREIGN KEY (`program_id`) REFERENCES `ae_ext_fit_program` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_ext_fit_program_recipe_ibfk_2` FOREIGN KEY (`recipe_id`) REFERENCES `ae_ext_food_recipe` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_ext_fit_program_selection`
--
ALTER TABLE `ae_ext_fit_program_selection`
  ADD CONSTRAINT `ae_ext_fit_program_selection_ibfk_1` FOREIGN KEY (`play_id`) REFERENCES `ae_game_play` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_ext_fit_program_selection_ibfk_2` FOREIGN KEY (`program_id`) REFERENCES `ae_ext_fit_program` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_ext_fit_program_subcategory`
--
ALTER TABLE `ae_ext_fit_program_subcategory`
  ADD CONSTRAINT `ae_ext_fit_program_subcategory_ibfk_1` FOREIGN KEY (`app_id`) REFERENCES `ae_game` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_ext_fit_pr_user`
--
ALTER TABLE `ae_ext_fit_pr_user`
  ADD CONSTRAINT `ae_ext_fit_pr_user_ibfk_1` FOREIGN KEY (`play_id`) REFERENCES `ae_game_play` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_ext_fit_pr_user_ibfk_2` FOREIGN KEY (`pr_id`) REFERENCES `ae_ext_fit_pr` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_ext_food_custom_ingredient`
--
ALTER TABLE `ae_ext_food_custom_ingredient`
  ADD CONSTRAINT `ae_ext_food_custom_ingredient_ibfk_1` FOREIGN KEY (`play_id`) REFERENCES `ae_game_play` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_ext_food_custom_ingredient_ibfk_2` FOREIGN KEY (`ingredient_category`) REFERENCES `ae_ext_food_ingredient_category` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION;

--
-- Constraints for table `ae_ext_food_ingredient`
--
ALTER TABLE `ae_ext_food_ingredient`
  ADD CONSTRAINT `category` FOREIGN KEY (`category_id`) REFERENCES `ae_ext_food_ingredient_category` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_ext_food_recipe`
--
ALTER TABLE `ae_ext_food_recipe`
  ADD CONSTRAINT `recipe_type` FOREIGN KEY (`type_id`) REFERENCES `ae_ext_food_recipe_type` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_ext_food_recipe_ingredient`
--
ALTER TABLE `ae_ext_food_recipe_ingredient`
  ADD CONSTRAINT `ingredient_id` FOREIGN KEY (`ingredient_id`) REFERENCES `ae_ext_food_ingredient` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `recipe_id` FOREIGN KEY (`recipe_id`) REFERENCES `ae_ext_food_recipe` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_ext_food_recipe_step`
--
ALTER TABLE `ae_ext_food_recipe_step`
  ADD CONSTRAINT `recipe` FOREIGN KEY (`recipe_id`) REFERENCES `ae_ext_food_recipe` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_ext_food_recipe_type`
--
ALTER TABLE `ae_ext_food_recipe_type`
  ADD CONSTRAINT `ae_ext_food_recipe_type.app_id` FOREIGN KEY (`app_id`) REFERENCES `ae_game` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `app_id` FOREIGN KEY (`app_id`) REFERENCES `ae_game` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_ext_food_shopping_list`
--
ALTER TABLE `ae_ext_food_shopping_list`
  ADD CONSTRAINT `ae_ext_food_shopping_list_ibfk_1` FOREIGN KEY (`ingredient_id`) REFERENCES `ae_ext_food_ingredient` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_ext_food_shopping_list_ibfk_2` FOREIGN KEY (`play_id`) REFERENCES `ae_game_play` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_ext_gallery_images`
--
ALTER TABLE `ae_ext_gallery_images`
  ADD CONSTRAINT `ae_ext_gallery_images_ibfk_1` FOREIGN KEY (`play_id`) REFERENCES `ae_game_play` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_ext_golf_hole`
--
ALTER TABLE `ae_ext_golf_hole`
  ADD CONSTRAINT `place_hole` FOREIGN KEY (`place_id`) REFERENCES `ae_ext_mobileplaces` (`id`) ON DELETE SET NULL ON UPDATE SET NULL;

--
-- Constraints for table `ae_ext_golf_hole_user`
--
ALTER TABLE `ae_ext_golf_hole_user`
  ADD CONSTRAINT `ae_ext_golf_hole_user_ibfk_1` FOREIGN KEY (`hole_id`) REFERENCES `ae_ext_golf_hole` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_ext_golf_hole_user_ibfk_2` FOREIGN KEY (`event_id`) REFERENCES `ae_ext_mobileevents` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_ext_golf_hole_user_ibfk_3` FOREIGN KEY (`play_id`) REFERENCES `ae_game_play` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_ext_items`
--
ALTER TABLE `ae_ext_items`
  ADD CONSTRAINT `ae_ext_items_ibfk_1` FOREIGN KEY (`play_id`) REFERENCES `ae_game_play` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_ext_items_ibfk_2` FOREIGN KEY (`game_id`) REFERENCES `ae_game` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_ext_items_ibfk_3` FOREIGN KEY (`category_id`) REFERENCES `ae_ext_items_categories` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_ext_items_ibfk_4` FOREIGN KEY (`category_id`) REFERENCES `ae_ext_items_categories` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION;

--
-- Constraints for table `ae_ext_items_categories`
--
ALTER TABLE `ae_ext_items_categories`
  ADD CONSTRAINT `ae_ext_items_categories_ibfk_1` FOREIGN KEY (`app_id`) REFERENCES `ae_game` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_ext_items_category_item`
--
ALTER TABLE `ae_ext_items_category_item`
  ADD CONSTRAINT `ae_ext_items_category_item_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `ae_ext_items` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_ext_items_category_item_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `ae_ext_items_categories` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_ext_items_images`
--
ALTER TABLE `ae_ext_items_images`
  ADD CONSTRAINT `ae_ext_items_images_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `ae_ext_items` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_ext_items_likes`
--
ALTER TABLE `ae_ext_items_likes`
  ADD CONSTRAINT `ae_ext_items_likes_ibfk_1` FOREIGN KEY (`play_id`) REFERENCES `ae_game_play` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_ext_items_likes_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `ae_ext_items` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_ext_items_reminders`
--
ALTER TABLE `ae_ext_items_reminders`
  ADD CONSTRAINT `ae_ext_items_reminders_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `ae_ext_items` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_ext_items_reports`
--
ALTER TABLE `ae_ext_items_reports`
  ADD CONSTRAINT `ae_ext_items_reports_ibfk_1` FOREIGN KEY (`play_id`) REFERENCES `ae_game_play` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_ext_items_reports_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `ae_ext_items` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_ext_items_reports_ibfk_3` FOREIGN KEY (`item_owner_id`) REFERENCES `ae_game_play` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_ext_meter_data`
--
ALTER TABLE `ae_ext_meter_data`
  ADD CONSTRAINT `ae_ext_meter_data_ibfk_1` FOREIGN KEY (`meter_id`) REFERENCES `ae_ext_meter` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_ext_mobileevents`
--
ALTER TABLE `ae_ext_mobileevents`
  ADD CONSTRAINT `ae_ext_mobileevents_ibfk_1` FOREIGN KEY (`place_id`) REFERENCES `ae_ext_mobileplaces` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `gameevents_1` FOREIGN KEY (`game_id`) REFERENCES `ae_game` (`id`),
  ADD CONSTRAINT `playevents_1` FOREIGN KEY (`play_id`) REFERENCES `ae_game_play` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ae_ext_mobileevents_participants`
--
ALTER TABLE `ae_ext_mobileevents_participants`
  ADD CONSTRAINT `aeplay` FOREIGN KEY (`play_id`) REFERENCES `ae_game_play` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `event` FOREIGN KEY (`event_id`) REFERENCES `ae_ext_mobileevents` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ae_ext_mobilefeedbacktool`
--
ALTER TABLE `ae_ext_mobilefeedbacktool`
  ADD CONSTRAINT `ae_ext_mobilefeedbacktool_ibfk_1` FOREIGN KEY (`game_id`) REFERENCES `ae_game` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_ext_mobilefeedbacktool_ibfk_3` FOREIGN KEY (`author_id`) REFERENCES `ae_game_play` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION;

--
-- Constraints for table `ae_ext_mobilefeedbacktool_departments`
--
ALTER TABLE `ae_ext_mobilefeedbacktool_departments`
  ADD CONSTRAINT `ae_ext_mobilefeedbacktool_departments_ibfk_1` FOREIGN KEY (`game_id`) REFERENCES `ae_game` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_ext_mobilefeedbacktool_fundamentals`
--
ALTER TABLE `ae_ext_mobilefeedbacktool_fundamentals`
  ADD CONSTRAINT `ae_ext_mobilefeedbacktool_fundamentals_ibfk_1` FOREIGN KEY (`game_id`) REFERENCES `ae_game` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_ext_mobilefeedbacktool_teams`
--
ALTER TABLE `ae_ext_mobilefeedbacktool_teams`
  ADD CONSTRAINT `ae_ext_mobilefeedbacktool_teams_ibfk_1` FOREIGN KEY (`game_id`) REFERENCES `ae_game` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_ext_mobilefeedbacktool_teams_ibfk_2` FOREIGN KEY (`owner_id`) REFERENCES `ae_game_play` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_ext_mobilefeedbacktool_teams_members`
--
ALTER TABLE `ae_ext_mobilefeedbacktool_teams_members`
  ADD CONSTRAINT `ae_ext_mobilefeedbacktool_teams_members_ibfk_1` FOREIGN KEY (`team_id`) REFERENCES `ae_ext_mobilefeedbacktool_teams` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_ext_mobilefeedbacktool_teams_members_ibfk_2` FOREIGN KEY (`play_id`) REFERENCES `ae_game_play` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_ext_mobilematching`
--
ALTER TABLE `ae_ext_mobilematching`
  ADD CONSTRAINT `game` FOREIGN KEY (`game_id`) REFERENCES `ae_game` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `play` FOREIGN KEY (`play_id`) REFERENCES `ae_game_play` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_ext_mobileplaces`
--
ALTER TABLE `ae_ext_mobileplaces`
  ADD CONSTRAINT `ae_ext_mobileplaces_ibfk_1` FOREIGN KEY (`game_id`) REFERENCES `ae_game` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_ext_mobileproperty`
--
ALTER TABLE `ae_ext_mobileproperty`
  ADD CONSTRAINT `ae_ext_mobileproperty_ibfk_1` FOREIGN KEY (`game_id`) REFERENCES `ae_game` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_ext_mobileproperty_ibfk_2` FOREIGN KEY (`play_id`) REFERENCES `ae_game_play` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_ext_mobileproperty_bookmark`
--
ALTER TABLE `ae_ext_mobileproperty_bookmark`
  ADD CONSTRAINT `ae_ext_mobileproperty_bookmark_ibfk_1` FOREIGN KEY (`play_id`) REFERENCES `ae_game_play` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_ext_mobileproperty_bookmark_ibfk_2` FOREIGN KEY (`mobileproperty_id`) REFERENCES `ae_ext_mobileproperty` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_ext_mobileproperty_bookmark_ibfk_3` FOREIGN KEY (`game_id`) REFERENCES `ae_game` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_ext_mobileproperty_users`
--
ALTER TABLE `ae_ext_mobileproperty_users`
  ADD CONSTRAINT `ae_ext_mobileproperty_users_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `ae_game_play` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_ext_mobileproperty_users_ibfk_2` FOREIGN KEY (`agent_id`) REFERENCES `ae_game_play` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_ext_mtasks_proof`
--
ALTER TABLE `ae_ext_mtasks_proof`
  ADD CONSTRAINT `ae_ext_mtasks_proof_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `ae_ext_mtasks` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_ext_notifications`
--
ALTER TABLE `ae_ext_notifications`
  ADD CONSTRAINT `ae_ext_notifications_ibfk_1` FOREIGN KEY (`app_id`) REFERENCES `ae_game` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_ext_notifications_ibfk_2` FOREIGN KEY (`play_id_to`) REFERENCES `ae_game_play` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_ext_notifications_ibfk_3` FOREIGN KEY (`play_id_from`) REFERENCES `ae_game_play` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_ext_notifications_ibfk_4` FOREIGN KEY (`notification_id`) REFERENCES `ae_notification` (`id`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `ae_ext_notifications_ibfk_5` FOREIGN KEY (`task_id`) REFERENCES `ae_ext_mtasks` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_ext_photostream`
--
ALTER TABLE `ae_ext_photostream`
  ADD CONSTRAINT `ae_ext_photostream_ibfk_1` FOREIGN KEY (`playtask_id`) REFERENCES `ae_game_play_action` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_ext_products`
--
ALTER TABLE `ae_ext_products`
  ADD CONSTRAINT `ae_ext_products_ibfk_2` FOREIGN KEY (`play_id`) REFERENCES `ae_game_play` (`id`) ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_ext_products_ibfk_3` FOREIGN KEY (`app_id`) REFERENCES `ae_game` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_ext_products_ibfk_4` FOREIGN KEY (`category_id`) REFERENCES `ae_ext_products_categories` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION;

--
-- Constraints for table `ae_ext_products_bookmarks`
--
ALTER TABLE `ae_ext_products_bookmarks`
  ADD CONSTRAINT `ae_ext_products_bookmarks_ibfk_1` FOREIGN KEY (`play_id`) REFERENCES `ae_game_play` (`id`),
  ADD CONSTRAINT `ae_ext_products_bookmarks_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `ae_ext_products` (`id`);

--
-- Constraints for table `ae_ext_products_carts`
--
ALTER TABLE `ae_ext_products_carts`
  ADD CONSTRAINT `ae_ext_products_carts_ibfk_1` FOREIGN KEY (`play_id`) REFERENCES `ae_game_play` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_ext_products_carts_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `ae_ext_products` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_ext_products_carts_ibfk_3` FOREIGN KEY (`task_id`) REFERENCES `ae_ext_mtasks` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_ext_products_categories`
--
ALTER TABLE `ae_ext_products_categories`
  ADD CONSTRAINT `ae_ext_products_categories_ibfk_1` FOREIGN KEY (`app_id`) REFERENCES `ae_game` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_ext_products_photos`
--
ALTER TABLE `ae_ext_products_photos`
  ADD CONSTRAINT `ae_ext_products_photos_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `ae_ext_products` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_ext_products_reviews`
--
ALTER TABLE `ae_ext_products_reviews`
  ADD CONSTRAINT `ae_ext_products_reviews_ibfk_1` FOREIGN KEY (`play_id`) REFERENCES `ae_game_play` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_ext_products_tags`
--
ALTER TABLE `ae_ext_products_tags`
  ADD CONSTRAINT `ae_ext_products_tags_ibfk_1` FOREIGN KEY (`app_id`) REFERENCES `ae_game` (`id`);

--
-- Constraints for table `ae_ext_products_tags_products`
--
ALTER TABLE `ae_ext_products_tags_products`
  ADD CONSTRAINT `ae_ext_products_tags_products_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `ae_ext_products` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_ext_products_tags_products_ibfk_2` FOREIGN KEY (`tag_id`) REFERENCES `ae_ext_products_tags` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_ext_quiz_question`
--
ALTER TABLE `ae_ext_quiz_question`
  ADD CONSTRAINT `ae_ext_quiz_question_ibfk_1` FOREIGN KEY (`app_id`) REFERENCES `ae_game` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_ext_quiz_question_option`
--
ALTER TABLE `ae_ext_quiz_question_option`
  ADD CONSTRAINT `ae_ext_quiz_question_option_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `ae_ext_quiz_question` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_ext_quiz_sets`
--
ALTER TABLE `ae_ext_quiz_sets`
  ADD CONSTRAINT `ae_ext_quiz_sets_ibfk_1` FOREIGN KEY (`app_id`) REFERENCES `ae_game` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ae_ext_quiz_sets_ibfk_2` FOREIGN KEY (`quiz_id`) REFERENCES `ae_ext_quiz` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ae_ext_quiz_sets_ibfk_3` FOREIGN KEY (`question_id`) REFERENCES `ae_ext_quiz_question` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `ae_ext_requests`
--
ALTER TABLE `ae_ext_requests`
  ADD CONSTRAINT `ae_ext_requests_ibfk_1` FOREIGN KEY (`requester_playid`) REFERENCES `ae_game_play` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_ext_request_payments`
--
ALTER TABLE `ae_ext_request_payments`
  ADD CONSTRAINT `ae_ext_request_payments_ibfk_1` FOREIGN KEY (`request_id`) REFERENCES `ae_ext_requests` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_ext_tattoos`
--
ALTER TABLE `ae_ext_tattoos`
  ADD CONSTRAINT `ae_ext_tattoos_ibfk_1` FOREIGN KEY (`play_id`) REFERENCES `ae_game_play` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_ext_tattoos_ibfk_2` FOREIGN KEY (`game_id`) REFERENCES `ae_game` (`id`),
  ADD CONSTRAINT `ae_ext_tattoos_ibfk_3` FOREIGN KEY (`category_id`) REFERENCES `ae_ext_tattoos_categories` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_ext_tattoos_likes`
--
ALTER TABLE `ae_ext_tattoos_likes`
  ADD CONSTRAINT `ae_ext_tattoos_likes_ibfk_1` FOREIGN KEY (`play_id`) REFERENCES `ae_game_play` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_ext_tattoos_likes_ibfk_2` FOREIGN KEY (`tattoo_id`) REFERENCES `ae_ext_tattoos` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_ext_user_bids`
--
ALTER TABLE `ae_ext_user_bids`
  ADD CONSTRAINT `ae_ext_user_bids_ibfk_1` FOREIGN KEY (`bid_item_id`) REFERENCES `ae_ext_bid_items` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_ext_user_bids_ibfk_2` FOREIGN KEY (`play_id`) REFERENCES `ae_game_play` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_ext_user_bid_item_images`
--
ALTER TABLE `ae_ext_user_bid_item_images`
  ADD CONSTRAINT `ae_ext_user_bid_item_images_ibfk_1` FOREIGN KEY (`bid_item_id`) REFERENCES `ae_ext_bid_items` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_ext_wallet`
--
ALTER TABLE `ae_ext_wallet`
  ADD CONSTRAINT `ae_ext_wallet_ibfk_1` FOREIGN KEY (`play_id`) REFERENCES `ae_game_play` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION;

--
-- Constraints for table `ae_ext_wallet_logs`
--
ALTER TABLE `ae_ext_wallet_logs`
  ADD CONSTRAINT `ae_ext_wallet_logs_ibfk_1` FOREIGN KEY (`play_id`) REFERENCES `ae_game_play` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION;

--
-- Constraints for table `ae_fbfriend_user`
--
ALTER TABLE `ae_fbfriend_user`
  ADD CONSTRAINT `ae_fbfriend_user_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `usergroups_user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_fbfriend_user_ibfk_2` FOREIGN KEY (`ae_fbfriend_id`) REFERENCES `ae_fbfriend` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_fb_invite`
--
ALTER TABLE `ae_fb_invite`
  ADD CONSTRAINT `ae_fb_invite_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `usergroups_user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_fb_invite_ibfk_2` FOREIGN KEY (`playtask_id`) REFERENCES `ae_game_play_action` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_filenames`
--
ALTER TABLE `ae_filenames`
  ADD CONSTRAINT `ae_filenames_ibfk_1` FOREIGN KEY (`game_id`) REFERENCES `ae_game` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_game`
--
ALTER TABLE `ae_game`
  ADD CONSTRAINT `ae_game_ibfk_4` FOREIGN KEY (`category_id`) REFERENCES `ae_category` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_game_ibfk_5` FOREIGN KEY (`user_id`) REFERENCES `usergroups_user` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION;

--
-- Constraints for table `ae_game_badge`
--
ALTER TABLE `ae_game_badge`
  ADD CONSTRAINT `ae_game_badge_ibfk_1` FOREIGN KEY (`game_id`) REFERENCES `ae_game` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_game_badge_ibfk_2` FOREIGN KEY (`game_id`) REFERENCES `ae_game` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_game_branch`
--
ALTER TABLE `ae_game_branch`
  ADD CONSTRAINT `ae_game_branch_ibfk_1` FOREIGN KEY (`game_id`) REFERENCES `ae_game` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_game_branch_ibfk_2` FOREIGN KEY (`trigger_id`) REFERENCES `ae_game_trigger` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_game_branch_ibfk_3` FOREIGN KEY (`channel_id`) REFERENCES `ae_channel` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION;

--
-- Constraints for table `ae_game_branch_action`
--
ALTER TABLE `ae_game_branch_action`
  ADD CONSTRAINT `ae_game_branch_action_ibfk_2` FOREIGN KEY (`channel_id`) REFERENCES `ae_channel` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_game_branch_action_ibfk_5` FOREIGN KEY (`type_id`) REFERENCES `ae_game_branch_action_type` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_game_branch_action_ibfk_6` FOREIGN KEY (`role_id`) REFERENCES `ae_game_role` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_game_branch_action_ibfk_7` FOREIGN KEY (`trigger_id`) REFERENCES `ae_game_trigger` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_game_branch_action_ibfk_8` FOREIGN KEY (`branch_id`) REFERENCES `ae_game_branch` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_game_branch_action_type`
--
ALTER TABLE `ae_game_branch_action_type`
  ADD CONSTRAINT `ae_game_branch_action_type_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `usergroups_user` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION;

--
-- Constraints for table `ae_game_keyvaluestorage`
--
ALTER TABLE `ae_game_keyvaluestorage`
  ADD CONSTRAINT `gameid` FOREIGN KEY (`game_id`) REFERENCES `ae_game` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_game_play`
--
ALTER TABLE `ae_game_play`
  ADD CONSTRAINT `ae_game_play_ibfk_2` FOREIGN KEY (`game_id`) REFERENCES `ae_game` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_game_play_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `usergroups_user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_game_play_action`
--
ALTER TABLE `ae_game_play_action`
  ADD CONSTRAINT `ae_game_play_action_ibfk_2` FOREIGN KEY (`play_id`) REFERENCES `ae_game_play` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_game_play_action_ibfk_3` FOREIGN KEY (`trigger_id`) REFERENCES `ae_game_trigger` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_game_play_action_ibfk_4` FOREIGN KEY (`user_id`) REFERENCES `usergroups_user` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_game_play_action_ibfk_5` FOREIGN KEY (`action_id`) REFERENCES `ae_game_branch_action` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_game_play_branch`
--
ALTER TABLE `ae_game_play_branch`
  ADD CONSTRAINT `ae_game_play_branch_ibfk_1` FOREIGN KEY (`play_id`) REFERENCES `ae_game_play` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_game_play_branch_ibfk_2` FOREIGN KEY (`branch_id`) REFERENCES `ae_game_branch` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_game_play_datastorage`
--
ALTER TABLE `ae_game_play_datastorage`
  ADD CONSTRAINT `play_id` FOREIGN KEY (`play_id`) REFERENCES `ae_game_play` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Constraints for table `ae_game_play_keyvaluestorage`
--
ALTER TABLE `ae_game_play_keyvaluestorage`
  ADD CONSTRAINT `ae_game_play_keyvaluestorage_ibfk_1` FOREIGN KEY (`value`) REFERENCES `ae_game_play` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `playid` FOREIGN KEY (`play_id`) REFERENCES `ae_game_play` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_game_play_role`
--
ALTER TABLE `ae_game_play_role`
  ADD CONSTRAINT `ae_game_play_role_ibfk_1` FOREIGN KEY (`play_id`) REFERENCES `ae_game_play` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_game_play_role_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `ae_game_role` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_game_play_user`
--
ALTER TABLE `ae_game_play_user`
  ADD CONSTRAINT `ae_game_play_user_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `usergroups_user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_game_play_user_ibfk_2` FOREIGN KEY (`id_play`) REFERENCES `ae_game_play` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_game_play_user_ibfk_3` FOREIGN KEY (`id_role`) REFERENCES `ae_game_role` (`game_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_game_play_variable`
--
ALTER TABLE `ae_game_play_variable`
  ADD CONSTRAINT `ae_game_play_variable_ibfk_1` FOREIGN KEY (`play_id`) REFERENCES `ae_game_play` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_game_play_variable_ibfk_2` FOREIGN KEY (`variable_id`) REFERENCES `ae_game_variable` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_game_role`
--
ALTER TABLE `ae_game_role`
  ADD CONSTRAINT `ae_game_role_ibfk_1` FOREIGN KEY (`game_id`) REFERENCES `ae_game` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_game_score`
--
ALTER TABLE `ae_game_score`
  ADD CONSTRAINT `ae_game_score_ibfk_1` FOREIGN KEY (`game_id`) REFERENCES `ae_game` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_game_trigger`
--
ALTER TABLE `ae_game_trigger`
  ADD CONSTRAINT `ae_game_trigger_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `usergroups_user` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION;

--
-- Constraints for table `ae_game_variable`
--
ALTER TABLE `ae_game_variable`
  ADD CONSTRAINT `ae_game_variable_ibfk_1` FOREIGN KEY (`game_id`) REFERENCES `ae_game` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_location_log`
--
ALTER TABLE `ae_location_log`
  ADD CONSTRAINT `ae_location_log_ibfk_1` FOREIGN KEY (`game_id`) REFERENCES `ae_game` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_location_log_ibfk_6` FOREIGN KEY (`beacon_id`) REFERENCES `ae_ext_mobilebeacons` (`id`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `ae_location_log_ibfk_7` FOREIGN KEY (`place_id`) REFERENCES `ae_ext_mobileplaces` (`id`) ON DELETE SET NULL ON UPDATE SET NULL;

--
-- Constraints for table `ae_mobile`
--
ALTER TABLE `ae_mobile`
  ADD CONSTRAINT `ae_mobile_ibfk_1` FOREIGN KEY (`game_id`) REFERENCES `ae_game` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_notification`
--
ALTER TABLE `ae_notification`
  ADD CONSTRAINT `ae_notification_ibfk_2` FOREIGN KEY (`play_id`) REFERENCES `ae_game_play` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_notification_ibfk_3` FOREIGN KEY (`play_id`) REFERENCES `ae_game_play` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_notification_ibfk_4` FOREIGN KEY (`play_id`) REFERENCES `ae_game_play` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_notification_ibfk_5` FOREIGN KEY (`play_id`) REFERENCES `ae_game_play` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_notification_ibfk_6` FOREIGN KEY (`play_id`) REFERENCES `ae_game_play` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_notification_ibfk_7` FOREIGN KEY (`play_id`) REFERENCES `ae_game_play` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_notification_ibfk_8` FOREIGN KEY (`play_id`) REFERENCES `ae_game_play` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_notification_ibfk_9` FOREIGN KEY (`play_id`) REFERENCES `ae_game_play` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_packages`
--
ALTER TABLE `ae_packages`
  ADD CONSTRAINT `ae_packages_ibfk_1` FOREIGN KEY (`game_id`) REFERENCES `ae_game` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_packages_themes`
--
ALTER TABLE `ae_packages_themes`
  ADD CONSTRAINT `ae_packages_themes_ibfk_1` FOREIGN KEY (`game_id`) REFERENCES `ae_game` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_purchase`
--
ALTER TABLE `ae_purchase`
  ADD CONSTRAINT `ae_purchase_ibfk_1` FOREIGN KEY (`game_id`) REFERENCES `ae_game` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_purchase_ibfk_2` FOREIGN KEY (`play_id`) REFERENCES `ae_game_play` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION;

--
-- Constraints for table `ae_role_user`
--
ALTER TABLE `ae_role_user`
  ADD CONSTRAINT `ae_role_user_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `usergroups_user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_role_user_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `ae_role` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `menu_item`
--
ALTER TABLE `menu_item`
  ADD CONSTRAINT `menu_item_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `menu_item` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION,
  ADD CONSTRAINT `menu_item_ibfk_2` FOREIGN KEY (`menu_id`) REFERENCES `menu` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION,
  ADD CONSTRAINT `menu_item_ibfk_3` FOREIGN KEY (`parent_id`) REFERENCES `menu_item` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION,
  ADD CONSTRAINT `menu_item_ibfk_4` FOREIGN KEY (`menu_id`) REFERENCES `menu` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION,
  ADD CONSTRAINT `menu_item_ibfk_5` FOREIGN KEY (`parent_id`) REFERENCES `menu_item` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION;

--
-- Constraints for table `ssh_keys`
--
ALTER TABLE `ssh_keys`
  ADD CONSTRAINT `ssh_keys_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `usergroups_user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `usergroups_user`
--
ALTER TABLE `usergroups_user`
  ADD CONSTRAINT `usergroups_user_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `usergroups_group` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
