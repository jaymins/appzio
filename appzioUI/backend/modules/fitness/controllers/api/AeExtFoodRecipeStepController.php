<?php

namespace backend\modules\fitness\controllers\api;

/**
* This is the class for REST controller "AeExtFoodRecipeStepController".
*/

use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class AeExtFoodRecipeStepController extends \yii\rest\ActiveController
{
public $modelClass = 'backend\modules\fitness\models\AeExtFoodRecipeStep';
}
