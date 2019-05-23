<?php

namespace common\models;

use Yii;
use common\models\Prize;
use common\models\Record;
use common\models\Store;

/**
 * This is the model class for table "appointment".
 *
 * @property string $id
 * @property string $name
 * @property string $mobile
 * @property integer $store_id
 * @property integer $created_at
 */
class Appointment extends \yii\db\ActiveRecord
{
    public $highest_prize;

    public $highest_user;

    public $store_name;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'appointment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'mobile', 'store_id'], 'required'],
            [['store_id', 'created_at', 'prize_id'], 'integer'],
            [['name', 'mobile'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '姓名',
            'mobile' => '手机号',
            'openid' => 'Openid',
            'prize_id' => '预约奖品',
            'store_id' => '预约门店',
            'created_at' => '预约时间',
            'highest_prize' => '最高奖品',
            'store_name' => '门店名字',
            'dyson'=>'戴森吹风机',
            'tool'=>'双人立刀具'
        ];
    }

    public function getPrize()
    {
        return $this->hasOne(Prize::classname(), ['id' => 'prize_id']);
    }

    public function getStore()
    {
        return $this->hasOne(Store::classname(), ['id' => 'store_id']);
    }
}
