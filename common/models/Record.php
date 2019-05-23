<?php

namespace common\models;

use Yii;
use common\models\Prize;
use common\models\User;

/**
 * This is the model class for table "record".
 *
 * @property string $id
 * @property integer $user_id
 * @property integer $type
 * @property string $date
 * @property integer $status
 * @property string $result
 * @property integer $get_at
 * @property integer $lottery_at
 * @property integer $exchange_at
 * @property integer $receipts
 */
class Record extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'record';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'type'], 'required'],
            [['user_id', 'type', 'status', 'store_id'], 'integer'],
            [['date'], 'string', 'max' => 25],
            [['result'], 'string', 'max' => 255],
        ];
    }



    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'type' => '抽奖类型',
            'date' => '日期',
            'status' => '抽奖状态',
            'result' => '中奖结果',
            'get_at' => '获得机会时间',
            'lottery_at' => '抽奖时间',
            'exchange_at' => '兑奖时间',
            'source' => '机会来源',
            'receipts' => '小票',
            'store_id' => '操作门店',
        ];
    }

    // 状态对应关系 抽奖状态 0 - 未获取 1 - 已获取 2 - 已抽奖 3 - 已领奖
    public static function statusMap($status = null)
    {
        $map = [
            // '0' => '未获取',
            '1' => '已获取机会',
            '2' => '已抽奖',
            '3' => '已领奖',
        ];

        return !isset($status) ? $map : $map[$status];
    }

    // 状态对应关系 抽奖状态 0 - 未获取 1 - 已获取 2 - 已抽奖 3 - 已领奖
    public static function typeMap($type = null)
    {
        $map = [
            '1' => '小奖',
            '2' => '大奖'
        ];

        return !isset($type) ? $map : $map[$type];
    }

    public function getPrize()
    {
        return $this->hasOne(Prize::classname(), ['id' => 'result']);
    }

    public function getUser()
    {        
        return $this->hasOne(User::classname(), ['id' => 'user_id'])->select(['id', 'username']);
    }
}
