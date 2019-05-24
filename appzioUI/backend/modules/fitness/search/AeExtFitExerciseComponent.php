<?php

namespace backend\modules\fitness\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\fitness\models\AeExtFitExerciseComponent as AeExtFitExerciseComponentModel;

/**
* AeExtFitExerciseComponent represents the model behind the search form about `backend\modules\fitness\models\AeExtFitExerciseComponent`.
*/
class AeExtFitExerciseComponent extends AeExtFitExerciseComponentModel
{
/**
* @inheritdoc
*/
public function rules()
{
return [
[['id', 'exercise_id', 'component_id'], 'integer'],
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
$query = AeExtFitExerciseComponentModel::find();

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
            'exercise_id' => $this->exercise_id,
            'component_id' => $this->component_id,
        ]);

return $dataProvider;
}
}