<?php

Yii::import('application.modules.aeapi.models.*');

/*
 * This runs any custom migrations needed by the action module
 *
 */

class MigrationsactionMitems extends Migrations {

    public static function runModuleMigrations(){
        self::createMitems();
        self::addZip();
        self::addApprovedCategory();
        self::addSentRecurringNotificationsField();

        /**
         * @todo Not tested, pls test locally.
         */
        //self::createJobData();
        return true;
    }


    private static function createMitems()
    {
        if(self::helperActionExists('mitems')){
            return false;
        }

        $sql = "
          INSERT INTO `ae_game_branch_action_type` (`title`, `icon`, `shortname`, `id_user`, `description`, `version`, `channels`, `uiformat`, `active`, `global`, `githubrepo`, `adminfeedback`, `requestupdate`, `uses_table`, `has_statistics`, `has_export`, `invisible`, `hide_from_api`, `ios_supports`, `android_supports`, `web_supports`, `article_view`) VALUES
          ('Mobile Items', 'new.png', 'mitems', 1, '<p>Generic items action for Bootstrap2</p>', '1', '', 'native', 1, 1, '', '', 0, 0, 0, 0, 0, 0, 1, 1, 0, 1);
        ";

        @Yii::app()->db->createCommand($sql)->query();
    }

    private static function addZip(){
        if(self::helperColumnExists('zip', 'ae_ext_items')){
            return false;
        }

        $sql = "ALTER TABLE `ae_ext_items` ADD `zip` VARCHAR(255) NOT NULL AFTER `country`;";
        @Yii::app()->db->createCommand($sql)->query();

    }

