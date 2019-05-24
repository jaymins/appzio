<?php

namespace backend\modules\fitness\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\fitness\models\AeExtFoodIngredient as AeExtFoodIngredientModel;

/**
* AeExtFoodIngredient represents the model behind the search form about `backend\modules\fitness\models\AeExtFoodIngredient`.
*/
class AeExtFoodIngredient extends AeExtFoodIngredientModel
{
/**
* @inheritdoc
*/
public function rules()
{
return [
[['id', 'category_id'], 'integer'],
            [['name', 'unit'], 'safe'],
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
$query = AeExtFoodIngredientModel::find();

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
            'category_id' => $this->category_id,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'unit', $this->unit]);

return $dataProvider;
}
}