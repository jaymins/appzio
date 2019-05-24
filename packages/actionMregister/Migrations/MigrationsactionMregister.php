<?php

Yii::import('application.modules.aeapi.models.*');
Yii::import('application.modules.aelogic.Bootstrap.Models.*');

/*
 * This runs any custom migrations needed by the action module
 *
 */

class MigrationsactionMregister extends BootstrapMigrations
{

    public $title = 'Mobile Registration';
    public $icon = 'calendar_add.png';
    public $description = 'Multi-purpose user registration module';

    public function runModuleMigrations()
    {
        $this->createHelperTables();
        return true;
    }

    public function createHelperTables(){
        if(!self::helperTableExists('ae_ext_mregister_companies')){
            $this->runMigrationFromFile('registration.sql');
        }

        return true;
    }



}