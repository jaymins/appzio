<?php

namespace backend\modules\tasks\models;

use Yii;
use \backend\modules\tasks\models\base\AeExtMtasksInvitation as BaseAeExtMtasksInvitation;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "ae_ext_mtasks_invitations".
 */
class AeExtMtasksInvitation extends BaseAeExtMtasksInvitation
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
