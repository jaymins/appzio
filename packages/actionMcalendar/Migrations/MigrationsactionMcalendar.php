<?php

Yii::import('application.modules.aeapi.models.*');
Yii::import('application.modules.aelogic.Bootstrap.Models.*');

/*
 * This runs any custom migrations needed by the action module
 *
 */

class MigrationsactionMcalendar extends BootstrapMigrations
{

    public $title = 'Mobile Calendar';
    public $icon = 'calendar_add.png';
    public $description = 'Calendar module';

    public function runModuleMigrations()
    {
        $this->checkArticle();
        $this->recreateTables();
        $this->addTotaltime();
        return true;
    }

    public function addTotaltime(){
        if(self::helperTableExists('ae_ext_fit_component')){
            if(!self::helperColumnExists('total_time', 'ae_ext_fit_component')){
                $sql = 'ALTER TABLE `ae_ext_fit_component` ADD `total_time` INT(11) NOT NULL AFTER `rounds`;';
                @Yii::app()->db->createCommand($sql)->query();
            }
        }
    }

    public function checkArticle(){
        $sql = "SELECT * FROM ae_ext_article WHERE id = '35'";
        $result = @Yii::app()->db->createCommand($sql)->queryAll();

        if(!$result){
            $this->runMigrationFromFile('articles.sql');
        }

        return true;
    }

    public function recreateTables(){
        if(!self::helperTableExists('ae_ext_exercise_timer_relations')){
            $this->runMigrationFromFile('01022019.sql');
        }

        return true;
    }


}