<?php

namespace backend\modules\quizzes\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\quizzes\models\AeExtQuizQuestionOption as AeExtQuizQuestionOptionModel;

/**
* AeExtQuizQuestionOption represents the model behind the search form about `backend\modules\quizzes\models\AeExtQuizQuestionOption`.
*/
class AeExtQuizQuestionOption extends AeExtQuizQuestionOptionModel
{
/**
* @inheritdoc
*/
public function rules()
{
return [
[['id', 'question_id', 'answer_order', 'is_correct'], 'integer'],
            [['answer'], 'safe'],
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
$query = AeExtQuizQuestionOptionModel::find();

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
            'question_id' => $this->question_id,
            'answer_order' => $this->answer_order,
            'is_correct' => $this->is_correct,
        ]);

        $query->andFilterWhere(['like', 'answer', $this->answer]);

return $dataProvider;
}
}