<?php
namespace backend\controllers;

use Yii;
use yii\web\Controller;
use backend\components\BaseAdminController;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use backend\models\AdminLoginForm;

/**
 * @OA\Info(
 *     version="1.0",
 *     title="Access Token Controller",
 *     description = "this is API description"
 * )
 */

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
class SiteController extends BaseAdminController
{
    /**
     * @inheritdoc
     */
        public function behaviors()
        {
            return [
                'access' => [
                    'class' => AccessControl::className(),
                    'rules' => [
                        [
                            'actions' => ['login', 'error'],
                            'allow' => true,
                        ],
                        [
                            'actions' => ['logout', 'index'],
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
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {        
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new AdminLoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
}
