<?php

namespace backend\modules\users\models\query;

/**
 * This is the ActiveQuery class for [[\backend\modules\users\models\AeExtMobilematching]].
 *
 * @see \backend\modules\users\models\AeExtMobilematching
 */
class AeExtMobilematchingQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return \backend\modules\users\models\AeExtMobilematching[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \backend\modules\users\models\AeExtMobilematching|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
