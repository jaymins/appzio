<?php

namespace backend\modules\articles\models\query;

/**
 * This is the ActiveQuery class for [[\backend\modules\articles\models\UsergroupsGroup]].
 *
 * @see \backend\modules\articles\models\UsergroupsGroup
 */
class UsergroupsGroupQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return \backend\modules\articles\models\UsergroupsGroup[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \backend\modules\articles\models\UsergroupsGroup|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
