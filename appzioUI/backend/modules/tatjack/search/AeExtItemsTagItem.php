<?php

namespace backend\modules\tatjack\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\tatjack\models\AeExtItemsTagItem as AeExtItemsTagItemModel;

/**
* AeExtItemsTagItem represents the model behind the search form about `backend\modules\tatjack\models\AeExtItemsTagItem`.
*/
class AeExtItemsTagItem extends AeExtItemsTagItemModel
{
/**
* @inheritdoc
*/
public function rules()
{
return [
[['id', 'item_id', 'tag_id'], 'integer'],
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
$query = AeExtItemsTagItemModel::find();

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
            'tag_id' => $this->tag_id,
        ]);

return $dataProvider;
}
}