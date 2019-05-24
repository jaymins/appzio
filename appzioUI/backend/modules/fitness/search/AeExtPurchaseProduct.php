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
use backend\modules\fitness\models\AeExtPurchaseProduct as AeExtPurchaseProductModel;

/**
 * AeExtPurchaseProduct represents the model behind the search form about `backend\modules\fitness\models\AeExtPurchaseProduct`.
 */
class AeExtPurchaseProduct extends AeExtPurchaseProductModel
{

	/**
	 *
	 * @inheritdoc
	 * @return unknown
	 */
	public function rules() {
		return [
			[['id', 'app_id', 'price'], 'integer'],
			[['name', 'type', 'code_ios', 'code_android', 'description', 'image', 'icon'], 'safe'],
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
		$query = AeExtPurchaseProductModel::find();

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
				'price' => $this->price,
			]);

		$query->andFilterWhere(['like', 'name', $this->name])
		->andFilterWhere(['like', 'type', $this->type])
		->andFilterWhere(['like', 'code_ios', $this->code_ios])
		->andFilterWhere(['like', 'code_android', $this->code_android])
		->andFilterWhere(['like', 'description', $this->description])
		->andFilterWhere(['like', 'image', $this->image])
		->andFilterWhere(['like', 'icon', $this->icon]);

		return $dataProvider;
	}


}
