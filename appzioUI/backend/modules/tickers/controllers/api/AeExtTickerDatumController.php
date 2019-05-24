<?php

namespace backend\modules\tickers\controllers\api;

/**
* This is the class for REST controller "AeExtTickerDatumController".
*/

use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class AeExtTickerDatumController extends \yii\rest\ActiveController
{
public $modelClass = 'backend\modules\tickers\models\AeExtTickerDatum';
}
