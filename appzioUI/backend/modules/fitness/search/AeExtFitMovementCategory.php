<?php

namespace backend\modules\fitness\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\fitness\models\AeExtFitMovementCategory as AeExtFitMovementCategoryModel;

/**
* AeExtFitMovementCategory represents the model behind the search form about `backend\modules\fitness\models\AeExtFitMovementCategory`.
*/
class AeExtFitMovementCategory extends AeExtFitMovementCategoryModel
{
/**
* @inheritdoc
*/
public function rules()
{
return [
[['id'], 'integer'],
            [['name', 'timer_type', 'background_image'], 'safe'],
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
$query = AeExtFitMovementCategoryModel::find();

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
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'timer_type', $this->timer_type])
            ->andFilterWhere(['like', 'background_image', $this->background_image]);

return $dataProvider;
}
}