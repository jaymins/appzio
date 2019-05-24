<?php

namespace backend\modules\tickers\controllers\api;

/**
* This is the class for REST controller "AeExtTickerController".
*/

use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class AeExtTickerController extends \yii\rest\ActiveController
{
public $modelClass = 'backend\modules\tickers\models\AeExtTicker';
}
