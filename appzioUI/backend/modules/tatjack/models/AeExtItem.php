<?php

namespace backend\modules\tatjack\models;

use Yii;
use \backend\modules\tatjack\models\base\AeExtItem as BaseAeExtItem;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "ae_ext_items".
 */
class AeExtItem extends BaseAeExtItem
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
