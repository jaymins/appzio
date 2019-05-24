<?php

namespace backend\modules\fitness\controllers\api;

/**
* This is the class for REST controller "AeExtFitProgramSubcategoryController".
*/

use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class AeExtFitProgramSubcategoryController extends \yii\rest\ActiveController
{
public $modelClass = 'backend\modules\fitness\models\AeExtFitProgramSubcategory';
}
