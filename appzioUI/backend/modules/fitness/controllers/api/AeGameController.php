<?php

namespace backend\modules\fitness\controllers\api;

/**
* This is the class for REST controller "AeGameController".
*/

use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class AeGameController extends \yii\rest\ActiveController
{
public $modelClass = 'backend\modules\fitness\models\AeGame';
}
