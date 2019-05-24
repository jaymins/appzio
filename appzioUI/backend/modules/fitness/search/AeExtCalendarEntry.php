<?php

namespace backend\modules\fitness\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\fitness\models\AeExtCalendarEntry as AeExtCalendarEntryModel;

/**
* AeExtCalendarEntry represents the model behind the search form about `backend\modules\fitness\models\AeExtCalendarEntry`.
*/
class AeExtCalendarEntry extends AeExtCalendarEntryModel
{
/**
* @inheritdoc
*/
public function rules()
{
return [
[['id', 'play_id', 'type_id', 'exercise_id', 'program_id', 'notes', 'points', 'time', 'completion', 'is_completed'], 'integer'],
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
$query = AeExtCalendarEntryModel::find();

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
            'play_id' => $this->play_id,
            'type_id' => $this->type_id,
            'exercise_id' => $this->exercise_id,
            'program_id' => $this->program_id,
            'notes' => $this->notes,
            'points' => $this->points,
            'time' => $this->time,
            'completion' => $this->completion,
            'is_completed' => $this->is_completed,
        ]);

return $dataProvider;
}
}