<?php

namespace backend\modules\places\models;

use Yii;
use \backend\modules\places\models\base\AeExtClubNews as BaseAeExtClubNews;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "ae_ext_club_news".
 */
class AeExtClubNews extends BaseAeExtClubNews
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
