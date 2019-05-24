<?php

namespace backend\modules\tickers\search;

use backend\modules\tickers\models\AeExtTicker as AeExtTickerModel;
use backend\modules\tickers\models\AeExtTickerTrade as AeExtTickerTradeModel;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * AeExtTickerTrade represents the model behind the search form about `backend\modules\tickers\models\AeExtTickerTrade`.
 */
class AeExtTickerTrade extends AeExtTickerTradeModel
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'trade_date', 'stop_date'], 'integer'],
            [['buy_range_from', 'buy_range_to', 'sell_range_from', 'sell_range_to'], 'number'],
            [['term', 'ticker_id', 'trade_notes', 'active'], 'safe'],
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
        $query = AeExtTickerTradeModel::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
// uncomment the following line if you do not want to any records when validation fails
// $query->where('0=1');
            return $dataProvider;
        }

        $active = '';

        if ( strtolower($this->active) == 'yes' ) {
            $active = 1;
        } else if ( strtolower($this->active) == 'no' ) {
            $active = 0;
        }

        $ticker_id = null;

        if ( !empty($this->ticker_id) ) {
            $ticker_name = strtoupper($this->ticker_id);
            $result = AeExtTickerModel::findBySql('SELECT id FROM ae_ext_ticker WHERE ticker LIKE "%' . $ticker_name . '%" LIMIT 1')->one();

            if ( $result AND isset($result->id) AND $result->id )
                $ticker_id = $result->id;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'ticker_id' => $ticker_id,
            'buy_range_from' => $this->buy_range_from,
            'buy_range_to' => $this->buy_range_to,
            'sell_range_from' => $this->sell_range_from,
            'sell_range_to' => $this->sell_range_to,
            'trade_date' => $this->trade_date,
            'active' => $active,
            'stop_date' => $this->stop_date,
        ]);

        $query->andFilterWhere(['like', 'term', $this->term])
            ->andFilterWhere(['like', 'trade_notes', $this->trade_notes]);

        return $dataProvider;
    }
}