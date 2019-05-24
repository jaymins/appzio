<?php

namespace backend\modules\fitness\models;

use Yii;
use \backend\modules\fitness\models\base\AeExtPurchaseProduct as BaseAeExtPurchaseProduct;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "ae_ext_purchase_product".
 */
class AeExtPurchaseProduct extends BaseAeExtPurchaseProduct
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
        return
            [
                [['app_id', 'code','name', 'type', 'price', 'code_ios', 'code_android','currency'], 'required'],
                [['app_id'], 'integer'],
                [['price'], 'string'],
                [['description'], 'string'],
                [['name', 'type', 'code_ios', 'code_android', 'image', 'icon'], 'string', 'max' => 255],
                [['app_id'], 'exist', 'skipOnError' => true, 'targetClass' => \backend\modules\fitness\models\AeGame::className(), 'targetAttribute' => ['app_id' => 'id']]
            ];

    }
}
