<?php

namespace backend\controllers;

use common\components\CustomUtil;
use Yii;
use common\models\Record;
use common\models\RecordSearch;
use common\models\User;
use common\models\Prize;
// use common\models\RedeemRecord;
// use common\models\Appointment;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use backend\components\BaseAdminController;


/**
 * RecordController implements the CRUD actions for Record model.
 */

class RecordController extends BaseAdminController
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
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
//                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Record models.
     * @return mixed
     */
    public function actionIndex($id)
    {
        $user = User::findOne($id);

        $prizes = Prize::allPrizes(null, true);
        $prizes = \yii\helpers\ArrayHelper::map($prizes, 'id', 'name');

        $searchModel = new RecordSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $id);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'user' => $user,
            'prizes' => $prizes,
        ]);
    }

    /**
     * Displays a single Record model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Record model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Record();

        if ($model->load(Yii::$app->request->post()) && $model->save(false)) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Record model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save(false)) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Record model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id, $user_id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index','id' => $user_id]);
    }

    /**
     * Finds the Record model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Record the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Record::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionRecordConsumption()
    {
        $data = Yii::$app->request->post();
        if (!$data['receipts']){
            Yii::$app->session->setFlash('error', '请填写小票信息');
            return $this->redirect(['user/index']);
        }
        if ($data['user_id']){
            $re = CustomUtil::addRecords('consumption', $data['user_id'], $data['receipts'], $data['receipts_type']);
            if ($re === '消费机会已达上限'){
                Yii::$app->session->setFlash('error', $re);
            }else {
                Yii::$app->session->setFlash('success', '操作成功！');
            }
        }
    }

    // 核销奖品
    public function actionRedeem()
    {
        if (Yii::$app->request->isPost) {
            // 判断授权码是否正确
            $auth_code = Yii::$app->request->post('auth_code');
            if ($auth_code != AUTH_CODE) { // AUTH_CODE define in common/config/bootstarp.php
                Yii::$app->session->setFlash('error', '不是正确的授权码');
                return $this->redirect(['appointment/index']);
            }

            // 获取用户信息
            $user_id = Yii::$app->request->post('user_id');            
            $user = User::findOne($user_id);

            // 获取所有能兑换的奖品
            $prize = User::getHighestPrize($user_id);

            // 核销
            if (!empty($user->mobile)) {
                if (!User::updateAll(['redeem_prize' => $prize->id, 'redeem_at' => time()], ['mobile' => $user->mobile])) {
                    \backend\components\Log::debug('updateAll fail');
                }
            } else {
                Yii::$app->session->setFlash('error', '无效的手机号');
                return $this->redirect(['appointment/index']);
            }

            // 修改库存
            $prize->exchange_num++;
            $prize->save(false);

            // 更新记录
            // $store_id = Appointment::find()->select('store_id')->where(['mobile' => $user->mobile])->scalar();
            // $redeem_record = new RedeemRecord();
            // $redeem_record->store_id = $store_id;
            // $redeem_record->prize_id = $prize->id;
            // $redeem_record->mobile = $user->mobile;
            // $redeem_record->created_at = time();
            // $redeem_record->save();
            
            Yii::$app->session->setFlash('success', '核销成功');
            return $this->redirect(['appointment/index']);
        } else {
            return $this->redirect(['appointment/index']);
        }
    }

    // 改变奖品
    public function actionChangePrize($id)
    {
        $model = User::findOne($id);

        // 获取所有能兑换的奖品
        $prize = User::getHighestPrize($id);

        if (Yii::$app->request->isPost) {
            $model->change_prize = Yii::$app->request->post('change_prize');
            $model->change_at = time();
            $model->save();

            // 改变库存
            $change_prize = Prize::find()->where(['id' => Yii::$app->request->post('change_prize')])->one();
            $change_prize->gain_num++;
            $change_prize->exchange_num++;
            $change_prize->save(false);

            // 退还库存
            $prize->gain_num--;
            $change_prize->exchange_num--;
            $prize->save(false);
            
            Yii::$app->session->setFlash('success', '已改变奖品');
            return $this->redirect(['appointment/index']);
        }
        
        if ($prize) {
            $prizes = Prize::find()->where('level > ' . $prize->level)->asArray()->all();
            $prizes = \yii\helpers\ArrayHelper::map($prizes, 'id', 'name');
        } else {
            Yii::$app->session->setFlash('error', '该用户没有可兑换的奖品！');
            return $this->redirect(['appointment/index']);
        }

        return $this->render('change-prize', [
            'model' => $model,
            'prizes' => $prizes,
        ]);
    }
}
