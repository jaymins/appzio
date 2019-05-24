<?php

namespace backend\modules\fitness\controllers\api;

/**
* This is the class for REST controller "AeExtFitExerciseComponentController".
*/

use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class AeExtFitExerciseComponentController extends \yii\rest\ActiveController
{
public $modelClass = 'backend\modules\fitness\models\AeExtFitExerciseComponent';
}
