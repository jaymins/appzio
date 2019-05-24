<?php

namespace backend\modules\quizzes\controllers\api;

/**
* This is the class for REST controller "AeExtQuizQuestionOptionController".
*/

use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class AeExtQuizQuestionOptionController extends \yii\rest\ActiveController
{
public $modelClass = 'backend\modules\quizzes\models\AeExtQuizQuestionOption';
}
