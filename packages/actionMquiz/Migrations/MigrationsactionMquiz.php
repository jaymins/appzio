<?php

Yii::import('application.modules.aeapi.models.*');

/*
 * This runs any custom migrations needed by the action module
 *
 */

class MigrationsactionMquiz extends Migrations {

    public static function runModuleMigrations(){
        self::createTables();
        self::checkUpdate();
        self::addSaveToDb();
        return true;
    }

    private static function addSaveToDb()
    {
        if(self::helperColumnExists('save_to_database', 'ae_ext_quiz')){
            return true;
        }

        $sql = "ALTER TABLE `ae_ext_quiz` ADD `save_to_database` TINYINT NOT NULL DEFAULT '0' AFTER `image`;";
        @Yii::app()->db->createCommand($sql)->query();
    }


    private static function checkUpdate(){
        if(self::helperTableExists('ae_ext_quiz_question_option')){
            return true;
        }

        $sql ="CREATE TABLE `ae_ext_quiz_question_option` (
  `id` int(11) UNSIGNED NOT NULL,
  `question_id` int(11) UNSIGNED NOT NULL,
  `answer` mediumtext COLLATE utf16_unicode_ci NOT NULL,
  `answer_order` int(4) NOT NULL,
  `is_correct` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf16 COLLATE=utf16_unicode_ci;";
        @Yii::app()->db->createCommand($sql)->query();

        $sql ="CREATE TABLE `ae_ext_quiz_question_answer` (
  `id` int(11) UNSIGNED NOT NULL,
  `question_id` int(11) UNSIGNED NOT NULL,
  `answer_id` int(11) UNSIGNED NOT NULL,
  `answer` mediumtext COLLATE utf16_unicode_ci NOT NULL,
  `play_id` int(11) UNSIGNED NOT NULL,
  `comment` mediumtext COLLATE utf16_unicode_ci NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf16 COLLATE=utf16_unicode_ci;";
        @Yii::app()->db->createCommand($sql)->query();


        $sql = "ALTER TABLE `ae_ext_quiz_question` DROP `answer_options`;
ALTER TABLE `ae_ext_quiz_question` DROP `correct_answer`;
ALTER TABLE `ae_ext_quiz_question`
  ADD CONSTRAINT `ae_ext_quiz_question_ibfk_1` FOREIGN KEY (`app_id`) REFERENCES `ae_game` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

ALTER TABLE `ae_ext_quiz_question_option`
  ADD PRIMARY KEY (`id`),
  ADD KEY `question_id` (`question_id`);

ALTER TABLE `ae_ext_quiz_question_option`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=146;

ALTER TABLE `ae_ext_quiz_question_option`
  ADD CONSTRAINT `ae_ext_quiz_question_option_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `ae_ext_quiz_question` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;
";

        @Yii::app()->db->createCommand($sql)->query();

        $sql = 'SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";
';

        $sql .= "INSERT INTO `ae_ext_quiz_question_option` (`id`, `question_id`, `answer`, `answer_order`, `is_correct`) VALUES
(1, 2, 'Female', 0, NULL),
(2, 2, 'Male', 0, NULL),
(3, 2, 'Genderqueer', 0, NULL),
(4, 2, 'Intersex', 0, NULL),
(5, 2, 'Other', 0, NULL),
(6, 3, 'Bisexual', 0, NULL),
(7, 3, 'Gay', 0, NULL),
(8, 3, 'Lesbian', 0, NULL),
(9, 3, 'Queer', 0, NULL),
(10, 3, 'Straight', 0, NULL),
(11, 3, 'Other', 0, NULL),
(12, 4, 'Female', 0, NULL),
(13, 4, 'Male', 0, NULL),
(14, 4, 'Genderqueer', 0, NULL),
(15, 4, 'Intersex', 0, NULL),
(16, 4, 'Other', 0, NULL),
(17, 4, 'No Preference', 0, NULL),
(18, 5, 'No answer', 0, NULL),
(19, 5, 'High school', 0, NULL),
(20, 5, 'Some college', 0, NULL),
(21, 5, 'Associates degree', 0, NULL),
(22, 5, 'Bachelors degree', 0, NULL),
(23, 5, 'Graduate degree', 0, NULL),
(24, 5, 'PhD / Post Doctoral', 0, NULL),
(25, 6, 'Arabic', 0, NULL),
(26, 6, 'Chinese', 0, NULL),
(27, 6, 'Dutch', 0, NULL),
(28, 6, 'English', 0, NULL),
(29, 6, 'French', 0, NULL),
(30, 6, 'German', 0, NULL),
(31, 6, 'Hebrew', 0, NULL),
(32, 6, 'Hindi', 0, NULL),
(33, 6, 'Italian', 0, NULL),
(34, 6, 'Japanese', 0, NULL),
(35, 6, 'Korean', 0, NULL),
(36, 6, 'Norwegian', 0, NULL),
(37, 6, 'Portuguese', 0, NULL),
(38, 6, 'Russian', 0, NULL),
(39, 6, 'Spanish', 0, NULL),
(40, 6, 'Swedish', 0, NULL),
(41, 6, 'Tagalog', 0, NULL),
(42, 6, 'Urdu', 0, NULL),
(43, 6, 'Other', 0, NULL),
(44, 7, 'No answer', 0, NULL),
(45, 7, 'Administrative / Secretarial', 0, NULL),
(46, 7, 'Architecture / Interior design', 0, NULL),
(47, 7, 'Artistic / Creative / Performance', 0, NULL),
(48, 7, 'Education / Teacher / Professor', 0, NULL),
(49, 7, 'Executive / Management', 0, NULL),
(50, 7, 'Fashion / Model / Beauty', 0, NULL),
(51, 7, 'Financial / Accounting / Real Estate', 0, NULL),
(52, 7, 'Labor / Construction', 0, NULL),
(53, 7, 'Law enforcement / Security / Military', 0, NULL),
(54, 7, 'Legal', 0, NULL),
(55, 7, 'Medical / Dental / Veterinary / Fitness', 0, NULL),
(56, 7, 'Nonprofit / Volunteer / Activist', 0, NULL),
(57, 7, 'Political / Govt / Civil Service / Military', 0, NULL),
(58, 7, 'Retail / Food services', 0, NULL),
(59, 7, 'Retired', 0, NULL),
(60, 7, 'Sales / Marketing', 0, NULL),
(61, 7, 'Self-Employed / Entrepreneur', 0, NULL),
(62, 7, 'Student', 0, NULL),
(63, 7, 'Technical / Science / Computers / Engineering', 0, NULL),
(64, 7, 'Travel / Hospitality / Transportation', 0, NULL),
(65, 7, 'Other profession', 0, NULL),
(66, 8, 'Aerobics', 0, NULL),
(67, 8, 'Auto racing / Motocross', 0, NULL),
(68, 8, 'Baseball', 0, NULL),
(69, 8, 'Basketball', 0, NULL),
(70, 8, 'Billiards / Pool', 0, NULL),
(71, 8, 'Bowling', 0, NULL),
(72, 8, 'Cycling', 0, NULL),
(73, 8, 'Dancing', 0, NULL),
(74, 8, 'Football', 0, NULL),
(75, 8, 'Golf', 0, NULL),
(76, 8, 'Hockey', 0, NULL),
(77, 8, 'Inline skating', 0, NULL),
(78, 8, 'Martial arts', 0, NULL),
(79, 8, 'Running', 0, NULL),
(80, 8, 'Skiing', 0, NULL),
(81, 8, 'Soccer', 0, NULL),
(82, 8, 'Swimming', 0, NULL),
(83, 8, 'Tennis / Racquet sports', 0, NULL),
(84, 8, 'Volleyball', 0, NULL),
(85, 8, 'Walking / Hiking', 0, NULL),
(86, 8, 'Weights / Machines', 0, NULL),
(87, 8, 'Yoga', 0, NULL),
(88, 8, 'Other types of exercise', 0, NULL),
(89, 9, 'Alumni connections', 0, NULL),
(90, 9, 'Book club', 0, NULL),
(91, 9, 'Business networking', 0, NULL),
(92, 9, 'Camping', 0, NULL),
(93, 9, 'Coffee and conversation', 0, NULL),
(94, 9, 'Cooking', 0, NULL),
(95, 9, 'Dining out', 0, NULL),
(96, 9, 'Exploring new areas', 0, NULL),
(97, 9, 'Fishing / Hunting', 0, NULL),
(98, 9, 'Gardening / Landscaping', 0, NULL),
(99, 9, 'Hobbies and crafts', 0, NULL),
(100, 9, 'Museums and art', 0, NULL),
(101, 9, 'Music and concerts', 0, NULL),
(102, 9, 'Nightclubs / Dancing', 0, NULL),
(103, 9, 'Performing arts', 0, NULL),
(104, 9, 'Playing cards', 0, NULL),
(105, 9, 'Playing sports', 0, NULL),
(106, 9, 'Political interests', 0, NULL),
(107, 9, 'Religion / Spiritual', 0, NULL),
(108, 9, 'Shopping / Antiques', 0, NULL),
(109, 9, 'Travel / Sightseeing', 0, NULL),
(110, 9, 'Video games', 0, NULL),
(111, 9, 'Volunteering', 0, NULL),
(112, 9, 'Watching sports', 0, NULL),
(113, 9, 'Wine tasting', 0, NULL),
(114, 10, 'No answer', 0, NULL),
(115, 10, 'Never', 0, NULL),
(116, 10, 'Social drinker', 0, NULL),
(117, 10, 'Moderately', 0, NULL),
(118, 10, 'Regularly', 0, NULL),
(119, 11, 'No answer', 0, NULL),
(120, 11, 'No way', 0, NULL),
(121, 11, 'Occasionally', 0, NULL),
(122, 11, 'Daily', 0, NULL),
(123, 11, 'Cigar aficionado', 0, NULL),
(124, 11, 'Yes, but trying to quit', 0, NULL),
(125, 12, 'No answer', 0, NULL),
(126, 12, 'Never', 0, NULL),
(127, 12, 'Exercise 1-2 times per week', 0, NULL),
(128, 12, 'Exercise 3-4 times per week', 0, NULL),
(129, 12, 'Exercise 5 or more times per week', 0, NULL),
(130, 13, 'Female', 0, NULL),
(131, 13, 'Male', 0, NULL),
(132, 13, 'Genderqueer', 0, NULL),
(133, 13, 'Intersex', 0, NULL),
(134, 13, 'Other', 0, NULL),
(135, 13, 'No Preference', 0, NULL),
(136, 14, 'Bisexual', 0, NULL),
(137, 14, 'Gay', 0, NULL),
(138, 14, 'Lesbian', 0, NULL),
(139, 14, 'Queer', 0, NULL),
(140, 14, 'Straight', 0, NULL),
(141, 14, 'Other', 0, NULL),
(142, 14, 'No Preference', 0, NULL),
(143, 15, 'Yes', 0, NULL),
(144, 15, 'No', 0, NULL),
(145, 15, 'No Preference', 0, NULL);
COMMIT;";

        @Yii::app()->db->createCommand($sql)->query();


    }


    private static function createTables()
    {
        if(self::helperTableExists('ae_ext_quiz')){
            return false;
        }

        $sql = "

CREATE TABLE `ae_ext_quiz` (
  `id` int(11) UNSIGNED NOT NULL,
  `app_id` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf16_unicode_ci NOT NULL,
  `description` mediumtext COLLATE utf16_unicode_ci NOT NULL,
  `valid_from` int(11) NOT NULL DEFAULT '0',
  `valid_to` int(11) NOT NULL DEFAULT '0',
  `active` tinyint(4) NOT NULL DEFAULT '1',
  `show_in_list` tinyint(1) NOT NULL DEFAULT '1',
  `image` varchar(255) COLLATE utf16_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf16 COLLATE=utf16_unicode_ci;

--
-- Dumping data for table `ae_ext_quiz`
--


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
  `answer_options` mediumtext NOT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'multiselect',
  `allow_multiple` tinyint(1) NOT NULL,
  `correct_answer` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `ae_ext_quiz_question`
--


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

--
-- Dumping data for table `ae_ext_quiz_sets`
--


-- Indexes for dumped tables
--

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
  ADD KEY `variable_id` (`variable_name`);

--
-- Indexes for table `ae_ext_quiz_sets`
--
ALTER TABLE `ae_ext_quiz_sets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quiz_id` (`quiz_id`),
  ADD KEY `app_id` (`app_id`),
  ADD KEY `question_id` (`question_id`);

--
-- AUTO_INCREMENT for dumped tables
--

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
-- AUTO_INCREMENT for table `ae_ext_quiz_sets`
--
ALTER TABLE `ae_ext_quiz_sets`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `ae_ext_quiz_sets`
--
ALTER TABLE `ae_ext_quiz_sets`
  ADD CONSTRAINT `ae_ext_quiz_sets_ibfk_1` FOREIGN KEY (`app_id`) REFERENCES `ae_game` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ae_ext_quiz_sets_ibfk_2` FOREIGN KEY (`quiz_id`) REFERENCES `ae_ext_quiz` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ae_ext_quiz_sets_ibfk_3` FOREIGN KEY (`question_id`) REFERENCES `ae_ext_quiz_question` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
";

        $sql .= 'ALTER TABLE `ae_ext_quiz_question_answer` ADD FOREIGN KEY (`question_id`) REFERENCES `ae_ext_quiz_question`(`id`) ON DELETE CASCADE ON UPDATE NO ACTION; ALTER TABLE `ae_ext_quiz_question_answer` ADD FOREIGN KEY (`answer_id`) REFERENCES `ae_ext_quiz_question_option`(`id`) ON DELETE CASCADE ON UPDATE NO ACTION; ALTER TABLE `ae_ext_quiz_question_answer` ADD FOREIGN KEY (`play_id`) REFERENCES `ae_game_play`(`id`) ON DELETE CASCADE ON UPDATE NO ACTION;';
        @Yii::app()->db->createCommand($sql)->query();
    }



}