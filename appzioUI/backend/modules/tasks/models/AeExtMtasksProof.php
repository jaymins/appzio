<?php

namespace backend\modules\tasks\models;

use Yii;
use \backend\modules\tasks\models\base\AeExtMtasksProof as BaseAeExtMtasksProof;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "ae_ext_mtasks_proof".
 */
class AeExtMtasksProof extends BaseAeExtMtasksProof
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
