<?php

Yii::import('application.modules.aeapi.models.*');

/*
 * This runs any custom migrations needed by the action module
 *
 */

class MigrationsactionMgallery extends Migrations
{

    public static function runModuleMigrations()
    {
        self::createMgalleryAction();
        self::createTable();
        return true;
    }

    private static function createTable()
    {
        if (self::helperTableExists('ae_ext_gallery_images')) {
            return true;
        }

        $sql = "CREATE TABLE `ae_ext_gallery_images` (
              `id` int(11) UNSIGNED NOT NULL,
              `play_id` int(11) UNSIGNED NOT NULL,
              `image` varchar(255) NOT NULL,
              `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            
            --
            -- Indexes for dumped tables
            --
            
            --
            -- Indexes for table `ae_ext_gallery_images`
            --
            ALTER TABLE `ae_ext_gallery_images`
              ADD PRIMARY KEY (`id`),
              ADD KEY `play_id` (`play_id`);
            
            --
            -- AUTO_INCREMENT for dumped tables
            --
            
            --
            -- AUTO_INCREMENT for table `ae_ext_gallery_images`
            --
            ALTER TABLE `ae_ext_gallery_images`
              MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
            --
            -- Constraints for dumped tables
            --
            
            --
            -- Constraints for table `ae_ext_gallery_images`
            --
            ALTER TABLE `ae_ext_gallery_images`
              ADD CONSTRAINT `ae_ext_gallery_images_ibfk_1` FOREIGN KEY (`play_id`) REFERENCES `ae_game_play` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;";

        @Yii::app()->db->createCommand($sql)->query();

        return true;


    }

    private static function createMgalleryAction()
    {
        if (self::helperActionExists('mgallery')) {
            return false;
        }

        $sql = "
          INSERT INTO `ae_game_branch_action_type` (`title`, `icon`, `shortname`, `id_user`, `description`, `version`, `channels`, `uiformat`, `active`, `global`, `githubrepo`, `adminfeedback`, `requestupdate`, `uses_table`, `has_statistics`, `has_export`, `invisible`, `hide_from_api`, `ios_supports`, `android_supports`, `web_supports`, `article_view`,`library`) VALUES
          ('Gallery', 'menu.png', 'mgallery', 1, '<p>General gallery</p>', '1', '', 'native', 1, 1, '', '', 0, 0, 0, 0, 0, 0, 1, 1, 0, 1,'PHP2');
        ";

        @Yii::app()->db->createCommand($sql)->query();

        return true;
    }

}
