<?php

namespace backend\modules\tatjack\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\tatjack\models\AeExtItemsFilter as AeExtItemsFilterModel;

/**
* AeExtItemsFilter represents the model behind the search form about `backend\modules\tatjack\models\AeExtItemsFilter`.
*/
class AeExtItemsFilter extends AeExtItemsFilterModel
{
/**
* @inheritdoc
*/
public function rules()
{
return [
[['id', 'play_id', 'category_id', 'price_from', 'price_to', 'category'], 'integer'],
            [['tags'], 'safe'],
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
$query = AeExtItemsFilterModel::find();

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
            'category_id' => $this->category_id,
            'price_from' => $this->price_from,
            'price_to' => $this->price_to,
            'category' => $this->category,
        ]);

        $query->andFilterWhere(['like', 'tags', $this->tags]);

return $dataProvider;
}
}