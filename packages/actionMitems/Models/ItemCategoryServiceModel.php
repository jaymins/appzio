<?php

namespace packages\actionMitems\Models;

use CActiveRecord;

class ItemCategoryServiceModel extends CActiveRecord
{
    public $id;
    public $category_id;
    public $name;
    public $description;

    public function tableName()
    {
        return 'ae_ext_items_category_service';
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function relations()
    {
        return array(
            'category' => array(self::BELONGS_TO, 'packages\actionMitems\Models\ItemCategoryModel', 'category_id')
        );
    }
}