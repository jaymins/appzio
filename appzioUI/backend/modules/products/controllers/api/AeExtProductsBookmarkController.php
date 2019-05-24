<?php

namespace backend\modules\products\controllers\api;

/**
* This is the class for REST controller "AeExtProductsBookmarkController".
*/

use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class AeExtProductsBookmarkController extends \yii\rest\ActiveController
{
public $modelClass = 'backend\modules\products\models\AeExtProductsBookmark';
}
