<?php

namespace backend\modules\products\controllers\api;

/**
* This is the class for REST controller "AeGamePlayController".
*/

use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class AeGamePlayController extends \yii\rest\ActiveController
{
public $modelClass = 'backend\modules\products\models\AeGamePlay';
}
