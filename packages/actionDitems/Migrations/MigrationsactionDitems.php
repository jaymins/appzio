<?php

Yii::import('application.modules.aeapi.models.*');

/*
 * This runs any custom migrations needed by the action module
 *
 */

class MigrationsactionDitems extends Migrations {

    public static function runModuleMigrations(){
        self::createDitemsAction();
        return true;
    }

    private static function createDitemsAction()
    {
        if(self::helperActionExists('Ditems')){
            return false;
        }

        $sql = "
          INSERT INTO `ae_game_branch_action_type` (`title`, `icon`, `shortname`, `id_user`, `description`, `version`, `channels`, `uiformat`, `active`, `global`, `githubrepo`, `adminfeedback`, `requestupdate`, `uses_table`, `has_statistics`, `has_export`, `invisible`, `hide_from_api`, `ios_supports`, `android_supports`, `web_supports`, `article_view`,`library`) VALUES
          ('Demoitems', 'menu.png', 'Ditems', 1, '<p></p>', '1', '', 'native', 1, 1, '', '', 0, 0, 0, 0, 0, 0, 1, 1, 0, 1,'PHP2');
        ";

        @Yii::app()->db->createCommand($sql)->query();
    }
}
