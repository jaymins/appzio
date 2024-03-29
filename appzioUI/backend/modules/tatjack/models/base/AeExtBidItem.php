<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace backend\modules\tatjack\models\base;

use Yii;

/**
 * This is the base-model class for table "ae_ext_bid_items".
 *
 * @property string $id
 * @property string $play_id
 * @property string $title
 * @property string $description
 * @property string $styles
 * @property string $valid_date
 * @property string $status
 * @property string $lat
 * @property string $lon
 *
 * @property \backend\modules\tatjack\models\AeGamePlay $play
 * @property \backend\modules\tatjack\models\AeExtUserBidItemImage[] $aeExtUserBidItemImages
 * @property \backend\modules\tatjack\models\AeExtUserBid[] $aeExtUserBs
 * @property string $aliasModel
 */
abstract class AeExtBidItem extends \backend\models\CrudBase
{



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ae_ext_bid_items';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['play_id', 'title', 'description', 'valid_date', 'lat', 'lon'], 'required'],
            [['play_id', 'valid_date'], 'integer'],
            [['styles'], 'string'],
            [['lat', 'lon'], 'number'],
            [['title'], 'string', 'max' => 50],
            [['description'], 'string', 'max' => 255],
            [['status'], 'string', 'max' => 10],
            [['play_id'], 'exist', 'skipOnError' => true, 'targetClass' => \backend\modules\tatjack\models\AeGamePlay::className(), 'targetAttribute' => ['play_id' => 'id']]
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
            'title' => Yii::t('backend', 'Title'),
            'description' => Yii::t('backend', 'Description'),
            'styles' => Yii::t('backend', 'Styles'),
            'valid_date' => Yii::t('backend', 'Valid Date'),
            'status' => Yii::t('backend', 'Status'),
            'lat' => Yii::t('backend', 'Lat'),
            'lon' => Yii::t('backend', 'Lon'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlay()
    {
        return $this->hasOne(\backend\modules\tatjack\models\AeGamePlay::className(), ['id' => 'play_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeExtUserBidItemImages()
    {
        return $this->hasMany(\backend\modules\tatjack\models\AeExtUserBidItemImage::className(), ['bid_item_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeExtUserBs()
    {
        return $this->hasMany(\backend\modules\tatjack\models\AeExtUserBid::className(), ['bid_item_id' => 'id']);
    }


    
    /**
     * @inheritdoc
     * @return \backend\modules\tatjack\models\query\AeExtBidItemQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \backend\modules\tatjack\models\query\AeExtBidItemQuery(get_called_class());
    }


}
