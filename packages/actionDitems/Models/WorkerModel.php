<?php


namespace packages\actionDitems\Models;

use CActiveRecord;

class WorkerModel extends CActiveRecord
{
    public $id;
    public $play_id;
    public $app_id;
    public $worker_play_id;
    public $first_name;
    public $last_name;
    public $email;

    public function tableName()
    {
        return 'ae_ext_mitem_worker';
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function relations()
    {
        return array();
    }
}