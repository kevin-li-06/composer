<?php 
namespace frontend\controllers;

use Yii;
use frontend\components\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use frontend\models\Signin;
use frontend\models\LotteryChance;
use \common\models\User;
use \common\models\LoginForm;
use \common\components\CustomUtil;
use \common\components\Jssdk;

/**
 * Class Pet
 *
 * @package Petstore30\controllers
 *
 * @author  Donii Sergii <doniysa@gmail.com>
 */
/**
 * @OA\Info(
 *     version="1.0",
 *     title="Lucky Draw Controller",
 *     description = "this is API description"
 * )
 */
class LuckyDrawController extends Controller
{	
    /**
     * @OA\Post(
     *     path="/pet",
     *     tags={"pets"},
     *     summary="Add a new pet to the store",
     *     operationId="addPet",
     *     @OA\Response(
     *         response=405,
     *         description="Invalid input"
     *     ),
     *     security={
     *         {"petstore_auth": {"write:pets", "read:pets"}}
     *     },
     *     requestBody={"$ref": "#/components/requestBodies/Pet"}
     * )
     */
    public function init()
    {
       
        //模拟hash手机登录
        $hash = '$2y$13$YzD/.blcC6f7p9AJXtGoVOcGV4UKIpOeQ.Q4VWsTY/sr0sOQSU1TO';
        $form = new LoginForm();
        $form->mobile_hash = $hash;
        $re = $form->loginFromMobile();
    }

    public function actionTest()
    {
        return $this->render('test');   
    }

    public function actionTest2()
    {
        $user_info = Yii::$app->user->identity;
        echo CustomUtil::addRecords('consumption',$user_info->id);
    }

    public function actionIndex()
    {
        $signPackage = Jssdk::getSignPackage();
        $user_info = Yii::$app->user->identity;
        $user = User::find()->where(['id' => $user_info->id])->asArray()->one();
        return $this->render('index', [
            'user' => $user,
            'signPackage' => $signPackage,
        ]);
    }

    //增加微信分享抽奖机会
    public function actionAjaxShare()
    {
        $user_info = Yii::$app->user->identity;
        $re= CustomUtil::addRecords('share', $user_info->id);
        if ($re == '已分享') {
            $result = ['status' => 'failure', 'message' => '您已分享过了'];
        } else  {
            $result = ['status' => 'succes', 'message' => '分享成功'];
        }
        echo json_encode($result);
    }

    // 测试抽奖概率
    public function actionTestPrize() 
    {
        if (Yii::$app->request->ispost) {
            $data = Yii::$app->request->post();
            $count = $data['count'];
            unset($data['_csrf-frontend']);
            unset($data['count']);            
            echo "<pre>";
            var_dump($data);die;
        }
    }


   
    
}