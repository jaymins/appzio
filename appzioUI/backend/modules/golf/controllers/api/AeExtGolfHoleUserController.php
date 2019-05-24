<?php

namespace backend\modules\golf\controllers\api;

/**
* This is the class for REST controller "AeExtGolfHoleUserController".
*/

use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class AeExtGolfHoleUserController extends \yii\rest\ActiveController
{
public $modelClass = 'backend\modules\golf\models\AeExtGolfHoleUser';
}
