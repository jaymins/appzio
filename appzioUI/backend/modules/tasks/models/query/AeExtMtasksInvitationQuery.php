<?php

namespace backend\modules\tasks\models\query;

/**
 * This is the ActiveQuery class for [[\backend\modules\tasks\models\AeExtMtasksInvitation]].
 *
 * @see \backend\modules\tasks\models\AeExtMtasksInvitation
 */
class AeExtMtasksInvitationQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return \backend\modules\tasks\models\AeExtMtasksInvitation[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \backend\modules\tasks\models\AeExtMtasksInvitation|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
