<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace backend\modules\tickers\models\base;

use Yii;

/**
 * This is the base-model class for table "ae_ext_ticker_notifications".
 *
 * @property integer $id
 * @property string $trade_id
 * @property string $play_id
 * @property string $notification_id
 * @property integer $date
 * @property integer $type
 * @property integer $status
 *
 * @property \backend\modules\tickers\models\AeNotification $notification
 * @property \backend\modules\tickers\models\AeGamePlay $play
 * @property \backend\modules\tickers\models\AeExtTickerTrade $trade
 * @property string $aliasModel
 */
abstract class AeExtTickerNotification extends \backend\models\CrudBase
{



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ae_ext_ticker_notifications';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['trade_id', 'play_id', 'notification_id', 'date'], 'required'],
            [['trade_id', 'play_id', 'notification_id', 'date', 'type', 'status'], 'integer'],
            [['notification_id'], 'exist', 'skipOnError' => true, 'targetClass' => \backend\modules\tickers\models\AeNotification::className(), 'targetAttribute' => ['notification_id' => 'id']],
            [['play_id'], 'exist', 'skipOnError' => true, 'targetClass' => \backend\modules\tickers\models\AeGamePlay::className(), 'targetAttribute' => ['play_id' => 'id']],
            [['trade_id'], 'exist', 'skipOnError' => true, 'targetClass' => \backend\modules\tickers\models\AeExtTickerTrade::className(), 'targetAttribute' => ['trade_id' => 'id']]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('backend', 'ID'),
            'trade_id' => Yii::t('backend', 'Trade ID'),
            'play_id' => Yii::t('backend', 'Play ID'),
            'notification_id' => Yii::t('backend', 'Notification ID'),
            'date' => Yii::t('backend', 'Date'),
            'type' => Yii::t('backend', 'Type'),
            'status' => Yii::t('backend', 'Status'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNotification()
    {
        return $this->hasOne(\backend\modules\tickers\models\AeNotification::className(), ['id' => 'notification_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlay()
    {
        return $this->hasOne(\backend\modules\tickers\models\AeGamePlay::className(), ['id' => 'play_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrade()
    {
        return $this->hasOne(\backend\modules\tickers\models\AeExtTickerTrade::className(), ['id' => 'trade_id']);
    }


    
    /**
     * @inheritdoc
     * @return \backend\modules\tickers\models\query\AeExtTickerNotificationQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \backend\modules\tickers\models\query\AeExtTickerNotificationQuery(get_called_class());
    }


}
