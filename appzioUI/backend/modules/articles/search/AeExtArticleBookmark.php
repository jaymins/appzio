<?php

namespace backend\modules\articles\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\articles\models\AeExtArticleBookmark as AeExtArticleBookmarkModel;

/**
* AeExtArticleBookmark represents the model behind the search form about `backend\modules\articles\models\AeExtArticleBookmark`.
*/
class AeExtArticleBookmark extends AeExtArticleBookmarkModel
{
/**
* @inheritdoc
*/
public function rules()
{
return [
[['id', 'play_id', 'article_id'], 'integer'],
            [['type'], 'safe'],
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
$query = AeExtArticleBookmarkModel::find();

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
            'article_id' => $this->article_id,
        ]);

        $query->andFilterWhere(['like', 'type', $this->type]);

return $dataProvider;
}
}