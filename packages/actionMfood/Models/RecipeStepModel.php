<?php
namespace packages\actionMfood\Models;
use CActiveRecord;

class RecipeStepModel extends CActiveRecord {

    public $recipe_id;
    public $step_id;
    public $gid;

    public function tableName()
    {
        return 'ae_ext_food_recipe_step_recipe';
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function relations()
    {
        return [
            'step' => [self::HAS_MANY, 'packages\actionMfood\Models\StepModel', 'id'],
        ];
    }


}