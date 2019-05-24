<?php

namespace backend\modules\fitness\models;

use Yii;
use \backend\modules\fitness\models\base\AeExtFitProgramCategory as BaseAeExtFitProgramCategory;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "ae_ext_fit_program_category".
 */
class AeExtFitProgramCategory extends BaseAeExtFitProgramCategory
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
