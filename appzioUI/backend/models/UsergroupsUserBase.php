<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "usergroups_user".
 */
class UsergroupsUserBase extends \yii\db\ActiveRecord
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
            [['group_id', 'status', 'developer_karmapoints', 'temp_user', 'last_push', 'play_id'], 'integer'],
            [['username', 'email', 'firstname', 'lastname', 'developer_phone', 'developer_verification', 'alert', 'phone', 'timezone', 'twitter', 'skype', 'fbid', 'fbtoken', 'fbtoken_long', 'nickname', 'creator_api_key', 'temp_user', 'source', 'last_push', 'play_id', 'laratoken', 'active_app_id'], 'required'],
            [['question', 'answer', 'ban_reason', 'alert', 'phone'], 'string'],
            [['creation_date', 'activation_time', 'last_login', 'ban'], 'safe'],
            [['language'], 'string', 'max' => 2],
            [['username', 'password', 'email', 'home'], 'string', 'max' => 120],
            [['firstname', 'lastname', 'developer_phone', 'developer_verification', 'twitter', 'skype', 'fbid', 'fbtoken', 'fbtoken_long', 'nickname', 'source', 'laratoken', 'active_app_id'], 'string', 'max' => 255],
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
            'id' => 'ID',
            'group_id' => 'Group ID',
            'language' => 'Language',
            'username' => 'Username',
            'password' => 'Password',
            'email' => 'Email',
            'firstname' => 'Firstname',
            'lastname' => 'Lastname',
            'home' => 'Home',
            'status' => 'Status',
            'question' => 'Question',
            'answer' => 'Answer',
            'creation_date' => 'Creation Date',
            'activation_code' => 'Activation Code',
            'activation_time' => 'Activation Time',
            'last_login' => 'Last Login',
            'ban' => 'Ban',
            'ban_reason' => 'Ban Reason',
            'developer_phone' => 'Developer Phone',
            'developer_verification' => 'Developer Verification',
            'developer_karmapoints' => 'Developer Karmapoints',
            'alert' => 'Alert',
            'phone' => 'Phone',
            'timezone' => 'Timezone',
            'twitter' => 'Twitter',
            'skype' => 'Skype',
            'fbid' => 'Fbid',
            'fbtoken' => 'Fbtoken',
            'fbtoken_long' => 'Fbtoken Long',
            'nickname' => 'Nickname',
            'creator_api_key' => 'Creator Api Key',
            'temp_user' => 'Temp User',
            'source' => 'Source',
            'last_push' => 'Last Push',
            'play_id' => 'Play ID',
            'laratoken' => 'Laratoken',
            'active_app_id' => 'Active App ID',
        ];
    }

}