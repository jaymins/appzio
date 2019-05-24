<?php

namespace backend\modules\tickers\search;

use backend\modules\tickers\models\AeExtTickerDaily as AeExtTickerDailyModel;
use backend\modules\tickers\models\AeExtTicker as AeExtTickerModel;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * AeExtTickerDaily represents the model behind the search form about `backend\modules\tickers\models\AeExtTickerDaily`.
 */
class AeExtTickerDaily extends AeExtTickerDailyModel
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'timestamp', 'volume', 'date', 'ref_lock'], 'integer'],
            [['ticker', 'ticker_id'], 'safe'],
            [['open', 'high', 'low', 'value', 'previousclose', 'valuechange'], 'number'],
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
        $query = AeExtTickerDailyModel::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
// uncomment the following line if you do not want to any records when validation fails
// $query->where('0=1');
            return $dataProvider;
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
            'timestamp' => $this->timestamp,
            'open' => $this->open,
            'high' => $this->high,
            'low' => $this->low,
            'value' => $this->value,
            'volume' => $this->volume,
            'previousclose' => $this->previousclose,
            'valuechange' => $this->valuechange,
            'date' => $this->date,
            'ref_lock' => $this->ref_lock,
        ]);

        $query->andFilterWhere(['like', 'ticker', $this->ticker]);

        return $dataProvider;
    }
}