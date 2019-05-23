<?php
namespace frontend\controllers;

use Yii;
use frontend\components\Log;
use frontend\components\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use common\models\Record;
use common\models\Prize;
use common\models\Appointment;
use common\models\User;
use common\models\LoginForm;
use common\components\CustomUtil;
use common\models\Store;
use common\models\Rule;
// use common\models\RedeemRecord;
use frontend\models\CheckIn;
use frontend\components\Wechat;
use frontend\components\Auth;
use yii\helpers\Url;
use common\models\Answer;
use yii\base\Model;


class MemberController extends Controller
{
	public $enableCsrfValidation = false;

	public $source;

	public $videoName = 'JebsenNoLogoVideo';

	/**
	 * 说明: 抽奖引擎 API Block 内才是本次整合的代码, 其余的全部是以前liber临时活动的代码.当整合完毕, 以前的代码可以全部删除
	 * Author: Chris Kuang
	 * Create_at: 2018-10-16
	*/
// >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> 抽奖引擎 API Start <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
	/** 
	 * 说明: 获取用户抽奖历史记录
	 * Author: Chris Kuang
	 * Create_at: 2018-10-16
	*/
	public function actionLotteryHistory()
	{
		// 修改响应格式为JSON
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		
		$auth = (new Auth(
							'member', 
							'lottery-history', 
							$data = Yii::$app->request->post(), 
							Yii::$app->request->get('access_token')
						 ))->getResponse(); // 授权验证
		$res = []; //response 数组
		if ($auth['status'] == 'error') 
		{
			$res = [
				'code' => 401, // 参数缺失
				'status' => 'error', 
				'message' => $auth['message']
			];			
		} 
		else // API 请求安全验证及数据验证合格, 开始API 正式逻辑部分
		{	
			// 1->用户是否存在
			$user = User::find();
			if (isset($data['code'])) $user->where(['code' => $data['code']]);
			if (isset($data['mobile'])) $user->where(['mobile' => $data['mobile']]);
			if (!$user = $user->one()) 
			{
				$user = New User();
				$user->username = 'Tester'.mt_rand(1,10000);
				$user->code = $data['code']; // 暂时只设置手机号, 以后可以从Loyalty API 同步更多用户信息到Local Db				
				$user->save();
			}
					
			// 2 -> 获取历史抽奖中奖记录
			$history = Record::find()->alias('a')->joinWith('user as b');
			if (isset($data['code'])) $history->where(['b.code' => $data['code']]);
			if (isset($data['mobile'])) $history->where(['b.mobile' => $data['mobile']]);
			if (isset($data['from_date'])) $history->andwhere(['>=', 'lottery_at', strtotime($data['from_date'])]);
			if (isset($data['to_date'])) $history->andWhere(['<=', 'lottery_at', strtotime($data['to_date'])]);
			$history = $history->asArray()->all();
									
			// echo '<pre>'; print_r($history);die;
			if (!empty($history)) 
				$res = ['code' => 200,'status' => 'success', 'total'=>count($history),'result'  => $history];
			else 
				$res = ['code' => 200,'status' => 'error','result'  => $history, 'message' => 'This member did not do any lottery'];
		}
		return $res;		
	}

	/**
	 * 说明: 进行一次抽奖
	 * Author: Chris Kuang
	 * Create_at: 2018-10-17
	*/
	public function actionLottery()
	{
		// 修改响应格式为JSON
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $auth = (new Auth(
					'member', 
					'lottery', 
					$data = Yii::$app->request->post(), 
					Yii::$app->request->get('access_token')
				))->getResponse(); // 授权验证

		if ($auth['status'] == 'error') 
		{
			return $res = [
				'code' => 401, // 参数缺失
				'status' => 'error', 
				'message' => $auth['message']
			];			
		} 
		else // API 请求安全验证及数据验证合格, 开始API 正式逻辑部分
		{			
			if (!$user = User::find()->where(['code' => $data['code']])->one()) 
					return $res = ['code' => 402, 'status' => 'error', 'message' => '用户不存在'];
			
			$small_chance_limit = Record::find()->alias('a')->joinWith('user as b')->where(['b.code' => $data['code']])
												->andwhere(['>=', 'lottery_at', strtotime(date('Y-m-d', time()))])
												->andWhere(['<=', 'lottery_at', strtotime(date('Y-m-d', strtotime("+1 day")))])
												->count();			
			// 是否开启限制每天的，没开启的话可以一直抽			
			if ($small_chance_limit < yii::$app->params['lottery_limit_per_day'])
			{ // 当天抽小奖未达上限
				$chance_type = 1;				
			} 
			else 
			{ // 抽奖达到上线
				return $res = ['status' => 'error', 'errmsg' => '该用户没有获得抽奖机会', 'errcode' => -2];
			}

			// 根据group属性和type属性查询出该用户的抽奖规则
			$rule = Rule::find()->select('prize_rate')->scalar();
			if (!$rule) {
				return ['status' => 'error', 'errmsg' => '没有设置抽奖规则', 'errcode' => -3];
			}
			$rates = json_decode($rule, true);

			// 过滤奖品库存不足的
			$rates = get_audit_prize($rates);
			// 获取当前概率的次方数
			$pow = get_pow($rates);
			// 进行抽奖算法
			$prize_id = get_prize($rates, $pow);

			// 更新抽奖记录表和奖品库存
			$record = new Record();
			if ($prize_id) { // 中奖				
				$current_prize = Prize::findOne($prize_id);				
				$current_prize->gain_num++; //修改奖品库存
				$current_prize->save(false);						
				// 保存抽奖结果
				$record->lottery_at = time();
				$record->result = $prize_id;
				$record->status = 2;
				$record->user_id = $user['id'];
                $record->date = date("Y-m-d H:i:s", time());
				$record->save(false);

				return $res = ['status' => 'success', 'data' => 
					[
						'record_id' => $record->id,	
						'code' => $user->code,
						'mobile' => $user->mobile,
						'lottery_at' => date('Y-m-d H:i:s', $record->lottery_at),
						'prize_id' => $current_prize->id,
						'prize_name' => $current_prize->name
					]
				];
			} 
			else 
			{ // 未中奖
				$record->lottery_at = time();
				$record->result = 0;
				$record->status = 2;
                $record->user_id = $user['id'];
                $record->date = date("Y-m-d H:i:s", time());
				$record->save(false);

				return $res = ['status' => 'success', 'data' => 
					[
						'record_id' => $record->id,		
						'code' => $user->code,
						'mobile' => $user->mobile,
						'lottery_at' => date('Y-m-d H:i:s', $record->lottery_at),
						'prize_id' => '-1',
						'prize_name' => '',
					]
				];
			}
		}		
	}

