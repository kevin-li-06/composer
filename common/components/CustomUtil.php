<?php

namespace common\components;

use Yii;
use yii\base\ErrorException;
use common\models\Record;
use common\models\User;
/**
* @author Chris.K
* 2017-11-06
* 自定义工具类
* 需要全局调用的自定义方法放在这里,防止冗余代码
*/
class CustomUtil 
{
	//添加抽奖记录
	/**
	* @author Aaron.luo
	* @param $user_id 用户id
	* @param $source  抽奖机会来源 checkin签到 share分享 consumption消费 seven连续签到满7天
 	* @param $receipts 小票
 	* @param $receipts_type小票类型 1为5000 2为10000 1能一天能有两条记录 2一天只能有一条记录
	* retun true or false or 已签到
	**/
	public static function addRecords($source, $user_id = null, $receipts = null, $receipts_type = null)
	{
		//根据当前日期查出该用户所有抽奖机会记录
		$time = date("Y-m-d",time());
		$opportunitys = Record::find()->where(['date' => $time, 'user_id'=>$user_id])->asArray()->all();
		//根据不同的来源检测该用户是否已经获得过抽奖机会..消费一天可以得到两次机会
		if($source == 'checkin'){
			foreach($opportunitys as $opportunity){
				if (in_array($source,$opportunity)) {
					
					// dev
					if (!Yii::$app->params['dev']) {
						return "已签到";
					}
										
				}
			}
		}

		if($source == 'seven'){
			foreach($opportunitys as $opportunity){
				if (in_array($source,$opportunity)) {

					// dev
					if (!Yii::$app->params['dev']) {
						return "已签到";
					}
				}
			}
		}

		if($source == 'share'){
			foreach($opportunitys as $opportunity){
				if (in_array($source,$opportunity)) {
					return "已分享";					
				}
			}
		}
		//检查消费小票 5000两条记录 10000一条记录 同时检查消费
		$count = 0;
		if($source == 'consumption'){
			foreach($opportunitys as $opportunity){
				if ($opportunity['receipts_type'] == 2){
					return "消费机会已达上限";
				} else if ($receipts_type == 2 && $opportunity['receipts_type'] == 1){
					return "消费机会已达上限";
				} else if(in_array($source,$opportunity) && $opportunity['receipts_type'] == 1) {
					$count = $count+1;				
				}
			}
			if ($count == 2){
				return "消费机会已达上限";
			}
		}

		//通过检测执行抽奖机会记录
		$records = new Record();
		switch ($source) {
			case 'checkin' :
			$type = 1;
			break;
			case 'share' :
			$type = 1;
			break;
			case 'consumption' :
			$type = 2;
			break;
			case 'seven' : 
			$type = 2;
		}
		//$date只在是签到才保存
		$records->date = $time;
		$records->user_id = $user_id;
		$records->type = $type;
		$records->source = $source;
		$records->status = 1;
		$records->get_at = time();
		$records->receipts = $receipts;
		$records->receipts_type = $receipts_type;

		if ($source == 'consumption') {
			$store_code = Yii::$app->user->identity->username;
			$store = \common\models\Store::find()->where(['store_code' => $store_code])->one();
			if ($store) {
				$records->store_id = $store->id;
			}
		}

		if ($records->save()) {
			//同时给用户增加机会
			$user = User::findOne($user_id);
			if ($user) {
				if ($receipts_type == 2){
					$user->big_chance = $user->big_chance+2;
				} elseif ($type == 1) {
					$user->small_chance = $user->small_chance+1;					
				} else {
					$user->big_chance = $user->big_chance+1;
				}
				if ($user->save()) {
					return true;
				} else {
					return '用户机会增加失败';
				}
			} else {
				return "用户不存在";
			}			
		} else {
			return "保存记录失败";
		}		

	}



}