<?php

namespace backend\modules\tasks\controllers\api;

/**
* This is the class for REST controller "AeExtMtaskController".
*/

use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class AeExtMtaskController extends \yii\rest\ActiveController
{
public $modelClass = 'backend\modules\tasks\models\AeExtMtask';
}
