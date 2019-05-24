<?php

namespace backend\modules\tatjack\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\tatjack\models\AeGameVariable as AeGameVariableModel;

/**
* AeGameVariable represents the model behind the search form about `backend\modules\tatjack\models\AeGameVariable`.
*/
class AeGameVariable extends AeGameVariableModel
{
/**
* @inheritdoc
*/
public function rules()
{
return [
[['id', 'game_id', 'set_on_players'], 'integer'],
            [['name', 'used_by_actions', 'value_type'], 'safe'],
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
$query = AeGameVariableModel::find();

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
            'game_id' => $this->game_id,
            'set_on_players' => $this->set_on_players,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'used_by_actions', $this->used_by_actions])
            ->andFilterWhere(['like', 'value_type', $this->value_type]);

return $dataProvider;
}
}