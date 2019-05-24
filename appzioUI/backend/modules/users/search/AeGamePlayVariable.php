<?php

namespace backend\modules\users\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\users\models\AeGamePlayVariable as AeGamePlayVariableModel;

/**
* AeGamePlayVariable represents the model behind the search form about `backend\modules\users\models\AeGamePlayVariable`.
*/
class AeGamePlayVariable extends AeGamePlayVariableModel
{
/**
* @inheritdoc
*/
public function rules()
{
return [
[['id', 'play_id', 'variable_id'], 'integer'],
            [['name', 'value', 'parameters'], 'safe'],
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
$query = AeGamePlayVariableModel::find();

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
            'variable_id' => $this->variable_id,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'value', $this->value])
            ->andFilterWhere(['like', 'parameters', $this->parameters]);

return $dataProvider;
}
}