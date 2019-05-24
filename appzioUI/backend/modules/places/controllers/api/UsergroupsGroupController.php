<?php

namespace backend\modules\places\controllers\api;

/**
* This is the class for REST controller "UsergroupsGroupController".
*/

use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class UsergroupsGroupController extends \yii\rest\ActiveController
{
public $modelClass = 'backend\modules\places\models\UsergroupsGroup';
}
