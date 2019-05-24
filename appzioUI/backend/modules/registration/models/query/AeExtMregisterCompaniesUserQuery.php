<?php

namespace backend\modules\registration\models\query;

/**
 * This is the ActiveQuery class for [[\backend\modules\registration\models\AeExtMregisterCompaniesUser]].
 *
 * @see \backend\modules\registration\models\AeExtMregisterCompaniesUser
 */
class AeExtMregisterCompaniesUserQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return \backend\modules\registration\models\AeExtMregisterCompaniesUser[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \backend\modules\registration\models\AeExtMregisterCompaniesUser|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
