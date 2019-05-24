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
use backend\modules\registration\models\AeExtMregisterCompany as AeExtMregisterCompanyModel;

/**
 * AeExtMregisterCompany represents the model behind the search form about `backend\modules\registration\models\AeExtMregisterCompany`.
 */
class AeExtMregisterCompany extends AeExtMregisterCompanyModel
{

	/**
	 *
	 * @inheritdoc
	 * @return unknown
	 */
	public function rules() {
		return [
			[['id', 'app_id', 'subscription_active', 'subscription_expires', 'user_limit'], 'integer'],
			[['name', 'notes'], 'safe'],
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
		$query = AeExtMregisterCompanyModel::find();

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
				'subscription_active' => $this->subscription_active,
				'subscription_expires' => $this->subscription_expires,
				'user_limit' => $this->user_limit,
			]);

		$query->andFilterWhere(['like', 'name', $this->name])
		->andFilterWhere(['like', 'notes', $this->notes]);

		return $dataProvider;
	}


}
