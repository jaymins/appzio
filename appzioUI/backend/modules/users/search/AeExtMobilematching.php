<?php

namespace backend\modules\users\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\users\models\AeExtMobilematching as AeExtMobilematchingModel;

/**
* AeExtMobilematching represents the model behind the search form about `backend\modules\users\models\AeExtMobilematching`.
*/
class AeExtMobilematching extends AeExtMobilematchingModel
{
/**
* @inheritdoc
*/
public function rules()
{
return [
[['id', 'game_id', 'play_id', 'match_always', 'score', 'flag', 'education', 'is_boosted', 'boosted_timestamp'], 'integer'],
            [['lat', 'lon'], 'number'],
            [['last_update', 'gender', 'hindu_caste', 'role'], 'safe'],
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
$query = AeExtMobilematchingModel::find();

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
            'lat' => $this->lat,
            'lon' => $this->lon,
            'last_update' => $this->last_update,
            'match_always' => $this->match_always,
            'score' => $this->score,
            'flag' => $this->flag,
            'education' => $this->education,
            'is_boosted' => $this->is_boosted,
            'boosted_timestamp' => $this->boosted_timestamp,
        ]);

        $query->andFilterWhere(['like', 'gender', $this->gender])
            ->andFilterWhere(['like', 'hindu_caste', $this->hindu_caste])
            ->andFilterWhere(['like', 'role', $this->role]);

return $dataProvider;
}
}