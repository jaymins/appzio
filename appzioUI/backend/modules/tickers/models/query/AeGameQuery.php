<?php

namespace backend\modules\tickers\models\query;

/**
 * This is the ActiveQuery class for [[\backend\modules\tickers\models\AeGame]].
 *
 * @see \backend\modules\tickers\models\AeGame
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
     * @return \backend\modules\tickers\models\AeGame[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \backend\modules\tickers\models\AeGame|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
