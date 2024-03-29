<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace backend\modules\quizzes\models\base;

use Yii;

/**
 * This is the base-model class for table "ae_game".
 *
 * @property string $id
 * @property string $user_id
 * @property string $category_id
 * @property string $name
 * @property integer $active
 * @property string $icon
 * @property string $headboard_portrait
 * @property string $headboard_landscape
 * @property string $background_image_landscape
 * @property string $background_image_portrait
 * @property string $logo
 * @property string $length
 * @property integer $timelimit
 * @property string $description
 * @property integer $levels
 * @property string $alert
 * @property integer $featured
 * @property string $last_update
 * @property integer $max_actions
 * @property string $custom_css
 * @property integer $show_toplist
 * @property integer $register_email
 * @property integer $register_sms
 * @property integer $start_without_registration
 * @property integer $show_homepage
 * @property integer $choose_playername
 * @property integer $choose_avatar
 * @property string $shorturl
 * @property string $custom_domain
 * @property string $home_instructions
 * @property integer $notifyme
 * @property string $skin
 * @property integer $show_logo
 * @property integer $show_social
 * @property string $social_share_url
 * @property string $social_share_description
 * @property string $social_force_to_canvas_url
 * @property string $app_fb_hash
 * @property integer $custom_colors
 * @property string $colors
 * @property integer $show_branches
 * @property string $api_key
 * @property string $api_secret_key
 * @property string $api_callback_url
 * @property string $api_application_id
 * @property integer $api_enabled
 * @property integer $keen_api_enabled
 * @property string $keen_api_master_key
 * @property string $keen_api_write_key
 * @property string $keen_api_read_key
 * @property string $keen_api_config
 * @property integer $google_api_enabled
 * @property string $google_api_code
 * @property string $google_api_config
 * @property integer $fb_api_enabled
 * @property string $fb_api_id
 * @property string $fb_api_secret
 * @property string $fb_invite_points
 * @property integer $hide_points
 * @property string $game_password
 * @property string $nickname_variable_id
 * @property string $show_toplist_points
 * @property string $show_toplist_entries
 * @property string $profilepic_variable_id
 * @property integer $notifications_enabled
 * @property integer $cookie_lifetime
 * @property string $notification_config
 * @property integer $perm_can_reset
 * @property integer $perm_can_delete
 * @property integer $lang_show
 * @property string $lang_default
 * @property integer $secondary_points
 * @property string $secondary_points_title
 * @property integer $tertiary_points
 * @property string $tertiary_points_title
 * @property string $primary_points_shortname
 * @property string $primary_points_title
 * @property string $secondary_points_shortname
 * @property string $tertiary_points_shortname
 * @property string $icon_primary_points
 * @property string $icon_secondary_points
 * @property string $icon_tertiary_points
 * @property integer $template
 * @property integer $game_wide
 * @property integer $asset_migration
 * @property string $thirdparty_api_config
 * @property string $auth_config
 * @property string $visual_config
 * @property string $visual_config_params
 *
 * @property \backend\modules\quizzes\models\AeApiFilelookup[] $aeApiFilelookups
 * @property \backend\modules\quizzes\models\AeAppMenu[] $aeAppMenus
 * @property \backend\modules\quizzes\models\AeChat[] $aeChats
 * @property \backend\modules\quizzes\models\AeExtArticle[] $aeExtArticles
 * @property \backend\modules\quizzes\models\AeExtArticleCategory[] $aeExtArticleCategories
 * @property \backend\modules\quizzes\models\AeExtArticleTag[] $aeExtArticleTags
 * @property \backend\modules\quizzes\models\AeExtArticleTemplate[] $aeExtArticleTemplates
 * @property \backend\modules\quizzes\models\AeExtDealsLog[] $aeExtDealsLogs
 * @property \backend\modules\quizzes\models\AeExtItem[] $aeExtItems
 * @property \backend\modules\quizzes\models\AeExtItemsCategory[] $aeExtItemsCategories
 * @property \backend\modules\quizzes\models\AeExtMobileevent[] $aeExtMobileevents
 * @property \backend\modules\quizzes\models\AeExtMobilefeedbacktool[] $aeExtMobilefeedbacktools
 * @property \backend\modules\quizzes\models\AeExtMobilefeedbacktoolDepartment[] $aeExtMobilefeedbacktoolDepartments
 * @property \backend\modules\quizzes\models\AeExtMobilefeedbacktoolFundamental[] $aeExtMobilefeedbacktoolFundamentals
 * @property \backend\modules\quizzes\models\AeExtMobilefeedbacktoolTeam[] $aeExtMobilefeedbacktoolTeams
 * @property \backend\modules\quizzes\models\AeExtMobilematching[] $aeExtMobilematchings
 * @property \backend\modules\quizzes\models\AeExtMobileplace[] $aeExtMobileplaces
 * @property \backend\modules\quizzes\models\AeExtMobileproperty[] $aeExtMobileproperties
 * @property \backend\modules\quizzes\models\AeExtMobilepropertyBookmark[] $aeExtMobilepropertyBookmarks
 * @property \backend\modules\quizzes\models\AeExtNotification[] $aeExtNotifications
 * @property \backend\modules\quizzes\models\AeExtProduct[] $aeExtProducts
 * @property \backend\modules\quizzes\models\AeExtProductsCategory[] $aeExtProductsCategories
 * @property \backend\modules\quizzes\models\AeExtProductsTag[] $aeExtProductsTags
 * @property \backend\modules\quizzes\models\AeExtQuizSet[] $aeExtQuizSets
 * @property \backend\modules\quizzes\models\AeExtTattoo[] $aeExtTattoos
 * @property \backend\modules\quizzes\models\AeFilename[] $aeFilenames
 * @property \backend\modules\quizzes\models\AeCategory $category
 * @property \backend\modules\quizzes\models\UsergroupsUser $user
 * @property \backend\modules\quizzes\models\AeGameBadge[] $aeGameBadges
 * @property \backend\modules\quizzes\models\AeGameBadge[] $aeGameBadges0
 * @property \backend\modules\quizzes\models\AeGameBranch[] $aeGameBranches
 * @property \backend\modules\quizzes\models\AeGameKeyvaluestorage[] $aeGameKeyvaluestorages
 * @property \backend\modules\quizzes\models\AeGamePlay[] $aeGamePlays
 * @property \backend\modules\quizzes\models\AeGameRole[] $aeGameRoles
 * @property \backend\modules\quizzes\models\AeGameScore[] $aeGameScores
 * @property \backend\modules\quizzes\models\AeGameVariable[] $aeGameVariables
 * @property \backend\modules\quizzes\models\AeLocationLog[] $aeLocationLogs
 * @property \backend\modules\quizzes\models\AeMobile $aeMobile
 * @property \backend\modules\quizzes\models\AePackage[] $aePackages
 * @property \backend\modules\quizzes\models\AePackagesTheme[] $aePackagesThemes
 * @property \backend\modules\quizzes\models\AePurchase[] $aePurchases
 * @property string $aliasModel
 */
