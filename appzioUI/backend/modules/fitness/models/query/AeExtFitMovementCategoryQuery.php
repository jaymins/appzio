<?php

namespace backend\modules\fitness\models\query;

/**
 * This is the ActiveQuery class for [[\backend\modules\fitness\models\AeExtFitMovementCategory]].
 *
 * @see \backend\modules\fitness\models\AeExtFitMovementCategory
 */
class AeExtFitMovementCategoryQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return \backend\modules\fitness\models\AeExtFitMovementCategory[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \backend\modules\fitness\models\AeExtFitMovementCategory|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
