<?php

namespace backend\modules\fitness\controllers\api;

/**
* This is the class for REST controller "AeExtFoodIngredientController".
*/

use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class AeExtFoodIngredientController extends \yii\rest\ActiveController
{
public $modelClass = 'backend\modules\fitness\models\AeExtFoodIngredient';
}
