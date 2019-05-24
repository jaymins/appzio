<?php

Yii::import('application.modules.aeapi.models.*');
Yii::import('application.modules.aelogic.Bootstrap.Models.*');

/*
 * This runs any custom migrations needed by the action module
 *
 */

class MigrationsactionMofficernd extends BootstrapMigrations {

    /* set these so that the action will get created automatically */
    public $title = 'Office RND connector';
    public $icon = 'new.png';
    public $description = 'Connector for Office RND system';

    public function runModuleMigrations(){
        return true;
    }

}
