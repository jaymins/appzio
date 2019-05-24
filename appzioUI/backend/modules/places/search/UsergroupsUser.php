<?php

namespace backend\modules\places\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\places\models\UsergroupsUser as UsergroupsUserModel;

/**
* UsergroupsUser represents the model behind the search form about `backend\modules\places\models\UsergroupsUser`.
*/
class UsergroupsUser extends UsergroupsUserModel
{
/**
* @inheritdoc
*/
public function rules()
{
return [
[['id', 'group_id', 'status', 'developer_karmapoints', 'temp_user', 'last_push', 'play_id', 'terms_approved'], 'integer'],
            [['language', 'username', 'password', 'email', 'firstname', 'lastname', 'home', 'question', 'answer', 'creation_date', 'activation_code', 'activation_time', 'last_login', 'ban', 'ban_reason', 'developer_phone', 'developer_verification', 'alert', 'phone', 'timezone', 'twitter', 'skype', 'fbid', 'fbtoken', 'fbtoken_long', 'nickname', 'creator_api_key', 'source', 'laratoken', 'active_app_id', 'sftp_username'], 'safe'],
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
$query = UsergroupsUserModel::find();

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
            'group_id' => $this->group_id,
            'status' => $this->status,
            'creation_date' => $this->creation_date,
            'activation_time' => $this->activation_time,
            'last_login' => $this->last_login,
            'ban' => $this->ban,
            'developer_karmapoints' => $this->developer_karmapoints,
            'temp_user' => $this->temp_user,
            'last_push' => $this->last_push,
            'play_id' => $this->play_id,
            'terms_approved' => $this->terms_approved,
        ]);

        $query->andFilterWhere(['like', 'language', $this->language])
            ->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'password', $this->password])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'firstname', $this->firstname])
            ->andFilterWhere(['like', 'lastname', $this->lastname])
            ->andFilterWhere(['like', 'home', $this->home])
            ->andFilterWhere(['like', 'question', $this->question])
            ->andFilterWhere(['like', 'answer', $this->answer])
            ->andFilterWhere(['like', 'activation_code', $this->activation_code])
            ->andFilterWhere(['like', 'ban_reason', $this->ban_reason])
            ->andFilterWhere(['like', 'developer_phone', $this->developer_phone])
            ->andFilterWhere(['like', 'developer_verification', $this->developer_verification])
            ->andFilterWhere(['like', 'alert', $this->alert])
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'timezone', $this->timezone])
            ->andFilterWhere(['like', 'twitter', $this->twitter])
            ->andFilterWhere(['like', 'skype', $this->skype])
            ->andFilterWhere(['like', 'fbid', $this->fbid])
            ->andFilterWhere(['like', 'fbtoken', $this->fbtoken])
            ->andFilterWhere(['like', 'fbtoken_long', $this->fbtoken_long])
            ->andFilterWhere(['like', 'nickname', $this->nickname])
            ->andFilterWhere(['like', 'creator_api_key', $this->creator_api_key])
            ->andFilterWhere(['like', 'source', $this->source])
            ->andFilterWhere(['like', 'laratoken', $this->laratoken])
            ->andFilterWhere(['like', 'active_app_id', $this->active_app_id])
            ->andFilterWhere(['like', 'sftp_username', $this->sftp_username]);

return $dataProvider;
}
}