<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace backend\modules\fitness\models\base;

use Yii;

/**
 * This is the base-model class for table "ae_game_play".
 *
 * @property string $id
 * @property string $role_id
 * @property string $game_id
 * @property string $user_id
 * @property string $last_update
 * @property string $created
 * @property integer $progress
 * @property integer $status
 * @property string $alert
 * @property integer $level
 * @property string $current_level_id
 * @property integer $priority
 * @property integer $branch_starttime
 * @property integer $last_action_update
 * @property string $autogenerated_styles
 *
 * @property \backend\modules\fitness\models\AeAccessToken[] $aeAccessTokens
 * @property \backend\modules\fitness\models\AeChat[] $aeChats
 * @property \backend\modules\fitness\models\AeChatUser[] $aeChatUsers
 * @property \backend\modules\fitness\models\AeExtArticle[] $aeExtArticles
 * @property \backend\modules\fitness\models\AeExtArticleBookmark[] $aeExtArticleBookmarks
 * @property \backend\modules\fitness\models\AeExtArticleComment[] $aeExtArticleComments
 * @property \backend\modules\fitness\models\AeExtBidItem[] $aeExtBidItems
 * @property \backend\modules\fitness\models\AeExtBooking[] $aeExtBookings
 * @property \backend\modules\fitness\models\AeExtBooking[] $aeExtBookings0
 * @property \backend\modules\fitness\models\AeExtCalendarEntry[] $aeExtCalendarEntries
 * @property \backend\modules\fitness\models\AeExtClassifiedsFilter[] $aeExtClassifiedsFilters
 * @property \backend\modules\fitness\models\AeExtGolfHoleUser[] $aeExtGolfHoleUsers
 * @property \backend\modules\fitness\models\AeExtItem[] $aeExtItems
 * @property \backend\modules\fitness\models\AeExtItemsLike[] $aeExtItemsLikes
 * @property \backend\modules\fitness\models\AeExtItemsReport[] $aeExtItemsReports
 * @property \backend\modules\fitness\models\AeExtItemsReport[] $aeExtItemsReports0
 * @property \backend\modules\fitness\models\AeExtMeter[] $aeExtMeters
 * @property \backend\modules\fitness\models\AeExtMobileevent[] $aeExtMobileevents
 * @property \backend\modules\fitness\models\AeExtMobileeventsParticipant[] $aeExtMobileeventsParticipants
 * @property \backend\modules\fitness\models\AeExtMobilefeedbacktool[] $aeExtMobilefeedbacktools
 * @property \backend\modules\fitness\models\AeExtMobilefeedbacktoolTeam[] $aeExtMobilefeedbacktoolTeams
 * @property \backend\modules\fitness\models\AeExtMobilefeedbacktoolTeamsMember[] $aeExtMobilefeedbacktoolTeamsMembers
 * @property \backend\modules\fitness\models\AeExtMobilematching[] $aeExtMobilematchings
 * @property \backend\modules\fitness\models\AeExtMobileproperty[] $aeExtMobileproperties
 * @property \backend\modules\fitness\models\AeExtMobilepropertyBookmark[] $aeExtMobilepropertyBookmarks
 * @property \backend\modules\fitness\models\AeExtMobilepropertyUser[] $aeExtMobilepropertyUsers
 * @property \backend\modules\fitness\models\AeExtMobilepropertyUser[] $aeExtMobilepropertyUsers0
 * @property \backend\modules\fitness\models\AeExtNotification[] $aeExtNotifications
 * @property \backend\modules\fitness\models\AeExtNotification[] $aeExtNotifications0
 * @property \backend\modules\fitness\models\AeExtProduct[] $aeExtProducts
 * @property \backend\modules\fitness\models\AeExtProductsBookmark[] $aeExtProductsBookmarks
 * @property \backend\modules\fitness\models\AeExtProductsCart[] $aeExtProductsCarts
 * @property \backend\modules\fitness\models\AeExtProductsReview[] $aeExtProductsReviews
 * @property \backend\modules\fitness\models\AeExtRequest[] $aeExtRequests
 * @property \backend\modules\fitness\models\AeExtTattoo[] $aeExtTattoos
 * @property \backend\modules\fitness\models\AeExtTattoosLike[] $aeExtTattoosLikes
 * @property \backend\modules\fitness\models\AeExtTickerNotification[] $aeExtTickerNotifications
 * @property \backend\modules\fitness\models\AeExtWallet[] $aeExtWallets
 * @property \backend\modules\fitness\models\AeExtWalletLog[] $aeExtWalletLogs
 * @property \backend\modules\fitness\models\AeGame $game
 * @property \backend\modules\fitness\models\UsergroupsUser $user
 * @property \backend\modules\fitness\models\AeGamePlayAction[] $aeGamePlayActions
 * @property \backend\modules\fitness\models\AeGamePlayBranch[] $aeGamePlayBranches
 * @property \backend\modules\fitness\models\AeGameBranch[] $branches
 * @property \backend\modules\fitness\models\AeGamePlayDatastorage[] $aeGamePlayDatastorages
 * @property \backend\modules\fitness\models\AeGamePlayKeyvaluestorage[] $aeGamePlayKeyvaluestorages
 * @property \backend\modules\fitness\models\AeGamePlayKeyvaluestorage[] $aeGamePlayKeyvaluestorages0
 * @property \backend\modules\fitness\models\AeGamePlayRole $aeGamePlayRole
 * @property \backend\modules\fitness\models\AeGamePlayUser[] $aeGamePlayUsers
 * @property \backend\modules\fitness\models\AeGamePlayVariable[] $aeGamePlayVariables
 * @property \backend\modules\fitness\models\AeNotification[] $aeNotifications
 * @property \backend\modules\fitness\models\AePurchase[] $aePurchases
 * @property \backend\modules\fitness\models\AeTask[] $aeTasks
 * @property string $aliasModel
 */
