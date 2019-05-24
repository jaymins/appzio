<?php

namespace backend\modules\quizzes\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\quizzes\models\AeExtQuiz as AeExtQuizModel;

/**
* AeExtQuiz represents the model behind the search form about `backend\modules\quizzes\models\AeExtQuiz`.
*/
class AeExtQuiz extends AeExtQuizModel
{
/**
* @inheritdoc
*/
public function rules()
{
return [
[['id', 'app_id', 'valid_from', 'valid_to', 'active', 'show_in_list'], 'integer'],
            [['name', 'description', 'image'], 'safe'],
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
$query = AeExtQuizModel::find();

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
            'valid_from' => $this->valid_from,
            'valid_to' => $this->valid_to,
            'active' => $this->active,
            'show_in_list' => $this->show_in_list,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'image', $this->image]);

return $dataProvider;
}
}