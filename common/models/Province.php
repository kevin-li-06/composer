<?php

namespace common\models;

use Yii;

class Province extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'province';
    }

    /**
     * 
     */
    public static function get_province($provinceid)
    {
        $provincename = self::find()->where(['ProvinceID'=>$provinceid])->select('ProvinceName')->one()->ProvinceName;
        return $provincename;
    }
}