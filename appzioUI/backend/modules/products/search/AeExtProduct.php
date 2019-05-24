<?php

namespace backend\modules\products\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\products\models\AeExtProduct as AeExtProductModel;

/**
* AeExtProduct represents the model behind the search form about `backend\modules\products\models\AeExtProduct`.
*/
class AeExtProduct extends AeExtProductModel
{
/**
* @inheritdoc
*/
public function rules()
{
return [
[['id', 'app_id', 'category_id', 'play_id', 'rating', 'points_value'], 'integer'],
            [['photo', 'additional_photos', 'title', 'header', 'description', 'link'], 'safe'],
            [['price'], 'number'],
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
$query = AeExtProductModel::find();

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
            'app_id' => $this->app_id,
            'category_id' => $this->category_id,
            'play_id' => $this->play_id,
            'rating' => $this->rating,
            'price' => $this->price,
            'points_value' => $this->points_value,
        ]);

        $query->andFilterWhere(['like', 'photo', $this->photo])
            ->andFilterWhere(['like', 'additional_photos', $this->additional_photos])
            ->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'header', $this->header])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'link', $this->link]);

return $dataProvider;
}
}