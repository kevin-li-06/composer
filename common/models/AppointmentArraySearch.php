<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ArrayDataProvider;
use common\models\Appointment;
use common\models\User;
use common\models\Store;
use common\models\Prize;

/**
 * AppointmentSearch represents the model behind the search form about `common\models\Appointment`.
 */
class AppointmentArraySearch extends Appointment
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'store_id'], 'integer'],

            // [['name', 'mobile', 'openid', 'created_at'], 'safe'],
            [['mobile'], 'safe'],
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
            $appointment_all = Appointment::find()->where(['store_id' => $store->id])->asArray()->all();
        } else {
            $appointment_all = Appointment::find()->asArray()->all();
        }
        // $appointment_all = Appointment::find()->asArray()->all();
        $appointment_user = User::find()->select('id, redeem_at, change_at, mobile')->where(['>', 'result', '0'])->distinct()->addSelect('result')->orderBy(['result' => SORT_ASC])->asArray()->all();
        // $appointment_user = \yii\helpers\ArrayHelper::map($appointment_user, 'mobile', ['result', 'id']);
        $n_a_u = [];
        foreach ($appointment_user as $k => $v) {
            $n_a_u[$v['mobile']] = [
                'user_id' => $v['id'],
                'redeem_at' => $v['redeem_at'],
                'change_at' => $v['change_at'],
            ];
        }

        $prizes = Prize::find()->asArray()->all();
        $prizesMap = \yii\helpers\ArrayHelper::map($prizes, 'id', 'name');

        $stores = Store::find()->asArray()->all();
        $stores = \yii\helpers\ArrayHelper::map($stores, 'id', 'storename');

        foreach ($appointment_all as $k => $v) {
            if (!empty($n_a_u[$v['mobile']]['user_id'])) {
                $appointment_all[$k]['user_id'] = $n_a_u[$v['mobile']]['user_id'];
                $appointment_all[$k]['redeem_at'] = $n_a_u[$v['mobile']]['redeem_at'];
                $appointment_all[$k]['change_at'] = $n_a_u[$v['mobile']]['change_at'];
                $appointment_all[$k]['prize_id'] = $prizesMap[$v['prize_id']];
                $appointment_all[$k]['store_id'] = $stores[$v['store_id']];
            }
        }

        if ($this->load($params)) {
            $mobile = strtolower(trim($this->mobile));
            $appointment_all = array_filter($appointment_all, function ($role) use ($mobile) {
                return (empty($mobile) || strpos((strtolower(is_object($role) ? $role->mobile : $role['mobile'])), $mobile) !== false);
            });
        }

        $dataProvider = new ArrayDataProvider([
            'allModels' => $appointment_all,
            'pagination' => [
                'pageSize' => 10,
            ],
            // 'sort' => [
            //     'attributes' => ['id', 'name'],
            // ],
        ]);

        return $dataProvider;
    }

    public static function storeMap($id)
    {
        $stores_model = Store::find()->all(); 
        $stores = \yii\helpers\ArrayHelper::map($stores_model, 'id', 'zh_storename'); 
        return $stores[$id];
    }
}
