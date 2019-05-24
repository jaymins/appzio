<?php

namespace backend\modules\fitness\models;

use Yii;
use \backend\modules\fitness\models\base\AeExtFoodIngredientCategory as BaseAeExtFoodIngredientCategory;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "ae_ext_food_ingredient_category".
 */
class AeExtFoodIngredientCategory extends BaseAeExtFoodIngredientCategory
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
}
