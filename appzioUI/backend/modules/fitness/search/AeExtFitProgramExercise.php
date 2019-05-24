<?php

namespace backend\modules\fitness\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\fitness\models\AeExtFitProgramExercise as AeExtFitProgramExerciseModel;

/**
* AeExtFitProgramExercise represents the model behind the search form about `backend\modules\fitness\models\AeExtFitProgramExercise`.
*/
class AeExtFitProgramExercise extends AeExtFitProgramExerciseModel
{
/**
* @inheritdoc
*/
public function rules()
{
return [
[['id', 'program_id', 'exercise_id', 'week', 'day'], 'integer'],
            [['time', 'repeat_days'], 'safe'],
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
$query = AeExtFitProgramExerciseModel::find();

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
            'exercise_id' => $this->exercise_id,
            'week' => $this->week,
            'day' => $this->day,
        ]);

        $query->andFilterWhere(['like', 'time', $this->time])
            ->andFilterWhere(['like', 'repeat_days', $this->repeat_days]);

return $dataProvider;
}
}