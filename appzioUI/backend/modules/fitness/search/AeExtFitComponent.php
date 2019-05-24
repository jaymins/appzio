<?php

namespace backend\modules\fitness\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\fitness\models\AeExtFitComponent as AeExtFitComponentModel;

/**
* AeExtFitComponent represents the model behind the search form about `backend\modules\fitness\models\AeExtFitComponent`.
*/
class AeExtFitComponent extends AeExtFitComponentModel
{
/**
* @inheritdoc
*/
public function rules()
{
return [
[['id', 'rounds'], 'integer'],
            [['name', 'admin_name', 'background_image', 'timer'], 'safe'],
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
$query = AeExtFitComponentModel::find();

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
            'rounds' => $this->rounds,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'admin_name', $this->admin_name])
            ->andFilterWhere(['like', 'background_image', $this->background_image])
            ->andFilterWhere(['like', 'timer', $this->timer]);

return $dataProvider;
}
}