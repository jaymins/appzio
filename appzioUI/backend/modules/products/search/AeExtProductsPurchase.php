<?php

namespace backend\modules\products\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\products\models\AeExtProductsPurchase as AeExtProductsPurchaseModel;

/**
* AeExtProductsPurchase represents the model behind the search form about `backend\modules\products\models\AeExtProductsPurchase`.
*/
class AeExtProductsPurchase extends AeExtProductsPurchaseModel
{
/**
* @inheritdoc
*/
public function rules()
{
return [
[['id', 'play_id', 'product_id', 'date'], 'integer'],
            [['price'], 'number'],
            [['status'], 'safe'],
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
$query = AeExtProductsPurchaseModel::find();

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
            'product_id' => $this->product_id,
            'date' => $this->date,
            'price' => $this->price,
        ]);

        $query->andFilterWhere(['like', 'status', $this->status]);

return $dataProvider;
}
}