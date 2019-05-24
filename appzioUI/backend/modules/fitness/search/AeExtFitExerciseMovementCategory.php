<?php

namespace backend\modules\fitness\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\fitness\models\AeExtFitExerciseMovementCategory as AeExtFitExerciseMovementCategoryModel;

/**
* AeExtFitExerciseMovementCategory represents the model behind the search form about `backend\modules\fitness\models\AeExtFitExerciseMovementCategory`.
*/
class AeExtFitExerciseMovementCategory extends AeExtFitExerciseMovementCategoryModel
{
/**
* @inheritdoc
*/
public function rules()
{
return [
[['id', 'exercise_id', 'movement_category', 'rounds'], 'integer'],
            [['timer_type'], 'safe'],
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
$query = AeExtFitExerciseMovementCategoryModel::find();

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
            'movement_category' => $this->movement_category,
            'rounds' => $this->rounds,
        ]);

        $query->andFilterWhere(['like', 'timer_type', $this->timer_type]);

return $dataProvider;
}
}