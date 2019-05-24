<?php

namespace backend\modules\fitness\models;

use Yii;
use \backend\modules\fitness\models\base\AeExtFoodRecipe as BaseAeExtFoodRecipe;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "ae_ext_food_recipe".
 */
class AeExtFoodRecipe extends BaseAeExtFoodRecipe
{

    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                # custom behaviors
            ]
        );
    }

    public function rules()
    {
        return ArrayHelper::merge(
            parent::rules(),
            [
                # custom validation rules
            ]
        );
    }
    public static function getAllIngredients()
    {
        $ingredients = AeExtFoodIngredient::find()
            ->asArray() // optional
            ->all();

        if ($ingredients) {
            return $ingredients;
        }

        return [];
    }
    public static function getAllSteps()
    {
        $ingredients = AeExtFoodRecipeStep::find()
            ->asArray() // optional
            ->all();

        if ($ingredients) {
            return $ingredients;
        }

        return [];
    }
}
