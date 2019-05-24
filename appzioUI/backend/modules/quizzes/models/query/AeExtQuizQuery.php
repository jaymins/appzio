<?php

namespace backend\modules\quizzes\models\query;

/**
 * This is the ActiveQuery class for [[\backend\modules\quizzes\models\AeExtQuiz]].
 *
 * @see \backend\modules\quizzes\models\AeExtQuiz
 */
class AeExtQuizQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return \backend\modules\quizzes\models\AeExtQuiz[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \backend\modules\quizzes\models\AeExtQuiz|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
