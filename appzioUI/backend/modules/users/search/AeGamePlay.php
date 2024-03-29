<?php

namespace backend\modules\users\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\users\models\AeGamePlay as AeGamePlayModel;

/**
* AeGamePlay represents the model behind the search form about `backend\modules\users\models\AeGamePlay`.
*/
class AeGamePlay extends AeGamePlayModel
{
/**
* @inheritdoc
*/
public function rules()
{
return [
[['id', 'role_id', 'game_id', 'user_id', 'progress', 'status', 'level', 'current_level_id', 'priority', 'branch_starttime', 'last_action_update'], 'integer'],
            [['last_update', 'created', 'alert'], 'safe'],
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
$query = AeGamePlayModel::find();

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
            'role_id' => $this->role_id,
            'game_id' => $this->game_id,
            'user_id' => $this->user_id,
            'last_update' => $this->last_update,
            'created' => $this->created,
            'progress' => $this->progress,
            'status' => $this->status,
            'level' => $this->level,
            'current_level_id' => $this->current_level_id,
            'priority' => $this->priority,
            'branch_starttime' => $this->branch_starttime,
            'last_action_update' => $this->last_action_update,
        ]);

        $query->andFilterWhere(['like', 'alert', $this->alert]);

return $dataProvider;
}
}