<?php

namespace backend\modules\products\controllers\api;

/**
* This is the class for REST controller "AeExtProductsTagController".
*/

use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class AeExtProductsTagController extends \yii\rest\ActiveController
{
public $modelClass = 'backend\modules\products\models\AeExtProductsTag';
}
