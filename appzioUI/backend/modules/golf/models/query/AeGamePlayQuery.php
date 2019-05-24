<?php

namespace backend\modules\golf\models\query;

/**
 * This is the ActiveQuery class for [[\backend\modules\golf\models\AeGamePlay]].
 *
 * @see \backend\modules\golf\models\AeGamePlay
 */
class AeGamePlayQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return \backend\modules\golf\models\AeGamePlay[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \backend\modules\golf\models\AeGamePlay|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
