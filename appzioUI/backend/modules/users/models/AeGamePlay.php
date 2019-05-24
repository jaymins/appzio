<?php

namespace backend\modules\users\models;

use Yii;
use \backend\modules\users\models\base\AeGamePlay as BaseAeGamePlay;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "ae_game_play".
 */
class AeGamePlay extends BaseAeGamePlay
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
