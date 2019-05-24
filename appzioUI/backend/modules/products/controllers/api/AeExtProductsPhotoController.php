<?php

namespace backend\modules\products\controllers\api;

/**
* This is the class for REST controller "AeExtProductsPhotoController".
*/

use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class AeExtProductsPhotoController extends \yii\rest\ActiveController
{
public $modelClass = 'backend\modules\products\models\AeExtProductsPhoto';
}
