<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "redeem_record".
 *
 * @property string $id
 * @property integer $store_id
 * @property integer $prize_id
 * @property string $mobile
 * @property integer $created_at
 */
class RedeemRecord extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'redeem_record';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['store_id', 'prize_id', 'mobile', 'created_at'], 'required'],
            [['store_id', 'prize_id', 'created_at'], 'integer'],
            [['mobile'], 'string', 'max' => 25],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'store_id' => 'Store ID',
            'prize_id' => 'Prize ID',
            'mobile' => 'Mobile',
            'created_at' => 'Created At',
        ];
    }
}
