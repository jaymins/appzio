<?php


namespace packages\actionMfood\Models;
use CActiveRecord;

class IngredientCustomModel extends CActiveRecord {
    public $id;
    public $play_id;
    public $name;
    public $ingredient_category;
    public $date;
    public $gid;

    public function tableName()
    {
        return 'ae_ext_food_custom_ingredient';
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function relations()
    {
        return [
        ];
    }
}