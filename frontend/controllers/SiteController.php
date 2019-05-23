<?php
namespace frontend\controllers;

use frontend\components\Swagger;
use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
// use yii\web\Controller;
use frontend\components\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use frontend\components\Loyalty;
use frontend\components\Wechat;
use frontend\components\Auth;

/**
 * @OA\Info(
 *     version="1.0",
 *     title="Access Token Controller",
 *     description = "this is API description"
 * )
 */
class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
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
            // 'error' => [
                // 'class' => 'yii\web\ErrorAction',
            // ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->render('index');
    }



    public function actionTest()
    {
        Swagger::CreateJson();
//        $b2app = Yii::getAlias('@app');
////        echo  dirname(dirname(dirname(__FILE__)));die;
//        $file_path = $b2app.'/controllers/';
//        // $file_path = $b2app.'/controllers/LuckyDrawController.php';
//        $openapi = \OpenApi\scan($file_path);
//        // echo '<pre>'; print_r($openapi->toJson());
////       $doc_handler = fopen('F:\swagger-ui\docs\chris\swagger.json', 'w');
//       $doc_handler = fopen(dirname(dirname(dirname(__FILE__))).'\swagger-ui\docs\chris\swagger.json', 'w');
//       fwrite($doc_handler, $openapi->toJson());
//       fclose($doc_handler);
//       echo 'success';

        $redis = Yii::$app->redis;
        echo '<pre>';print_r($redis);die;

    }



   
    /**
     * Logs in a user.
     *
     * @return mixed
     */
    // public function actionLogin()
    // {
    //     if (!Yii::$app->user->isGuest) {
    //         return $this->goHome();
    //     }

    //     $model = new LoginForm();

    //     if (yii::$app->request->isPost) {
    //         foreach (Yii::$app->request->post('LoginForm') as $key => $value) {
    //             $model->$key = $value;
    //         }
    //         if ($model->login()) {
    //             return $this->goBack();
    //         } 
    //     } else {
    //         return $this->render('login', [
    //             'model' => $model
    //         ]);
    //     }
    // }

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return mixed
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
            } else {
                Yii::$app->session->setFlash('error', 'There was an error sending your message.');
            }
            return $this->refresh();
        } else {
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Displays about page.
     *
     * @return mixed
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    /**
     * @OA\Post(
     *     tags = {"Signup/Get"},
     *     path="/hk-lottery/frontend/web/index.php/site/signup",
     *     summary="signup",
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


    public function actionSignup()
    {
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
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($user = $model->signup()) {
                if (Yii::$app->getUser()->login($user)) {
                    return $this->goHome();
                }
            }
        }
        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');

                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for the provided email address.');
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password saved.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    // 测试使用mobile_hash登录
    public function actionLogin()
    {
        Yii::$app->user->logout();        
        $hash = (!empty($_GET['user']))? $_GET['user']:'$13$oL8fa5u1d8E1ZqBiVeskC.qu.4zBzwu.XhrY2rrzJu7kbPWNfLHc2';       
        $form = new LoginForm();
        $form->mobile_hash = $hash;
        $re = $form->loginFromMobile();
        if ($re) {
            $this->redirect(['check-in/index']);
        }
    }

    public function actionWxjs()
    {
        // Wechat::force_access_token();
        $this->bp(Wechat::ips());
        // echo $access_token;
    }

    public function actionError() 
    {
        $exception = Yii::$app->errorHandler->exception;
        if ($exception !== null) {
            return $this->render('myerror');
        }
    }
}
