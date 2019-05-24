<?php

namespace backend\modules\tatjack\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\tatjack\models\AeExtItemsReport as AeExtItemsReportModel;

/**
* AeExtItemsReport represents the model behind the search form about `backend\modules\tatjack\models\AeExtItemsReport`.
*/
class AeExtItemsReport extends AeExtItemsReportModel
{
/**
* @inheritdoc
*/
public function rules()
{
return [
[['id', 'play_id', 'item_id', 'item_owner_id'], 'integer'],
            [['reason', 'created_at', 'updated_at'], 'safe'],
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
$query = AeExtItemsReportModel::find();

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
            'play_id' => $this->play_id,
            'item_id' => $this->item_id,
            'item_owner_id' => $this->item_owner_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'reason', $this->reason]);

return $dataProvider;
}
}