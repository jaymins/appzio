<?php

namespace backend\modules\articles\models;

use Yii;
use \backend\modules\articles\models\base\AeExtArticle as BaseAeExtArticle;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "ae_ext_article".
 */
class AeExtArticle extends BaseAeExtArticle
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
                [['rating'], 'default', 'value' => '0'],
                [['featured'], 'default', 'value' => '0'],
                [['article_date'], 'default', 'value' => date('Y-m-d H:i:s')],
            ]
        );
    }
}