<?php

namespace backend\modules\products\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\products\models\AeGameRole as AeGameRoleModel;

/**
* AeGameRole represents the model behind the search form about `backend\modules\products\models\AeGameRole`.
*/
class AeGameRole extends AeGameRoleModel
{
/**
* @inheritdoc
*/
public function rules()
{
return [
[['id', 'game_id'], 'integer'],
            [['name'], 'safe'],
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
$query = AeGameRoleModel::find();

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
            'game_id' => $this->game_id,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]);

return $dataProvider;
}
}