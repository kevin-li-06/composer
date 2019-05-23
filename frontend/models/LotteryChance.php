<?php

namespace frontend\models;

use Yii;

class LotteryChance extends \yii\db\ActiveRecord
{
	public static function tableName()
	{
		return 'lottery_chance';
	}
}