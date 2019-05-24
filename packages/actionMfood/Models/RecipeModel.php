<?php

namespace packages\actionMfood\Models;

use CActiveRecord;

class RecipeModel extends CActiveRecord
{

    public $id;
    public $name;
    public $difficult;
    public $serve;
    public $type_id;
    public $photo;
    public $gid;
    public $total_time;

    public function tableName()
    {
        return 'ae_ext_food_recipe';
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function relations()
    {
        return [
            'type' => [self::HAS_ONE, 'packages\actionMfood\Models\RecipeTypeModel', 'id'],
            'step' => [self::HAS_MANY, 'packages\actionMfood\Models\RecipeStepModel', 'id'],
            'ingredient_id' => [self::HAS_MANY, 'packages\actionMfood\Models\IngredientRecipeModel', 'recipe_id'],
        ];
    }

}