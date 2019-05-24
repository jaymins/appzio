<?php

namespace backend\modules\golf\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\golf\models\AeExtMobileplace as AeExtMobileplaceModel;

/**
* AeExtMobileplace represents the model behind the search form about `backend\modules\golf\models\AeExtMobileplace`.
*/
class AeExtMobileplace extends AeExtMobileplaceModel
{
/**
* @inheritdoc
*/
public function rules()
{
return [
[['id', 'game_id', 'zip', 'premium'], 'integer'],
            [['lat', 'lon'], 'number'],
            [['last_update', 'name', 'address', 'city', 'county', 'country', 'info', 'logo', 'images', 'headerimage1', 'headerimage2', 'headerimage3'], 'safe'],
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
$query = AeExtMobileplaceModel::find();

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
            'lat' => $this->lat,
            'lon' => $this->lon,
            'last_update' => $this->last_update,
            'zip' => $this->zip,
            'premium' => $this->premium,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'address', $this->address])
            ->andFilterWhere(['like', 'city', $this->city])
            ->andFilterWhere(['like', 'county', $this->county])
            ->andFilterWhere(['like', 'country', $this->country])
            ->andFilterWhere(['like', 'info', $this->info])
            ->andFilterWhere(['like', 'logo', $this->logo])
            ->andFilterWhere(['like', 'images', $this->images])
            ->andFilterWhere(['like', 'headerimage1', $this->headerimage1])
            ->andFilterWhere(['like', 'headerimage2', $this->headerimage2])
            ->andFilterWhere(['like', 'headerimage3', $this->headerimage3]);

return $dataProvider;
}
}