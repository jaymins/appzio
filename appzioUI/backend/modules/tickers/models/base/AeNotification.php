<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace backend\modules\tickers\models\base;

use Yii;

/**
 * This is the base-model class for table "ae_notification".
 *
 * @property string $id
 * @property string $id_user
 * @property string $id_channel
 * @property string $id_playaction
 * @property string $app_id
 * @property string $onesignal_msgid
 * @property string $action_id
 * @property string $play_id
 * @property string $menu_id
 * @property string $menuid
 * @property integer $shown_in_app
 * @property integer $read_in_app
 * @property string $type
 * @property string $subject
 * @property string $message
 * @property string $email_to
 * @property string $parameters
 * @property string $badge_count
 * @property string $manual_config
 * @property integer $sendtime
 * @property string $created
 * @property string $updated
 * @property integer $repeated
 * @property integer $lastsent
 * @property integer $expired
 * @property string $debug
 * @property integer $os_success
 * @property integer $os_failed
 * @property integer $os_converted
 *
 * @property \backend\modules\tickers\models\AeExtNotification[] $aeExtNotifications
 * @property \backend\modules\tickers\models\AeExtTickerNotification[] $aeExtTickerNotifications
 * @property \backend\modules\tickers\models\AeGamePlay $play
 * @property string $aliasModel
 */
abstract class AeNotification extends \backend\models\CrudBase
{



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ae_notification';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_user', 'id_channel', 'id_playaction', 'app_id', 'onesignal_msgid', 'action_id', 'play_id', 'menuid', 'subject', 'message', 'email_to', 'parameters', 'badge_count', 'manual_config', 'sendtime', 'lastsent', 'debug'], 'required'],
            [['id_user', 'id_channel', 'id_playaction', 'app_id', 'action_id', 'play_id', 'shown_in_app', 'read_in_app', 'badge_count', 'sendtime', 'repeated', 'lastsent', 'expired', 'os_success', 'os_failed', 'os_converted'], 'integer'],
            [['subject', 'message', 'manual_config', 'debug'], 'string'],
            [['created', 'updated'], 'safe'],
            [['onesignal_msgid', 'menu_id', 'menuid', 'email_to', 'parameters'], 'string', 'max' => 255],
            [['type'], 'string', 'max' => 20],
            [['play_id'], 'exist', 'skipOnError' => true, 'targetClass' => \backend\modules\tickers\models\AeGamePlay::className(), 'targetAttribute' => ['play_id' => 'id']]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('backend', 'ID'),
            'id_user' => Yii::t('backend', 'Id User'),
            'id_channel' => Yii::t('backend', 'Id Channel'),
            'id_playaction' => Yii::t('backend', 'Id Playaction'),
            'app_id' => Yii::t('backend', 'App ID'),
            'onesignal_msgid' => Yii::t('backend', 'Onesignal Msgid'),
            'action_id' => Yii::t('backend', 'Action ID'),
            'play_id' => Yii::t('backend', 'Play ID'),
            'menu_id' => Yii::t('backend', 'Menu ID'),
            'menuid' => Yii::t('backend', 'Menuid'),
            'shown_in_app' => Yii::t('backend', 'Shown In App'),
            'read_in_app' => Yii::t('backend', 'Read In App'),
            'type' => Yii::t('backend', 'Type'),
            'subject' => Yii::t('backend', 'Subject'),
            'message' => Yii::t('backend', 'Message'),
            'email_to' => Yii::t('backend', 'Email To'),
            'parameters' => Yii::t('backend', 'Parameters'),
            'badge_count' => Yii::t('backend', 'Badge Count'),
            'manual_config' => Yii::t('backend', 'Manual Config'),
            'sendtime' => Yii::t('backend', 'Sendtime'),
            'created' => Yii::t('backend', 'Created'),
            'updated' => Yii::t('backend', 'Updated'),
            'repeated' => Yii::t('backend', 'Repeated'),
            'lastsent' => Yii::t('backend', 'Lastsent'),
            'expired' => Yii::t('backend', 'Expired'),
            'debug' => Yii::t('backend', 'Debug'),
            'os_success' => Yii::t('backend', 'Os Success'),
            'os_failed' => Yii::t('backend', 'Os Failed'),
            'os_converted' => Yii::t('backend', 'Os Converted'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeExtNotifications()
    {
        return $this->hasMany(\backend\modules\tickers\models\AeExtNotification::className(), ['notification_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeExtTickerNotifications()
    {
        return $this->hasMany(\backend\modules\tickers\models\AeExtTickerNotification::className(), ['notification_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlay()
    {
        return $this->hasOne(\backend\modules\tickers\models\AeGamePlay::className(), ['id' => 'play_id']);
    }


    
    /**
     * @inheritdoc
     * @return \backend\modules\tickers\models\query\AeNotificationQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \backend\modules\tickers\models\query\AeNotificationQuery(get_called_class());
    }


}
