<?php


namespace packages\actionDitems\Models;

use CActiveRecord;

class ItemTagModel extends CActiveRecord
{
    public $id;
    public $app_id;
    public $name;

    public function tableName()
    {
        return 'ae_ext_items_tags';
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