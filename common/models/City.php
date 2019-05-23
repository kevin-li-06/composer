<?php

namespace common\models;

use Yii;

class City extends \yii\db\ActiveRecord
{


    public static function tableName()
    {
        return 'city';
    }

    /**
     * @author bob qiu
     * 
     */
    public static function get_city($cityid)
    {
        $cityname = self::find()->where(['CityID'=>$cityid])->select('CityName')->one()->CityName;
        return $cityname;
    }
}