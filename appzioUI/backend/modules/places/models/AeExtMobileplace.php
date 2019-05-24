<?php

namespace backend\modules\places\models;

use Yii;
use \backend\modules\places\models\base\AeExtMobileplace as BaseAeExtMobileplace;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "ae_ext_mobileplaces".
 */
class AeExtMobileplace extends BaseAeExtMobileplace
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
