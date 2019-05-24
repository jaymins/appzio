<?php

namespace backend\modules\tatjack\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\tatjack\models\AeExtItem as AeExtItemModel;

/**
* AeExtItem represents the model behind the search form about `backend\modules\tatjack\models\AeExtItem`.
*/
class AeExtItem extends AeExtItemModel
{
/**
* @inheritdoc
*/
public function rules()
{
return [
[['id', 'play_id', 'game_id', 'category_id', 'date_added', 'featured', 'external', 'buyer_play_id', 'importa_date'], 'integer'],
            [['name', 'description', 'time', 'images', 'status', 'city', 'country', 'source', 'external_id'], 'safe'],
            [['price', 'lat', 'lon'], 'number'],
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
$query = AeExtItemModel::find();

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
            'play_id' => $this->play_id,
            'game_id' => $this->game_id,
            'category_id' => $this->category_id,
            'price' => $this->price,
            'date_added' => $this->date_added,
            'featured' => $this->featured,
            'external' => $this->external,
            'lat' => $this->lat,
            'lon' => $this->lon,
            'buyer_play_id' => $this->buyer_play_id,
            'importa_date' => $this->importa_date,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'time', $this->time])
            ->andFilterWhere(['like', 'images', $this->images])
            ->andFilterWhere(['like', 'status', $this->status])
            ->andFilterWhere(['like', 'city', $this->city])
            ->andFilterWhere(['like', 'country', $this->country])
            ->andFilterWhere(['like', 'source', $this->source])
            ->andFilterWhere(['like', 'external_id', $this->external_id]);

return $dataProvider;
}
}