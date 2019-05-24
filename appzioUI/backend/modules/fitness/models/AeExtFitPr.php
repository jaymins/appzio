<?php

namespace backend\modules\fitness\models;

use Yii;
use \backend\modules\fitness\models\base\AeExtFitPr as BaseAeExtFitPr;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "ae_ext_fit_pr".
 */
class AeExtFitPr extends BaseAeExtFitPr
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
    public static function getAllPr()
    {
        $pr = AeExtFitPr::find()
            ->asArray() // optional
            ->all();

        if ($pr) {
            return $pr;
        }

        return [];
    }
}
