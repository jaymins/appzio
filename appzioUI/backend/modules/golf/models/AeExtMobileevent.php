<?php

namespace backend\modules\golf\models;

use Yii;
use \backend\modules\golf\models\base\AeExtMobileevent as BaseAeExtMobileevent;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "ae_ext_mobileevents".
 */
class AeExtMobileevent extends BaseAeExtMobileevent
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
