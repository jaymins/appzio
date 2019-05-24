<?php

namespace backend\modules\golf\models\query;

/**
 * This is the ActiveQuery class for [[\backend\modules\golf\models\AeExtMobileevent]].
 *
 * @see \backend\modules\golf\models\AeExtMobileevent
 */
class AeExtMobileeventQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return \backend\modules\golf\models\AeExtMobileevent[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \backend\modules\golf\models\AeExtMobileevent|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
