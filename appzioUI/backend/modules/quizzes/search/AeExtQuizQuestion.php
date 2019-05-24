<?php

namespace backend\modules\quizzes\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\quizzes\models\AeExtQuizQuestion as AeExtQuizQuestionModel;

/**
* AeExtQuizQuestion represents the model behind the search form about `backend\modules\quizzes\models\AeExtQuizQuestion`.
*/
class AeExtQuizQuestion extends AeExtQuizQuestionModel
{
/**
* @inheritdoc
*/
public function rules()
{
return [
[['id', 'app_id', 'active', 'allow_multiple'], 'integer'],
            [['variable_name', 'title', 'question', 'picture', 'type'], 'safe'],
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
$query = AeExtQuizQuestionModel::find();

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
            'active' => $this->active,
            'allow_multiple' => $this->allow_multiple,
        ]);

        $query->andFilterWhere(['like', 'variable_name', $this->variable_name])
            ->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'question', $this->question])
            ->andFilterWhere(['like', 'picture', $this->picture])
            ->andFilterWhere(['like', 'type', $this->type]);

return $dataProvider;
}
}