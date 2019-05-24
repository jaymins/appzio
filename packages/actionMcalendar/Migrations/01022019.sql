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
SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `ae_ext_calendar_entry`;
DROP TABLE IF EXISTS `ae_ext_calendar_notification`;
DROP TABLE IF EXISTS `ae_ext_exercise_timer`;
DROP TABLE IF EXISTS `ae_ext_exercise_timer_relations`;

DROP TABLE IF EXISTS `ae_ext_fit_exercise`;
DROP TABLE IF EXISTS `ae_ext_fit_exercise_mcategory_movement`;
DROP TABLE IF EXISTS `ae_ext_fit_exercise_movement`;
DROP TABLE IF EXISTS `ae_ext_fit_exercise_movement_category`;

DROP TABLE IF EXISTS `ae_ext_fit_movement`;
DROP TABLE IF EXISTS `ae_ext_fit_movement_category`;
DROP TABLE IF EXISTS `ae_ext_fit_pr`;
DROP TABLE IF EXISTS `ae_ext_fit_program`;

DROP TABLE IF EXISTS `ae_ext_fit_program_category`;
DROP TABLE IF EXISTS `ae_ext_fit_program_exercise`;
DROP TABLE IF EXISTS `ae_ext_fit_program_selection`;
DROP TABLE IF EXISTS `ae_ext_fit_program_subcategory`;
DROP TABLE IF EXISTS `ae_ext_fit_program_recipe`;

DROP TABLE IF EXISTS `ae_ext_fit_pr_user`;
DROP TABLE IF EXISTS `ae_ext_food_custom_ingredient`;
DROP TABLE IF EXISTS `ae_ext_food_ingredient`;
DROP TABLE IF EXISTS `ae_ext_food_ingredient_category`;

DROP TABLE IF EXISTS `ae_ext_food_recipe`;
DROP TABLE IF EXISTS `ae_ext_food_recipe_ingredient`;
DROP TABLE IF EXISTS `ae_ext_food_recipe_step`;
DROP TABLE IF EXISTS `ae_ext_food_recipe_type`;

