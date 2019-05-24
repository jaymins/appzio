<?php

namespace backend\modules\quizzes\controllers\api;

/**
* This is the class for REST controller "AeExtQuizQuestionController".
*/

use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class AeExtQuizQuestionController extends \yii\rest\ActiveController
{
public $modelClass = 'backend\modules\quizzes\models\AeExtQuizQuestion';
}
