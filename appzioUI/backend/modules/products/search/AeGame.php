<?php

namespace backend\modules\products\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\products\models\AeGame as AeGameModel;

/**
* AeGame represents the model behind the search form about `backend\modules\products\models\AeGame`.
*/
class AeGame extends AeGameModel
{
/**
* @inheritdoc
*/
public function rules()
{
return [
[['id', 'user_id', 'category_id', 'active', 'timelimit', 'levels', 'featured', 'max_actions', 'show_toplist', 'register_email', 'register_sms', 'start_without_registration', 'show_homepage', 'choose_playername', 'choose_avatar', 'notifyme', 'api_enabled', 'keen_api_enabled', 'google_api_enabled', 'fb_api_enabled', 'api_application_id', 'fb_invite_points', 'hide_points', 'nickname_variable_id', 'show_toplist_points', 'show_toplist_entries', 'profilepic_variable_id', 'show_branches', 'notifications_enabled', 'cookie_lifetime', 'perm_can_reset', 'perm_can_delete', 'lang_show', 'secondary_points', 'tertiary_points', 'template', 'show_logo', 'show_social', 'custom_colors', 'game_wide', 'zip_period', 'asset_migration'], 'integer'],
            [['name', 'icon', 'length', 'description', 'alert', 'last_update', 'custom_css', 'home_instructions', 'shorturl', 'custom_domain', 'skin', 'api_key', 'api_secret_key', 'api_callback_url', 'keen_api_master_key', 'keen_api_write_key', 'keen_api_read_key', 'keen_api_config', 'google_api_code', 'google_api_config', 'fb_api_id', 'fb_api_secret', 'app_fb_hash', 'game_password', 'notification_config', 'lang_default', 'secondary_points_title', 'tertiary_points_title', 'primary_points_title', 'primary_points_shortname', 'secondary_points_shortname', 'tertiary_points_shortname', 'icon_primary_points', 'icon_secondary_points', 'icon_tertiary_points', 'social_share_url', 'social_share_description', 'social_force_to_canvas_url', 'colors', 'thirdparty_api_config', 'auth_config', 'visual_config', 'visual_config_params', 'headboard_portrait', 'headboard_landscape', 'background_image_landscape', 'background_image_portrait', 'logo'], 'safe'],
];
}

/**
* @inheritdoc
*/
public function scenarios()
{
// bypass scenarios() implementation in the parent class
return Model::scenarios();
}

/**
* Creates data provider instance with search query applied
*
* @param array $params
*
* @return ActiveDataProvider
*/
public function search($params)
{
$query = AeGameModel::find();

$dataProvider = new ActiveDataProvider([
'query' => $query,
]);

$this->load($params);

if (!$this->validate()) {
// uncomment the following line if you do not want to any records when validation fails
// $query->where('0=1');
return $dataProvider;
}

$query->andFilterWhere([
            'id' => $this->id,
            'user_id' => $this->user_id,
            'category_id' => $this->category_id,
            'active' => $this->active,
            'timelimit' => $this->timelimit,
            'levels' => $this->levels,
            'featured' => $this->featured,
            'last_update' => $this->last_update,
            'max_actions' => $this->max_actions,
            'show_toplist' => $this->show_toplist,
            'register_email' => $this->register_email,
            'register_sms' => $this->register_sms,
            'start_without_registration' => $this->start_without_registration,
            'show_homepage' => $this->show_homepage,
            'choose_playername' => $this->choose_playername,
            'choose_avatar' => $this->choose_avatar,
            'notifyme' => $this->notifyme,
            'api_enabled' => $this->api_enabled,
            'keen_api_enabled' => $this->keen_api_enabled,
            'google_api_enabled' => $this->google_api_enabled,
            'fb_api_enabled' => $this->fb_api_enabled,
            'api_application_id' => $this->api_application_id,
            'fb_invite_points' => $this->fb_invite_points,
            'hide_points' => $this->hide_points,
            'nickname_variable_id' => $this->nickname_variable_id,
            'show_toplist_points' => $this->show_toplist_points,
            'show_toplist_entries' => $this->show_toplist_entries,
            'profilepic_variable_id' => $this->profilepic_variable_id,
            'show_branches' => $this->show_branches,
            'notifications_enabled' => $this->notifications_enabled,
            'cookie_lifetime' => $this->cookie_lifetime,
            'perm_can_reset' => $this->perm_can_reset,
            'perm_can_delete' => $this->perm_can_delete,
            'lang_show' => $this->lang_show,
            'secondary_points' => $this->secondary_points,
            'tertiary_points' => $this->tertiary_points,
            'template' => $this->template,
            'show_logo' => $this->show_logo,
            'show_social' => $this->show_social,
            'custom_colors' => $this->custom_colors,
            'game_wide' => $this->game_wide,
            'zip_period' => $this->zip_period,
            'asset_migration' => $this->asset_migration,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'icon', $this->icon])
            ->andFilterWhere(['like', 'length', $this->length])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'alert', $this->alert])
            ->andFilterWhere(['like', 'custom_css', $this->custom_css])
            ->andFilterWhere(['like', 'home_instructions', $this->home_instructions])
            ->andFilterWhere(['like', 'shorturl', $this->shorturl])
            ->andFilterWhere(['like', 'custom_domain', $this->custom_domain])
            ->andFilterWhere(['like', 'skin', $this->skin])
            ->andFilterWhere(['like', 'api_key', $this->api_key])
            ->andFilterWhere(['like', 'api_secret_key', $this->api_secret_key])
            ->andFilterWhere(['like', 'api_callback_url', $this->api_callback_url])
            ->andFilterWhere(['like', 'keen_api_master_key', $this->keen_api_master_key])
            ->andFilterWhere(['like', 'keen_api_write_key', $this->keen_api_write_key])
            ->andFilterWhere(['like', 'keen_api_read_key', $this->keen_api_read_key])
            ->andFilterWhere(['like', 'keen_api_config', $this->keen_api_config])
            ->andFilterWhere(['like', 'google_api_code', $this->google_api_code])
            ->andFilterWhere(['like', 'google_api_config', $this->google_api_config])
            ->andFilterWhere(['like', 'fb_api_id', $this->fb_api_id])
            ->andFilterWhere(['like', 'fb_api_secret', $this->fb_api_secret])
            ->andFilterWhere(['like', 'app_fb_hash', $this->app_fb_hash])
            ->andFilterWhere(['like', 'game_password', $this->game_password])
            ->andFilterWhere(['like', 'notification_config', $this->notification_config])
            ->andFilterWhere(['like', 'lang_default', $this->lang_default])
            ->andFilterWhere(['like', 'secondary_points_title', $this->secondary_points_title])
            ->andFilterWhere(['like', 'tertiary_points_title', $this->tertiary_points_title])
            ->andFilterWhere(['like', 'primary_points_title', $this->primary_points_title])
            ->andFilterWhere(['like', 'primary_points_shortname', $this->primary_points_shortname])
            ->andFilterWhere(['like', 'secondary_points_shortname', $this->secondary_points_shortname])
            ->andFilterWhere(['like', 'tertiary_points_shortname', $this->tertiary_points_shortname])
            ->andFilterWhere(['like', 'icon_primary_points', $this->icon_primary_points])
            ->andFilterWhere(['like', 'icon_secondary_points', $this->icon_secondary_points])
            ->andFilterWhere(['like', 'icon_tertiary_points', $this->icon_tertiary_points])
            ->andFilterWhere(['like', 'social_share_url', $this->social_share_url])
            ->andFilterWhere(['like', 'social_share_description', $this->social_share_description])
            ->andFilterWhere(['like', 'social_force_to_canvas_url', $this->social_force_to_canvas_url])
            ->andFilterWhere(['like', 'colors', $this->colors])
            ->andFilterWhere(['like', 'thirdparty_api_config', $this->thirdparty_api_config])
            ->andFilterWhere(['like', 'auth_config', $this->auth_config])
            ->andFilterWhere(['like', 'visual_config', $this->visual_config])
            ->andFilterWhere(['like', 'visual_config_params', $this->visual_config_params])
            ->andFilterWhere(['like', 'headboard_portrait', $this->headboard_portrait])
            ->andFilterWhere(['like', 'headboard_landscape', $this->headboard_landscape])
            ->andFilterWhere(['like', 'background_image_landscape', $this->background_image_landscape])
            ->andFilterWhere(['like', 'background_image_portrait', $this->background_image_portrait])
            ->andFilterWhere(['like', 'logo', $this->logo]);

return $dataProvider;
}
}