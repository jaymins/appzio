<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace backend\modules\products\models\base;

use Yii;

/**
 * This is the base-model class for table "ae_ext_products_purchases".
 *
 * @property integer $id
 * @property integer $play_id
 * @property integer $product_id
 * @property integer $date
 * @property string $price
 * @property string $status
 * @property string $aliasModel
 */
abstract class AeExtProductsPurchase extends \backend\models\CrudBase
{



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ae_ext_products_purchases';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'play_id', 'product_id', 'date', 'price', 'status'], 'required'],
            [['id', 'play_id', 'product_id', 'date'], 'integer'],
            [['price'], 'number'],
            [['status'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('backend', 'ID'),
            'play_id' => Yii::t('backend', 'Play ID'),
            'product_id' => Yii::t('backend', 'Product ID'),
            'date' => Yii::t('backend', 'Date'),
            'price' => Yii::t('backend', 'Price'),
            'status' => Yii::t('backend', 'Status'),
        ];
    }


    
    /**
     * @inheritdoc
     * @return \backend\modules\products\models\query\AeExtProductsPurchaseQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \backend\modules\products\models\query\AeExtProductsPurchaseQuery(get_called_class());
    }


}
