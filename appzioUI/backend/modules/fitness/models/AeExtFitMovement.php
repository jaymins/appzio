<?php

namespace backend\modules\fitness\models;

use Yii;
use \backend\modules\fitness\models\base\AeExtFitMovement as BaseAeExtFitMovement;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "ae_ext_fit_movement".
 */
class AeExtFitMovement extends BaseAeExtFitMovement
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
    public static function getAllMovements()
    {
        $movements = AeExtFitMovement::find()
            ->asArray() // optional
            ->all();

        if ($movements) {
            return $movements;
        }

        return [];
    }

}
