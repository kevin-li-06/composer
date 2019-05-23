<?php

namespace backend\controllers;

use backend\components\BaseAdminController;
use backend\models\StoreStock;
use common\models\User;
use common\models\Appointment;
use common\models\Record;
use common\models\Store;
use common\models\Prize;
use yii\helpers\ArrayHelper;
use yii\filters\AccessControl;


class ReportController extends BaseAdminController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionRanking()
    {
        // 开启内存限制
        ini_set('memory_limit', '3000M');

        // 奖品id和奖品名称的数组
        $prizes = Prize::find()->select('id, name, level, stock_num, gain_num')->orderBy('level asc')->asArray()->all();
        $prizesMap = \yii\helpers\ArrayHelper::map($prizes, 'id', 'name');
        // 统计出预约次数最多的门店
        $stores = Appointment::find()->select('zh_storename, count(appointment.id) as sum')
                                    ->join('LEFT JOIN', 'store', 'appointment.store_id = store.id')
                                    ->groupBy('appointment.store_id')
                                    ->limit(10)
                                    ->orderBy('sum DESC')
                                    ->asArray()
                                    ->all();
        
        // 获取数组中最大的键
        $key = count($stores) - 1;        
        // 如果取出的店名不足10个，添加空数组
        if ($key < 9) {
            $arr = [
                'zh_storename' => '',
                'sum' => ''
            ];
            $store_fill = array_fill($key+1, 9-$key, $arr);
            $stores = array_merge($stores, $store_fill);
        }
        ArrayHelper::multisort($stores, ['sum'], [SORT_ASC]);
        
        // 参加活动总次数
        $personSum = Record::find()->where(['status' => 2])->count();

        // 未中奖人次
        $emptySum = Record::find()->where(['status' => 2, 'result' => 0])->count();
        // 增加未中奖字段
        $prizesMap[7] = '未中奖';
        // 添加总计字段
        $prizesMap[8] = '总计';
        
        // 参加活动总人数
        $attendSum = Record::find()->select('user_id')->distinct()->count();

        // 基础数据总人数
        $userSum = User::find()->select('id')->orderBy('id desc')->limit(1)->scalar();

        //占比
        $accounted = @round(($attendSum / $userSum * 100), 4).'%';
        $data = ['userSum' => $userSum, 'attendSum' => $attendSum, 'accounted' => $accounted, 'personSum' => $personSum, 'emptySum' => $emptySum];

        //中奖人数及比例
        foreach ($prizesMap as $k => $v) {
            $resultLevels[$v] = ['sum' => 0, 'gainSum' => 0, 'appointmentSum' => 0, 'redeemSum' => 0, 'stockSum' => 0, 'leftSum' => 0, 'leftPercent' => 0];
        }
        
        // 单个奖品的被刮中次数
        $res = Record::find()->select('count(result), result')->where(['>', 'result', 0])->groupBy(['result'])->asArray()->all();
        $re = array_column($res,'count(result)','result');
        if ($re){
            // 增加额外字段
            $re[7] = $emptySum;
            $re[8] = $personSum;
            foreach ($prizesMap as $k => $v) {
                $resultLevels[$v]['sum'] = isset($re[$k]) ? $re[$k] : 0;
            }
        }

        // 参与中奖的人次
        $gain_num = Prize::find()->select('id, gain_num')->asArray()->all();
        $gain_num = Arrayhelper::map($gain_num, 'id', 'gain_num');
        if ($gain_num) {
            $t1 = 0;
            foreach ($prizesMap as $k => $v) {
                $resultLevels[$v]['gainSum'] = isset($gain_num[$k]) ? $gain_num[$k] : 0;
                $t1 += $resultLevels[$v]['gainSum'];
            }
            // 增加额外字段
            $resultLevels['未中奖']['gainSum'] = '';
            $resultLevels['总计']['gainSum'] = $t1;
        }

        // 连续签到的粉丝总量
        $today = date('Y-m-d');
        $continue = User::find()->select('count(distinct user.id) as sum, continuous')
                                ->join('LEFT JOIN', 'record', 'user.id = record.user_id')
                                ->where(['record.date' => $today])
                                ->andWhere('source in ("checkin", "seven")')
                                ->groupBy('continuous')
                                ->asArray()
                                ->all();

        // 获取数组中最大的键
        $key = count($continue);
        // 检查不满7天的情况
        $continueMap = ArrayHelper::map($continue, 'continuous', 'sum');   
        for ($i = 1; $i < 8; $i++) {
            if (!isset($continueMap[$i])) {
                $arr = [
                    'sum' => 0,
                    'continuous' => $i
                ];
                $continue_fill = array_fill($k, 1, $arr);
                $continue = array_merge($continue, $continue_fill);
            }
        }
        ArrayHelper::multisort($continue, ['continuous'], [SORT_ASC]);

        // >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>统计门店增加抽大奖次数最多的门店前十<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
        $big_chance = 0;
        $store_bigchance = Record::find()->select('zh_storename, count(record.id) as bignum')
                                         ->join('LEFT JOIN', 'store', 'record.store_id = store.id')
                                         ->where(['type' => 2])
                                         ->andwhere(['NOT', ['store_id' => null]])
                                         ->groupBy('record.store_id')->orderBy('bignum DESC')
                                         ->limit(10)
                                         ->asArray()->all();
        if (empty($store_bigchance)) {
            // 如果为空添加10个空数组
            $big = [
                'zh_storename' => '',
                'bignum' => ''
            ];
            $store_bigchance = array_fill(0, 10, $big);
        }

        // 获取数组中最大的键
        $key = count($store_bigchance) - 1;        
        // 如果取出的店名不足10个，添加空数组
        if ($key < 9) {
            $arr = [
                'zh_storename' => '',
                'bignum' => ''
            ];
            $store_fill = array_fill($key+1, 9-$key, $arr);
            $store_bigchance = array_merge($store_bigchance, $store_fill);
        }
        ArrayHelper::multisort($store_bigchance, ['bignum'], [SORT_ASC]);
        // >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>统计门店增加抽大奖次数最多的门店前十<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<

        // >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>不同日期不同来源参与刮奖的次数<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
        $records = Record::find()->select('count(*) as sum, date, source')->groupBy('date, source')->asArray()->all();
        $date = array_unique(ArrayHelper::getColumn($records, 'date'));

        $checkin = [];
        $seven = [];
        $share = [];
        $consumption = [];

        foreach ($records as $k => $v) {
            switch ($v['source']) {
                case 'checkin':
                    $checkin[$v['date']] = $v['sum'];
                    break;
                case 'seven':
                    $seven[$v['date']] = $v['sum'];
                    break;
                case 'share':
                    $share[$v['date']] = $v['sum'];
                    break;
                case 'consumption':
                    $consumption[$v['date']] = $v['sum'];
                    break;
            }
        }
        
        // $this->bp($records);

        foreach ($date as $v) {
            if (!isset($checkin[$v])) {
                $checkin[$v] = 0;
            }
            if (!isset($seven[$v])) {
                $seven[$v] = 0;
            }
            if (!isset($share[$v])) {
                $share[$v] = 0;
            }
            if (!isset($consumption[$v])) {
                $consumption[$v] = 0;
            }
        }

        ksort($checkin);
        ksort($seven);
        ksort($share);
        ksort($consumption);

        $sourceSum = [
            'date' => $date,
            'checkin' => $checkin,
            'seven' => $seven,
            'share' => $share,
            'consumption' => $consumption,
        ];
        // >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>不同日期不同来源参与刮奖的次数<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<

        // 单个奖品预约总量
        $prizeIds = Appointment::find()->select('prize_id, count(prize_id)')->where(['>', 'prize_id', 0])->groupBy(['prize_id'])->asArray()->all();
        $prize_ids = array_column($prizeIds,'count(prize_id)','prize_id');
        if ($prize_ids){
            $t2 = 0;
            foreach ($prizesMap as $k => $v) {
                $resultLevels[$v]['appointmentSum'] = isset($prize_ids[$k]) ? $prize_ids[$k] : 0;
                $t2 += $resultLevels[$v]['appointmentSum'];
            }
            // 增加额外字段
            $resultLevels['未中奖']['appointmentSum'] = '';
            $resultLevels['总计']['appointmentSum'] = $t2;
        }
        
        // 单个奖品总核销量
        $users = User::find()->select('count(distinct mobile) as sum, redeem_prize, mobile')->where(['>', 'redeem_prize', '0'])->groupBy('redeem_prize')->asArray()->all();
        $users_column = array_column($users, 'sum', 'redeem_prize');
        if ($users_column){
            $t3 = 0;
            foreach ($prizesMap as $k => $v) {
                $resultLevels[$v]['redeemSum'] = isset($users_column[$k]) ? $users_column[$k] : 0;
                $t3 += $resultLevels[$v]['redeemSum'];
            }
            // 增加额外字段
            $resultLevels['未中奖']['redeemSum'] = '';
            $resultLevels['总计']['redeemSum'] = $t3;
        }
        
        // 单个奖品的总库存
        $StoreStocks = StoreStock::find()->select('sum(stock) as stock, prize_id')->groupBy('prize_id')->asArray()->all();        
        $stock_column = array_column($StoreStocks,'stock','prize_id');
        if ($stock_column) {
            $t4 = 0;
            foreach ($prizesMap as $k => $v) {
                $resultLevels[$v]['stockSum'] = isset($stock_column[$k]) ? $stock_column[$k] : 0;
                $t4 += $resultLevels[$v]['stockSum'];
            }
            // 增加额外字段
            $resultLevels['未中奖']['stockSum'] = '';
            $resultLevels['总计']['stockSum'] = $t4;
        }

        // 奖品剩余
		$appointment = Appointment::find()->select('count(prize_id) as sum, prize.level')->join('LEFT JOIN', 'prize', 'prize_id = prize.id')->groupBy('prize_id')->asArray()->all();
		$appointment = \yii\helpers\ArrayHelper::map($appointment, 'level', 'sum');
		
		$np = [];
		foreach ($prizes as $k => $prize) {
			if ($prize['level'] == 1) {
				$np[1] = ['total' => 5, 'left' => 5 - $prize['gain_num']];
			} elseif ($prize['level'] == 2) {
				$np[2] = ['total' => 20, 'left' => 20 - $prize['gain_num']];
			} elseif ($prize['level'] == 3) {
				$np[3] = ['total' => 80, 'left' => 80 - (isset($appointment[3]) ? $appointment[3] : 0)];
			} elseif ($prize['level'] == 4) {
				$np[4] = ['total' => 200, 'left' => 200 - (isset($appointment[4]) ? $appointment[4] : 0)];
			} elseif ($prize['level'] == 5) {
				$np[5] = ['total' => 2000, 'left' => 2000 - (isset($appointment[6]) ? $appointment[6] : 0)];
			}
        }
        $t5 = 0;
        foreach ($prizesMap as $k => $v) {
            $resultLevels[$v]['leftSum'] = isset($np[$k]['left']) ? $np[$k]['left'] : 0;
            if ($k < 6) {
                $resultLevels[$v]['leftPercent'] = round($resultLevels[$v]['leftSum'] / $np[$k]['total'], 4) * 100 . '%';
            }
            
            $t5 += $resultLevels[$v]['leftSum'];
        }
        // 增加额外字段
        $resultLevels['冰箱贴']['leftSum'] = '';
        $resultLevels['未中奖']['leftSum'] = '';
        $resultLevels['总计']['leftSum'] = $t5;
        $resultLevels['冰箱贴']['leftPercent'] = '';
        $resultLevels['未中奖']['leftPercent'] = '';
        $resultLevels['总计']['leftPercent'] = '';

        return $this->render('ranking', [
            'stores' => $stores, 
            'data' => $data, 
            'resultLevels' => $resultLevels, 
            'attendSum' => $attendSum,
            'sourceSum' => $sourceSum,
            'continue' => $continue,
            'store_bigchance' => $store_bigchance,
        ]);
    }

}