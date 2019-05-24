<?php

namespace backend\modules\fitness\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\fitness\models\AeExtFitComponentMovement as AeExtFitComponentMovementModel;

/**
* AeExtFitComponentMovement represents the model behind the search form about `backend\modules\fitness\models\AeExtFitComponentMovement`.
*/
class AeExtFitComponentMovement extends AeExtFitComponentMovementModel
{
/**
* @inheritdoc
*/
public function rules()
{
return [
[['id', 'component_id', 'movement_id', 'weight', 'reps', 'movement_time', 'points'], 'integer'],
            [['unit'], 'safe'],
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
$query = AeExtFitComponentMovementModel::find();

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
            'component_id' => $this->component_id,
            'movement_id' => $this->movement_id,
            'weight' => $this->weight,
            'reps' => $this->reps,
            'movement_time' => $this->movement_time,
            'points' => $this->points,
        ]);

        $query->andFilterWhere(['like', 'unit', $this->unit]);

return $dataProvider;
}
}