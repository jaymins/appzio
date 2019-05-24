<?php

namespace backend\modules\items\controllers\api;

/**
* This is the class for REST controller "AeExtItemsCategoryController".
*/

use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class AeExtItemsCategoryController extends \yii\rest\ActiveController
{
public $modelClass = 'backend\modules\items\models\AeExtItemsCategory';
}
