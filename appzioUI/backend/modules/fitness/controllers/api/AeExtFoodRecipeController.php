<?php

namespace backend\modules\fitness\controllers\api;

/**
* This is the class for REST controller "AeExtFoodRecipeController".
*/

use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class AeExtFoodRecipeController extends \yii\rest\ActiveController
{
public $modelClass = 'backend\modules\fitness\models\AeExtFoodRecipe';
}
