<?php


namespace packages\actionMfood\Models;
use CActiveRecord;

class IngredientModel extends CActiveRecord {
    public $id;
    public $category_id;
    public $name;
    public $unit;
    public $gid;

    public function tableName()
    {
        return 'ae_ext_food_ingredient';
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function relations()
    {
        return [
            'category' => [self::BELONGS_TO, 'packages\actionMfood\Models\IngredientTypeModel', 'ingredient_category'],
        ];
    }
}