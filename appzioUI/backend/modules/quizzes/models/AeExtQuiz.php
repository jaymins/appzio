<?php

namespace backend\modules\quizzes\models;

use backend\modules\quizzes\models\base\AeExtQuiz as BaseAeExtQuiz;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "ae_ext_quiz".
 */
class AeExtQuiz extends BaseAeExtQuiz
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
                [['valid_from'], 'default', 'value' => '0'],
                [['valid_to'], 'default', 'value' => '0'],
                [['active'], 'default', 'value' => '1'],
                [['show_in_list'], 'default', 'value' => '1'],
            ]
        );
    }
}
