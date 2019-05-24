<?php

namespace backend\modules\tasks\models\query;

/**
 * This is the ActiveQuery class for [[\backend\modules\tasks\models\AeExtMtasksProof]].
 *
 * @see \backend\modules\tasks\models\AeExtMtasksProof
 */
class AeExtMtasksProofQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return \backend\modules\tasks\models\AeExtMtasksProof[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \backend\modules\tasks\models\AeExtMtasksProof|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
