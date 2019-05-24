<?php

namespace backend\modules\tickers\search;

use backend\modules\tickers\models\AeExtTicker as AeExtTickerModel;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * AeExtTicker represents the model behind the search form about `backend\modules\tickers\models\AeExtTicker`.
 */
class AeExtTicker extends AeExtTickerModel
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'ticker_date', 'last_update'], 'integer'],
            [['ticker', 'company', 'currency', 'exchange', 'exchange_name', 'overall'], 'safe'],
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
        $query = AeExtTickerModel::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
// uncomment the following line if you do not want to any records when validation fails
// $query->where('0=1');
            return $dataProvider;
        }

        $overall_query = '';

        if ( strtolower($this->overall) == 'yes' ) {
            $overall_query = 1;
        } else if ( strtolower($this->overall) == 'no' ) {
            $overall_query = 0;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'exchange' => $this->exchange,
            'currency' => $this->currency,
            'ticker_date' => $this->ticker_date,
            'overall' => $overall_query,
            'last_update' => $this->last_update,
        ]);

        $query->andFilterWhere(['like', 'ticker', strtoupper($this->ticker)])
            ->andFilterWhere(['like', 'company', $this->company]);

        return $dataProvider;
    }
}