DROP TABLE IF EXISTS `ae_ext_food_shopping_list`;

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

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
CREATE TABLE `ae_ext_calendar_notification` (
                                              `id` int(11) UNSIGNED NOT NULL,
                                              `play_id` int(11) UNSIGNED NOT NULL,
                                              `notification_id` int(11) UNSIGNED DEFAULT NULL,
                                              `calendar_entry_id` int(11) UNSIGNED DEFAULT NULL,
                                              `item_id` int(11) UNSIGNED DEFAULT NULL,
                                              `text` varchar(255) NOT NULL,
                                              `type` varchar(255) NOT NULL,
                                              `timestamp` int(11) UNSIGNED NOT NULL,
                                              `status` smallint(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
CREATE TABLE `ae_ext_exercise_timer` (
                                       `id` int(11) UNSIGNED NOT NULL,
                                       `timer_relation_id` int(11) UNSIGNED NOT NULL,
                                       `round` int(11) NOT NULL,
                                       `movement` int(11) DEFAULT NULL,
                                       `type` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
                                       `time` int(11) NOT NULL,
                                       `step` int(11) NOT NULL,
                                       `start` int(11) DEFAULT NULL,
                                       `stop` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
INSERT INTO `ae_ext_exercise_timer` (`id`, `timer_relation_id`, `round`, `movement`, `type`, `time`, `step`, `start`, `stop`) VALUES
(31, 7, 1, 7, 'rest', 8, 2, NULL, 1548427027),
(32, 7, 1, 10, 'rest', 5, 4, NULL, 1548427032),
(33, 7, 2, 7, 'rest', 8, 2, NULL, 1548427041),
(34, 7, 2, 10, 'rest', 5, 4, NULL, 1548427046),
(35, 7, 2, 10, 'rest', 5, 4, NULL, 1548427052),
(36, 8, 1, 0, 'movement', 10, 1, NULL, 1548663236),
(43, 11, 1, 7, 'movement', 5, 1, NULL, 1548495747),
(44, 11, 1, 7, 'rest', 8, 2, NULL, 1548495756),
(45, 11, 1, 10, 'movement', 5, 3, NULL, 1548495761),
(46, 11, 1, 10, 'rest', 5, 4, NULL, NULL),
(47, 11, 2, 7, 'movement', 5, 1, NULL, NULL),
(48, 11, 2, 7, 'rest', 8, 2, NULL, NULL),
(49, 11, 2, 10, 'movement', 5, 3, NULL, NULL),
(50, 11, 2, 10, 'rest', 5, 4, NULL, NULL),
(51, 11, 2, 10, 'rest', 5, 4, NULL, NULL),
(52, 12, 1, 0, 'movement', 20, 1, NULL, NULL),
(53, 13, 1, 7, 'movement', 5, 1, NULL, 1548668441),
(54, 13, 1, 7, 'rest', 8, 2, NULL, 1548668449),
(55, 13, 1, 10, 'movement', 5, 3, NULL, NULL),
(56, 13, 1, 10, 'rest', 5, 4, NULL, NULL),
(57, 13, 2, 7, 'movement', 5, 1, NULL, NULL),
(58, 13, 2, 7, 'rest', 8, 2, NULL, NULL),
(59, 13, 2, 10, 'movement', 5, 3, NULL, NULL),
(60, 13, 2, 10, 'rest', 5, 4, NULL, NULL),
(61, 13, 2, 10, 'rest', 5, 4, NULL, NULL),
(62, 14, 1, 0, 'movement', 20, 1, NULL, NULL),
(63, 15, 1, 0, 'movement', 13, 1, NULL, NULL);
CREATE TABLE `ae_ext_exercise_timer_relations` (
                                                 `id` int(11) UNSIGNED NOT NULL,
                                                 `calendar_entry_id` int(11) UNSIGNED NOT NULL,
                                                 `movement_category_id` int(11) UNSIGNED NOT NULL,
                                                 `timer_type` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
                                                 `timer_status` int(11) DEFAULT NULL,
                                                 `timestamp` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE `ae_ext_fit_exercise` (
                                     `id` int(11) UNSIGNED NOT NULL,
                                     `app_id` int(11) UNSIGNED NOT NULL,
                                     `name` varchar(255) COLLATE utf8_bin NOT NULL,
                                     `category_id` int(11) UNSIGNED DEFAULT NULL,
                                     `article_id` int(11) UNSIGNED DEFAULT NULL,
                                     `points` int(11) DEFAULT NULL,
                                     `duration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
INSERT INTO `ae_ext_fit_exercise` (`id`, `app_id`, `name`, `category_id`, `article_id`, `points`, `duration`) VALUES
(1, {appid}, 'Mindfulness', NULL, 36, 10, 0),
(2, {appid}, 'Fitness', NULL, 35, 1, 0),
(3, {appid}, 'Discipline', NULL, 35, 1, 0),
(4, {appid}, 'Personal Growth', NULL, 35, 1, 0),
(5, {appid}, 'Minimalism', NULL, 35, 1, 0),
(6, {appid}, 'Time Management', NULL, 35, 1, 0),
(7, {appid}, 'Sleep', NULL, 35, 1, 0),
(8, {appid}, 'Nutrition', NULL, 35, 1, 0),
(9, {appid}, 'Training', 8, 36, 1, 30),
(10, {appid}, 'Power workout', 8, 36, 10, 30);
CREATE TABLE `ae_ext_fit_exercise_mcategory_movement` (
                                                        `id` int(11) UNSIGNED NOT NULL,
                                                        `exercise_movement_cat_id` int(11) UNSIGNED NOT NULL,
                                                        `weight` int(11) DEFAULT NULL,
                                                        `reps` int(11) DEFAULT NULL,
                                                        `rest` int(11) DEFAULT NULL,
                                                        `movement_time` int(11) DEFAULT NULL,
                                                        `pionts` int(11) DEFAULT NULL,
                                                        `movement_id` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

ALTER TABLE `ae_ext_fit_exercise_mcategory_movement` ADD `unit` VARCHAR(255) NULL DEFAULT NULL AFTER `weight`;

INSERT INTO `ae_ext_fit_exercise_mcategory_movement` (`id`, `exercise_movement_cat_id`, `weight`, `reps`, `rest`, `movement_time`, `pionts`, `movement_id`) VALUES
(7, 6, 0, 8, 8, 5, NULL, 1),
(8, 7, 0, 2, 8, 5, NULL, 1),
(9, 8, 3, 3, 10, 10, NULL, 5),
(10, 6, 2, 5, 5, 5, NULL, 2);
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


INSERT INTO `ae_ext_fit_exercise_movement` (`id`, `exercise_id`, `movement_id`, `movement_category_id`, `weight`, `reps`, `rounds`, `rest`, `points`) VALUES
(1, 1, 1, 1, NULL, 1, 1, '30 sec rest', 1),
(2, 1, 2, 2, NULL, 2, 1, '2 min rest', 1),
(3, 1, 3, 3, NULL, 1, 1, '1 min rest', 1),
(4, 2, 4, 1, NULL, 110, 4, NULL, 2),
(5, 2, 5, 1, NULL, 10, 4, NULL, 1),
(6, 10, 1, 1, 60.00, 10, 4, '2', NULL),
(7, 10, 2, 2, 60.00, 10, 4, '2', NULL);
CREATE TABLE `ae_ext_fit_exercise_movement_category` (
                                                       `id` int(11) UNSIGNED NOT NULL,
                                                       `exercise_id` int(11) UNSIGNED NOT NULL,
                                                       `movement_category` int(11) UNSIGNED NOT NULL,
                                                       `timer_type` varchar(255) COLLATE utf8_bin NOT NULL,
                                                       `rounds` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


INSERT INTO `ae_ext_fit_exercise_movement_category` (`id`, `exercise_id`, `movement_category`, `timer_type`, `rounds`) VALUES
(4, 1, 1, 'HiiT', 4),
(6, 10, 1, 'hiit', 2),
(7, 9, 1, 'count_down', 1),
(8, 10, 2, 'count_down', 1);
CREATE TABLE `ae_ext_fit_movement` (
                                     `id` int(11) UNSIGNED NOT NULL,
                                     `app_id` int(11) UNSIGNED NOT NULL,
                                     `article_id` int(11) UNSIGNED DEFAULT NULL,
                                     `name` varchar(255) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

ALTER TABLE `ae_ext_fit_movement` ADD `description` TEXT NULL AFTER `name`, ADD `video_url` TEXT NULL AFTER `description`;

INSERT INTO `ae_ext_fit_movement` (`id`, `app_id`, `article_id`, `name`) VALUES
(1, {appid}, 36, 'Single Leg Romanian Deadlift'),
(2, {appid}, 36, 'Lateral Squat'),
(3, {appid}, 36, 'Split Squat'),
(4, {appid}, 36, 'Barbell Deadlift'),
(5, {appid}, 36, 'Bent-Over Barbell Deadlift'),
(6, {appid}, 36, 'Tester');
CREATE TABLE `ae_ext_fit_movement_category` (
                                              `id` int(11) UNSIGNED NOT NULL,
                                              `name` varchar(255) COLLATE utf8_bin NOT NULL,
                                              `timer_type` varchar(255) COLLATE utf8_bin NOT NULL,
                                              `background_image` varchar(255) COLLATE utf8_bin DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
INSERT INTO `ae_ext_fit_movement_category` (`id`, `name`, `timer_type`, `background_image`) VALUES
(1, 'Warmup', 'countdown', 'swiss8-exercise-bg-1.jpg'),
(2, 'Power', 'rest', 'swiss8-exercise-bg-2.jpg'),
(3, 'Conditioning', 'wod', 'swiss8-exercise-bg-3.jpg'),
(4, 'Test', 'wod', NULL);
CREATE TABLE `ae_ext_fit_pr` (
                               `id` int(11) UNSIGNED NOT NULL,
                               `app_id` int(11) UNSIGNED NOT NULL,
                               `title` varchar(255) NOT NULL,
                               `unit` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
INSERT INTO `ae_ext_fit_pr` (`id`, `app_id`, `title`, `unit`) VALUES
(1, {appid}, 'Benchpress', 'kg'),
(2, {appid}, 'Back Squat', 'kg'),
(3, {appid}, 'Front squat', 'kg'),
(4, {appid}, 'Deadlift', 'kg'),
(5, {appid}, 'Ohp', 'kg'),
(6, {appid}, 'Weighted pullup', 'r'),
(7, {appid}, 'Clean', 'kg'),
(8, {appid}, 'snatch', 'kg');
CREATE TABLE `ae_ext_fit_program` (
                                    `id` int(11) UNSIGNED NOT NULL,
                                    `app_id` int(11) UNSIGNED NOT NULL,
                                    `name` varchar(255) COLLATE utf8_bin NOT NULL,
                                    `category_id` int(11) UNSIGNED DEFAULT NULL,
                                    `subcategory_id` int(11) UNSIGNED DEFAULT NULL,
                                    `article_id` int(11) UNSIGNED DEFAULT NULL,
                                    `program_type` varchar(255) COLLATE utf8_bin NOT NULL,
                                    `program_sub_type` varchar(255) COLLATE utf8_bin NOT NULL,
                                    `is_challenge` tinyint(1) DEFAULT NULL,
                                    `exercises_per_day` smallint(3) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
INSERT INTO `ae_ext_fit_program` (`id`, `app_id`, `name`, `category_id`, `subcategory_id`, `article_id`, `program_type`, `program_sub_type`, `is_challenge`, `exercises_per_day`) VALUES
(1, {appid}, 'Strength', 8, 2, 35, 'fitness', 'weekly_based', 0, 1),
(2, {appid}, 'Well-being', 6, 2, 35, 'fitness', 'weekly_based', 0, 1),
(3, {appid}, 'Weight Loss', 8, 3, 35, 'fitness', 'weekly_based', 0, 1),
(4, {appid}, 'Zen mind', 6, 4, 35, 'fitness', 'weekly_based', 0, 1),
(5, {appid}, 'Better Sleep', 5, 1, 35, 'fitness', 'weekly_based', 0, 1),
(6, {appid}, 'Stress Management', 6, 5, 35, 'fitness', 'weekly_based', 0, 1),
(7, {appid}, 'Eat Well', 7, 7, 35, 'food', 'weekly_based', NULL, 1),
(13, {appid}, 'Test Food Program', 7, 6, 35, 'food', 'weekly_based', NULL, NULL);
CREATE TABLE `ae_ext_fit_program_category` (
                                             `id` int(11) UNSIGNED NOT NULL,
                                             `app_id` int(11) UNSIGNED NOT NULL,
                                             `name` varchar(255) COLLATE utf8_bin NOT NULL,
                                             `icon` varchar(255) COLLATE utf8_bin NOT NULL,
                                             `color` char(7) COLLATE utf8_bin NOT NULL,
                                             `category_order` smallint(2) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
INSERT INTO `ae_ext_fit_program_category` (`id`, `app_id`, `name`, `icon`, `color`, `category_order`) VALUES
(1, {appid}, 'Discipline', 'icon-discipline.png', '#3b9536', 0),
(2, {appid}, 'Personal Growth', 'icon-growth.png', '#e2328d', 0),
(3, {appid}, 'Minimalism', 'icon-minimalism.png', '#b5420f', 0),
(4, {appid}, 'Time Management', 'icon-time.png', '#32b2e2', 0),
(5, {appid}, 'Sleep', 'icon-sleep.png', '#614792', 0),
(6, {appid}, 'Mindfulness', 'icon-mindfulness.png', '#e2a532', 0),
(7, {appid}, 'Nutrition', 'icon-nutrition.png', '#4aa688', 0),
(8, {appid}, 'Training', 'icon-fitness.png', '#b31e2c', 0);
CREATE TABLE `ae_ext_fit_program_exercise` (
                                             `id` int(11) UNSIGNED NOT NULL,
                                             `program_id` int(11) UNSIGNED NOT NULL,
                                             `exercise_id` int(11) UNSIGNED NOT NULL,
                                             `week` smallint(3) DEFAULT NULL,
                                             `day` smallint(3) DEFAULT NULL,
                                             `priority` smallint(2) NOT NULL DEFAULT 0,
                                             `time` varchar(255) COLLATE utf8_bin DEFAULT NULL,
                                             `repeat_days` varchar(255) COLLATE utf8_bin DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
INSERT INTO `ae_ext_fit_program_exercise` (`id`, `program_id`, `exercise_id`, `week`, `day`, `priority`, `time`, `repeat_days`) VALUES
(6, 2, 1, NULL, NULL, 0, '', ''),
(7, 2, 2, NULL, NULL, 0, '', ''),
(10, 4, 1, NULL, NULL, 0, '', ''),
(11, 4, 2, NULL, NULL, 0, '', ''),
(14, 6, 1, NULL, NULL, 0, '', ''),
(15, 6, 2, NULL, NULL, 0, '', ''),
(16, 7, 1, NULL, NULL, 0, '', ''),
(17, 7, 2, NULL, NULL, 0, '', ''),
(18, 5, 1, 1, NULL, 1, '', NULL),
(19, 1, 10, 1, NULL, 1, '40', NULL),
(22, 1, 9, 1, NULL, 2, '40', NULL),
(23, 1, 10, 2, NULL, 1, '60', NULL),
(24, 2, 3, 1, NULL, 1, '30', NULL),
(25, 2, 3, 1, NULL, 2, '30', NULL),
(26, 2, 4, 2, NULL, 1, '30', NULL),
(27, 3, 7, 1, NULL, 1, '60', NULL),
(28, 3, 7, 1, NULL, 2, '60', NULL),
(29, 4, 9, 1, NULL, 1, '60', NULL),
(30, 4, 9, 2, NULL, 1, '60', NULL),
(31, 5, 7, 1, NULL, 2, '', NULL),
(32, 6, 6, 1, NULL, 1, '60', NULL),
(33, 6, 1, 2, NULL, 1, '60', NULL);
CREATE TABLE `ae_ext_fit_program_recipe` (
                                           `id` int(11) UNSIGNED NOT NULL,
                                           `program_id` int(11) UNSIGNED NOT NULL,
                                           `recipe_id` int(11) UNSIGNED NOT NULL,
                                           `week` smallint(3) DEFAULT NULL,
                                           `recipe_order` smallint(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
INSERT INTO `ae_ext_fit_program_recipe` (`id`, `program_id`, `recipe_id`, `week`, `recipe_order`) VALUES
(3, 7, 1, 1, 1),
(4, 7, 2, 1, 2),
(5, 7, 4, 1, 3),
(6, 7, 1, 2, 1),
(7, 7, 1, 3, 1),
(8, 13, 1, 1, 1),
(9, 13, 2, 1, 2),
(10, 13, 2, 2, 1),
(11, 13, 1, 2, 2);
CREATE TABLE `ae_ext_fit_program_selection` (
                                              `id` int(11) UNSIGNED NOT NULL,
                                              `play_id` int(11) UNSIGNED NOT NULL,
                                              `program_id` int(11) UNSIGNED NOT NULL,
                                              `program_type` varchar(255) NOT NULL,
                                              `start_time` int(11) NOT NULL,
                                              `program_start_date` int(11) NOT NULL,
                                              `training_days_per_week` int(11) DEFAULT NULL,
                                              `training_days` varchar(255) DEFAULT NULL,
                                              `times` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
CREATE TABLE `ae_ext_fit_program_subcategory` (
                                                `id` int(11) UNSIGNED NOT NULL,
                                                `app_id` int(11) UNSIGNED NOT NULL,
                                                `name` varchar(255) NOT NULL,
                                                `type` varchar(255) NOT NULL,
                                                `category_order` smallint(2) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
INSERT INTO `ae_ext_fit_program_subcategory` (`id`, `app_id`, `name`, `type`, `category_order`) VALUES
(1, {appid}, 'To Feel Better', 'fitness', 0),
(2, {appid}, 'To Get Stronger', 'fitness', 0),
(3, {appid}, 'To Compete', 'fitness', 0),
(4, {appid}, 'To Have Fun', 'fitness', 0),
(5, {appid}, 'To Loose Weight', 'fitness', 0),
(6, {appid}, 'Vegetarian', 'food', 0),
(7, {appid}, 'Low Carb', 'food', 0),
(8, {appid}, 'Fodmap', 'food', 0),
(9, {appid}, 'Paleo', 'food', 0),
(10, {appid}, 'Atkins', 'food', 0);
CREATE TABLE `ae_ext_fit_pr_user` (
                                    `id` int(11) UNSIGNED NOT NULL,
                                    `play_id` int(11) UNSIGNED NOT NULL,
                                    `pr_id` int(11) UNSIGNED NOT NULL,
                                    `value` int(11) NOT NULL,
                                    `date` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE `ae_ext_food_custom_ingredient` (
                                               `id` int(11) NOT NULL,
                                               `play_id` int(10) UNSIGNED NOT NULL,
                                               `name` varchar(255) NOT NULL,
                                               `ingredient_category` int(11) UNSIGNED DEFAULT NULL,
                                               `date` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE `ae_ext_food_ingredient` (
                                        `id` int(11) NOT NULL,
                                        `name` varchar(255) COLLATE utf8_bin NOT NULL,
                                        `unit` varchar(255) COLLATE utf8_bin DEFAULT NULL,
                                        `category_id` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
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
CREATE TABLE `ae_ext_food_ingredient_category` (
                                                 `id` int(11) UNSIGNED NOT NULL,
                                                 `app_id` int(11) UNSIGNED NOT NULL,
                                                 `name` varchar(255) COLLATE utf8_bin NOT NULL,
                                                 `icon` varchar(255) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
INSERT INTO `ae_ext_food_ingredient_category` (`id`, `app_id`, `name`, `icon`) VALUES
(1, {appid}, 'Fridge', 'theme-icon-fridge.png'),
(2, {appid}, 'Fresh', 'theme-icon-plum.png'),
(3, {appid}, 'Frozen', 'theme-icon-snow.png'),
(4, {appid}, 'Dry', 'theme-icon-dry.png');
CREATE TABLE `ae_ext_food_recipe` (
                                    `id` int(11) UNSIGNED NOT NULL,
                                    `name` varchar(255) COLLATE utf8_bin NOT NULL,
                                    `difficult` varchar(50) COLLATE utf8_bin DEFAULT NULL,
                                    `serve` int(11) DEFAULT NULL,
                                    `type_id` int(11) NOT NULL,
                                    `photo` varchar(255) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
INSERT INTO `ae_ext_food_recipe` (`id`, `name`, `difficult`, `serve`, `type_id`, `photo`) VALUES
(1, 'Greenie', '', 1, 1, 'green-smoothie.jpg'),
(2, 'Blue power', 'Easy', 1, 1, 'blueberry-tahini-basil-smoothie.jpg'),
(3, 'Gulf Coast Grill', 'Easy', 1, 2, 'gril.jpg'),
(4, 'whey-proteins', 'EASY', 1, 3, 'whey-proteins.png');
CREATE TABLE `ae_ext_food_recipe_ingredient` (
                                               `id` int(11) NOT NULL,
                                               `recipe_id` int(11) UNSIGNED NOT NULL,
                                               `ingredient_id` int(11) NOT NULL,
                                               `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
INSERT INTO `ae_ext_food_recipe_ingredient` (`id`, `recipe_id`, `ingredient_id`, `quantity`) VALUES
(1, 2, 1, 1),
(8, 2, 2, 1),
(9, 1, 2, 2),
(10, 3, 2, 1),
(11, 3, 3, 1),
(12, 3, 5, 2),
(13, 3, 4, 1),
(14, 4, 6, 10),
(15, 4, 1, 1);
CREATE TABLE `ae_ext_food_recipe_step` (
                                         `id` int(11) UNSIGNED NOT NULL,
                                         `recipe_id` int(11) UNSIGNED NOT NULL,
                                         `time` int(11) DEFAULT NULL,
                                         `description` text COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
INSERT INTO `ae_ext_food_recipe_step` (`id`, `recipe_id`, `time`, `description`) VALUES
(16, 1, 5, 'Wash items carefully and dice them'),
(17, 1, 2, 'Put items in a blender and blend well');
CREATE TABLE `ae_ext_food_recipe_type` (
                                         `id` int(11) NOT NULL,
                                         `app_id` int(11) UNSIGNED NOT NULL,
                                         `name` varchar(255) COLLATE utf8_bin NOT NULL,
                                         `time_start` datetime NOT NULL,
                                         `time_end` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
INSERT INTO `ae_ext_food_recipe_type` (`id`, `app_id`, `name`, `time_start`, `time_end`) VALUES
(1, {appid}, 'smoothie', '2018-10-02 06:00:00', '2018-10-02 14:00:00'),
(2, {appid}, 'meal', '2018-10-02 06:00:00', '2018-10-02 14:00:00'),
(3, {appid}, 'protein', '2018-10-30 00:00:00', '2018-10-30 00:00:00');
CREATE TABLE `ae_ext_food_shopping_list` (
                                           `id` int(11) NOT NULL,
                                           `play_id` int(11) UNSIGNED NOT NULL,
                                           `date_from` int(11) NOT NULL,
                                           `date_to` int(11) NOT NULL,
                                           `ingredient_id` int(11) DEFAULT NULL,
                                           `quantity` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `ae_ext_calendar_entry`
  ADD PRIMARY KEY (`id`),
  ADD KEY `play_id` (`play_id`),
  ADD KEY `type_id` (`type_id`),
  ADD KEY `exercise_id` (`exercise_id`),
  ADD KEY `program_id` (`program_id`),
  ADD KEY `recipe_id` (`recipe_id`);
ALTER TABLE `ae_ext_calendar_notification`
  ADD PRIMARY KEY (`id`),
  ADD KEY `calendar_entry_id` (`calendar_entry_id`),
  ADD KEY `play_id` (`play_id`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `notification_id` (`notification_id`);
ALTER TABLE `ae_ext_exercise_timer`
  ADD PRIMARY KEY (`id`),
  ADD KEY `timer_relation` (`timer_relation_id`);
ALTER TABLE `ae_ext_exercise_timer_relations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `calendar_entry` (`calendar_entry_id`),
  ADD KEY `ae_ext_exercise_timer_relations_ibfk_1` (`movement_category_id`);
ALTER TABLE `ae_ext_fit_exercise`
  ADD PRIMARY KEY (`id`),
  ADD KEY `article_id` (`article_id`),
  ADD KEY `ae_ext_fit_exercise.app_id` (`app_id`),
  ADD KEY `category_id` (`category_id`);
ALTER TABLE `ae_ext_fit_exercise_mcategory_movement`
  ADD PRIMARY KEY (`id`),
  ADD KEY `exercise_movement_category` (`exercise_movement_cat_id`),
  ADD KEY `movement` (`movement_id`);
ALTER TABLE `ae_ext_fit_exercise_movement`
  ADD PRIMARY KEY (`id`),
  ADD KEY `exercise_id` (`exercise_id`),
  ADD KEY `movement_id` (`movement_id`),
  ADD KEY `movement_category_id` (`movement_category_id`);
ALTER TABLE `ae_ext_fit_exercise_movement_category`
  ADD PRIMARY KEY (`id`),
  ADD KEY `exercise` (`exercise_id`),
  ADD KEY `movement_category` (`movement_category`);

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
-- AUTO_INCREMENT for dumped tables
--


-- AUTO_INCREMENT for table `ae_ext_calendar_entry`
--
ALTER TABLE `ae_ext_calendar_entry`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=87;

--
-- AUTO_INCREMENT for table `ae_ext_calendar_notification`
--
ALTER TABLE `ae_ext_calendar_notification`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `ae_ext_exercise_timer`
--
ALTER TABLE `ae_ext_exercise_timer`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT for table `ae_ext_exercise_timer_relations`
--
ALTER TABLE `ae_ext_exercise_timer_relations`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `ae_ext_fit_exercise`
--
ALTER TABLE `ae_ext_fit_exercise`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `ae_ext_fit_exercise_mcategory_movement`
--
ALTER TABLE `ae_ext_fit_exercise_mcategory_movement`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `ae_ext_fit_exercise_movement`
--
ALTER TABLE `ae_ext_fit_exercise_movement`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `ae_ext_fit_exercise_movement_category`
--
ALTER TABLE `ae_ext_fit_exercise_movement_category`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `ae_ext_fit_movement`
--
ALTER TABLE `ae_ext_fit_movement`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `ae_ext_fit_program_category`
--
ALTER TABLE `ae_ext_fit_program_category`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `ae_ext_fit_program_exercise`
--
ALTER TABLE `ae_ext_fit_program_exercise`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `ae_ext_fit_program_recipe`
--
ALTER TABLE `ae_ext_fit_program_recipe`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `ae_ext_fit_program_selection`
--
ALTER TABLE `ae_ext_fit_program_selection`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `ae_ext_fit_program_subcategory`
--
ALTER TABLE `ae_ext_fit_program_subcategory`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `ae_ext_fit_pr_user`
--
ALTER TABLE `ae_ext_fit_pr_user`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `ae_ext_food_recipe_step`
--
ALTER TABLE `ae_ext_food_recipe_step`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `ae_ext_food_recipe_type`
--
ALTER TABLE `ae_ext_food_recipe_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `ae_ext_food_shopping_list`
--
ALTER TABLE `ae_ext_food_shopping_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `ae_ext_article`
--

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
-- Constraints for table `ae_ext_calendar_notification`
--
ALTER TABLE `ae_ext_calendar_notification`
  ADD CONSTRAINT `ae_ext_calendar_notification_ibfk_1` FOREIGN KEY (`calendar_entry_id`) REFERENCES `ae_ext_calendar_entry` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_ext_calendar_notification_ibfk_2` FOREIGN KEY (`play_id`) REFERENCES `ae_game_play` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_ext_calendar_notification_ibfk_3` FOREIGN KEY (`item_id`) REFERENCES `ae_ext_items` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_ext_calendar_notification_ibfk_4` FOREIGN KEY (`notification_id`) REFERENCES `ae_notification` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_ext_exercise_timer`
--
ALTER TABLE `ae_ext_exercise_timer`
  ADD CONSTRAINT `timer_relation` FOREIGN KEY (`timer_relation_id`) REFERENCES `ae_ext_exercise_timer_relations` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_ext_exercise_timer_relations`
--
ALTER TABLE `ae_ext_exercise_timer_relations`
  ADD CONSTRAINT `ae_ext_exercise_timer_relations_ibfk_1` FOREIGN KEY (`movement_category_id`) REFERENCES `ae_ext_fit_exercise_movement_category` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `calendar_entry` FOREIGN KEY (`calendar_entry_id`) REFERENCES `ae_ext_calendar_entry` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_ext_fit_exercise`
--
ALTER TABLE `ae_ext_fit_exercise`
  ADD CONSTRAINT `ae_ext_fit_exercise.app_id` FOREIGN KEY (`app_id`) REFERENCES `ae_game` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_ext_fit_exercise_ibfk_1` FOREIGN KEY (`article_id`) REFERENCES `ae_ext_article` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_ext_fit_exercise_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `ae_ext_fit_program_category` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_ext_fit_exercise_mcategory_movement`
--
ALTER TABLE `ae_ext_fit_exercise_mcategory_movement`
  ADD CONSTRAINT `exercise_movement_category` FOREIGN KEY (`exercise_movement_cat_id`) REFERENCES `ae_ext_fit_exercise_movement_category` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `movement` FOREIGN KEY (`movement_id`) REFERENCES `ae_ext_fit_movement` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_ext_fit_exercise_movement`
--
ALTER TABLE `ae_ext_fit_exercise_movement`
  ADD CONSTRAINT `ae_ext_fit_exercise_movement_ibfk_1` FOREIGN KEY (`exercise_id`) REFERENCES `ae_ext_fit_exercise` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_ext_fit_exercise_movement_ibfk_2` FOREIGN KEY (`movement_id`) REFERENCES `ae_ext_fit_movement` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `ae_ext_fit_exercise_movement_ibfk_3` FOREIGN KEY (`movement_category_id`) REFERENCES `ae_ext_fit_movement_category` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `ae_ext_fit_exercise_movement_category`
--
ALTER TABLE `ae_ext_fit_exercise_movement_category`
  ADD CONSTRAINT `exercise` FOREIGN KEY (`exercise_id`) REFERENCES `ae_ext_fit_exercise` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `movement_category` FOREIGN KEY (`movement_category`) REFERENCES `ae_ext_fit_movement_category` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

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
COMMIT;

SET FOREIGN_KEY_CHECKS=1;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
