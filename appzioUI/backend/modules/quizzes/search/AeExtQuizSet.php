<?php

namespace backend\modules\quizzes\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\quizzes\models\AeExtQuizSet as AeExtQuizSetModel;

/**
* AeExtQuizSet represents the model behind the search form about `backend\modules\quizzes\models\AeExtQuizSet`.
*/
class AeExtQuizSet extends AeExtQuizSetModel
{
/**
* @inheritdoc
*/
public function rules()
{
return [
[['id', 'app_id', 'quiz_id', 'question_id', 'sorting'], 'integer'],
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
$query = AeExtQuizSetModel::find();

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
            'quiz_id' => $this->quiz_id,
            'question_id' => $this->question_id,
            'sorting' => $this->sorting,
        ]);

return $dataProvider;
}
}