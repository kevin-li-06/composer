<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\User;

/**
 * UserSearch1 represents the model behind the search form about `common\models\User`.
 */
class UserSearch extends User
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'small_chance', 'big_chance', 'continuous', 'redeem_prize', 'redeem_at', 'created_at', 'updated_at', 'change_prize', 'change_at', 'result'], 'integer'],
            [['username', 'region', 'auth_key', 'mobile', 'mobile_hash', 'card', 'openid', 'lottery_chance'], 'safe'],
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
        // echo '<pre>';var_dump($params);exit;
        // $source = $params['UserSearch1']['source'];
        // echo $source;exit;

        // if ($source) {
        //     if ($source == 1) {
        //         $query = User::find()->where(['exists', 'openid']);
        //     } else {
        //         $query = User::find()->where(['not exists', 'openid']);
        //     }
        // } else {
            $query = User::find();
        // }
        

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
            'region' => $this->region,
            'small_chance' => $this->small_chance,
            'big_chance' => $this->big_chance,
            'continuous' => $this->continuous,
            'redeem_prize' => $this->redeem_prize,
            'redeem_at' => $this->redeem_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'change_prize' => $this->change_prize,
            'change_at' => $this->change_at,
            'result' => $this->result,
            // 'lottery_chance' => $this->lottery_chance,
        ]);

        $query->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'auth_key', $this->auth_key])
            ->andFilterWhere(['like', 'mobile', $this->mobile])
            ->andFilterWhere(['like', 'mobile_hash', $this->mobile_hash])
            ->andFilterWhere(['like', 'card', $this->card])
            // ->andFilterWhere(['like', 'card', $this->lottery_chance])
            ->andFilterWhere(['like', 'openid', $this->openid]);


        return $dataProvider;
    }
}
