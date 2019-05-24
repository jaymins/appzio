<?php

namespace backend\modules\tatjack\models;

use Yii;
use \backend\modules\tatjack\models\base\AeExtBooking as BaseAeExtBooking;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "ae_ext_bookings".
 */
class AeExtBooking extends BaseAeExtBooking
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
