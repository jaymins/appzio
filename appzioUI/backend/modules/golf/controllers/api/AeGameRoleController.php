<?php

namespace backend\modules\golf\controllers\api;

/**
* This is the class for REST controller "AeGameRoleController".
*/

use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class AeGameRoleController extends \yii\rest\ActiveController
{
public $modelClass = 'backend\modules\golf\models\AeGameRole';
}