	/**
	 * 说明: 兑换奖品
	 * Author: Chris Kuang
	 * Create_at: 2018-11-16
	*/
	public function actionRedeemPrize()
	{ 
		// 修改响应格式为JSON
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		
		$auth = (new Auth(
							'member', 
							'redeem-prize', 
							$data = Yii::$app->request->post(), 
							Yii::$app->request->get('access_token')
						 ))->getResponse(); // 授权验证
		$res = []; //response 数组
		if ($auth['status'] == 'error') 
		{
			return $res = [
				'code' => 401, // 参数缺失
				'status' => 'error', 
				'message' => $auth['message']
			];			
		} 
		else // API 请求安全验证及数据验证合格, 开始API 正式逻辑部分
		{			
			$record = Record::find()->alias('a')->joinWith('user as b')
									->where(['b.code' => $data['code'], 'a.id' => $data['record_id']])
									->one();
			// echo '<pre>'; print_r($record);die;
			if (!empty($record)) 
			{
				if ($record->result > 0)
				{
					if ($record->status !==3)
					{
						$record->status=3;
						$record->exchange_at = time();
						$record->save(false);

						return $res = [
							'status' => 'success',
							'code' => $data['code'],
							'lottery_at' => date('Y-m-d H:i:s', $record->lottery_at),
							'redeemed_at' => date('Y-m-d H:i:s', $record->exchange_at),
							'record_id' => $record->id,
							'prized_id' => $record->result					
						];
					}
					else
					{
						return $res = [
							'code' => 402,
							'status' => 'error',
							'message' => 'The Record has been redeemed'
						];
					}
					
				}
				else 
				{
					return $res = [
						'code' => 402,
						'status' => 'error',
						'message' => 'The Record does not get any prize'
					];
				}
				
			} 
			else
			{
				return $res = [
					'code' => 402,
					'status' => 'error',
					'message' => 'The Record does not exists'
				];
			}
												
													
		}
	}
// >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> 抽奖引擎 API End <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<

