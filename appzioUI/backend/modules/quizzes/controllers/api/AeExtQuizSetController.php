<?php

namespace backend\modules\quizzes\controllers\api;

/**
* This is the class for REST controller "AeExtQuizSetController".
*/

use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class AeExtQuizSetController extends \yii\rest\ActiveController
{
public $modelClass = 'backend\modules\quizzes\models\AeExtQuizSet';
}
