<?php

namespace backend\modules\quizzes\models;

use Yii;
use \backend\modules\quizzes\models\base\AeExtQuizQuestionAnswer as BaseAeExtQuizQuestionAnswer;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "ae_ext_quiz_question_answer".
 *
 * @property \backend\modules\quizzes\models\AeExtQuizQuestion $question
 * @property \backend\modules\quizzes\models\AeExtQuizQuestionOption $answer0
 */
class AeExtQuizQuestionAnswer extends BaseAeExtQuizQuestionAnswer
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
                [['question'], 'exist', 'skipOnError' => true, 'targetClass' => \backend\modules\quizzes\models\AeExtQuizQuestion::className(), 'targetAttribute' => ['question_id' => 'id']]
            ]
        );
    }




}