	// 用户处理用户进入活动首页的数据准备
	public function actionIndex()
	{
// 	    echo $_GET['hash'];die;
        // $link = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxeca2e361be1de14c&redirect_uri=https%3A%2F%2Fwcapi.xgatecorp.com%2Fwechat%2Fauth%2Furl&response_type=code&scope=snsapi_base&state=https://campaigns.xgatecorp.com&component_appid=wx5aec7706b086aa5b#wechat_redirect';
        // return $this->redirect($link);exit;
		if (isset($_GET['hash'])) { // 来自短信连接的会员
			$mobile_hash = $_GET['hash'];
			$form = new LoginForm();
			$form->mobile_hash = $mobile_hash;
			if (!$form->validateMobile()) {
				throw new NotFoundHttpException;
			} else {
				$form->loginFromMobile();
				$this->source = 'sms';
			}
		} elseif (isset($_GET['openid'])) { // 来自微信授权的会员
			$openid = $_GET['openid'];
			$isExists = User::find()->where(['openid'=>$openid])->one();
			if (!$isExists) { // 不存在,先注册
				$model = new User();
				$model->openid = $openid;
				$model->save(false);
			} 
			$form = new LoginForm();
			$form->openid = $openid;
			if (!$form->validateOpenid()) {
				throw new NotFoundHttpException;
			} else {
				$form->loginFromWechat();
				$this->source = 'wechat';
			}
			
			// 获取微信信息
			$wechatInfo = Wechat::userinfo([$openid]);
			Log::debug('wechatInfo', $wechatInfo);
			if (isset($wechatInfo['subscribe']) && $wechatInfo['subscribe'] == 1) {
				$headimg = $wechatInfo['headimgurl'];
			} else {
				$headimg = false;
			}
		} else { // 不知来源
			throw new NotFoundHttpException;
		}

		$user = Yii::$app->user->identity;
		// $user_id = $user_info['user_id'];
		// $user = User::findOne($user_id);
		$lotteryUrl = \yii\helpers\Url::to(['member/home', 'user_id' => $user->id]);
		$replayUrl = \yii\helpers\Url::to(['member/video', 'user_id' => $user->id]);
		if (isset($_GET['openid'])) {
		    return $this->renderPartial('home', [
		        'lotteryUrl' => $lotteryUrl,
		        'replayUrl' => $replayUrl,
		        'user_id' => $user->id,
		    ]);
		}
		//是否已完成所有答题
		if ($user['is_answer']) {
			return $this->renderPartial('home', [
				'lotteryUrl' => $lotteryUrl,
				'replayUrl' => $replayUrl,
				'user_id' => $user->id,
			]);
		} else {
		    //答题以微信入口为准,返回剩余没有答题的页面
		    $answer = Answer::find()->where(['user_id' => $user['id'] ])->asArray()->one();
		    if (isset($answer)){
		        foreach ($answer as $k => $v){
		            if (empty($v)){
		                $notAnswer[] = $k;
		            }else {
		                $notAnswer[] = "";
		            }
		        }
		    } else {
		        $notAnswer[] = "";
		    }
		    return $this->render('weaponTest', [
			    'user_id' => $user['id'],
		        'notAnswer' => json_encode($notAnswer),
			]);
		}
		
	}
	//保存用户每次提交的答题
	public function actionAddAnswer()
	{
	    // 修改响应格式为JSON
	    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
	    if (Yii::$app->request->isPost) {
	        $user = Yii::$app->user->identity;
	        $data = Yii::$app->request->post();
	        $model = new Answer();
	        $model->user_id = $user['id'];
	        $model->topic_one = $data['one'];
	        $model->topic_two = $data['two'];
	        $model->topic_three = $data['three'];
	        $model->topic_four = $data['four'];
	        $model->brands = $data['hobby'];
	        $user = User::find()->where(['id' => $user['id']])->one();
	        $user->is_answer = 1;
	        $user->save(false);
	        if ($model->save(false)){
	            $re = ['status' => 'success', 'data' => ['msg' => '答题成功']];
	        } else {
	            $re = ['status' => 'error', 'data' => ['msg' => '答题失败']];
	        }
// 	        //第一次答题
//             if (empty($model)){
//                 $model = new Answer();
//                 $model->user_id = $user['id'];
//                 $model->topic_one = 
//                 $model = $this->choiceQuestion($model, $data);
//                 if ($model->save(false)){
//                     $re = ['status' => 'success', 'data' => ['msg' => '答题成功']];
//                 } else {  
//                     $re = ['status' => 'error', 'data' => ['msg' => '答题失败']];
//                 }
//             } else {
//                 //补充问题
//                 $model = $this->choiceQuestion($model, $data);
//                 if ($model->save(false)){
//                     $re = ['status' => 'success', 'data' => ['msg' => '答题成功']];
//                 } else {
//                     $re = ['status' => 'error', 'data' => ['msg' => '答题失败']];
//                 }
//             }
            return $re;
	    }
	}
	
