<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "prize".
 *
 * @property string $id
 * @property string $name
 * @property integer $level
 * @property integer $stock_num
 * @property integer $gain_num
 * @property integer $exchange_num
 * @property integer $created_at
 * @property integer $updated_at
 */
class Prize extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'prize';
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '奖品名称',
            'level' => '奖品等级',
            'stock_num' => '奖品库存数量',
            'gain_num' => '已抽取数量',
            'exchange_num' => '已兑换数量',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }

    // 返回所有奖品
    public static function allPrizes($ids = null, $asArray = false)
    {
        if ($asArray) {
            if ($ids) {
                $models = static::find()->where(['id' => $ids])->asArray()->all();
            } else {
                $models = static::find()->asArray()->all();
            }
        } else {
            if ($ids) {
                $models = static::find()->where(['id' => $ids])->all();
            } else {
                $models = static::find()->all();
            }
        }
        
        return $models;
    }
    
    // 状态对应关系 抽奖状态 0 - 未获取 1 - 已获取 2 - 已抽奖 3 - 已领奖
    public static function typeMap($type = null)
    {
        $map = [
            '1' => 'KFC优惠券',
            '2' => '联合品牌优惠券',
            '3' => '周边产品',
        ];
        
        return !isset($type) ? $map : $map[$type];
    }
    
    // 奖品等级对应关系 1-一等奖  2 二等奖
    public static function levelType($level = null)
    {
        $map = [
            '1' => '一等奖',
            '2' => '二等奖',
            '3' => '三等奖',
            '4' => '四等奖',
        ];
        
        return !isset($level) ? $map : $map[$level];
    }
}
