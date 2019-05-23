<?php

namespace backend\controllers;

use Yii;
use common\models\Rule;
use backend\models\Prize;
use yii\data\ActiveDataProvider;
use backend\components\BaseAdminController;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * RuleController implements the CRUD actions for Rule model.
 */

class RuleController extends BaseAdminController
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
                    'delete' => ['POST'],
                ],
            ],
        ];
    }
    
    /**
     * Lists all Rule models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Rule::find(),
        ]);
        
        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }
    
    /**
     * Displays a single Rule model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        $prizes = $this->allPrizes();
        
        // 读取分组和配置信息
        $model = $this->findModel($id);
        $rates = json_decode($model->prize_rate, true);
        
        return $this->render('view', [
            'model' => $model,
            'prizes' => $prizes,
            'rates' => $rates,
        ]);
    }
    
    /**
     * Creates a new Rule model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        // 检查是否有奖品
        $prizes = $this->allPrizes();
        if (empty($prizes)) {
            $createPrizeUrl = \yii\helpers\Url::to(['prize/create']);
            exit('您还没有添加过奖品!<br><a href="' . $createPrizeUrl . '">添加奖品</a>');
        }
        $model = new Rule();
        
        if ($model->prize_rate) {
            $rates = json_decode($model->prize_rate, true);
        } else {
            $rates = null;
        }


        if (Yii::$app->request->isPost) {
            $rates = Yii::$app->request->post('Rate');
            $sum = 0;
            foreach($rates as $v) {
                $sum += $v;
            }
            // 总概率不能超过100%
            if ($sum > 100) {
                Yii::$app->session->setFlash('error', '当天所有奖品的总概率不能大于100%');
            } else {
                foreach($rates as $k => $v) {
                    if (empty($v)) {
                        unset($rates[$k]);
                    }
                }
                $model->prize_rate = json_encode($rates);

                if ($model->save()) {
                    Yii::$app->session->setFlash('success', '配置成功');
                    return $this->redirect(['rule/view', 'id' => $model->id]);
                }
            }
            return $this->render('create', [
                'model' => $model,
                'prizes' => $prizes,
                'rates' => $rates,
            ]);
        } else {
            return $this->render('create', [
                'model' => $model,
                'prizes' => $prizes,
                'rates' => $rates,
            ]);
        }
    }
    
    /**
     * Updates an existing Rule model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $prizes = $this->allPrizes();
        
        // 读取分组和配置信息
        $model = $this->findModel($id);
        $rates = json_decode($model->prize_rate, true);
        
        if (Yii::$app->request->isPost) {
            // $this->bp($model);
            $rates = Yii::$app->request->post('Rate');
            $sum = 0;
            foreach($rates as $v) {
                $sum += $v;
            }
            // 总概率不能超过100%
            if ($sum > 100) {
                Yii::$app->session->setFlash('error', '当天所有奖品的总概率不能大于100%');
            } else {
                foreach($rates as $k => $v) {
                    if (empty($v)) {
                        unset($rates[$k]);
                    }
                }
                
                $model->prize_rate = json_encode($rates);

                if ($model->save(false)) {
                    Yii::$app->session->setFlash('success', '配置成功');
                    return $this->redirect(['rule/view', 'id' => $model->id]);
                }
            }
            return $this->render('update', [
                'model' => $model,
                'prizes' => $prizes,
                'rates' => $rates,
                'preview' => false,
            ]);
        } else {
            return $this->render('update', [
                'model' => $model,
                'prizes' => $prizes,
                'rates' => $rates,
                'preview' => true,
            ]);
        }
    }
    
    /**
     * Deletes an existing Rule model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->delete();
        return $this->redirect('index');
    }
    
    // 模拟抽奖
    public function actionTest($id)
    {
        $model = $this->findModel($id);
        
        if (Yii::$app->request->isPost) {
            $times = Yii::$app->request->post('times');
            
            // 查询当前id的概率
            $rate = Rule::findrate($id);
            
            // 过滤掉溢出库存的奖品数
            $rates = get_audit_prize($rate);
            // echo "<pre>";var_dump($rates);die;
            // 查询当前规则下的奖品
            $prizes = $this->allPrizes(array_keys($rates), true);
            
            // 构造未中奖数据
            $result = [];
            $result[0]['score'] = 0;
            $result[0]['name'] = "未中奖";
            $result[0]['rate'] = 100 - (array_sum($rates));
            
            // 组装后台展示数据
            foreach ($prizes as $key => $value) {
                $result[$value['id']]['score'] = 0;
                $result[$value['id']]['name'] = $value['name'];
                $result[$value['id']]['rate'] = $rates[$value['id']];
            }
            
            // 小数点位数，10的多少次方
            $pow = get_pow($rates);
            // echo "<pre>";var_dump($rates);die;
            
            for ($i = 1;$i <= $times;$i++) {
                $re = get_prize($rates, $pow);
                // var_dump($re);die;
                $result[$re]['score']++;
            }
            // $this->bp($result);
            // 当未中奖概率为零时，前台不展示
            if ($result[0]['score']==0 && $result[0]['rate']==0) unset($result[0]);
            return $this->render('test', [
                'model' => $model,
                'result' => $result,
                'times' => $times,
            ]);
        }
        
        return $this->render('test', [
            'model' => $model
        ]);
    }
    /**
     * Finds the Rule model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Rule the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Rule::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    // 返回所有奖品
    protected function allPrizes($ids = null, $asArray = false)
    {
        if ($asArray) {
            if ($ids) {
                $models = Prize::find()->where(['id' => $ids])->asArray()->all();
            } else {
                $models = Prize::find()->asArray()->all();
            }
        } else {
            if ($ids) {
                $models = Prize::find()->where(['id' => $ids])->all();
            } else {
                $models = Prize::find()->all();
            }
        }
        
        return $models;
    }
}
