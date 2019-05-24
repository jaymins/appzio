<?php

namespace backend\modules\tickers\models\query;

/**
 * This is the ActiveQuery class for [[\backend\modules\tickers\models\UsergroupsGroup]].
 *
 * @see \backend\modules\tickers\models\UsergroupsGroup
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
     * @return \backend\modules\tickers\models\UsergroupsGroup[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \backend\modules\tickers\models\UsergroupsGroup|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
