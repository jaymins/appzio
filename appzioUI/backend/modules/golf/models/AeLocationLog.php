<?php

namespace backend\modules\golf\models;

use Yii;
use \backend\modules\golf\models\base\AeLocationLog as BaseAeLocationLog;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "ae_location_log".
 */
class AeLocationLog extends BaseAeLocationLog
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
