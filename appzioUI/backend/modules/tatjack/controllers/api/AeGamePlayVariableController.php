<?php

namespace backend\modules\tatjack\controllers\api;

/**
* This is the class for REST controller "AeGamePlayVariableController".
*/

use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class AeGamePlayVariableController extends \yii\rest\ActiveController
{
public $modelClass = 'backend\modules\tatjack\models\AeGamePlayVariable';
}