	public function choiceQuestion($model,$data)
	{
	  
	    switch ($data){
	        case $data['topic'] == 'topic_one':
	            $model->topic_one = $data['answer'];
	            break;
	        case $data['topic'] == 'topic_two':
	            $model->topic_two = $data['answer'];
	            break;
	        case $data['topic'] == 'topic_three':
	            $model->topic_theer = $data['answer'];
	            break;
	        case $data['topic'] == 'topic_four':
	            $model->topic_four = $data['answer'];
	            break;
	    }
	    return $model;
	}

	
	public function actionHome()
	{	    
		// $user_info = Yii::$app->user->identity;
		$user_id = Yii::$app->request->get('user_id');
		if (empty($user_id)) {
			throw new NotFoundHttpException;
		}

		//拿到用户信息
		$user = User::find()->where(['id' => $user_id])->one();

		if (empty($user->openid)) {
			$this->source = 'sms';
		} else {
			$this->source = 'wechat';
		}
		
		if ($this->source == 'sms') {
			// SMS是否预约
			$isAppointment = Appointment::find()->where(['mobile' => $user['mobile'] ])->asArray()->one();
		} else {
			// Wechat是否预约
			$isAppointment = Appointment::find()->where(['openid' => $user['openid'] ])->asArray()->one();
		}

		// 查询预约门店
		if ($isAppointment) {
			$store = Store::find()->where(['id' => $isAppointment['store_id']])->asArray()->one();
		} else {
			$store = false;
		}
		
		//拿到已经使用的所有抽奖记录
		// $records = Record::find()->where(['user_id' => $user_info->id])->andWhere(['status' => 2])->asArray()->all();

		$records = Record::find()->where(['user_id' => $user['id']])
									->andWhere(['>', 'lottery_at', strtotime(date('Y-m-d'))])
									->andWhere(['<=', 'lottery_at', strtotime(date('Y-m-d'))+3600*24])	
									->orderBy('get_at asc')
									->asArray()
									->all();	
		
		//判断用户当天是否已经签到
		$today = date('Y-m-d');
		$whether_checkin = CheckIn::find()->where(['user_id' => $user->id, 'date' => strtotime($today)])->one();
		$whether_checkin = isset($whether_checkin) ? true : false;
		
		// 判断用户前一天是否签到
		$yesterday = date('Y-m-d', strtotime('-1 day'));
		$yesterday_checkin = CheckIn::find()->where(['user_id' => $user->id, 'date' => strtotime($yesterday)])->one();
		
		if (!$yesterday_checkin && !$whether_checkin) {
			$user['continuous'] = 0;
		}

		// 判断是前一天是否为第7天
		if (!$whether_checkin && $yesterday_checkin && ($yesterday_checkin['continuous'] == 4)) {
			$user['continuous'] = 0;
		}

		// dev
		if (Yii::$app->params['dev']) {
			$whether_checkin = false;
			// $user['viewed'] = 0;
		}
		Log::debug('whether_checkin', $whether_checkin);

		// 计算大奖和小奖使用次数
		$big_use_sum = 0;
		$small_use_sum = 0;

		// 是否开启限制每天的，没开启的话可以一直抽
		if (Yii::$app->params['limit']) {
			if (isset($records)) {
				foreach ($records as $record){
					if ($record['type'] == 2) {
						$big_use_sum = $big_use_sum+1;
					} else {
						$small_use_sum = $small_use_sum+1;
					}
				}
			}	
		}

		$data['smallMax'] = SMALL_MAX;
		$data['bigMax'] = BIG_MAX;
		$data['small_use_sum'] = $small_use_sum;
		$data['big_use_sum'] = $big_use_sum;
		$data['user_id'] = $user['id'];
		$data['username'] = isset($isAppointment) ? $isAppointment['name'] : $user['username'];
		$data['mobile'] = isset($isAppointment) ? $isAppointment['mobile'] : $user['mobile'];
		$data['headimg'] = isset($headimg) ? $headimg : false;
		$data['openid'] = $user['openid'];
		$data['small_chance'] = $user['small_chance'];
		$data['big_chance'] = $user['big_chance'];
		$data['continuous'] = $user['continuous'];
		$data['whether_checkin'] = $whether_checkin;
		$data['isRedeem'] = empty($user['redeem_prize']) ? '' : $user['redeem_prize']; // 是否已经核销过
		$data['baseUrl'] = Yii::$app->request->baseUrl;
// 		$data['viewed'] = $user['viewed']; // 是否看过动画 0-没 1-看过	
		$data['is_appointment']	 = isset($isAppointment) ? $isAppointment : [];
		$data['store'] = $store;
		
		// 配置是否开启功能
		$disableCheckin = isset(Yii::$app->params['disableCheckin']) ? Yii::$app->params['disableCheckin'] : false;
		$disableAppointment = isset(Yii::$app->params['disableAppointment']) ? Yii::$app->params['disableAppointment'] : false;
		$disableRedeem = isset(Yii::$app->params['disableRedeem']) ? Yii::$app->params['disableRedeem'] : false;
		$disableLottery = isset(Yii::$app->params['disableLottery']) ? Yii::$app->params['disableLottery'] : false;
		$data['disableCheckin'] = $disableCheckin;
		$data['disableAppointment'] = $disableAppointment;
		$data['disableRedeem'] = $disableRedeem;
		$data['disableLottery'] = $disableLottery;
		$ActivityEndtime = isset(Yii::$app->params['ActivityEndtime']) ? Yii::$app->params['ActivityEndtime'] : '2019-12-31' ;
		$ActivityStarttime = isset(Yii::$app->params['ActivityStarttime']) ? Yii::$app->params['ActivityStarttime'] : '2019-12-31' ;
		// 活动未开始 或 活动已结束  均不能参加活动
		if ( time() > strtotime($ActivityEndtime) || time() < strtotime($ActivityStarttime)) {
			$data['disableCheckin'] = true;
			$data['disableAppointment'] = true;
			$data['disableRedeem'] = true;
			$data['disableLottery'] = true;
		}
		return $this->render('index', [
			'data' => $data,
		]);
	}

	// 再次观看
	public function actionVideo()
	{
		$user_id = $_GET['user_id'];
		$user = User::findOne($user_id);
		if ($user->openid) {
			$url = \yii\helpers\Url::to(['member/index', 'openid' => $user->openid]);	
		} else {
			$url = \yii\helpers\Url::to(['member/index', 'hash' => $user->mobile_hash]);	
		}

		// $videoLink = Url::to('@web/static/src/' . $this->videoName . '.mp4', true);
		$videoLink = Url::to('@web/static/src/' . Yii::$app->params['videoName'] . '.mp4', true);

		return $this->renderPartial('video_again', [
			'url' => $url,
			'videoLink' => $videoLink,
		]);
	}

