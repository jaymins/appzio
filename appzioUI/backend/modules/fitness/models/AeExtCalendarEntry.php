<?php

namespace backend\modules\fitness\models;

use Yii;
use \backend\modules\fitness\models\base\AeExtCalendarEntry as BaseAeExtCalendarEntry;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "ae_ext_calendar_entry".
 */
class AeExtCalendarEntry extends BaseAeExtCalendarEntry
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
