<?php


namespace packages\actionMitems\Models;

use CActiveRecord;

class WorkerServicesModel extends CActiveRecord
{
    public $id;
    public $app_id;
    public $worker_id;
    public $service_id;

    public function tableName()
    {
        return 'ae_ext_mitem_worker_services';
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function relations()
    {
        return array(
            'worker' => array(self::BELONGS_TO, 'packages\actionMitems\Models\WorkerModel', 'worker_id'),
            'service' => array(self::BELONGS_TO, 'packages\actionMitems\Models\ItemCategoryServiceModel', 'service_id')
        );
    }
}