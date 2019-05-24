<?php

namespace backend\modules\tasks\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\tasks\models\AeExtMtask as AeExtMtaskModel;

/**
* AeExtMtask represents the model behind the search form about `backend\modules\tasks\models\AeExtMtask`.
*/
class AeExtMtask extends AeExtMtaskModel
{
/**
* @inheritdoc
*/
public function rules()
{
return [
[['id', 'owner_id', 'invitation_id', 'assignee_id', 'category_id', 'created_time', 'start_time', 'deadline', 'repeat_frequency', 'times_frequency', 'completion'], 'integer'],
            [['title', 'description', 'picture', 'status', 'comments'], 'safe'],
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
$query = AeExtMtaskModel::find();

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
            'owner_id' => $this->owner_id,
            'invitation_id' => $this->invitation_id,
            'assignee_id' => $this->assignee_id,
            'category_id' => $this->category_id,
            'created_time' => $this->created_time,
            'start_time' => $this->start_time,
            'deadline' => $this->deadline,
            'repeat_frequency' => $this->repeat_frequency,
            'times_frequency' => $this->times_frequency,
            'completion' => $this->completion,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'picture', $this->picture])
            ->andFilterWhere(['like', 'status', $this->status])
            ->andFilterWhere(['like', 'comments', $this->comments]);

return $dataProvider;
}
}