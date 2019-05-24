<?php

namespace backend\modules\golf\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\golf\models\AeExtGolfHoleUser as AeExtGolfHoleUserModel;

/**
* AeExtGolfHoleUser represents the model behind the search form about `backend\modules\golf\models\AeExtGolfHoleUser`.
*/
class AeExtGolfHoleUser extends AeExtGolfHoleUserModel
{
/**
* @inheritdoc
*/
public function rules()
{
return [
[['id', 'hole_id', 'event_id', 'play_id', 'strokes'], 'integer'],
            [['comments'], 'safe'],
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
$query = AeExtGolfHoleUserModel::find();

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
            'hole_id' => $this->hole_id,
            'event_id' => $this->event_id,
            'play_id' => $this->play_id,
            'strokes' => $this->strokes,
        ]);

        $query->andFilterWhere(['like', 'comments', $this->comments]);

return $dataProvider;
}
}