<?php
/**
 * /Users/trailo/dev/aecore/app/appzioUI/console/runtime/giiant/e0080b9d6ffa35acb85312bf99a557f2
 *
 * @package default
 */


namespace backend\modules\quizzes\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\quizzes\models\AeExtQuizQuestionAnswer as AeExtQuizQuestionAnswerModel;

/**
 * AeExtQuizQuestionAnswer represents the model behind the search form about `backend\modules\quizzes\models\AeExtQuizQuestionAnswer`.
 */
class AeExtQuizQuestionAnswer extends AeExtQuizQuestionAnswerModel
{

	/**
	 *
	 * @inheritdoc
	 * @return unknown
	 */
	public function rules() {
		return [
			[['id', 'question_id', 'answer_id', 'play_id'], 'integer'],
			[['answer', 'comment', 'date_created'], 'safe'],
		];
	}


	/**
	 *
	 * @inheritdoc
	 * @return unknown
	 */
	public function scenarios() {
		// bypass scenarios() implementation in the parent class
		return Model::scenarios();
	}


	/**
	 * Creates data provider instance with search query applied
	 *
	 *
	 * @param array   $params
	 * @return ActiveDataProvider
	 */
	public function search($params) {
		$query = AeExtQuizQuestionAnswerModel::find();

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
				'question_id' => $this->question_id,
				'answer_id' => $this->answer_id,
				'play_id' => $this->play_id,
				'date_created' => $this->date_created,
			]);

		$query->andFilterWhere(['like', 'answer', $this->answer])
		->andFilterWhere(['like', 'comment', $this->comment]);

		return $dataProvider;
	}


}
