<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Appointment;
use common\models\User;

/**
 * AppointmentSearch represents the model behind the search form about `common\models\Appointment`.
 */
class AppointmentSearch extends Appointment
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'store_id'], 'integer'],

            [['name', 'mobile', 'openid', 'created_at'], 'safe'],
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
        if (Yii::$app->user->identity->id_role == 3) {
            $store_code = Yii::$app->user->identity->username;
            $store = \common\models\Store::find()->where(['store_code' => $store_code])->one();
            $query = Appointment::find()->where(['store_id' => $store->id]);
        } else {
            $query = Appointment::find();
        }

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'key' => function ($model) {
                $user = User::getHighestPrizeUser($model->mobile);
                return $user;
            }
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
                                    'prize_id' => $this->prize_id,
                                    'store_id' => $this->store_id,
                                    'openid' => $this->openid,
                                ])                 
             ->andFilterWhere(['like', 'name', $this->name])
             ->andFilterWhere(['like', 'mobile', $this->mobile])
             ->andFilterWhere(['>=','created_at',strtotime($this->created_at)-0]);
        return $dataProvider;
    }

    public static function storeMap($id)
    {
        $stores_model = Store::find()->all(); 
        $stores = \yii\helpers\ArrayHelper::map($stores_model, 'id', 'zh_storename'); 
        return $stores[$id];
    }
}
