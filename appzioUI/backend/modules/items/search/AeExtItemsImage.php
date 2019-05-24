<?php

namespace backend\modules\items\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\items\models\AeExtItemsImage as AeExtItemsImageModel;

/**
* AeExtItemsImage represents the model behind the search form about `backend\modules\items\models\AeExtItemsImage`.
*/
class AeExtItemsImage extends AeExtItemsImageModel
{
/**
* @inheritdoc
*/
public function rules()
{
return [
[['id', 'item_id', 'date', 'featured', 'image_order'], 'integer'],
            [['image'], 'safe'],
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
$query = AeExtItemsImageModel::find();

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
            'item_id' => $this->item_id,
            'date' => $this->date,
            'featured' => $this->featured,
            'image_order' => $this->image_order,
        ]);

        $query->andFilterWhere(['like', 'image', $this->image]);

return $dataProvider;
}
}