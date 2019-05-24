<?php

namespace backend\modules\tickers\controllers\api;

/**
* This is the class for REST controller "AeGameController".
*/

use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class AeGameController extends \yii\rest\ActiveController
{
public $modelClass = 'backend\modules\tickers\models\AeGame';
}
