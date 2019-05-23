<?php

namespace common\models;

use Yii;

class District extends \yii\db\ActiveRecord
{


    public static function tableName()
    {
        return 'district';
    }

    /**
     * @author bob qiu
     */
    public static function get_district($districtid)
    {
        $districtname = self::find()->where(['DistrictID'=>$districtid])->select('DistrictName')->one()->DistrictName;
        return $districtname;
    }
}