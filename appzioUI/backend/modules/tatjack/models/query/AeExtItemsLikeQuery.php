<?php

namespace backend\modules\tatjack\models\query;

/**
 * This is the ActiveQuery class for [[\backend\modules\tatjack\models\AeExtItemsLike]].
 *
 * @see \backend\modules\tatjack\models\AeExtItemsLike
 */
class AeExtItemsLikeQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return \backend\modules\tatjack\models\AeExtItemsLike[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \backend\modules\tatjack\models\AeExtItemsLike|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
