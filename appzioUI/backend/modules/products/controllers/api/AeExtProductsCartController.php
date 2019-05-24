<?php

namespace backend\modules\products\controllers\api;

/**
* This is the class for REST controller "AeExtProductsCartController".
*/

use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class AeExtProductsCartController extends \yii\rest\ActiveController
{
public $modelClass = 'backend\modules\products\models\AeExtProductsCart';
}
