<?php

namespace backend\modules\golf\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\golf\models\AeLocationLog as AeLocationLogModel;

/**
* AeLocationLog represents the model behind the search form about `backend\modules\golf\models\AeLocationLog`.
*/
class AeLocationLog extends AeLocationLogModel
{
/**
* @inheritdoc
*/
public function rules()
{
return [
[['id', 'game_id', 'play_id', 'beacon_id', 'place_id'], 'integer'],
            [['lat', 'lon'], 'number'],
            [['date'], 'safe'],
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
$query = AeLocationLogModel::find();

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
            'beacon_id' => $this->beacon_id,
            'place_id' => $this->place_id,
            'lat' => $this->lat,
            'lon' => $this->lon,
            'date' => $this->date,
        ]);

return $dataProvider;
}
}