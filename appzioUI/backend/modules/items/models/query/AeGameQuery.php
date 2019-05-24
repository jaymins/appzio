<?php

namespace backend\modules\items\models\query;

/**
 * This is the ActiveQuery class for [[\backend\modules\items\models\AeGame]].
 *
 * @see \backend\modules\items\models\AeGame
 */
class AeGameQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return \backend\modules\items\models\AeGame[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \backend\modules\items\models\AeGame|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
