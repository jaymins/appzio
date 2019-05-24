<?php

namespace backend\modules\items\controllers\api;

/**
* This is the class for REST controller "AeExtItemsImageController".
*/

use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class AeExtItemsImageController extends \yii\rest\ActiveController
{
public $modelClass = 'backend\modules\items\models\AeExtItemsImage';
}
