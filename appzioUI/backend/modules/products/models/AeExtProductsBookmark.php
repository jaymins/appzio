<?php

namespace backend\modules\products\models;

use Yii;
use \backend\modules\products\models\base\AeExtProductsBookmark as BaseAeExtProductsBookmark;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "ae_ext_products_bookmarks".
 */
class AeExtProductsBookmark extends BaseAeExtProductsBookmark
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
