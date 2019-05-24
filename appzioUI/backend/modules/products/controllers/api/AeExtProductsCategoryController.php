<?php

namespace backend\modules\products\controllers\api;

/**
* This is the class for REST controller "AeExtProductsCategoryController".
*/

use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class AeExtProductsCategoryController extends \yii\rest\ActiveController
{
public $modelClass = 'backend\modules\products\models\AeExtProductsCategory';
}
