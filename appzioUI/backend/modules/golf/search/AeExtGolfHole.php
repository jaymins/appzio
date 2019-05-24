<?php

namespace backend\modules\golf\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\golf\models\AeExtGolfHole as AeExtGolfHoleModel;

/**
* AeExtGolfHole represents the model behind the search form about `backend\modules\golf\models\AeExtGolfHole`.
*/
class AeExtGolfHole extends AeExtGolfHoleModel
{
/**
* @inheritdoc
*/
public function rules()
{
return [
[['id', 'place_id', 'number', 'par', 'hcp', 'length_pro', 'length_men', 'length_women', 'length_junior'], 'integer'],
            [['type', 'beacon_id', 'map', 'map_approach', 'comments'], 'safe'],
            [['tee_lat', 'tee_lon', 'flag_lat', 'flag_lon'], 'number'],
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
$query = AeExtGolfHoleModel::find();

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
            'place_id' => $this->place_id,
            'number' => $this->number,
            'par' => $this->par,
            'hcp' => $this->hcp,
            'tee_lat' => $this->tee_lat,
            'tee_lon' => $this->tee_lon,
            'flag_lat' => $this->flag_lat,
            'flag_lon' => $this->flag_lon,
            'length_pro' => $this->length_pro,
            'length_men' => $this->length_men,
            'length_women' => $this->length_women,
            'length_junior' => $this->length_junior,
        ]);

        $query->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['like', 'beacon_id', $this->beacon_id])
            ->andFilterWhere(['like', 'map', $this->map])
            ->andFilterWhere(['like', 'map_approach', $this->map_approach])
            ->andFilterWhere(['like', 'comments', $this->comments]);

return $dataProvider;
}
}