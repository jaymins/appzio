<?php

namespace backend\modules\quizzes\models;

use Yii;
use \backend\modules\quizzes\models\base\AeExtQuizQuestion as BaseAeExtQuizQuestion;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "ae_ext_quiz_question".
 */
class AeExtQuizQuestion extends BaseAeExtQuizQuestion
{

    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                # custom behaviors
            ]
        );
    }

    public function rules()
    {
        return ArrayHelper::merge(
            parent::rules(),
            [
                # custom validation rules
            ]
        );
    }
}
