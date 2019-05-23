<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Prize;

/**
 * PrizeSearch represents the model behind the search form about `backend\models\Prize`.
 */
class PrizeSearch extends Prize
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'stock_num', 'gain_num', 'exchange_num', 'created_at', 'updated_at'], 'integer'],
            [['name'], 'safe'],
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
        $query = Prize::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'stock_num' => $this->stock_num,
            'gain_num' => $this->gain_num,
            'exchange_num' => $this->exchange_num,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }

    public function prizeMap($id)
    {
        $prizes_model = Prize::allPrizes();
        $prizes = \yii\helpers\ArrayHelper::map($prizes_model, 'id', 'name');
        return $prizes[$id];
    }
}
