<?php


namespace packages\actionMitems\Models;

use CActiveRecord;

class ItemLikeModel extends CActiveRecord
{
    public $id;
    public $play_id;
    public $item_id;
    public $status;

    public function tableName()
    {
        return 'ae_ext_items_likes';
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function relations()
    {
        return array(
            'item' => array(self::BELONGS_TO, 'packages\actionMitems\Models\ItemModel', 'item_id')
        );
    }
}