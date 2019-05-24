<?php

namespace backend\modules\registration\models;

use Yii;
use \backend\modules\registration\models\base\AeExtMregisterCompany as BaseAeExtMregisterCompany;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "ae_ext_mregister_companies".
 */
class AeExtMregisterCompany extends BaseAeExtMregisterCompany
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
