<?php

namespace backend\modules\products\controllers\api;

/**
* This is the class for REST controller "AeExtMtaskController".
*/

use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class AeExtMtaskController extends \yii\rest\ActiveController
{
public $modelClass = 'backend\modules\products\models\AeExtMtask';
}
