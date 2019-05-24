<?php

namespace backend\modules\tatjack\models;

use Yii;
use \backend\modules\tatjack\models\base\AeExtItemsTagItem as BaseAeExtItemsTagItem;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "ae_ext_items_tag_item".
 */
class AeExtItemsTagItem extends BaseAeExtItemsTagItem
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
