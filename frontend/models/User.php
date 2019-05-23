<?php

namespace frontend\models;

use Yii;

class User extends \common\models\User 
{
	public static function tableName()
	{
		return 'user';
	}

	public function getCheckinList()
    {       
        return $this->hasMany(CheckIn::className(), ['user_id' => 'id']);
    }
}