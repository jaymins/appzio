<?php

namespace backend\modules\fitness\models;

use Yii;
use \backend\modules\fitness\models\base\AeExtFitProgram as BaseAeExtFitProgram;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "ae_ext_fit_program".
 */
class AeExtFitProgram extends BaseAeExtFitProgram
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

    public static function getAllRecipes()
    {
        $recipes = AeExtFoodRecipe::find()
            ->asArray()// optional
            ->all();

        if ($recipes) {
            return $recipes;
        }

        return [];
    }

    public static function getAllExercises($category = 'fitness')
    {
        $filter = ($category!= 'fitness')?['category_id' => 6 ]:[];

        $recipes = AeExtFitExercise::find()

            ->where($filter)
            ->asArray()// optional
            ->all();

        if ($recipes) {
            return $recipes;
        }

        return [];
    }

}
