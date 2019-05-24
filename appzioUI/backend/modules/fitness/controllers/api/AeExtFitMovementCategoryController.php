<?php

namespace backend\modules\fitness\controllers\api;

/**
* This is the class for REST controller "AeExtFitMovementCategoryController".
*/

use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class AeExtFitMovementCategoryController extends \yii\rest\ActiveController
{
public $modelClass = 'backend\modules\fitness\models\AeExtFitMovementCategory';
}
