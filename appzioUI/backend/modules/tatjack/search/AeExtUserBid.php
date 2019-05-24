<?php

namespace backend\modules\tatjack\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\tatjack\models\AeExtUserBid as AeExtUserBidModel;

/**
* AeExtUserBid represents the model behind the search form about `backend\modules\tatjack\models\AeExtUserBid`.
*/
class AeExtUserBid extends AeExtUserBidModel
{
/**
* @inheritdoc
*/
public function rules()
{
return [
[['id', 'bid_item_id', 'play_id', 'created_date'], 'integer'],
            [['price'], 'number'],
            [['message', 'status'], 'safe'],
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
$query = AeExtUserBidModel::find();

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
            'bid_item_id' => $this->bid_item_id,
            'play_id' => $this->play_id,
            'price' => $this->price,
            'created_date' => $this->created_date,
        ]);

        $query->andFilterWhere(['like', 'message', $this->message])
            ->andFilterWhere(['like', 'status', $this->status]);

return $dataProvider;
}
}