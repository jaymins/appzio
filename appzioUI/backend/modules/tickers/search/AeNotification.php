<?php

namespace backend\modules\tickers\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\tickers\models\AeNotification as AeNotificationModel;

/**
* AeNotification represents the model behind the search form about `backend\modules\tickers\models\AeNotification`.
*/
class AeNotification extends AeNotificationModel
{
/**
* @inheritdoc
*/
public function rules()
{
return [
[['id', 'id_user', 'id_channel', 'id_playaction', 'app_id', 'action_id', 'play_id', 'shown_in_app', 'read_in_app', 'badge_count', 'sendtime', 'repeated', 'lastsent', 'expired', 'os_success', 'os_failed', 'os_converted'], 'integer'],
            [['onesignal_msgid', 'menu_id', 'menuid', 'type', 'subject', 'message', 'email_to', 'parameters', 'manual_config', 'created', 'updated', 'debug'], 'safe'],
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
$query = AeNotificationModel::find();

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
            'id_user' => $this->id_user,
            'id_channel' => $this->id_channel,
            'id_playaction' => $this->id_playaction,
            'app_id' => $this->app_id,
            'action_id' => $this->action_id,
            'play_id' => $this->play_id,
            'shown_in_app' => $this->shown_in_app,
            'read_in_app' => $this->read_in_app,
            'badge_count' => $this->badge_count,
            'sendtime' => $this->sendtime,
            'created' => $this->created,
            'updated' => $this->updated,
            'repeated' => $this->repeated,
            'lastsent' => $this->lastsent,
            'expired' => $this->expired,
            'os_success' => $this->os_success,
            'os_failed' => $this->os_failed,
            'os_converted' => $this->os_converted,
        ]);

        $query->andFilterWhere(['like', 'onesignal_msgid', $this->onesignal_msgid])
            ->andFilterWhere(['like', 'menu_id', $this->menu_id])
            ->andFilterWhere(['like', 'menuid', $this->menuid])
            ->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['like', 'subject', $this->subject])
            ->andFilterWhere(['like', 'message', $this->message])
            ->andFilterWhere(['like', 'email_to', $this->email_to])
            ->andFilterWhere(['like', 'parameters', $this->parameters])
            ->andFilterWhere(['like', 'manual_config', $this->manual_config])
            ->andFilterWhere(['like', 'debug', $this->debug]);

return $dataProvider;
}
}