	// 微信分享
	public function actionWechatShare()
	{
		$link = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxeca2e361be1de14c&redirect_uri=https%3A%2F%2Fwcapi.xgatecorp.com%2Fwechat%2Fauth%2Furl&response_type=code&scope=snsapi_base&state=http://jebsen.onthemooner.com&component_appid=wx5aec7706b086aa5b#wechat_redirect';
		// $link = 'https://mp.weixin.qq.com/mp/profile_ext?action=home&__biz=MzA4ODc0MDgwNw==&scene=124#wechat_redirect';
		return $this->redirect($link);
	}

	public function actionTest()
	{
		// echo 'test';
		// var_dump(CustomUtil::addRecords('checkin', 1));
		return $this->render('test');
	}

	/**
	 * 创建一次预约
	 * @url .../index.php/appointment/create
	 * @method post
	 * @param {name:string, mobile:string|int, prize_id:int, record_id:int, store_id:int}
	 * @return json
	 */
	public function actionAppointmentCreate()
	{
	    // 修改响应格式为JSON
	    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
	    
	    if (Yii::$app->request->isPost) {
	        Log::debug('appointment data',$_POST);
	        // $this->bp(Yii::$app->request->post());
	        $name = Yii::$app->request->post('name');
	        $mobile = Yii::$app->request->post('mobile');
	        
	        // 去掉86
	        if (substr($mobile, 0, 2) == '86') {
	            $mobile = substr($mobile, 2);
	        }
	        
	        $openid = Yii::$app->request->post('openid');
	        $store_id = Yii::$app->request->post('store_id');
	        
	        // 更新该用户的资料
	        if ($openid) {
	            $user = User::find()->where(['openid' => $openid])->one();
	            $user->username = $name;
	            $user->mobile = $mobile;
	            $user->mobile_hash = md5($mobile);
	            $user->save(false);
	        } else {
	            $user = User::find()->where(['mobile' => $mobile])->one();
	        }
	        
	        // 查询该用户最高奖
	        $highest_user = User::getHighestPrizeUser($mobile);
	        
	        // 检查是否有这个门店
	        $store = Store::find()->where(['id' => $store_id])->exists();
	        if (!$store) return ['status' => 'error', 'errmsg' => '该门店不存在'];
	        
	        // 检查是否已经预约
	        if (!empty($openid)) { // 微信预约需要考虑到短信是否已经预约
	            $isWechatAppointment = Appointment::find()->where(['openid' => $openid])->one();
	            if ($isWechatAppointment) { // 微信已经预约
	                return ['status' => 'error', 'errmsg' => '该用户已经预约过了'];
	            } else { // 微信没有预约
	                $isSmsAppointment = Appointment::find()->where(['mobile' => $mobile])->one();
	                if ($isSmsAppointment) { // sms已经预约
	                    $model = Appointment::find()->where(['mobile' => $mobile])->one();
	                    $model->openid = $openid;
	                } else { // sms 没有预约,wechat 也没有预约,则全部一起预约
	                    $model = new Appointment();
	                    $model->name = $name;
	                    $model->created_at = time();
	                    $model->openid = $openid;
	                    $model->mobile = $mobile;
	                    $model->store_id = $store_id;
	                    $model->prize_id = $highest_user['result'];
	                }
	            }
	        } else { // SMS 预约则直接预约
	            $isSmsAppointment = Appointment::find()->where(['mobile' => $mobile])->one();
	            // 注意 从sms预约不需要考虑wechat 是否已经预约,因为wechat第一次预约已经全部预约了
	            if ($isSmsAppointment) {
	                return ['status' => 'error', 'errmsg' => '该用户已经预约过了'];
	            } else { // sms 还没有预约,则这是第一预约
	                $model = new Appointment();
	                $model->name = $name;
	                $model->created_at = time();
	                $model->mobile = $mobile;
	                $model->store_id = $store_id;
	                $model->prize_id = $highest_user['result'];
	            }
	        }
	        
	        // Log::debug('did has isWechatAppointment',$isWechatAppointment);
	        // Log::debug('did has isSmsAppointment',$isSmsAppointment);
	        
	        // 验证数据
	        if (!$model->validate()) {
	            // $errors = $model->getErrors();
	            $firsts = $model->getFirstErrors();
	            // 返回第一个错误
	            $first = current(array_values($firsts));
	            Log::debug('validate', $first);
	            return ['status' => 'error', 'errmsg' => $first];
	        }
	        
	        if (!$model->save(false)) {
	            Log::debug('操作失败');
	            return ['status' => 'error', 'errmsg' => '操作失败'];
	        }
	        
	        // 返回成功
	        $re = ['status' => 'success', 'data' => ['id' => $model->id]];
	    } else {
	        $re = ['status' => 'error', 'errmsg' => '无效请求'];
	    }
	    Log::debug('appointment result',$re);
	    return $re;
	}

