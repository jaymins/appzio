<?php

namespace backend\modules\tickers\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\tickers\models\AeExtTickerNotification as AeExtTickerNotificationModel;

/**
* AeExtTickerNotification represents the model behind the search form about `backend\modules\tickers\models\AeExtTickerNotification`.
*/
class AeExtTickerNotification extends AeExtTickerNotificationModel
{
/**
* @inheritdoc
*/
public function rules()
{
return [
[['id', 'trade_id', 'play_id', 'notification_id', 'date', 'status'], 'integer'],
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
$query = AeExtTickerNotificationModel::find();

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
            'trade_id' => $this->trade_id,
            'play_id' => $this->play_id,
            'notification_id' => $this->notification_id,
            'date' => $this->date,
            'status' => $this->status,
        ]);

return $dataProvider;
}
}