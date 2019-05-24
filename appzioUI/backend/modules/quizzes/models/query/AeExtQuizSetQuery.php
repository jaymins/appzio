<?php

namespace backend\modules\quizzes\models\query;

/**
 * This is the ActiveQuery class for [[\backend\modules\quizzes\models\AeExtQuizSet]].
 *
 * @see \backend\modules\quizzes\models\AeExtQuizSet
 */
class AeExtQuizSetQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return \backend\modules\quizzes\models\AeExtQuizSet[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \backend\modules\quizzes\models\AeExtQuizSet|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
