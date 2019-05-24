<?php

namespace backend\modules\registration\models;

use Yii;
use \backend\modules\registration\models\base\AeGame as BaseAeGame;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "ae_game".
 */
class AeGame extends BaseAeGame
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