	/**
     * @author bob.qiu
     * @param $uiser_id
     * 传入用户id 查询出用户信息 获取抽奖机会
     */
    public function actionLotteryOnce()
    {
		// 修改响应格式为JSON
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if (Yii::$app->request->isPost) {
            $user_id = Yii::$app->request->post('user_id');
            if (!User::find()->where(['id' => $user_id])->exists()) {
                return ['status' => 'error', 'errmsg' => '用户不存在', 'errcode' => -1];
            }
            // 获取用户信息 
			$user = User::find()->where(['id' => $user_id])->one();
			
			// 获取预约信息
			if ($user->mobile) {
				$appointment = Appointment::find()->where(['mobile' => $user->mobile])->one();
			}

			// 判断是否已经核销
			if ($user->redeem_prize) {
				return ['status' => 'error', 'errmsg' => '该用户已领取奖品', 'errcode' => -4];
			}
            
            // 判断用户是否有抽奖机会
			$chances = Record::find()->where(['user_id' => $user_id])
											->andWhere(['>', 'lottery_at', strtotime(date('Y-m-d'))])
											->andWhere(['<=', 'lottery_at', strtotime(date('Y-m-d'))+3600*24])	
											->orderBy('get_at asc')
											->asArray()
											->all();											
			$small_chance_limit = 0;
			$big_chance_limit = 0;
			
			// 是否开启限制每天的，没开启的话可以一直抽
			if (Yii::$app->params['limit']) {
				foreach($chances as $chance) {
					if ($chance['type']==1) $small_chance_limit ++;
					if ($chance['type']==2) $big_chance_limit ++;
				}
			}
			
			// echo'<pre>';var_dump($small_chance_limit,$big_chance_limit);die;
			if ($big_chance_limit < BIG_MAX) { // 当天抽小奖已达上限,开始抽大奖
				// 是否还有抽大奖机会的来源
				$record = Record::find()->where(['status'=>1,'type'=>2,'user_id'=>$user_id])->orderBy('get_at asc')->one();
				if (!$record) { // 大奖机会来源已经消耗完
					// 是否还有抽小奖机会的来源
					$record = Record::find()->where(['status'=>1,'type'=>1,'user_id'=>$user_id])->orderBy('get_at asc')->one();
					if (!$record) { // 小奖机会来源已经消耗完
						return ['status' => 'error', 'errmsg' => '该用户没有获得抽奖机会', 'errcode' => -2];
					} else { // 还有抽小奖机会,设置当前抽奖类型为小奖
						$chance_type = 1;
					}
				} else { // 还有抽大奖机会,设置当前抽奖类型为大奖
					$chance_type = 2;
				}
			} elseif ($small_chance_limit < SMALL_MAX) { // 当天抽小奖未达上限
				// 是否还有抽小奖机会的来源
				$record = Record::find()->where(['status'=>1,'type'=>1,'user_id'=>$user_id])->orderBy('get_at asc')->one();
				if (!$record) { // 小奖机会来源已经消耗完
					return ['status' => 'error', 'errmsg' => '该用户没有获得抽奖机会', 'errcode' => -2];
				} else { // 还有抽小奖机会,设置当前抽奖类型为小奖
					$chance_type = 1;
				}
			} else { // 大奖小奖机会都消耗完了
				return ['status' => 'error', 'errmsg' => '该用户没有获得抽奖机会', 'errcode' => -2];
			}

            // 根据group属性和type属性查询出该用户的抽奖规则
            $rule = Rule::find()->where(['type' => $chance_type])->select('prize_rate')->scalar();
            if (!$rule) {
                return ['status' => 'error', 'errmsg' => '没有设置抽奖规则', 'errcode' => -3];
            }
            $rates = json_decode($rule, true);

            // 过滤奖品库存不足的
            $rates = get_audit_prize($rates);
            // 获取当前概率的次方数
            $pow = get_pow($rates);
            // 进行抽奖算法
            $prize_id = get_prize($rates, $pow);
            // 更新抽奖记录表和奖品库存
            if ($prize_id) {				
				$current_prize = Prize::findOne($prize_id);
				$highest_prize = User::getHighestPrize($user_id);
				if (!empty($highest_prize)) { // 已经中过奖
					if ($current_prize->level < $highest_prize->level) { //本次奖品等级高于上次奖品,将上次奖品库存释放
						$highest_prize->gain_num--;
						$highest_prize->save(false);
						$current_prize->gain_num++;
						$current_prize->save(false);
						// 更新用户奖品为最高奖
						$user->result = $prize_id;

						// 更新预约为最高奖
						if (!empty($appointment)) {
							$appointment->prize_id = $prize_id;
							$appointment->save(false);
						}
					}				
				} else { //　第一次中奖
					$current_prize->gain_num++;
					$current_prize->save(false);
					// 第一次写入用户奖品
					$user->result = $prize_id;
				}
				
				// 保存抽奖结果
                $record->lottery_at = time();
                $record->result = $prize_id;
                $record->status = 2;
				$record->save(false);

				$re = ['status' => 'success', 'data' => 
					[
						'prize_id' => $current_prize->id,
						'prize_name' => $current_prize->name,
						'prize_level' => $current_prize->level,
					]
				];
            } else {
                $record->lottery_at = time();
                $record->result = 0;
                $record->status = 2;
                $record->save(false);

				$re = ['status' => 'success', 'data' => 
					[
						'prize_id' => '',
						'prize_name' => '',
						'prize_level' => '',
					]
				];
            }

            // 减少一次抽奖机会
            if ($record->type == 1) {
                $user->small_chance--;
            } else {
                $user->big_chance--;
            }
            $user->save(false);

            return $re;
        } else {
            return ['status' => 'error', 'errmsg' => 'invalid request'];
        }
	}
	
