<?php

namespace backend\modules\items\models\query;

/**
 * This is the ActiveQuery class for [[\backend\modules\items\models\AeExtItem]].
 *
 * @see \backend\modules\items\models\AeExtItem
 */
class AeExtItemQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return \backend\modules\items\models\AeExtItem[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \backend\modules\items\models\AeExtItem|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
