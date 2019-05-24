<?php

namespace backend\modules\fitness\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\fitness\models\AeExtFoodRecipeStep as AeExtFoodRecipeStepModel;

/**
* AeExtFoodRecipeStep represents the model behind the search form about `backend\modules\fitness\models\AeExtFoodRecipeStep`.
*/
class AeExtFoodRecipeStep extends AeExtFoodRecipeStepModel
{
/**
* @inheritdoc
*/
public function rules()
{
return [
[['id', 'recipe_id', 'time'], 'integer'],
            [['description'], 'safe'],
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
$query = AeExtFoodRecipeStepModel::find();

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
            'time' => $this->time,
        ]);

        $query->andFilterWhere(['like', 'description', $this->description]);

return $dataProvider;
}
}