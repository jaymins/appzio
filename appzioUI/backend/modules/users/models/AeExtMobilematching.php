<?php

namespace backend\modules\users\models;

use Yii;
use \backend\modules\users\models\base\AeExtMobilematching as BaseAeExtMobilematching;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "ae_ext_mobilematching".
 */
class AeExtMobilematching extends BaseAeExtMobilematching
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
