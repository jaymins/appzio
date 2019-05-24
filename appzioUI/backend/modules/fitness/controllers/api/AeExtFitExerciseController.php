<?php

namespace backend\modules\fitness\controllers\api;

/**
* This is the class for REST controller "AeExtFitExerciseController".
*/

use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class AeExtFitExerciseController extends \yii\rest\ActiveController
{
public $modelClass = 'backend\modules\fitness\models\AeExtFitExercise';
}
