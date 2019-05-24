<?php

namespace backend\modules\golf\controllers\api;

/**
* This is the class for REST controller "AeRoleController".
*/

use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class AeRoleController extends \yii\rest\ActiveController
{
public $modelClass = 'backend\modules\golf\models\AeRole';
}
