<?php

namespace backend\modules\fitness\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\fitness\models\AeExtFoodRecipeIngredient as AeExtFoodRecipeIngredientModel;

/**
* AeExtFoodRecipeIngredient represents the model behind the search form about `backend\modules\fitness\models\AeExtFoodRecipeIngredient`.
*/
class AeExtFoodRecipeIngredient extends AeExtFoodRecipeIngredientModel
{
/**
* @inheritdoc
*/
public function rules()
{
return [
[['id', 'recipe_id', 'ingredient_id', 'quantity'], 'integer'],
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
$query = AeExtFoodRecipeIngredientModel::find();

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
            'recipe_id' => $this->recipe_id,
            'ingredient_id' => $this->ingredient_id,
            'quantity' => $this->quantity,
        ]);

return $dataProvider;
}
}