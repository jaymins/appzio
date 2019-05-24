<?php

namespace backend\modules\places\models\query;

/**
 * This is the ActiveQuery class for [[\backend\modules\places\models\AeCategory]].
 *
 * @see \backend\modules\places\models\AeCategory
 */
class AeCategoryQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return \backend\modules\places\models\AeCategory[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \backend\modules\places\models\AeCategory|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
