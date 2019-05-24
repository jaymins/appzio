<?php

namespace backend\modules\golf\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\golf\models\AeExtMobileeventsParticipant as AeExtMobileeventsParticipantModel;

/**
* AeExtMobileeventsParticipant represents the model behind the search form about `backend\modules\golf\models\AeExtMobileeventsParticipant`.
*/
class AeExtMobileeventsParticipant extends AeExtMobileeventsParticipantModel
{
/**
* @inheritdoc
*/
public function rules()
{
return [
[['id', 'play_id', 'event_id'], 'integer'],
            [['status'], 'safe'],
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
$query = AeExtMobileeventsParticipantModel::find();

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
            'event_id' => $this->event_id,
        ]);

        $query->andFilterWhere(['like', 'status', $this->status]);

return $dataProvider;
}
}