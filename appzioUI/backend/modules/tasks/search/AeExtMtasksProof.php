<?php

namespace backend\modules\tasks\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\tasks\models\AeExtMtasksProof as AeExtMtasksProofModel;

/**
* AeExtMtasksProof represents the model behind the search form about `backend\modules\tasks\models\AeExtMtasksProof`.
*/
class AeExtMtasksProof extends AeExtMtasksProofModel
{
/**
* @inheritdoc
*/
public function rules()
{
return [
[['id', 'task_id', 'created_date'], 'integer'],
            [['description', 'comment', 'status', 'photo'], 'safe'],
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
$query = AeExtMtasksProofModel::find();

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
            'task_id' => $this->task_id,
            'created_date' => $this->created_date,
        ]);

        $query->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'comment', $this->comment])
            ->andFilterWhere(['like', 'status', $this->status])
            ->andFilterWhere(['like', 'photo', $this->photo]);

return $dataProvider;
}
}