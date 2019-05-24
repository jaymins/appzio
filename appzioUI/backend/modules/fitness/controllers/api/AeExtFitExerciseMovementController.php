<?php

namespace backend\modules\fitness\controllers\api;

/**
* This is the class for REST controller "AeExtFitExerciseMovementController".
*/

use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class AeExtFitExerciseMovementController extends \yii\rest\ActiveController
{
public $modelClass = 'backend\modules\fitness\models\AeExtFitExerciseMovement';
}
