<?php

namespace backend\modules\tickers\models\query;

/**
 * This is the ActiveQuery class for [[\backend\modules\tickers\models\AeExtTickerDatum]].
 *
 * @see \backend\modules\tickers\models\AeExtTickerDatum
 */
class AeExtTickerDatumQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return \backend\modules\tickers\models\AeExtTickerDatum[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \backend\modules\tickers\models\AeExtTickerDatum|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
