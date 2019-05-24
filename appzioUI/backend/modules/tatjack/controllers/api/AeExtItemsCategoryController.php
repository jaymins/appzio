<?php

namespace backend\modules\tatjack\controllers\api;

/**
* This is the class for REST controller "AeExtItemsCategoryController".
*/

use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class AeExtItemsCategoryController extends \yii\rest\ActiveController
{
public $modelClass = 'backend\modules\tatjack\models\AeExtItemsCategory';
}
