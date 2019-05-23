<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "rule".
 *
 * @property string $id
 * @property integer $type
 * @property integer $group
 * @property string $prize_rate
 */
class Rule extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'rule';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['prize_rate'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'group' => '用户组',
            'prize_rate' => '奖品及概率',
        ];
    }

    // 新增时验证奖品类型
//    public function validateTypeOnCreate($attribute, $params)
//    {
//        $re = self::find()->where(['type' => $this->type])->exists();
//        if ($re) {
//            $this->addError($attribute, '已经设置了' . self::types($this->type) . '，不能重复设置');
//        }
//    }

    // 奖品类型
    public static function types($key = null)
    {
        $types = [
            1 => '小奖',
            2 => '大奖',
        ];
        return isset($key) ? $types[$key] : $types;
    }

    //展示概率
    public static function showPrizeRate($id)
    {
        $prize_rates = self::find()->select('prize_rate')->where(['id' => $id])->scalar();
        $prize_rates = json_decode($prize_rates);
        $data = [];
        foreach ($prize_rates as $k => $v){
            $prize_name = Prize::find()->select('name')->where(['id' => $k])->one();
            $data[] = $prize_name->name.' == '.$v."% "."<br/>";
        }
        return implode(" ",$data);
    }
}
