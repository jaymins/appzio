<?php

namespace backend\modules\users\models;

use Yii;
use \backend\modules\users\models\base\AeRole as BaseAeRole;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "ae_role".
 */
class AeRole extends BaseAeRole
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
