<?php

namespace backend\modules\fitness\controllers\api;

/**
* This is the class for REST controller "AeExtFoodRecipeTypeController".
*/

use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class AeExtFoodRecipeTypeController extends \yii\rest\ActiveController
{
public $modelClass = 'backend\modules\fitness\models\AeExtFoodRecipeType';
}
