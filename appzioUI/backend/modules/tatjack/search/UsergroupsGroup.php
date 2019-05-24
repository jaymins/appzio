<?php

namespace backend\modules\tatjack\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\tatjack\models\UsergroupsGroup as UsergroupsGroupModel;

/**
* UsergroupsGroup represents the model behind the search form about `backend\modules\tatjack\models\UsergroupsGroup`.
*/
class UsergroupsGroup extends UsergroupsGroupModel
{
/**
* @inheritdoc
*/
public function rules()
{
return [
[['id', 'level'], 'integer'],
            [['groupname', 'home'], 'safe'],
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
$query = UsergroupsGroupModel::find();

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
            'level' => $this->level,
        ]);

        $query->andFilterWhere(['like', 'groupname', $this->groupname])
            ->andFilterWhere(['like', 'home', $this->home]);

return $dataProvider;
}
}