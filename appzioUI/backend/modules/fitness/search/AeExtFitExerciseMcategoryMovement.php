<?php

namespace backend\modules\fitness\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\fitness\models\AeExtFitExerciseMcategoryMovement as AeExtFitExerciseMcategoryMovementModel;

/**
* AeExtFitExerciseMcategoryMovement represents the model behind the search form about `backend\modules\fitness\models\AeExtFitExerciseMcategoryMovement`.
*/
class AeExtFitExerciseMcategoryMovement extends AeExtFitExerciseMcategoryMovementModel
{
/**
* @inheritdoc
*/
public function rules()
{
return [
[['id', 'exercise_movement_cat_id', 'weight', 'reps', 'rest', 'pionts', 'movement_id'], 'integer'],
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
$query = AeExtFitExerciseMcategoryMovementModel::find();

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
            'exercise_movement_cat_id' => $this->exercise_movement_cat_id,
            'weight' => $this->weight,
            'reps' => $this->reps,
            'rest' => $this->rest,
            'pionts' => $this->pionts,
            'movement_id' => $this->movement_id,
        ]);

return $dataProvider;
}
}