<?php

namespace backend\modules\tickers\models;

use backend\modules\tickers\models\base\AeExtTickerTrade as BaseAeExtTickerTrade;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "ae_ext_ticker_trades".
 */
class AeExtTickerTrade extends BaseAeExtTickerTrade
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
//                To do: check if we should allow empty values
//                [['buy_range_from'], 'default', 'value' => '0'],
//                [['buy_range_to'], 'default', 'value' => '0'],
//                [['sell_range_from'], 'default', 'value' => '0'],
//                [['sell_range_to'], 'default', 'value' => '0'],
            ]
        );
    }
}