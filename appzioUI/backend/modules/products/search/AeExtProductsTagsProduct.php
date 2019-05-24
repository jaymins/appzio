<?php

namespace backend\modules\products\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\products\models\AeExtProductsTagsProduct as AeExtProductsTagsProductModel;

/**
* AeExtProductsTagsProduct represents the model behind the search form about `backend\modules\products\models\AeExtProductsTagsProduct`.
*/
class AeExtProductsTagsProduct extends AeExtProductsTagsProductModel
{
/**
* @inheritdoc
*/
public function rules()
{
return [
[['tag_id', 'product_id'], 'integer'],
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
$query = AeExtProductsTagsProductModel::find();

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
            'tag_id' => $this->tag_id,
            'product_id' => $this->product_id,
        ]);

return $dataProvider;
}
}