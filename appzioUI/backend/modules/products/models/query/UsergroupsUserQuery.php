<?php

namespace backend\modules\products\models\query;

/**
 * This is the ActiveQuery class for [[\backend\modules\products\models\UsergroupsUser]].
 *
 * @see \backend\modules\products\models\UsergroupsUser
 */
class UsergroupsUserQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return \backend\modules\products\models\UsergroupsUser[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \backend\modules\products\models\UsergroupsUser|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
