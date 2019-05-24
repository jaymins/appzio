<?php

namespace backend\modules\golf\controllers\api;

/**
* This is the class for REST controller "AeExtGolfHoleController".
*/

use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class AeExtGolfHoleController extends \yii\rest\ActiveController
{
public $modelClass = 'backend\modules\golf\models\AeExtGolfHole';
}
