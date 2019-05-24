<?php

namespace backend\modules\articles\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\articles\models\AeExtArticleComment as AeExtArticleCommentModel;

/**
* AeExtArticleComment represents the model behind the search form about `backend\modules\articles\models\AeExtArticleComment`.
*/
class AeExtArticleComment extends AeExtArticleCommentModel
{
/**
* @inheritdoc
*/
public function rules()
{
return [
[['id', 'play_id', 'rating'], 'integer'],
            [['comment'], 'safe'],
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
$query = AeExtArticleCommentModel::find();

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
            'rating' => $this->rating,
        ]);

        $query->andFilterWhere(['like', 'comment', $this->comment]);

return $dataProvider;
}
}