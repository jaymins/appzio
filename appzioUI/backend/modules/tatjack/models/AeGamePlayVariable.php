<?php

namespace backend\modules\tatjack\models;

use Yii;
use \backend\modules\tatjack\models\base\AeGamePlayVariable as BaseAeGamePlayVariable;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "ae_game_play_variable".
 */
class AeGamePlayVariable extends BaseAeGamePlayVariable
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
