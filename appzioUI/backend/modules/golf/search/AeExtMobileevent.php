<?php

namespace backend\modules\golf\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\golf\models\AeExtMobileevent as AeExtMobileeventModel;

/**
* AeExtMobileevent represents the model behind the search form about `backend\modules\golf\models\AeExtMobileevent`.
*/
class AeExtMobileevent extends AeExtMobileeventModel
{
/**
* @inheritdoc
*/
public function rules()
{
return [
[['id', 'game_id', 'play_id', 'place_id'], 'integer'],
            [['title', 'date', 'time', 'time_of_day', 'status', 'notes', 'starting_time'], 'safe'],
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
$query = AeExtMobileeventModel::find();

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
            'play_id' => $this->play_id,
            'place_id' => $this->place_id,
            'date' => $this->date,
            'time' => $this->time,
            'starting_time' => $this->starting_time,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'time_of_day', $this->time_of_day])
            ->andFilterWhere(['like', 'status', $this->status])
            ->andFilterWhere(['like', 'notes', $this->notes]);

return $dataProvider;
}
}