<?php
/**
 * /Users/trailo/dev/aecore/app/appzioUI/console/runtime/giiant/e0080b9d6ffa35acb85312bf99a557f2
 *
 * @package default
 */


namespace backend\modules\registration\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\registration\models\AeExtMregisterCompaniesUser as AeExtMregisterCompaniesUserModel;

/**
 * AeExtMregisterCompaniesUser represents the model behind the search form about `backend\modules\registration\models\AeExtMregisterCompaniesUser`.
 */
class AeExtMregisterCompaniesUser extends AeExtMregisterCompaniesUserModel
{

	/**
	 *
	 * @inheritdoc
	 * @return unknown
	 */
	public function rules() {
		return [
			[['id', 'app_id', 'company_id', 'play_id', 'registered'], 'integer'],
			[['firstname', 'lastname', 'department', 'email', 'phone', 'registered_date'], 'safe'],
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
		$query = AeExtMregisterCompaniesUserModel::find();

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
				'company_id' => $this->company_id,
				'play_id' => $this->play_id,
				'registered' => $this->registered,
				'registered_date' => $this->registered_date,
			]);

		$query->andFilterWhere(['like', 'firstname', $this->firstname])
		->andFilterWhere(['like', 'lastname', $this->lastname])
		->andFilterWhere(['like', 'department', $this->department])
		->andFilterWhere(['like', 'email', $this->email])
		->andFilterWhere(['like', 'phone', $this->phone]);

		return $dataProvider;
	}


}
