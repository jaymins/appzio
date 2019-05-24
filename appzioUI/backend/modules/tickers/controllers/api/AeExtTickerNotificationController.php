<?php

namespace backend\modules\tickers\controllers\api;

/**
* This is the class for REST controller "AeExtTickerNotificationController".
*/

use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class AeExtTickerNotificationController extends \yii\rest\ActiveController
{
public $modelClass = 'backend\modules\tickers\models\AeExtTickerNotification';
}
