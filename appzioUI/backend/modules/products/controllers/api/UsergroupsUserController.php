<?php

namespace backend\modules\products\controllers\api;

/**
* This is the class for REST controller "UsergroupsUserController".
*/

use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class UsergroupsUserController extends \yii\rest\ActiveController
{
public $modelClass = 'backend\modules\products\models\UsergroupsUser';
}
