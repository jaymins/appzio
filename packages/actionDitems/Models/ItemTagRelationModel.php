<?php


namespace packages\actionDitems\Models;

use CActiveRecord;

class ItemTagRelationModel extends CActiveRecord
{
    public $id;
    public $tag_id;
    public $item_id;

    public function tableName()
    {
        return 'ae_ext_items_tag_item';
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