abstract class AeGamePlay extends \backend\models\CrudBase
{



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ae_game_play';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['role_id', 'game_id', 'user_id', 'progress', 'status', 'level', 'current_level_id', 'priority', 'branch_starttime', 'last_action_update'], 'integer'],
            [['last_update', 'created'], 'safe'],
            [['alert', 'current_level_id', 'branch_starttime', 'last_action_update', 'autogenerated_styles'], 'required'],
            [['autogenerated_styles'], 'string'],
            [['alert'], 'string', 'max' => 255],
            [['game_id'], 'exist', 'skipOnError' => true, 'targetClass' => \backend\modules\fitness\models\AeGame::className(), 'targetAttribute' => ['game_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => \backend\modules\fitness\models\UsergroupsUser::className(), 'targetAttribute' => ['user_id' => 'id']]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('backend', 'ID'),
            'role_id' => Yii::t('backend', 'Role ID'),
            'game_id' => Yii::t('backend', 'Game ID'),
            'user_id' => Yii::t('backend', 'User ID'),
            'last_update' => Yii::t('backend', 'Last Update'),
            'created' => Yii::t('backend', 'Created'),
            'progress' => Yii::t('backend', 'Progress'),
            'status' => Yii::t('backend', 'Status'),
            'alert' => Yii::t('backend', 'Alert'),
            'level' => Yii::t('backend', 'Level'),
            'current_level_id' => Yii::t('backend', 'Current Level ID'),
            'priority' => Yii::t('backend', 'Priority'),
            'branch_starttime' => Yii::t('backend', 'Branch Starttime'),
            'last_action_update' => Yii::t('backend', 'Last Action Update'),
            'autogenerated_styles' => Yii::t('backend', 'Autogenerated Styles'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeAccessTokens()
    {
        return $this->hasMany(\backend\modules\fitness\models\AeAccessToken::className(), ['play_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeChats()
    {
        return $this->hasMany(\backend\modules\fitness\models\AeChat::className(), ['owner_play_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeChatUsers()
    {
        return $this->hasMany(\backend\modules\fitness\models\AeChatUser::className(), ['chat_user_play_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeExtArticles()
    {
        return $this->hasMany(\backend\modules\fitness\models\AeExtArticle::className(), ['play_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeExtArticleBookmarks()
    {
        return $this->hasMany(\backend\modules\fitness\models\AeExtArticleBookmark::className(), ['play_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeExtArticleComments()
    {
        return $this->hasMany(\backend\modules\fitness\models\AeExtArticleComment::className(), ['play_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeExtBidItems()
    {
        return $this->hasMany(\backend\modules\fitness\models\AeExtBidItem::className(), ['play_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeExtBookings()
    {
        return $this->hasMany(\backend\modules\fitness\models\AeExtBooking::className(), ['play_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeExtBookings0()
    {
        return $this->hasMany(\backend\modules\fitness\models\AeExtBooking::className(), ['assignee_play_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeExtCalendarEntries()
    {
        return $this->hasMany(\backend\modules\fitness\models\AeExtCalendarEntry::className(), ['play_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeExtClassifiedsFilters()
    {
        return $this->hasMany(\backend\modules\fitness\models\AeExtClassifiedsFilter::className(), ['play_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeExtGolfHoleUsers()
    {
        return $this->hasMany(\backend\modules\fitness\models\AeExtGolfHoleUser::className(), ['play_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeExtItems()
    {
        return $this->hasMany(\backend\modules\fitness\models\AeExtItem::className(), ['play_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeExtItemsLikes()
    {
        return $this->hasMany(\backend\modules\fitness\models\AeExtItemsLike::className(), ['play_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeExtItemsReports()
    {
        return $this->hasMany(\backend\modules\fitness\models\AeExtItemsReport::className(), ['play_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeExtItemsReports0()
    {
        return $this->hasMany(\backend\modules\fitness\models\AeExtItemsReport::className(), ['item_owner_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeExtMeters()
    {
        return $this->hasMany(\backend\modules\fitness\models\AeExtMeter::className(), ['play_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeExtMobileevents()
    {
        return $this->hasMany(\backend\modules\fitness\models\AeExtMobileevent::className(), ['play_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeExtMobileeventsParticipants()
    {
        return $this->hasMany(\backend\modules\fitness\models\AeExtMobileeventsParticipant::className(), ['play_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeExtMobilefeedbacktools()
    {
        return $this->hasMany(\backend\modules\fitness\models\AeExtMobilefeedbacktool::className(), ['author_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeExtMobilefeedbacktoolTeams()
    {
        return $this->hasMany(\backend\modules\fitness\models\AeExtMobilefeedbacktoolTeam::className(), ['owner_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeExtMobilefeedbacktoolTeamsMembers()
    {
        return $this->hasMany(\backend\modules\fitness\models\AeExtMobilefeedbacktoolTeamsMember::className(), ['play_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeExtMobilematchings()
    {
        return $this->hasMany(\backend\modules\fitness\models\AeExtMobilematching::className(), ['play_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeExtMobileproperties()
    {
        return $this->hasMany(\backend\modules\fitness\models\AeExtMobileproperty::className(), ['play_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeExtMobilepropertyBookmarks()
    {
        return $this->hasMany(\backend\modules\fitness\models\AeExtMobilepropertyBookmark::className(), ['play_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeExtMobilepropertyUsers()
    {
        return $this->hasMany(\backend\modules\fitness\models\AeExtMobilepropertyUser::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeExtMobilepropertyUsers0()
    {
        return $this->hasMany(\backend\modules\fitness\models\AeExtMobilepropertyUser::className(), ['agent_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeExtNotifications()
    {
        return $this->hasMany(\backend\modules\fitness\models\AeExtNotification::className(), ['play_id_to' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeExtNotifications0()
    {
        return $this->hasMany(\backend\modules\fitness\models\AeExtNotification::className(), ['play_id_from' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeExtProducts()
    {
        return $this->hasMany(\backend\modules\fitness\models\AeExtProduct::className(), ['play_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeExtProductsBookmarks()
    {
        return $this->hasMany(\backend\modules\fitness\models\AeExtProductsBookmark::className(), ['play_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeExtProductsCarts()
    {
        return $this->hasMany(\backend\modules\fitness\models\AeExtProductsCart::className(), ['play_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeExtProductsReviews()
    {
        return $this->hasMany(\backend\modules\fitness\models\AeExtProductsReview::className(), ['play_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeExtRequests()
    {
        return $this->hasMany(\backend\modules\fitness\models\AeExtRequest::className(), ['requester_playid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeExtTattoos()
    {
        return $this->hasMany(\backend\modules\fitness\models\AeExtTattoo::className(), ['play_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeExtTattoosLikes()
    {
        return $this->hasMany(\backend\modules\fitness\models\AeExtTattoosLike::className(), ['play_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeExtTickerNotifications()
    {
        return $this->hasMany(\backend\modules\fitness\models\AeExtTickerNotification::className(), ['play_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeExtWallets()
    {
        return $this->hasMany(\backend\modules\fitness\models\AeExtWallet::className(), ['play_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeExtWalletLogs()
    {
        return $this->hasMany(\backend\modules\fitness\models\AeExtWalletLog::className(), ['play_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGame()
    {
        return $this->hasOne(\backend\modules\fitness\models\AeGame::className(), ['id' => 'game_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(\backend\modules\fitness\models\UsergroupsUser::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeGamePlayActions()
    {
        return $this->hasMany(\backend\modules\fitness\models\AeGamePlayAction::className(), ['play_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeGamePlayBranches()
    {
        return $this->hasMany(\backend\modules\fitness\models\AeGamePlayBranch::className(), ['play_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBranches()
    {
        return $this->hasMany(\backend\modules\fitness\models\AeGameBranch::className(), ['id' => 'branch_id'])->viaTable('ae_game_play_branch', ['play_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeGamePlayDatastorages()
    {
        return $this->hasMany(\backend\modules\fitness\models\AeGamePlayDatastorage::className(), ['play_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeGamePlayKeyvaluestorages()
    {
        return $this->hasMany(\backend\modules\fitness\models\AeGamePlayKeyvaluestorage::className(), ['value' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeGamePlayKeyvaluestorages0()
    {
        return $this->hasMany(\backend\modules\fitness\models\AeGamePlayKeyvaluestorage::className(), ['play_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeGamePlayRole()
    {
        return $this->hasOne(\backend\modules\fitness\models\AeGamePlayRole::className(), ['play_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeGamePlayUsers()
    {
        return $this->hasMany(\backend\modules\fitness\models\AeGamePlayUser::className(), ['id_play' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeGamePlayVariables()
    {
        return $this->hasMany(\backend\modules\fitness\models\AeGamePlayVariable::className(), ['play_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeNotifications()
    {
        return $this->hasMany(\backend\modules\fitness\models\AeNotification::className(), ['play_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAePurchases()
    {
        return $this->hasMany(\backend\modules\fitness\models\AePurchase::className(), ['play_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeTasks()
    {
        return $this->hasMany(\backend\modules\fitness\models\AeTask::className(), ['play_id' => 'id']);
    }


    
    /**
     * @inheritdoc
     * @return \backend\modules\fitness\models\query\AeGamePlayQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \backend\modules\fitness\models\query\AeGamePlayQuery(get_called_class());
    }


}
