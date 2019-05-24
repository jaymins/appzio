<?php

namespace backend\modules\products\controllers\api;

/**
* This is the class for REST controller "AeExtProductsReviewController".
*/

use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class AeExtProductsReviewController extends \yii\rest\ActiveController
{
public $modelClass = 'backend\modules\products\models\AeExtProductsReview';
}
