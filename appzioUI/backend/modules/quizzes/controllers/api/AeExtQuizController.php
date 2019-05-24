<?php

namespace backend\modules\quizzes\controllers\api;

/**
* This is the class for REST controller "AeExtQuizController".
*/

use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class AeExtQuizController extends \yii\rest\ActiveController
{
public $modelClass = 'backend\modules\quizzes\models\AeExtQuiz';
}
