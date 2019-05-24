<?php

namespace backend\modules\articles\models;

use Yii;
use \backend\modules\articles\models\base\AeExtArticleTag as BaseAeExtArticleTag;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "ae_ext_article_tags".
 */
class AeExtArticleTag extends BaseAeExtArticleTag
{

    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                # custom behaviors
            ]
        );
    }

    public function rules()
    {
        return ArrayHelper::merge(
            parent::rules(),
            [
                # custom validation rules
            ]
        );
    }
}
