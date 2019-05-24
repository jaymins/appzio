<?php

namespace backend\modules\users\controllers\api;

/**
* This is the class for REST controller "AeGamePlayVariableController".
*/

use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class AeGamePlayVariableController extends \yii\rest\ActiveController
{
public $modelClass = 'backend\modules\users\models\AeGamePlayVariable';
}
