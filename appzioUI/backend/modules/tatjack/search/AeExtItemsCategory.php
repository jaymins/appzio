<?php

namespace backend\modules\tatjack\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\tatjack\models\AeExtItemsCategory as AeExtItemsCategoryModel;

/**
* AeExtItemsCategory represents the model behind the search form about `backend\modules\tatjack\models\AeExtItemsCategory`.
*/
class AeExtItemsCategory extends AeExtItemsCategoryModel
{
/**
* @inheritdoc
*/
public function rules()
{
return [
[['id', 'app_id', 'parent_id'], 'integer'],
            [['picture', 'name'], 'safe'],
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
$query = AeExtItemsCategoryModel::find();

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
            'app_id' => $this->app_id,
            'parent_id' => $this->parent_id,
        ]);

        $query->andFilterWhere(['like', 'picture', $this->picture])
            ->andFilterWhere(['like', 'name', $this->name]);

return $dataProvider;
}
}