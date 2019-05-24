<?php

namespace backend\modules\products\models;

use Yii;
use \backend\modules\products\models\base\AeExtProductsTag as BaseAeExtProductsTag;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "ae_ext_products_tags".
 */
class AeExtProductsTag extends BaseAeExtProductsTag
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
