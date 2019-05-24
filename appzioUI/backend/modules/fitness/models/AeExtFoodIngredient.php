<?php

namespace backend\modules\fitness\models;

use Yii;
use \backend\modules\fitness\models\base\AeExtFoodIngredient as BaseAeExtFoodIngredient;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "ae_ext_food_ingredient".
 */
class AeExtFoodIngredient extends BaseAeExtFoodIngredient
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
