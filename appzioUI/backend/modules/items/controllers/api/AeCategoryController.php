<?php

namespace backend\modules\items\controllers\api;

/**
* This is the class for REST controller "AeCategoryController".
*/

use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class AeCategoryController extends \yii\rest\ActiveController
{
public $modelClass = 'backend\modules\items\models\AeCategory';
}