	/**
     * @author bob.qiu
     * 查找组装所有店名
     */
    public function actionAllStore()
    {
		// 修改响应格式为JSON
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		
        return Store::getAllStore();
    }

	// 签到
	public function actionAjaxCheckIn()
	{
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		//判断用户当天是否已经签到
		if (Yii::$app->request->isPost){
			$user_id = Yii::$app->request->post('user_id');
			//检查用户是否存在
			$user = User::findOne($user_id);
			if (!$user){
				return $re = ['status' => 'error', 'errmsg' => '无效的用户'];
			}

			//检查签到记录
			$isSigned = CheckIn::find()->where(['user_id'=>$user_id, 'date'=>strtotime(date('Y-m-d'))])->one();

			// dev
			if (Yii::$app->params['dev']) {
				$isSigned = false;
			}
			
			if (empty($isSigned)) {

				// 检查是否连续签到
				$last_checkin = CheckIn::find()->where(['user_id' => $user_id])->orderBy('date desc')->limit('1')->asArray()->one();
				if ($last_checkin) {
					// $last_continuous = $last_checkin['continuous'];
					$last_continuous = $user['continuous'];
					// 如果是连续签到，并且前一天为第7天
					if ((strtotime(date('Y-m-d'))-24*60*60  == $last_checkin['date']) && ($last_continuous == 5)) {
						$continuous = 1;
					// 如果是连续签到，并且不到第7天
					} elseif ((strtotime(date('Y-m-d'))-24*60*60  == $last_checkin['date']) && ($last_continuous < 5)) {
						$continuous = $last_continuous+1;
					// 如果不是连续签到
					} else {
						$continuous = 1;
					}
				// 如果是第一次签到
				} else {
					$continuous = 1;
				}
				$model = new CheckIn();
				$model->user_id = $user_id;
				$model->date = strtotime(date('Y-m-d'));
				$model->continuous = $continuous;
				$model->save();

				//修改用户连续签到天数
				$user->continuous = $continuous;
				$user->save();
				// 查询用户连续签到天数是否满足7天,满足7天给予大奖机会,否则给予小奖.
				if ($model->continuous == 5) {
					$checkin = customUtil::addRecords('seven', $user_id);
				} else {
					$checkin = customUtil::addRecords('checkin', $user_id);
				}				
	
				if ($checkin){
					$re = ['status' => 'success', 'data' => ['msg' => '签到成功']];
				} else {
					return ['status' => 'error', 'errmsg' => '签到失败'];
				}
			} else {
				return ['status' => 'error', 'errmsg' => '已经签到了'];
			}
		} else {
			return ['status' => 'error', 'errmsg' => '无效请求'];
		}

		return $re;
	}

	//返回用户的奖品信息
	public function actionRecord()
	{
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		if (Yii::$app->request->isPost) {
			$user_id = Yii::$app->request->post('user_id');
			//检查用户是否存在
			$user = User::findOne($user_id);
			if (!$user){
				return ['status' => 'error', 'errmsg' => '无效的用户'];
			}

			// 检查是否有手机号
			if (!empty($user->mobile)) {
				$appointment = Appointment::find()->where(['mobile' => $user->mobile])->one();
				if ($appointment) {
					$highest_user = User::getHighestPrizeUser($user->mobile);
					$user_id = $highest_user['id'];
				}
			}
			
			// 拿到已经使用的所有抽奖记录
			$records = Record::find()->where(['user_id' => $user_id])->andWhere(['status' => 2])->asArray()->all();

			if ($records){
				foreach ($records as $record){
					$level[] = Prize::find()->where(['id' => $record['result']])->select(['level'])->asArray()->one();
				}
				$re = ['status' => 'success', 'data' => ['data' => $level]];
			} else {
				return  ['status' => 'error', 'errmsg' => '没有抽奖记录'];
			}
		}
		return $re;
	}

