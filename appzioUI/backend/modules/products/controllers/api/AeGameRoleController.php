<?php

namespace backend\modules\products\controllers\api;

/**
* This is the class for REST controller "AeGameRoleController".
*/

use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class AeGameRoleController extends \yii\rest\ActiveController
{
public $modelClass = 'backend\modules\products\models\AeGameRole';
}
