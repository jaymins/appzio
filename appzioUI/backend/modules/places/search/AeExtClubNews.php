<?php

namespace backend\modules\places\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\places\models\AeExtClubNews as AeExtClubNewsModel;

/**
* AeExtClubNews represents the model behind the search form about `backend\modules\places\models\AeExtClubNews`.
*/
class AeExtClubNews extends AeExtClubNewsModel
{
/**
* @inheritdoc
*/
public function rules()
{
return [
[['id', 'club_id'], 'integer'],
            [['name', 'description', 'photo', 'link_url'], 'safe'],
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
$query = AeExtClubNewsModel::find();

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
            'club_id' => $this->club_id,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'photo', $this->photo])
            ->andFilterWhere(['like', 'link_url', $this->link_url]);

return $dataProvider;
}
}