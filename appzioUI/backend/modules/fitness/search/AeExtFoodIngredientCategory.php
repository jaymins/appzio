<?php

namespace backend\modules\fitness\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\fitness\models\AeExtFoodIngredientCategory as AeExtFoodIngredientCategoryModel;

/**
* AeExtFoodIngredientCategory represents the model behind the search form about `backend\modules\fitness\models\AeExtFoodIngredientCategory`.
*/
class AeExtFoodIngredientCategory extends AeExtFoodIngredientCategoryModel
{
/**
* @inheritdoc
*/
public function rules()
{
return [
[['id', 'app_id'], 'integer'],
            [['name', 'icon'], 'safe'],
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
$query = AeExtFoodIngredientCategoryModel::find();

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
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'icon', $this->icon]);

return $dataProvider;
}
}