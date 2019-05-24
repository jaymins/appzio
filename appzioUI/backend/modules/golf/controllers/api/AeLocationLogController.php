<?php

namespace backend\modules\golf\controllers\api;

/**
* This is the class for REST controller "AeLocationLogController".
*/

use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class AeLocationLogController extends \yii\rest\ActiveController
{
public $modelClass = 'backend\modules\golf\models\AeLocationLog';
}
