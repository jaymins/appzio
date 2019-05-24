<?php

namespace backend\modules\products\models;

use Yii;
use \backend\modules\products\models\base\AeExtProductsReview as BaseAeExtProductsReview;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "ae_ext_products_reviews".
 */
class AeExtProductsReview extends BaseAeExtProductsReview
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
