<?php

namespace backend\modules\tickers\controllers\api;

/**
* This is the class for REST controller "AeExtTickerDailyController".
*/

use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class AeExtTickerDailyController extends \yii\rest\ActiveController
{
public $modelClass = 'backend\modules\tickers\models\AeExtTickerDaily';
}
