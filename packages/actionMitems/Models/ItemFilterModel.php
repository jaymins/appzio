<?php


namespace packages\actionMitems\Models;

use CActiveRecord;

class ItemFilterModel extends CActiveRecord
{
    public $id;
    public $play_id;
    public $category_id;
    public $price_from;
    public $price_to;
    public $tags;

    public function tableName()
    {
        return 'ae_ext_items_filters';
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}