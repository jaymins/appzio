<?php

namespace backend\modules\tatjack\controllers\api;

/**
* This is the class for REST controller "UsergroupsUserController".
*/

use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class UsergroupsUserController extends \yii\rest\ActiveController
{
public $modelClass = 'backend\modules\tatjack\models\UsergroupsUser';
}
