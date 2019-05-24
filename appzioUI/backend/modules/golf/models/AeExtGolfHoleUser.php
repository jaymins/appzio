<?php

namespace backend\modules\golf\models;

use Yii;
use \backend\modules\golf\models\base\AeExtGolfHoleUser as BaseAeExtGolfHoleUser;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "ae_ext_golf_hole_user".
 */
class AeExtGolfHoleUser extends BaseAeExtGolfHoleUser
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
