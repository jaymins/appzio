<?php

namespace backend\modules\fitness\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\fitness\models\AeExtFitExercise as AeExtFitExerciseModel;

/**
* AeExtFitExercise represents the model behind the search form about `backend\modules\fitness\models\AeExtFitExercise`.
*/
class AeExtFitExercise extends AeExtFitExerciseModel
{
/**
* @inheritdoc
*/
public function rules()
{
return [
[['id', 'app_id', 'category_id', 'article_id', 'points', 'duration'], 'integer'],
            [['name'], 'safe'],
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
$query = AeExtFitExerciseModel::find();

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
            'category_id' => $this->category_id,
            'article_id' => $this->article_id,
            'points' => $this->points,
            'duration' => $this->duration,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]);

return $dataProvider;
}
}