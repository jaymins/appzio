<?php

namespace backend\modules\products\models\query;

/**
 * This is the ActiveQuery class for [[\backend\modules\products\models\AeExtProductsPhoto]].
 *
 * @see \backend\modules\products\models\AeExtProductsPhoto
 */
class AeExtProductsPhotoQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return \backend\modules\products\models\AeExtProductsPhoto[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \backend\modules\products\models\AeExtProductsPhoto|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
