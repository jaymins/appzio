<?php

Yii::import('application.modules.aeapi.models.*');

/*
 * This runs any custom migrations needed by the action module
 *
 */

class MigrationsactionMfood extends BootstrapMigrations
{

    public function runModuleMigrations()
    {
        self::createFoodAction();
        self::addTime();
        return true;
    }

    private static function addTime(){
        if(self::helperTableExists('ae_ext_food_recipe')){
            if(!self::helperColumnExists('total_time', 'ae_ext_food_recipe')){
                $sql = "ALTER TABLE `ae_ext_food_recipe` ADD `total_time` INT NOT NULL AFTER `photo`;";
                @Yii::app()->db->createCommand($sql)->query();
            }
        }
    }

    private static function createFoodAction()
    {
        if (self::helperActionExists('mfood')) {
            return false;
        }

        $sql = "
          INSERT INTO `ae_game_branch_action_type` (`title`, `icon`, `shortname`, `id_user`, `description`, `version`, `channels`, `uiformat`, `active`, `global`, `githubrepo`, `adminfeedback`, `requestupdate`, `uses_table`, `has_statistics`, `has_export`, `invisible`, `hide_from_api`, `ios_supports`, `android_supports`, `web_supports`, `article_view`,`library`) VALUES
          ('Food', 'green.png', 'mfood', 1, '<p>Food, recipes & shopping list</p>', '1', '', 'native', 1, 1, '', '', 0, 0, 0, 0, 0, 0, 1, 1, 0, 1,'PHP2');
        ";

        @Yii::app()->db->createCommand($sql)->query();
    }


}