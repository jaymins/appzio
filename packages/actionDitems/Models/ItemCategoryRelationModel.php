<?php


namespace packages\actionDitems\Models;

use CActiveRecord;

class ItemCategoryRelationModel extends CActiveRecord
{
    public $id;
    public $item_id;
    public $category_id;
    public $description;

    public function tableName()
    {
        return 'ae_ext_items_category_item';
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function relations()
    {
        return array(
            'item' => array(self::BELONGS_TO, 'packages\actionDitems\Models\ItemModel', 'item_id'),
            'category' => array(self::BELONGS_TO, 'packages\actionDitems\Models\ItemCategoryModel', 'category_id')
        );
    }
}