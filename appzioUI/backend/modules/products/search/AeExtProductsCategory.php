<?php

namespace backend\modules\products\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\products\models\AeExtProductsCategory as AeExtProductsCategoryModel;

/**
* AeExtProductsCategory represents the model behind the search form about `backend\modules\products\models\AeExtProductsCategory`.
*/
class AeExtProductsCategory extends AeExtProductsCategoryModel
{
/**
* @inheritdoc
*/
public function rules()
{
return [
[['id', 'app_id', 'sorting'], 'integer'],
            [['title', 'headertext', 'icon'], 'safe'],
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
$query = AeExtProductsCategoryModel::find();

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
            'sorting' => $this->sorting,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'headertext', $this->headertext])
            ->andFilterWhere(['like', 'icon', $this->icon]);

return $dataProvider;
}
}