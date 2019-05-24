<?php


namespace packages\actionMitems\Models;

use CActiveRecord;

class ProviderServicesModel extends CActiveRecord
{
    public $id;
    public $app_id;
    public $play_id;
    public $service_id;

    public function tableName()
    {
        return 'ae_ext_mitem_provider_services';
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function relations()
    {
        return array(
            'service' => array(self::BELONGS_TO, 'packages\actionMitems\Models\ItemCategoryServiceModel', 'service_id')
        );
    }
}