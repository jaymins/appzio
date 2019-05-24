<?php

namespace backend\modules\fitness\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\fitness\models\AeExtFitProgramRecipe as AeExtFitProgramRecipeModel;

/**
* AeExtFitProgramRecipe represents the model behind the search form about `backend\modules\fitness\models\AeExtFitProgramRecipe`.
*/
class AeExtFitProgramRecipe extends AeExtFitProgramRecipeModel
{
/**
* @inheritdoc
*/
public function rules()
{
return [
[['id', 'program_id', 'recipe_id', 'recipe_order'], 'integer'],
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
$query = AeExtFitProgramRecipeModel::find();

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
            'program_id' => $this->program_id,
            'recipe_id' => $this->recipe_id,
            'recipe_order' => $this->recipe_order,
        ]);

return $dataProvider;
}
}