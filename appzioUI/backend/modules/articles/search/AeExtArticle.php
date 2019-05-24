<?php

namespace backend\modules\articles\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\articles\models\AeExtArticle as AeExtArticleModel;

/**
* AeExtArticle represents the model behind the search form about `backend\modules\articles\models\AeExtArticle`.
*/
class AeExtArticle extends AeExtArticleModel
{
/**
* @inheritdoc
*/
public function rules()
{
return [
[['id', 'app_id', 'category_id', 'play_id', 'rating', 'featured'], 'integer'],
            [['title', 'header', 'content', 'link', 'article_date'], 'safe'],
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
$query = AeExtArticleModel::find();

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
            'play_id' => $this->play_id,
            'rating' => $this->rating,
            'featured' => $this->featured,
            'article_date' => $this->article_date,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'header', $this->header])
            ->andFilterWhere(['like', 'content', $this->content])
            ->andFilterWhere(['like', 'link', $this->link]);

return $dataProvider;
}
}