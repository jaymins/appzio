<?php

namespace backend\modules\tickers\models;

use Yii;
use \backend\modules\tickers\models\base\AeExtTickerDaily as BaseAeExtTickerDaily;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "ae_ext_ticker_daily".
 */
class AeExtTickerDaily extends BaseAeExtTickerDaily
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
