<?php

namespace backend\modules\fitness\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\fitness\models\AeExtFitProgram as AeExtFitProgramModel;

/**
* AeExtFitProgram represents the model behind the search form about `backend\modules\fitness\models\AeExtFitProgram`.
*/
class AeExtFitProgram extends AeExtFitProgramModel
{
/**
* @inheritdoc
*/
public function rules()
{
return [
[['id', 'app_id', 'category_id', 'subcategory_id', 'article_id', 'is_challenge', 'exercises_per_day'], 'integer'],
            [['name', 'program_type', 'program_sub_type'], 'safe'],
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
$query = AeExtFitProgramModel::find();

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
            'category_id' => $this->category_id,
            'subcategory_id' => $this->subcategory_id,
            'article_id' => $this->article_id,
            'is_challenge' => $this->is_challenge,
            'exercises_per_day' => $this->exercises_per_day,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'program_type', $this->program_type])
            ->andFilterWhere(['like', 'program_sub_type', $this->program_sub_type]);

return $dataProvider;
}
}