<?php

namespace backend\modules\articles\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\articles\models\AeExtArticlePhoto as AeExtArticlePhotoModel;

/**
* AeExtArticlePhoto represents the model behind the search form about `backend\modules\articles\models\AeExtArticlePhoto`.
*/
class AeExtArticlePhoto extends AeExtArticlePhotoModel
{
/**
* @inheritdoc
*/
public function rules()
{
return [
[['id', 'article_id'], 'integer'],
            [['photo', 'position'], 'safe'],
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
$query = AeExtArticlePhotoModel::find();

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
            'article_id' => $this->article_id,
        ]);

        $query->andFilterWhere(['like', 'photo', $this->photo])
            ->andFilterWhere(['like', 'position', $this->position]);

return $dataProvider;
}
}