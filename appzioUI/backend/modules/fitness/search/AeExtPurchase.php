<?php
/**
 * /Users/trailo/dev/aecore/app/appzioUI/console/runtime/giiant/e0080b9d6ffa35acb85312bf99a557f2
 *
 * @package default
 */


namespace backend\modules\fitness\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\fitness\models\AeExtPurchase as AeExtPurchaseModel;

/**
 * AeExtPurchase represents the model behind the search form about `backend\modules\fitness\models\AeExtPurchase`.
 */
class AeExtPurchase extends AeExtPurchaseModel
{

	/**
	 *
	 * @inheritdoc
	 * @return unknown
	 */
	public function rules() {
		return [
			[['id', 'app_id', 'play_id', 'product_id', 'price', 'subscription', 'expiry'], 'integer'],
			[['currency', 'type', 'date', 'store_id', 'receipt', 'subject', 'email'], 'safe'],
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
		$query = AeExtPurchaseModel::find();

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
				'play_id' => $this->play_id,
				'product_id' => $this->product_id,
				'price' => $this->price,
				'date' => $this->date,
				'subscription' => $this->subscription,
				'expiry' => $this->expiry,
			]);

		$query->andFilterWhere(['like', 'currency', $this->currency])
		->andFilterWhere(['like', 'type', $this->type])
		->andFilterWhere(['like', 'store_id', $this->store_id])
		->andFilterWhere(['like', 'receipt', $this->receipt])
		->andFilterWhere(['like', 'subject', $this->subject])
		->andFilterWhere(['like', 'email', $this->email]);

		return $dataProvider;
	}


}