    /**
     * @todo Highly untested migration and thus commented. There are so many alterations that could fit into one query
     */
    private static function createJobData() {
        $sql = "CREATE TABLE IF NOT EXISTS  `appziodb`.`ae_ext_items_category_service` (
          `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
          `category_id` INT NOT NULL,
          `name` VARCHAR (200) NOT NULL,
          `description` LONGTEXT,
          PRIMARY KEY (`id`),
          FOREIGN KEY (`category_id`) REFERENCES `appziodb`.`ae_ext_items_categories` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
        );";
        @Yii::app()->db->createCommand($sql)->query();


        $sql = "CREATE TABLE IF NOT EXISTS  `ae_ext_mitem_provider_services` (
                  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                  `app_id` int(10) UNSIGNED NOT NULL,
                  `play_id` int(11) UNSIGNED NOT NULL,
                  `service_id` int(10) UNSIGNED NOT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
        @Yii::app()->db->createCommand($sql)->query();

        $sql = "CREATE TABLE IF NOT EXISTS   `ae_ext_mitem_worker` (
                  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                  `app_id` int(11) UNSIGNED NOT NULL,
                  `play_id` int(11) UNSIGNED NOT NULL,
                  `worker_play_id` int(11) DEFAULT NULL,
                  `first_name` varchar(50) NOT NULL,
                  `last_name` varchar(50) NOT NULL,
                  `email` varchar(60) NOT NULL,
                  `password` varchar(120) NOT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
        @Yii::app()->db->createCommand($sql)->query();

        $sql = "CREATE TABLE IF NOT EXISTS `ae_ext_mitem_worker_services` (
                  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                  `app_id` int(10) UNSIGNED NOT NULL,
                  `worker_id` int(10) UNSIGNED NOT NULL,
                  `service_id` int(10) UNSIGNED NOT NULL,
                  `experience` varchar(30) DEFAULT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
        @Yii::app()->db->createCommand($sql)->query();

        $sql = "ALTER TABLE `ae_ext_mitem_provider_services`
                  ADD PRIMARY KEY (`id`),
                  ADD KEY `ae_ext_mitem_worker_services_fk1` (`app_id`),
                  ADD KEY `ae_ext_mitem_worker_services_fk2` (`service_id`),
                  ADD KEY `ae_ext_mitem_worker_services_fk4` (`play_id`);";
        @Yii::app()->db->createCommand($sql)->query();

        $sql = "ALTER TABLE `ae_ext_mitem_worker`
              ADD PRIMARY KEY (`id`),
              ADD KEY `ae_ext_mitem_worker_fk1` (`play_id`),
              ADD KEY `ae_ext_mitem_worker_fk2` (`app_id`);";
        @Yii::app()->db->createCommand($sql)->query();

        $sql = "ALTER TABLE `ae_ext_mitem_worker_services`
              ADD PRIMARY KEY (`id`),
              ADD KEY `ae_ext_mitem_worker_services_fk1` (`app_id`),
              ADD KEY `ae_ext_mitem_worker_services_fk2` (`service_id`),
              ADD KEY `ae_ext_mitem_worker_services_fk3` (`worker_id`);";
        @Yii::app()->db->createCommand($sql)->query();

        $sql = "ALTER TABLE `ae_ext_mitem_provider_services`
            MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;";
        @Yii::app()->db->createCommand($sql)->query();

        $sql = "ALTER TABLE `ae_ext_mitem_provider_services`
            ADD CONSTRAINT `ae_ext_mitem_worker_services_fk4` FOREIGN KEY (`play_id`) REFERENCES `ae_game_play` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;";
        @Yii::app()->db->createCommand($sql)->query();

        $sql = "ALTER TABLE `ae_ext_mitem_worker`
                  ADD CONSTRAINT `ae_ext_mitem_worker_fk1` FOREIGN KEY (`play_id`) REFERENCES `ae_game_play` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
                  ADD CONSTRAINT `ae_ext_mitem_worker_fk2` FOREIGN KEY (`app_id`) REFERENCES `ae_game` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;";
        @Yii::app()->db->createCommand($sql)->query();

        $sql = "ALTER TABLE `ae_ext_mitem_worker_services`
              ADD CONSTRAINT `ae_ext_mitem_worker_services_fk1` FOREIGN KEY (`app_id`) REFERENCES `ae_game` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
              ADD CONSTRAINT `ae_ext_mitem_worker_services_fk2` FOREIGN KEY (`service_id`) REFERENCES `ae_ext_items_category_service` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
              ADD CONSTRAINT `ae_ext_mitem_worker_services_fk3` FOREIGN KEY (`worker_id`) REFERENCES `ae_ext_mitem_worker` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;";
        @Yii::app()->db->createCommand($sql)->query();

        $sql = "ALTER TABLE `ae_ext_items`
          ADD `category_service_id` INT (11) UNSIGNED NOT NULL AFTER `game_id`,
          ADD `is_flexible_fee` TINYINT (1) NOT NULL DEFAULT 0 AFTER `description`,
          ADD `is_flexible_time` TINYINT (1) NOT NULL DEFAULT 0 AFTER `is_flexible_fee`,
          ADD `min_price` DECIMAL (10, 2) NOT NULL AFTER `is_flexible_time`,
          ADD `max_price` DECIMAL (10, 2) NOT NULL AFTER `min_price`,
          ADD `pdf` VARCHAR (255) NOT NULL AFTER `date_added`,
          ADD KEY `ae_ext_items_ibfk_5` (`category_service_id`),
          ADD CONSTRAINT `ae_ext_items_ibfk_5` FOREIGN KEY (`category_service_id`) REFERENCES `ae_ext_items_category_service` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
          ADD `job_time` INT(11) NOT NULL;";
        @Yii::app()->db->createCommand($sql)->query();
    }

    private static function addApprovedCategory(){
        if(self::helperColumnExists('approved', 'ae_ext_items_categories')){
            return false;
        }

        if(!self::helperColumnExists('description', 'ae_ext_items_categories')){
            return false;
        }

        if(!self::helperTableExists('ae_ext_items_categories')){
            return false;
        }

        $sql = "ALTER TABLE `ae_ext_items_categories` ADD `approved` TINYINT NULL DEFAULT NULL AFTER `description`;";
        @Yii::app()->db->createCommand($sql)->query();

    }

    public static function addSentRecurringNotificationsField()
    {
        if(self::helperColumnExists('sent_recurring_notifications', 'ae_ext_items_reminders')){
            return false;
        }

        $sql = "ALTER TABLE `ae_ext_items_reminders` ADD `sent_recurring_notifications` JSON NULL AFTER `notification_sent`;";
        @Yii::app()->db->createCommand($sql)->query();

        return true;
    }

}