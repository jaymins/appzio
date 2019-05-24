<?php

namespace backend\modules\tatjack\models;

use Yii;
use \backend\modules\tatjack\models\base\AeExtUserBidItemImage as BaseAeExtUserBidItemImage;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "ae_ext_user_bid_item_images".
 */
class AeExtUserBidItemImage extends BaseAeExtUserBidItemImage
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
