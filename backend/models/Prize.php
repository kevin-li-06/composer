<?php

namespace backend\models;

use Yii;
use yii\behaviors\TimestampBehavior;

class Prize extends \common\models\Prize
{
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    public static function tableName()
    {
        return 'prize';
    }

    public function rules()
    {
        return [
            [['name', 'level', 'stock_num'], 'required'],
            [['level', 'stock_num', 'gain_num', 'exchange_num', 'created_at', 'updated_at'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['name'], 'unique'],
        ];
    }
}
