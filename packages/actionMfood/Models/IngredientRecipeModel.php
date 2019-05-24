<?php


namespace packages\actionMfood\Models;

use CActiveRecord;

class IngredientRecipeModel extends CActiveRecord {
    public $recipe_id;
    public $ingredient_id;
    public $quantity;
    public $gid;

    public function tableName()
    {
        return 'ae_ext_food_recipe_ingredient';
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function relations()
    {
        return [
            'recipe_ingredient'=> [self::BELONGS_TO, 'packages\actionMfood\Models\IngredientModel', 'ingredient_id'],
            'recipe'=> [self::BELONGS_TO, 'packages\actionMfood\Models\RecipeModel', 'recipe_id'],
        ];

    }


}