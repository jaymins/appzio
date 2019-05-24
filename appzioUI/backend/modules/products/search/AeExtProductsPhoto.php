<?php

namespace backend\modules\products\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\products\models\AeExtProductsPhoto as AeExtProductsPhotoModel;

/**
* AeExtProductsPhoto represents the model behind the search form about `backend\modules\products\models\AeExtProductsPhoto`.
*/
class AeExtProductsPhoto extends AeExtProductsPhotoModel
{
/**
* @inheritdoc
*/
public function rules()
{
return [
[['id', 'product_id'], 'integer'],
            [['photo'], 'safe'],
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
$query = AeExtProductsPhotoModel::find();

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
            'product_id' => $this->product_id,
        ]);

        $query->andFilterWhere(['like', 'photo', $this->photo]);

return $dataProvider;
}
}