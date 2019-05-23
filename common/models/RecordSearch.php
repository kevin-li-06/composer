<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Record;

/**
 * RecordSearch represents the model behind the search form about `\common\models\Record`.
 */
class RecordSearch extends Record
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'type', 'status', 'get_at', 'lottery_at', 'exchange_at', 'store_id'], 'integer'],
            [['date', 'result', 'source'], 'safe'],
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
    public function search($params, $user_id = null)
    { 
        if ($user_id){
            $query = Record::find()->where(['user_id' => $user_id]);
        } else {
            $query = Record::find();
        }
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
            'user_id' => $this->user_id,
            'type' => $this->type,
            'status' => $this->status,
            'get_at' => $this->get_at,
            'lottery_at' => $this->lottery_at,
            'exchange_at' => $this->exchange_at,
            'receipts' => $this->receipts,
        ]);

        $query->andFilterWhere(['like', 'date', $this->date])
            ->andFilterWhere(['like', 'result', $this->result])
            ->andFilterWhere(['like', 'source', $this->source]);
            
        return $dataProvider;
    }
}
