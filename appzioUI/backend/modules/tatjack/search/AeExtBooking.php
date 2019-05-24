<?php

namespace backend\modules\tatjack\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\tatjack\models\AeExtBooking as AeExtBookingModel;

/**
* AeExtBooking represents the model behind the search form about `backend\modules\tatjack\models\AeExtBooking`.
*/
class AeExtBooking extends AeExtBookingModel
{

    /**
    * @inheritdoc
    */
    public function rules() {
        return [
            [['id', 'play_id', 'assignee_play_id', 'item_id', 'date', 'length', 'price'], 'integer'],
            [['notes', 'status', 'created_at', 'updated_at'], 'safe'],
            [['lat', 'lon'], 'number'],
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
        $query = AeExtBookingModel::find();

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
            'assignee_play_id' => $this->assignee_play_id,
            'item_id' => $this->item_id,
            'date' => $this->date,
            'length' => $this->length,
            'price' => $this->price,
            'lat' => $this->lat,
            'lon' => $this->lon,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query
            ->andFilterWhere(['like', 'notes', $this->notes])
            ->andFilterWhere(['like', 'status', $this->status]);

        return $dataProvider;
    }

}