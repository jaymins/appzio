<?php

namespace backend\modules\tickers\controllers\api;

/**
* This is the class for REST controller "AeExtTickerTradeController".
*/

use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class AeExtTickerTradeController extends \yii\rest\ActiveController
{
public $modelClass = 'backend\modules\tickers\models\AeExtTickerTrade';
}
