<?php

namespace backend\modules\tatjack\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\tatjack\models\AeExtItemsTag as AeExtItemsTagModel;

/**
* AeExtItemsTag represents the model behind the search form about `backend\modules\tatjack\models\AeExtItemsTag`.
*/
class AeExtItemsTag extends AeExtItemsTagModel
{
/**
* @inheritdoc
*/
public function rules()
{
return [
[['id'], 'integer'],
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
$query = AeExtItemsTagModel::find();

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
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]);

return $dataProvider;
}
}