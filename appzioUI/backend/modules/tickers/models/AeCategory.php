<?php

namespace backend\modules\tickers\models;

use Yii;
use \backend\modules\tickers\models\base\AeCategory as BaseAeCategory;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "ae_category".
 */
class AeCategory extends BaseAeCategory
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
