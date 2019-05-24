<?php

namespace backend\modules\tatjack\controllers\api;

/**
* This is the class for REST controller "AeGameVariableController".
*/

use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class AeGameVariableController extends \yii\rest\ActiveController
{
public $modelClass = 'backend\modules\tatjack\models\AeGameVariable';
}