abstract class AeGame extends \backend\models\CrudBase
{



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ae_game';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'category_id', 'active', 'timelimit', 'levels', 'featured', 'max_actions', 'show_toplist', 'register_email', 'register_sms', 'start_without_registration', 'show_homepage', 'choose_playername', 'choose_avatar', 'notifyme', 'show_logo', 'show_social', 'custom_colors', 'show_branches', 'api_enabled', 'keen_api_enabled', 'google_api_enabled', 'fb_api_enabled', 'fb_invite_points', 'hide_points', 'nickname_variable_id', 'show_toplist_points', 'show_toplist_entries', 'profilepic_variable_id', 'notifications_enabled', 'cookie_lifetime', 'perm_can_reset', 'perm_can_delete', 'lang_show', 'secondary_points', 'tertiary_points', 'template', 'game_wide', 'asset_migration'], 'integer'],
            [['background_image_landscape', 'background_image_portrait', 'logo', 'length', 'description', 'levels', 'alert', 'custom_css', 'shorturl', 'custom_domain', 'home_instructions', 'social_share_url', 'social_share_description', 'social_force_to_canvas_url', 'app_fb_hash', 'colors', 'api_key', 'api_secret_key', 'api_callback_url', 'api_application_id', 'keen_api_enabled', 'keen_api_master_key', 'keen_api_write_key', 'keen_api_read_key', 'keen_api_config', 'google_api_enabled', 'google_api_code', 'google_api_config', 'fb_api_enabled', 'fb_api_id', 'fb_api_secret', 'fb_invite_points', 'game_password', 'nickname_variable_id', 'show_toplist_points', 'show_toplist_entries', 'profilepic_variable_id', 'notification_config', 'secondary_points', 'secondary_points_title', 'tertiary_points', 'tertiary_points_title', 'primary_points_shortname', 'primary_points_title', 'secondary_points_shortname', 'tertiary_points_shortname', 'template', 'thirdparty_api_config', 'auth_config', 'visual_config', 'visual_config_params'], 'required'],
            [['description', 'home_instructions', 'colors', 'keen_api_write_key', 'keen_api_read_key', 'keen_api_config', 'google_api_config', 'notification_config', 'thirdparty_api_config', 'auth_config', 'visual_config', 'visual_config_params'], 'string'],
            [['last_update'], 'safe'],
            [['name', 'icon', 'headboard_portrait', 'headboard_landscape', 'background_image_landscape', 'background_image_portrait', 'logo', 'length', 'alert', 'custom_css', 'shorturl', 'custom_domain', 'skin', 'social_share_url', 'social_share_description', 'social_force_to_canvas_url', 'app_fb_hash', 'api_key', 'api_secret_key', 'api_callback_url', 'api_application_id', 'keen_api_master_key', 'google_api_code', 'fb_api_id', 'fb_api_secret', 'secondary_points_title', 'tertiary_points_title', 'primary_points_shortname', 'primary_points_title', 'secondary_points_shortname', 'tertiary_points_shortname', 'icon_primary_points', 'icon_secondary_points', 'icon_tertiary_points'], 'string', 'max' => 255],
            [['game_password'], 'string', 'max' => 120],
            [['lang_default'], 'string', 'max' => 2],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => \backend\modules\quizzes\models\AeCategory::className(), 'targetAttribute' => ['category_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => \backend\modules\quizzes\models\UsergroupsUser::className(), 'targetAttribute' => ['user_id' => 'id']]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('backend', 'ID'),
            'user_id' => Yii::t('backend', 'User ID'),
            'category_id' => Yii::t('backend', 'Category ID'),
            'name' => Yii::t('backend', 'Name'),
            'active' => Yii::t('backend', 'Active'),
            'icon' => Yii::t('backend', 'Icon'),
            'headboard_portrait' => Yii::t('backend', 'Headboard Portrait'),
            'headboard_landscape' => Yii::t('backend', 'Headboard Landscape'),
            'background_image_landscape' => Yii::t('backend', 'Background Image Landscape'),
            'background_image_portrait' => Yii::t('backend', 'Background Image Portrait'),
            'logo' => Yii::t('backend', 'Logo'),
            'length' => Yii::t('backend', 'Length'),
            'timelimit' => Yii::t('backend', 'Timelimit'),
            'description' => Yii::t('backend', 'Description'),
            'levels' => Yii::t('backend', 'Levels'),
            'alert' => Yii::t('backend', 'Alert'),
            'featured' => Yii::t('backend', 'Featured'),
            'last_update' => Yii::t('backend', 'Last Update'),
            'max_actions' => Yii::t('backend', 'Max Actions'),
            'custom_css' => Yii::t('backend', 'Custom Css'),
            'show_toplist' => Yii::t('backend', 'Show Toplist'),
            'register_email' => Yii::t('backend', 'Register Email'),
            'register_sms' => Yii::t('backend', 'Register Sms'),
            'start_without_registration' => Yii::t('backend', 'Start Without Registration'),
            'show_homepage' => Yii::t('backend', 'Show Homepage'),
            'choose_playername' => Yii::t('backend', 'Choose Playername'),
            'choose_avatar' => Yii::t('backend', 'Choose Avatar'),
            'shorturl' => Yii::t('backend', 'Shorturl'),
            'custom_domain' => Yii::t('backend', 'Custom Domain'),
            'home_instructions' => Yii::t('backend', 'Home Instructions'),
            'notifyme' => Yii::t('backend', 'Notifyme'),
            'skin' => Yii::t('backend', 'Skin'),
            'show_logo' => Yii::t('backend', 'Show Logo'),
            'show_social' => Yii::t('backend', 'Show Social'),
            'social_share_url' => Yii::t('backend', 'Social Share Url'),
            'social_share_description' => Yii::t('backend', 'Social Share Description'),
            'social_force_to_canvas_url' => Yii::t('backend', 'Social Force To Canvas Url'),
            'app_fb_hash' => Yii::t('backend', 'App Fb Hash'),
            'custom_colors' => Yii::t('backend', 'Custom Colors'),
            'colors' => Yii::t('backend', 'Colors'),
            'show_branches' => Yii::t('backend', 'Show Branches'),
            'api_key' => Yii::t('backend', 'Api Key'),
            'api_secret_key' => Yii::t('backend', 'Api Secret Key'),
            'api_callback_url' => Yii::t('backend', 'Api Callback Url'),
            'api_application_id' => Yii::t('backend', 'Api Application ID'),
            'api_enabled' => Yii::t('backend', 'Api Enabled'),
            'keen_api_enabled' => Yii::t('backend', 'Keen Api Enabled'),
            'keen_api_master_key' => Yii::t('backend', 'Keen Api Master Key'),
            'keen_api_write_key' => Yii::t('backend', 'Keen Api Write Key'),
            'keen_api_read_key' => Yii::t('backend', 'Keen Api Read Key'),
            'keen_api_config' => Yii::t('backend', 'Keen Api Config'),
            'google_api_enabled' => Yii::t('backend', 'Google Api Enabled'),
            'google_api_code' => Yii::t('backend', 'Google Api Code'),
            'google_api_config' => Yii::t('backend', 'Google Api Config'),
            'fb_api_enabled' => Yii::t('backend', 'Fb Api Enabled'),
            'fb_api_id' => Yii::t('backend', 'Fb Api ID'),
            'fb_api_secret' => Yii::t('backend', 'Fb Api Secret'),
            'fb_invite_points' => Yii::t('backend', 'Fb Invite Points'),
            'hide_points' => Yii::t('backend', 'Hide Points'),
            'game_password' => Yii::t('backend', 'Game Password'),
            'nickname_variable_id' => Yii::t('backend', 'Nickname Variable ID'),
            'show_toplist_points' => Yii::t('backend', 'Show Toplist Points'),
            'show_toplist_entries' => Yii::t('backend', 'Show Toplist Entries'),
            'profilepic_variable_id' => Yii::t('backend', 'Profilepic Variable ID'),
            'notifications_enabled' => Yii::t('backend', 'Notifications Enabled'),
            'cookie_lifetime' => Yii::t('backend', 'Cookie Lifetime'),
            'notification_config' => Yii::t('backend', 'Notification Config'),
            'perm_can_reset' => Yii::t('backend', 'Perm Can Reset'),
            'perm_can_delete' => Yii::t('backend', 'Perm Can Delete'),
            'lang_show' => Yii::t('backend', 'Lang Show'),
            'lang_default' => Yii::t('backend', 'Lang Default'),
            'secondary_points' => Yii::t('backend', 'Secondary Points'),
            'secondary_points_title' => Yii::t('backend', 'Secondary Points Title'),
            'tertiary_points' => Yii::t('backend', 'Tertiary Points'),
            'tertiary_points_title' => Yii::t('backend', 'Tertiary Points Title'),
            'primary_points_shortname' => Yii::t('backend', 'Primary Points Shortname'),
            'primary_points_title' => Yii::t('backend', 'Primary Points Title'),
            'secondary_points_shortname' => Yii::t('backend', 'Secondary Points Shortname'),
            'tertiary_points_shortname' => Yii::t('backend', 'Tertiary Points Shortname'),
            'icon_primary_points' => Yii::t('backend', 'Icon Primary Points'),
            'icon_secondary_points' => Yii::t('backend', 'Icon Secondary Points'),
            'icon_tertiary_points' => Yii::t('backend', 'Icon Tertiary Points'),
            'template' => Yii::t('backend', 'Template'),
            'game_wide' => Yii::t('backend', 'Game Wide'),
            'asset_migration' => Yii::t('backend', 'Asset Migration'),
            'thirdparty_api_config' => Yii::t('backend', 'Thirdparty Api Config'),
            'auth_config' => Yii::t('backend', 'Auth Config'),
            'visual_config' => Yii::t('backend', 'Visual Config'),
            'visual_config_params' => Yii::t('backend', 'Visual Config Params'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeApiFilelookups()
    {
        return $this->hasMany(\backend\modules\quizzes\models\AeApiFilelookup::className(), ['game_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeAppMenus()
    {
        return $this->hasMany(\backend\modules\quizzes\models\AeAppMenu::className(), ['app_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeChats()
    {
        return $this->hasMany(\backend\modules\quizzes\models\AeChat::className(), ['game_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeExtArticles()
    {
        return $this->hasMany(\backend\modules\quizzes\models\AeExtArticle::className(), ['app_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeExtArticleCategories()
    {
        return $this->hasMany(\backend\modules\quizzes\models\AeExtArticleCategory::className(), ['app_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeExtArticleTags()
    {
        return $this->hasMany(\backend\modules\quizzes\models\AeExtArticleTag::className(), ['app_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeExtArticleTemplates()
    {
        return $this->hasMany(\backend\modules\quizzes\models\AeExtArticleTemplate::className(), ['app_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeExtDealsLogs()
    {
        return $this->hasMany(\backend\modules\quizzes\models\AeExtDealsLog::className(), ['app_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeExtItems()
    {
        return $this->hasMany(\backend\modules\quizzes\models\AeExtItem::className(), ['game_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeExtItemsCategories()
    {
        return $this->hasMany(\backend\modules\quizzes\models\AeExtItemsCategory::className(), ['app_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeExtMobileevents()
    {
        return $this->hasMany(\backend\modules\quizzes\models\AeExtMobileevent::className(), ['game_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeExtMobilefeedbacktools()
    {
        return $this->hasMany(\backend\modules\quizzes\models\AeExtMobilefeedbacktool::className(), ['game_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeExtMobilefeedbacktoolDepartments()
    {
        return $this->hasMany(\backend\modules\quizzes\models\AeExtMobilefeedbacktoolDepartment::className(), ['game_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeExtMobilefeedbacktoolFundamentals()
    {
        return $this->hasMany(\backend\modules\quizzes\models\AeExtMobilefeedbacktoolFundamental::className(), ['game_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeExtMobilefeedbacktoolTeams()
    {
        return $this->hasMany(\backend\modules\quizzes\models\AeExtMobilefeedbacktoolTeam::className(), ['game_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeExtMobilematchings()
    {
        return $this->hasMany(\backend\modules\quizzes\models\AeExtMobilematching::className(), ['game_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeExtMobileplaces()
    {
        return $this->hasMany(\backend\modules\quizzes\models\AeExtMobileplace::className(), ['game_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeExtMobileproperties()
    {
        return $this->hasMany(\backend\modules\quizzes\models\AeExtMobileproperty::className(), ['game_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeExtMobilepropertyBookmarks()
    {
        return $this->hasMany(\backend\modules\quizzes\models\AeExtMobilepropertyBookmark::className(), ['game_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeExtNotifications()
    {
        return $this->hasMany(\backend\modules\quizzes\models\AeExtNotification::className(), ['app_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeExtProducts()
    {
        return $this->hasMany(\backend\modules\quizzes\models\AeExtProduct::className(), ['app_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeExtProductsCategories()
    {
        return $this->hasMany(\backend\modules\quizzes\models\AeExtProductsCategory::className(), ['app_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeExtProductsTags()
    {
        return $this->hasMany(\backend\modules\quizzes\models\AeExtProductsTag::className(), ['app_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeExtQuizSets()
    {
        return $this->hasMany(\backend\modules\quizzes\models\AeExtQuizSet::className(), ['app_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeExtTattoos()
    {
        return $this->hasMany(\backend\modules\quizzes\models\AeExtTattoo::className(), ['game_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeFilenames()
    {
        return $this->hasMany(\backend\modules\quizzes\models\AeFilename::className(), ['game_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(\backend\modules\quizzes\models\AeCategory::className(), ['id' => 'category_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(\backend\modules\quizzes\models\UsergroupsUser::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeGameBadges()
    {
        return $this->hasMany(\backend\modules\quizzes\models\AeGameBadge::className(), ['game_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeGameBadges0()
    {
        return $this->hasMany(\backend\modules\quizzes\models\AeGameBadge::className(), ['game_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeGameBranches()
    {
        return $this->hasMany(\backend\modules\quizzes\models\AeGameBranch::className(), ['game_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeGameKeyvaluestorages()
    {
        return $this->hasMany(\backend\modules\quizzes\models\AeGameKeyvaluestorage::className(), ['game_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeGamePlays()
    {
        return $this->hasMany(\backend\modules\quizzes\models\AeGamePlay::className(), ['game_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeGameRoles()
    {
        return $this->hasMany(\backend\modules\quizzes\models\AeGameRole::className(), ['game_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeGameScores()
    {
        return $this->hasMany(\backend\modules\quizzes\models\AeGameScore::className(), ['game_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeGameVariables()
    {
        return $this->hasMany(\backend\modules\quizzes\models\AeGameVariable::className(), ['game_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeLocationLogs()
    {
        return $this->hasMany(\backend\modules\quizzes\models\AeLocationLog::className(), ['game_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeMobile()
    {
        return $this->hasOne(\backend\modules\quizzes\models\AeMobile::className(), ['game_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAePackages()
    {
        return $this->hasMany(\backend\modules\quizzes\models\AePackage::className(), ['game_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAePackagesThemes()
    {
        return $this->hasMany(\backend\modules\quizzes\models\AePackagesTheme::className(), ['game_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAePurchases()
    {
        return $this->hasMany(\backend\modules\quizzes\models\AePurchase::className(), ['game_id' => 'id']);
    }


    
    /**
     * @inheritdoc
     * @return \backend\modules\quizzes\models\query\AeGameQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \backend\modules\quizzes\models\query\AeGameQuery(get_called_class());
    }


}
