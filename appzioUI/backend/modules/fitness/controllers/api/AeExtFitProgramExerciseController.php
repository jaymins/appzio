<?php

namespace backend\modules\fitness\controllers\api;

/**
* This is the class for REST controller "AeExtFitProgramExerciseController".
*/

use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class AeExtFitProgramExerciseController extends \yii\rest\ActiveController
{
public $modelClass = 'backend\modules\fitness\models\AeExtFitProgramExercise';
}
