<?php

namespace backend\modules\fitness\controllers\api;

/**
* This is the class for REST controller "AeExtFoodIngredientCategoryController".
*/

use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class AeExtFoodIngredientCategoryController extends \yii\rest\ActiveController
{
public $modelClass = 'backend\modules\fitness\models\AeExtFoodIngredientCategory';
}
