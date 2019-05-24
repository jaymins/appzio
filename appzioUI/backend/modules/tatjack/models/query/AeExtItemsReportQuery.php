<?php

namespace backend\modules\tatjack\models\query;

/**
 * This is the ActiveQuery class for [[\backend\modules\tatjack\models\AeExtItemsReport]].
 *
 * @see \backend\modules\tatjack\models\AeExtItemsReport
 */
class AeExtItemsReportQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return \backend\modules\tatjack\models\AeExtItemsReport[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \backend\modules\tatjack\models\AeExtItemsReport|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
