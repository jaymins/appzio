<?php

namespace backend\modules\tatjack\models;

use Yii;
use \backend\modules\tatjack\models\base\AeGameVariable as BaseAeGameVariable;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "ae_game_variable".
 */
class AeGameVariable extends BaseAeGameVariable
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
