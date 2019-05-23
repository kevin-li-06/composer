<?php 
namespace frontend\controllers;

use Yii;
use frontend\components\Log;
use frontend\components\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\web\NotFoundHttpException;

use frontend\components\Auth;
use frontend\models\Token;

/**
 * @OA\Info(
 *     version="1.0",
 *     title="Access Token Controller",
 *     description = "this is API description"
 * )
 */
class TokenController extends Controller
{
    public $enableCsrfValidation = false;
/**
 * @OA\Post(
 *     tags = {"Token/Get"},
 *     path="/hk-lottery/frontend/web/index.php/token/get",
 *     summary="Create a new access token",
 *     @OA\Parameter(
 *         name="username",
 *         in="query",
 *         description="Psername values that needed to be considered for filter",
 *         required=true,
 *         explode=true,
 *         @OA\Schema(
 *             type="array",
 *             default="admin@xgate.com",
 *             @OA\Items(
 *                 type="string",
 *                 enum = {"admin@xgate.com"},
 *             )
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="password",
 *         in="query",
 *         description="Password values that needed to be considered for filter",
 *         required=true,
 *         explode=true,
 *         @OA\Schema(
 *             type="array",
 *             default="xg1234",
 *             @OA\Items(
 *                 type="string",
 *                 enum = {"xg1234"},
 *             )
 *         )
 *     ),

 *     @OA\RequestBody(
 *         @OA\MediaType(
 *             mediaType="application/x-www-form-urlencoded",
 *             @OA\Schema(
 *                 @OA\Property(
 *                     property="username",
 *                     type="string"
 *                 ),
 *                 @OA\Property(
 *                     property="password",
 *                     type="string"
 *                 ),
 *                  @OA\Property(
 *                     property="account_id",
 *                     type="string"
 *                 ),
 *                 example={"username": "admin@xgate.com", "password": "xg1234", "account_id": "demo"}
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="success",         
 *     ),
 *     @OA\Response(
 *        response=400,
 *        description="Invalid ID supplied"
 *     ),
 * )
 */
    public function actionGet()
    {
        // $data = (Yii::$app->request->post() ? : json_decode(file_get_contents("php://input"), true));
        // echo '<pre>'; print_r(file_get_contents("php://input"));die;
        // 修改响应格式为JSON
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;      
		$auth = (new Auth(
							'token', 
                            'get', 
                            $data = Yii::$app->request->post()
							// $data = (Yii::$app->request->post() ? : json_decode(file_get_contents("php://input"), true))
							// Yii::$app->request->get('access_token')
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
            // 注意: 将来还需要验证 username, password, account_id 的真实性
            $token_model = Token::find()->where(['username'=>$data['username'], 'password'=>$data['password'], 'account_id'=>$data['account_id']])->one(); 
            if ($token_model) 
            {
                if ($token_model->access_expires_at < time()) // token过期               
                {
                    $token_model->access_token = $this->generateToken($data);
                    $token_model->refresh_token = $this->generateToken($data);
                    $token_model->access_expires_at = time()+$token_model->expires_in;
                    $token_model->update_at = time();
                    $token_model->save();
                }

                return $res = [
                    'status' => 'success', 
                    'expires_in' => $token_model->expires_in,
                    'access_token' => $token_model->access_token,
                    'refresh_token' => $token_model->refresh_token
                ];
            }  
            else // 第一次请求token
            {
                $token_model = new Token();
                $token_model->access_token = $this->generateToken($data);
                $token_model->refresh_token = $this->generateToken($data);
                $token_model->expires_in = 7200;
                $token_model->access_expires_at = time()+$token_model->expires_in;
                $token_model->create_at = time();
                $token_model->update_at = time();
                $token_model->username = $data['username'];
                $token_model->password = $data['password'];
                $token_model->account_id = $data['account_id'];
                $token_model->save();

                return $res = [
                    'status' => 'success', 
                    'expires_in' => $token_model->expires_in,
                    'access_token' => $token_model->access_token,
                    'refresh_token' => $token_model->refresh_token
                ];
            }             
        }
    }


    public function actionRefresh()
    {
          // 修改响应格式为JSON
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		
		$auth = (new Auth(
							'token', 
							'refresh', 
							$data = Yii::$app->request->post() 
							// Yii::$app->request->get('access_token')
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
            $token_model = Token::find()->where(['refresh_token'=>$data['refresh_token']])->one(); 
            if ($token_model) 
            {
                if ($token_model->access_expires_at < time()) // token过期则替换token              
                {
                    $token_model->access_token = $this->generateToken($data);
                    $token_model->refresh_token = $this->generateToken($data);
                    $token_model->access_expires_at = time() + $token_model->expires_in;
                    $token_model->update_at = time();
                    $token_model->save();
                    $res = [
                        'status' => 'success', 
                        'expires_in' => $token_model->expires_in,
                        'access_token' => $token_model->access_token,
                        'refresh_token' => $token_model->refresh_token
                    ];
                }
                else
                if ($token_model->alternate_expires_at < time()) // token未过期则创建一个新的备用token
                {
                    $token_model->alternate_token = $this->generateToken($data);
                    $token_model->refresh_token = $this->generateToken($data);
                    $token_model->alternate_expires_at = time() + $token_model->expires_in;
                    $token_model->update_at = time();
                    $token_model->save();
                    $res = [
                        'status' => 'success', 
                        'expires_in' => $token_model->expires_in,
                        'access_token' => $token_model->alternate_token,
                        'refresh_token' => $token_model->refresh_token
                    ];
                }

                return $res;
            }  
            else 
            {
                return $res = [
                    'status' => 'error', 
                    'errorCode' => 40012,
                    'message' => 'Invalid Refresh Token'
                ];
            }             
        }
    }

/**
 * @OA\Get(
 *     tags = {"Token/GetSSs"},
 *     path="/hk-lottery/frontend/web/index.php/token/get",
 *     summary="Create a new access token",
 *     @OA\Parameter(
 *         name="username",
 *         in="query",
 *         description="Psername values that needed to be considered for filter",
 *         required=true,
 *         explode=true,
 *         @OA\Schema(
 *             type="array",
 *             default="admin@xgate.com",
 *             @OA\Items(
 *                 type="string",
 *                 enum = {"admin@xgate.com"},
 *             )
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="password",
 *         in="query",
 *         description="Password values that needed to be considered for filter",
 *         required=true,
 *         explode=true,
 *         @OA\Schema(
 *             type="array",
 *             default="xg1234",
 *             @OA\Items(
 *                 type="string",
 *                 enum = {"xg1234"},
 *             )
 *         )
 *     ),

 *     @OA\RequestBody(
 *         @OA\MediaType(
 *             mediaType="application/x-www-form-urlencoded",
 *             @OA\Schema(
 *                 @OA\Property(
 *                     property="username",
 *                     type="string"
 *                 ),
 *                 @OA\Property(
 *                     property="password",
 *                     type="string"
 *                 ),
 *                  @OA\Property(
 *                     property="account_id",
 *                     type="string"
 *                 ),
 *                 example={"username": "admin@xgate.com", "password": "xg1234", "account_id": "demo"}
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="success",         
 *     ),
 *     @OA\Response(
 *        response=400,
 *        description="Invalid ID supplied"
 *     )
 * )
 */
    protected function generateToken($data)
    {        
        return Yii::$app->security->generateRandomString(40,join('-', $data));        
    }


    
}