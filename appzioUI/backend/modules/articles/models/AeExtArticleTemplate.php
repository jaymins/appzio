<?php

namespace backend\modules\articles\models;

use Yii;
use \backend\modules\articles\models\base\AeExtArticleTemplate as BaseAeExtArticleTemplate;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "ae_ext_article_templates".
 */
class AeExtArticleTemplate extends BaseAeExtArticleTemplate
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
