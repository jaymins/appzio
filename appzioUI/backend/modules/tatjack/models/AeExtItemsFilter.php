<?php

namespace backend\modules\tatjack\models;

use Yii;
use \backend\modules\tatjack\models\base\AeExtItemsFilter as BaseAeExtItemsFilter;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "ae_ext_items_filters".
 */
class AeExtItemsFilter extends BaseAeExtItemsFilter
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
