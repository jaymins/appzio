<?php

Yii::import('application.modules.aeapi.models.*');
Yii::import('application.modules.aelogic.Bootstrap.Models.*');

/*
 * This runs any custom migrations needed by the action module
 *
 */

class MigrationsactionMsubscription extends BootstrapMigrations
{

    public $title = 'Mobile Subscriptions';
    public $icon = 'calendar_add.png';
    public $description = 'Handle inApp subscriptions';

    public function runModuleMigrations()
    {
        $this->createPurchases();
        return true;
    }

    public function createPurchases(){
        if(!self::helperTableExists('ae_ext_purchase_product')){
            $this->runMigrationFromFile('purchase.sql');
        }

        return true;
    }



}