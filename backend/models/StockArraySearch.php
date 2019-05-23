<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ArrayDataProvider;
use common\models\Appointment;
use common\models\User;
use common\models\Store;
use common\models\Prize;
use backend\models\StoreStock;

/**
 * AppointmentSearch represents the model behind the search form about `common\models\Appointment`.
 */
class StockArraySearch extends Store
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['zh_storename'], 'safe'],
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
        // 所有门店
        $stores = Store::find()->select('id, zh_storename')->asArray()->all();
        $stores = \yii\helpers\ArrayHelper::map($stores, 'id', 'zh_storename');

        // 所有预约
        $appointments = Appointment::find()
        ->select('count(id) as sum, store_id, prize_id')
        ->groupBy('store_id, prize_id')
        ->asArray()->all();
        $new_a = [];
        foreach ($appointments as $k => $v) {
            $new_a[$v['store_id'] . '_' . $v['prize_id']] = $v['sum'];
        }

        // 手机号去重重复
        $mobiles = User::find()->select('distinct(mobile)')->where(['>', 'redeem_prize', 0])->asArray()->all();
        $mobiles = \yii\helpers\Arrayhelper::getColumn($mobiles, 'mobile');
        // 所有核销
        $redeems = Appointment::find()
        ->select('store_id, prize_id, count(prize_id) as sum')
        ->where(['in', 'mobile', $mobiles])
        ->groupBy('store_id, prize_id')
        ->asArray()->all();
        $new_r = [];
        foreach ($redeems as $k => $v) {
            $new_r[$v['store_id'] . '_' . $v['prize_id']] = $v['sum'];
        }
        
        // 门店库存表
        $stock = StoreStock::find()->select('store_id, prize_id, stock')->asArray()->all();

        // 新的数据，先处理库存
        $data = [];
        foreach ($stock as $k => $v) {
            // 预约
            if (isset($new_a[$v['store_id'] . '_' . $v['prize_id']])) {
                $data[$v['store_id']][$v['prize_id'] . '_appointment'] = $new_a[$v['store_id'] . '_' . $v['prize_id']];
            } else {
                $data[$v['store_id']][$v['prize_id'] . '_appointment'] = 0;
            }
            // 核销
            if (isset($new_r[$v['store_id'] . '_' . $v['prize_id']])) {
                $data[$v['store_id']][$v['prize_id'] . '_redeem'] = $new_r[$v['store_id'] . '_' . $v['prize_id']];
            } else {
                $data[$v['store_id']][$v['prize_id'] . '_redeem'] = 0;
            }
            // 库存
            $data[$v['store_id']]['store_id'] = $v['stock'];
            $data[$v['store_id']]['zh_storename'] = $stores[$v['store_id']];
            $data[$v['store_id']][$v['prize_id'] . '_stock'] = $v['stock'];
        }

        if ($this->load($params)) {
            $zh_storename = strtolower(trim($this->zh_storename));
            $data = array_filter($data, function ($role) use ($zh_storename) {
                return (empty($zh_storename) || strpos((strtolower(is_object($role) ? $role->zh_storename : $role['zh_storename'])), $zh_storename) !== false);
            });
        }

        $dataProvider = new ArrayDataProvider([
            'allModels' => $data,
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => [
                'attributes' => [
                    '1_appointment', '1_redeem', '1_stock',
                    '2_appointment', '2_redeem', '2_stock',
                    '3_appointment', '3_redeem', '3_stock',
                    '4_appointment', '4_redeem', '4_stock',
                    '5_appointment', '5_redeem', '5_stock',
                    '6_appointment', '6_redeem', '6_stock',
                ],
                'defaultOrder' => [
                    // '1' => SORT_DESC,
                    // '2' => SORT_DESC,
                    // '3' => SORT_DESC,
                    // '4' => SORT_DESC,
                    // '5' => SORT_DESC,
                    // '6' => SORT_DESC,
                ],
            ],
        ]);

        return $dataProvider;
    }
}
