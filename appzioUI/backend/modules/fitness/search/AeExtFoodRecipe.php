<?php

namespace backend\modules\fitness\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\fitness\models\AeExtFoodRecipe as AeExtFoodRecipeModel;

/**
* AeExtFoodRecipe represents the model behind the search form about `backend\modules\fitness\models\AeExtFoodRecipe`.
*/
class AeExtFoodRecipe extends AeExtFoodRecipeModel
{
/**
* @inheritdoc
*/
public function rules()
{
return [
[['id', 'serve', 'type_id'], 'integer'],
            [['name', 'difficult', 'photo'], 'safe'],
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
$query = AeExtFoodRecipeModel::find();

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
            'serve' => $this->serve,
            'type_id' => $this->type_id,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'difficult', $this->difficult])
            ->andFilterWhere(['like', 'photo', $this->photo]);

return $dataProvider;
}
}