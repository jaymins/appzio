<?php

namespace backend\modules\tickers\controllers\api;

/**
* This is the class for REST controller "AeNotificationController".
*/

use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class AeNotificationController extends \yii\rest\ActiveController
{
public $modelClass = 'backend\modules\tickers\models\AeNotification';
}
