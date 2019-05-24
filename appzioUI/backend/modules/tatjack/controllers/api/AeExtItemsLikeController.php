<?php

namespace backend\modules\tatjack\controllers\api;

/**
* This is the class for REST controller "AeExtItemsLikeController".
*/

use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class AeExtItemsLikeController extends \yii\rest\ActiveController
{
public $modelClass = 'backend\modules\tatjack\models\AeExtItemsLike';
}
