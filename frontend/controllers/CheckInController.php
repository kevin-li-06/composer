<?php 
namespace frontend\controllers;

use Yii;
use frontend\components\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use frontend\models\CheckIn;
use frontend\models\User;
use \common\components\customUtil;
use common\models\Record;
use \common\models\LoginForm;
use \common\components\Jssdk;

/**
* Created By Chris 
* 2017-11-02
* 会员签到控制器,用于处理会员签到相关逻辑
*/

class CheckInController extends Controller
{

	public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => [],
                'rules' => [
                    [
                        'actions' => [],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                   
                ],
            ],           
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }
	public function init()
	{

		//模拟hash手机登录
		$hash = '$2y$13$DrRaMzzqeAc7.kH2cHO4xuwZQ/rR4uhhSP2k9DJ/cNUngn./M93VO';
		$form = new LoginForm();
		$form->mobile_hash = $hash;
		$re = $form->loginFromMobile();
	}

	/*
	* 默认展示签到页面 
	*/ 
	public function actionIndex()
	{	
		// 会员的签到记录
		$user_id = (!empty(yii::$app->user->id))?:2;
		$checkinData = User::find()->alias('a')
									->joinWith('checkinList')
									->where(['a.id'=>$user_id])
									->asArray()	
									->one();		

		// 签到的日历面板								
		$calendar = array();
		$calendar_begin = strtotime(date('Y-m-d')) - 5*24*3600;

		for ($i=0; $i < 11 ; $i++) { 
			$calendar[] = $calendar_begin + $i*24*3600;
		}

		// echo "<pre>"; var_dump($checkinData); die;
		return $this->render('index', ['checkinData'=>$checkinData, 'calendar'=>$calendar]);
	}

	public function actionAjaxDoSign()
	{	
		$isSigned = CheckIn::find() ->where(['user_id'=>$_POST['user_id'], 'date'=>strtotime(date('Y-m-d'))])->one();
		
		if (empty($isSigned)) {
			$model = new CheckIn();
			$model -> user_id = $_POST['user_id'];
			$model -> date = strtotime(date('Y-m-d'));
			$model -> continuous = $this->getContinuous($_POST['user_id']);
			// echo '<pre>'; var_dump($model);die;
			$a = customUtil::addRecords('checkin',$_POST['user_id']);
			$model -> save();
			
			echo json_encode('success');
		} else {
			echo json_encode('fail');
		}
	}

	public function actionTest()
	{
		// echo ;

	}
	public function getContinuous($user_id)
	{
		$last_checkin = CheckIn::find()->where(['user_id'=>$user_id])->asArray()->orderBy('date desc')->limit('1')->one();	
		return (strtotime(date('Y-m-d'))-24*60*60  == $last_checkin['date'])? $last_checkin['continuous']+1:1;
	}

} 