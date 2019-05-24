<?php

namespace backend\modules\tatjack\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\tatjack\models\AeExtBidItem as AeExtBidItemModel;

/**
* AeExtBidItem represents the model behind the search form about `backend\modules\tatjack\models\AeExtBidItem`.
*/
class AeExtBidItem extends AeExtBidItemModel
{
/**
* @inheritdoc
*/
public function rules()
{
return [
[['id', 'play_id', 'valid_date'], 'integer'],
            [['title', 'description', 'styles', 'status'], 'safe'],
            [['lat', 'lon'], 'number'],
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
$query = AeExtBidItemModel::find();

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
            'valid_date' => $this->valid_date,
            'lat' => $this->lat,
            'lon' => $this->lon,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'styles', $this->styles])
            ->andFilterWhere(['like', 'status', $this->status]);

return $dataProvider;
}
}