	// 分享获得抽奖次数
	public function actionShareOnce()
	{
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		if (Yii::$app->request->isPost) {

			$scene = Yii::$app->request->post('scene');
			if ($scene == 'friend') {
				return ['status' => 'success'];
			} else {
				$user_id = Yii::$app->request->post('user_id');
				$re = CustomUtil::addRecords('share', $user_id);
				\frontend\components\Log::debug('share re', $re);
				if ($re === true) {
					return ['status' => 'success'];
				} else {
					return ['status' => 'error', 'errmsg' => $re];
				}
			}
		} else {
			return ['status' => 'error', 'errmsg' => '无效请求'];
		}
	}

	// 微信分享禁止 不进行数据库操作
	public function actionShareDisable (){
		if (Yii::$app->request->isPost) {
			return ['status' => 'success', 'errmsg' => '分享已禁止'];
		}else {
			return ['status' => 'error', 'errmsg' => '无效请求'];
		}
	}

	// 是否观看动画的接口
	public function actionUserViewed()
	{
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		if (Yii::$app->request->isPost) {
			$user_id = Yii::$app->request->post('user_id');
			$user = User::findOne($user_id);
			if (!$user) {
				return ['status' => 'error', 'errmsg' => '该用户不存在'];
			}
			$user->viewed = 1;
			if ($user->save(false)) {
				Log::debug('viewed = 1');
				return ['status' => 'success'];
			} else {
				Log::debug('viewed = 0');
				return ['status' => 'error', 'errmsg' => '操作失败'];
			}
		} else {
			return ['status' => 'error', 'errmsg' => '无效请求'];
		}
	}

	//前台核销
	public function actionRedeem()
	{
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

		if (Yii::$app->request->isPost) {
			$auth_code = Yii::$app->request->post('auth_code');
			if ($auth_code != AUTH_CODE) { // AUTH_CODE define in common/config/bootstarp.php
			    return ['status' => 'error', 'errmsg' => '授权码错误'];
			}

			$user_id = Yii::$app->request->post('user_id');
			//检查用户是否存在
			$user = User::findOne($user_id);
			if (!$user) {
				return ['status' => 'error', 'errmsg' => '无效的用户'];
			}  

			//拿到用户最高奖励
			$highest_user = User::getHighestPrizeUser($user->mobile);
			$prize = User::getHighestPrize($highest_user['id']);
			if (!$prize){
				return ['status' => 'error', 'errmsg' => '没有抽奖记录'];	
			}

			// 核销
			if (!empty($user->mobile)) {
				if (!User::updateAll(['redeem_prize' => $prize->id, 'redeem_at' => time()], ['mobile' => $user->mobile])) {
					\frontend\components\Log::debug('updateAll fail');
				}
			} else {
				return ['status' => 'error', 'errmsg' => '没有手机号'];
			}
			
			// // 更新记录
            // $store_id = Appointment::find()->select('store_id')->where(['mobile' => $user->mobile])->scalar();
            // $redeem_record = new RedeemRecord();
            // $redeem_record->store_id = $store_id;
            // $redeem_record->prize_id = $prize->id;
            // $redeem_record->mobile = $user->mobile;
            // $redeem_record->created_at = time();
            // $redeem_record->save();

			// 修改库存
            $prize->exchange_num++;
            
			if ($prize->save(false)){
				return ['status' => 'success'];
			}else{
				return ['status' => 'error', 'errmsg' => '核销失败'];
			}
		} else {
			return ['status' => 'error', 'errmsg' => '无效请求'];
		}
	}

	// 获取所有奖品的库存情况
	public function actionPrizeStock()
	{
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

		$prizes = Prize::find()->select('level, stock_num, gain_num')->asArray()->all();

		$appointment = Appointment::find()->select('count(prize_id) as sum, prize.level')->join('LEFT JOIN', 'prize', 'prize_id = prize.id')->groupBy('prize_id')->asArray()->all();
		$appointment = \yii\helpers\ArrayHelper::map($appointment, 'level', 'sum');
		
		$np = [];
		foreach ($prizes as $k => $prize) {
			if ($prize['level'] == 1) {
				$left = 5 - $prize['gain_num'];
				if ($left < 0) {
					$left = 0;
				}
				$np[1] = ['total' => 5, 'left' => $left];
			} elseif ($prize['level'] == 2) {
				$left = 20 - $prize['gain_num'];
				if ($left < 0) {
					$left = 0;
				}
				$np[2] = ['total' => 20, 'left' => $left];
			} elseif ($prize['level'] == 3) {
				$left = 80 - (isset($appointment[3]) ? $appointment[3] : 0);
				if ($left < 0) {
					$left = 0;
				}
				$np[3] = ['total' => 80, 'left' => $left];
			} elseif ($prize['level'] == 4) {
				$left = 200 - (isset($appointment[4]) ? $appointment[4] : 0);
				if ($left < 0) {
					$left = 0;
				}
				$np[4] = ['total' => 200, 'left' => $left];
			} elseif ($prize['level'] == 5) {
				$left = 2000 - (isset($appointment[6]) ? $appointment[6] : 0);
				if ($left < 0) {
					$left = 0;
				}
				$np[5] = ['total' => 2000, 'left' => $left];
			}
		}
		return $np;
	}

	//创建DMS微信用户
	public function actionCreateWeChat()
	{

	}
}
