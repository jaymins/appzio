<?php

namespace backend\modules\fitness\models;

use Yii;
use \backend\modules\fitness\models\base\AeExtFitProgramSubcategory as BaseAeExtFitProgramSubcategory;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "ae_ext_fit_program_subcategory".
 */
class AeExtFitProgramSubcategory extends BaseAeExtFitProgramSubcategory
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
