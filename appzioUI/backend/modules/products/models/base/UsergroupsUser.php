<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace backend\modules\products\models\base;

use Yii;

/**
 * This is the base-model class for table "usergroups_user".
 *
 * @property integer $id
 * @property integer $group_id
 * @property string $language
 * @property string $username
 * @property string $password
 * @property string $email
 * @property string $firstname
 * @property string $lastname
 * @property string $home
 * @property integer $status
 * @property string $question
 * @property string $answer
 * @property string $creation_date
 * @property string $activation_code
 * @property string $activation_time
 * @property string $last_login
 * @property string $ban
 * @property string $ban_reason
 * @property string $developer_phone
 * @property string $developer_verification
 * @property integer $developer_karmapoints
 * @property string $alert
 * @property string $phone
 * @property string $timezone
 * @property string $twitter
 * @property string $skype
 * @property string $fbid
 * @property string $fbtoken
 * @property string $fbtoken_long
 * @property string $nickname
 * @property string $creator_api_key
 * @property integer $temp_user
 * @property string $source
 * @property integer $last_push
 * @property integer $play_id
 * @property string $laratoken
 * @property string $active_app_id
 * @property string $sftp_username
 * @property integer $terms_approved
 *
 * @property \backend\modules\products\models\AeAccessToken[] $aeAccessTokens
 * @property \backend\modules\products\models\AeChannelSettingUser[] $aeChannelSettingUsers
 * @property \backend\modules\products\models\AeChannelUser[] $aeChannelUsers
 * @property \backend\modules\products\models\AeChannel[] $idChannels
 * @property \backend\modules\products\models\AeExtBb[] $aeExtBbs
 * @property \backend\modules\products\models\AeExtDiary[] $aeExtDiaries
 * @property \backend\modules\products\models\AeFbfriendUser[] $aeFbfriendUsers
 * @property \backend\modules\products\models\AeFbfriend[] $aeFbfriends
 * @property \backend\modules\products\models\AeGame[] $aeGames
 * @property \backend\modules\products\models\AeGameBranchActionType[] $aeGameBranchActionTypes
 * @property \backend\modules\products\models\AeGamePlay[] $aeGamePlays
 * @property \backend\modules\products\models\AeGamePlayAction[] $aeGamePlayActions
 * @property \backend\modules\products\models\AeGamePlayUser[] $aeGamePlayUsers
 * @property \backend\modules\products\models\AeGameRate[] $aeGameRates
 * @property \backend\modules\products\models\AeGameTrigger[] $aeGameTriggers
 * @property \backend\modules\products\models\SshKey[] $sshKeys
 * @property string $aliasModel
 */
