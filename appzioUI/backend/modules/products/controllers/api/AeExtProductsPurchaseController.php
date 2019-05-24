<?php

namespace backend\modules\products\controllers\api;

/**
* This is the class for REST controller "AeExtProductsPurchaseController".
*/

use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class AeExtProductsPurchaseController extends \yii\rest\ActiveController
{
public $modelClass = 'backend\modules\products\models\AeExtProductsPurchase';
}
