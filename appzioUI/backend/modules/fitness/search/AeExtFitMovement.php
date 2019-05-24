<?php

namespace backend\modules\fitness\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\fitness\models\AeExtFitMovement as AeExtFitMovementModel;

/**
* AeExtFitMovement represents the model behind the search form about `backend\modules\fitness\models\AeExtFitMovement`.
*/
class AeExtFitMovement extends AeExtFitMovementModel
{
/**
* @inheritdoc
*/
public function rules()
{
return [
[['id', 'app_id', 'article_id'], 'integer'],
            [['name', 'description', 'video_url'], 'safe'],
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
$query = AeExtFitMovementModel::find();

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
            'article_id' => $this->article_id,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'video_url', $this->video_url]);

return $dataProvider;
}
}