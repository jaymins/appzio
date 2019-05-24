<?php

namespace backend\modules\items\models\query;

/**
 * This is the ActiveQuery class for [[\backend\modules\items\models\UsergroupsGroup]].
 *
 * @see \backend\modules\items\models\UsergroupsGroup
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
     * @return \backend\modules\items\models\UsergroupsGroup[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \backend\modules\items\models\UsergroupsGroup|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
