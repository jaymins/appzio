<?php

namespace backend\modules\items\controllers\api;

/**
* This is the class for REST controller "AeExtItemsCategoryItemController".
*/

use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class AeExtItemsCategoryItemController extends \yii\rest\ActiveController
{
public $modelClass = 'backend\modules\items\models\AeExtItemsCategoryItem';
}
