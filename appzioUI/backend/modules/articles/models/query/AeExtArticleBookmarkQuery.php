<?php

namespace backend\modules\articles\models\query;

/**
 * This is the ActiveQuery class for [[\backend\modules\articles\models\AeExtArticleBookmark]].
 *
 * @see \backend\modules\articles\models\AeExtArticleBookmark
 */
class AeExtArticleBookmarkQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return \backend\modules\articles\models\AeExtArticleBookmark[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \backend\modules\articles\models\AeExtArticleBookmark|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
