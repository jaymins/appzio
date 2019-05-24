<?php

namespace backend\modules\places\models\query;

/**
 * This is the ActiveQuery class for [[\backend\modules\places\models\AeExtMobileplace]].
 *
 * @see \backend\modules\places\models\AeExtMobileplace
 */
class AeExtMobileplaceQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return \backend\modules\places\models\AeExtMobileplace[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \backend\modules\places\models\AeExtMobileplace|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
