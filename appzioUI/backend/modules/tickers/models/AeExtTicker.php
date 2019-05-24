<?php

namespace backend\modules\tickers\models;

use backend\modules\tickers\models\base\AeExtTicker as BaseAeExtTicker;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "ae_ext_ticker".
 */
class AeExtTicker extends BaseAeExtTicker
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