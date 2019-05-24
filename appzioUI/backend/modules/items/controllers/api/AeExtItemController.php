<?php

namespace backend\modules\items\controllers\api;

/**
* This is the class for REST controller "AeExtItemController".
*/

use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class AeExtItemController extends \yii\rest\ActiveController
{
public $modelClass = 'backend\modules\items\models\AeExtItem';
}
