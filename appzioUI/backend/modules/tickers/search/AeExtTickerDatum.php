<?php

namespace backend\modules\tickers\search;

use backend\modules\tickers\models\AeExtTickerDatum as AeExtTickerDatumModel;
use backend\modules\tickers\models\AeExtTicker as AeExtTickerModel;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * AeExtTickerDatum represents the model behind the search form about `backend\modules\tickers\models\AeExtTickerDatum`.
 */
class AeExtTickerDatum extends AeExtTickerDatumModel
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'update_date'], 'integer'],
            [['date', 'ticker_id'], 'safe'],
            [['open', 'hight', 'low', 'close', 'volume'], 'number'],
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
        $query = AeExtTickerDatumModel::find();

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
            'date' => $this->date,
            'open' => $this->open,
            'hight' => $this->hight,
            'low' => $this->low,
            'close' => $this->close,
            'volume' => $this->volume,
            'update_date' => $this->update_date,
        ]);

        return $dataProvider;
    }
}