<?php


namespace packages\actionMfood\Models;
use CActiveRecord;

class StepModel extends CActiveRecord {
    public $id;
    public $time;
    public $gid;

    public function tableName()
    {
        return 'ae_ext_food_recipe_step';
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