abstract class UsergroupsUser extends \backend\models\CrudBase
{



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'usergroups_user';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['group_id', 'status', 'developer_karmapoints', 'temp_user', 'last_push', 'play_id', 'terms_approved'], 'integer'],
            [['username', 'email', 'firstname', 'lastname', 'developer_phone', 'developer_verification', 'alert', 'phone', 'timezone', 'twitter', 'skype', 'fbid', 'fbtoken', 'fbtoken_long', 'nickname', 'creator_api_key', 'temp_user', 'source', 'last_push', 'play_id', 'laratoken', 'active_app_id', 'sftp_username', 'terms_approved'], 'required'],
            [['question', 'answer', 'ban_reason', 'alert', 'phone'], 'string'],
            [['creation_date', 'activation_time', 'last_login', 'ban'], 'safe'],
            [['language'], 'string', 'max' => 2],
            [['username', 'password', 'email', 'home'], 'string', 'max' => 120],
            [['firstname', 'lastname', 'developer_phone', 'developer_verification', 'twitter', 'skype', 'fbid', 'fbtoken', 'fbtoken_long', 'nickname', 'source', 'laratoken', 'active_app_id', 'sftp_username'], 'string', 'max' => 255],
            [['activation_code'], 'string', 'max' => 30],
            [['timezone'], 'string', 'max' => 10],
            [['creator_api_key'], 'string', 'max' => 16],
            [['username'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('backend', 'ID'),
            'group_id' => Yii::t('backend', 'Group ID'),
            'language' => Yii::t('backend', 'Language'),
            'username' => Yii::t('backend', 'Username'),
            'password' => Yii::t('backend', 'Password'),
            'email' => Yii::t('backend', 'Email'),
            'firstname' => Yii::t('backend', 'Firstname'),
            'lastname' => Yii::t('backend', 'Lastname'),
            'home' => Yii::t('backend', 'Home'),
            'status' => Yii::t('backend', 'Status'),
            'question' => Yii::t('backend', 'Question'),
            'answer' => Yii::t('backend', 'Answer'),
            'creation_date' => Yii::t('backend', 'Creation Date'),
            'activation_code' => Yii::t('backend', 'Activation Code'),
            'activation_time' => Yii::t('backend', 'Activation Time'),
            'last_login' => Yii::t('backend', 'Last Login'),
            'ban' => Yii::t('backend', 'Ban'),
            'ban_reason' => Yii::t('backend', 'Ban Reason'),
            'developer_phone' => Yii::t('backend', 'Developer Phone'),
            'developer_verification' => Yii::t('backend', 'Developer Verification'),
            'developer_karmapoints' => Yii::t('backend', 'Developer Karmapoints'),
            'alert' => Yii::t('backend', 'Alert'),
            'phone' => Yii::t('backend', 'Phone'),
            'timezone' => Yii::t('backend', 'Timezone'),
            'twitter' => Yii::t('backend', 'Twitter'),
            'skype' => Yii::t('backend', 'Skype'),
            'fbid' => Yii::t('backend', 'Fbid'),
            'fbtoken' => Yii::t('backend', 'Fbtoken'),
            'fbtoken_long' => Yii::t('backend', 'Fbtoken Long'),
            'nickname' => Yii::t('backend', 'Nickname'),
            'creator_api_key' => Yii::t('backend', 'Creator Api Key'),
            'temp_user' => Yii::t('backend', 'Temp User'),
            'source' => Yii::t('backend', 'Source'),
            'last_push' => Yii::t('backend', 'Last Push'),
            'play_id' => Yii::t('backend', 'Play ID'),
            'laratoken' => Yii::t('backend', 'Laratoken'),
            'active_app_id' => Yii::t('backend', 'Active App ID'),
            'sftp_username' => Yii::t('backend', 'Sftp Username'),
            'terms_approved' => Yii::t('backend', 'Terms Approved'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeAccessTokens()
    {
        return $this->hasMany(\backend\modules\products\models\AeAccessToken::className(), ['id_user' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeChannelSettingUsers()
    {
        return $this->hasMany(\backend\modules\products\models\AeChannelSettingUser::className(), ['id_user' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeChannelUsers()
    {
        return $this->hasMany(\backend\modules\products\models\AeChannelUser::className(), ['id_user' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdChannels()
    {
        return $this->hasMany(\backend\modules\products\models\AeChannel::className(), ['id' => 'id_channel'])->viaTable('ae_channel_user', ['id_user' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeExtBbs()
    {
        return $this->hasMany(\backend\modules\products\models\AeExtBb::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeExtDiaries()
    {
        return $this->hasMany(\backend\modules\products\models\AeExtDiary::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeFbfriendUsers()
    {
        return $this->hasMany(\backend\modules\products\models\AeFbfriendUser::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeFbfriends()
    {
        return $this->hasMany(\backend\modules\products\models\AeFbfriend::className(), ['id' => 'ae_fbfriend_id'])->viaTable('ae_fbfriend_user', ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeGames()
    {
        return $this->hasMany(\backend\modules\products\models\AeGame::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeGameBranchActionTypes()
    {
        return $this->hasMany(\backend\modules\products\models\AeGameBranchActionType::className(), ['id_user' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeGamePlays()
    {
        return $this->hasMany(\backend\modules\products\models\AeGamePlay::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeGamePlayActions()
    {
        return $this->hasMany(\backend\modules\products\models\AeGamePlayAction::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeGamePlayUsers()
    {
        return $this->hasMany(\backend\modules\products\models\AeGamePlayUser::className(), ['id_user' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeGameRates()
    {
        return $this->hasMany(\backend\modules\products\models\AeGameRate::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeGameTriggers()
    {
        return $this->hasMany(\backend\modules\products\models\AeGameTrigger::className(), ['id_user' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSshKeys()
    {
        return $this->hasMany(\backend\modules\products\models\SshKey::className(), ['user_id' => 'id']);
    }


    
    /**
     * @inheritdoc
     * @return \backend\modules\products\models\query\UsergroupsUserQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \backend\modules\products\models\query\UsergroupsUserQuery(get_called_class());
    }


}
