<?php

Yii::import('application.modules.aeapi.models.*');

/*
 * This runs any custom migrations needed by the action module
 *
 */

class MigrationsactionMnexudus extends Migrations {

    public static function runModuleMigrations(){
        self::createMnexudusAction();
        return true;
    }

    private static function createMnexudusAction()
    {
        if(self::helperActionExists('Mnexudus')){
            return false;
        }

        $sql = "
          INSERT INTO `ae_game_branch_action_type` (`title`, `icon`, `shortname`, `id_user`, `description`, `version`, `channels`, `uiformat`, `active`, `global`, `githubrepo`, `adminfeedback`, `requestupdate`, `uses_table`, `has_statistics`, `has_export`, `invisible`, `hide_from_api`, `ios_supports`, `android_supports`, `web_supports`, `article_view`,`library`) VALUES
          ('Nexudus Connector', 'menu.png', 'Mnexudus', 1, '<p>Action for connecting with Nexudus booking system.</p>', '1', '', 'native', 1, 1, '', '', 0, 0, 0, 0, 0, 0, 1, 1, 0, 1,'PHP2');
        ";

        @Yii::app()->db->createCommand($sql)->query();
    }
}
