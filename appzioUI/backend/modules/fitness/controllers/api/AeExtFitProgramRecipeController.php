<?php

namespace backend\modules\fitness\controllers\api;

/**
* This is the class for REST controller "AeExtFitProgramRecipeController".
*/

use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class AeExtFitProgramRecipeController extends \yii\rest\ActiveController
{
public $modelClass = 'backend\modules\fitness\models\AeExtFitProgramRecipe';
}
