<?php

namespace backend\modules\tasks\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\tasks\models\AeExtMtasksInvitation as AeExtMtasksInvitationModel;

/**
* AeExtMtasksInvitation represents the model behind the search form about `backend\modules\tasks\models\AeExtMtasksInvitation`.
*/
class AeExtMtasksInvitation extends AeExtMtasksInvitationModel
{
/**
* @inheritdoc
*/
public function rules()
{
return [
[['id', 'play_id', 'invited_play_id', 'primary_contact'], 'integer'],
            [['name', 'email', 'nickname', 'status'], 'safe'],
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
$query = AeExtMtasksInvitationModel::find();

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
            'invited_play_id' => $this->invited_play_id,
            'primary_contact' => $this->primary_contact,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'nickname', $this->nickname])
            ->andFilterWhere(['like', 'status', $this->status]);

return $dataProvider;
}
}