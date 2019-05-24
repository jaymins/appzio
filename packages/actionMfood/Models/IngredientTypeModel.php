<?php


namespace packages\actionMfood\Models;
use CActiveRecord;

class IngredientTypeModel extends CActiveRecord {
    public $id;
    public $name;
    public $icon;
    public $gid;
    public $app_id;

    public function tableName()
    {
        return 'ae_ext_food_ingredient_category';
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function relations()
    {
        return [];
    }
}