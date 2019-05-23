<?php

namespace backend\controllers;

use Yii;
use backend\models\Prize;
use common\models\Store;
use backend\models\StoreSearch;
use backend\models\StockArraySearch;
use common\models\Appointment;
use common\models\User;
use common\models\AppointmentSearch;
use backend\components\BaseAdminController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use backend\models\UploadForm;
use yii\web\UploadedFile;
use common\components\Excel;
use backend\models\StoreStock;
use yii\data\Sort;
use yii\data\ArrayDataProvider;


/**
 * AppointmentController implements the CRUD actions for Appointment model.
 */

class AppointmentController extends BaseAdminController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Appointment models.
     * @return mixed
     */
    public function actionIndex()
    {
        ini_set('memory_limit', '3000M');

        $prizes = Prize::find()->asArray()->all();
        $prizesMap = \yii\helpers\ArrayHelper::map($prizes, 'id', 'name');

        if (Yii::$app->user->identity->id_role == 3) {
            $store_code = Yii::$app->user->identity->username;
            $store = \common\models\Store::find()->where(['store_code' => $store_code])->one();
            $appointment_all = Appointment::find()->where(['store_id' => $store->id])->asArray()->all();

            foreach ($prizesMap as $key => $v) {
                $stock[$key] = StoreStock::find()->select('stock')->where(['prize_id' => $key, 'store_id' => $store->id])->scalar();
            }
        } else {
            $store = '';
            $appointment_all = Appointment::find()->asArray()->all();
            foreach ($prizesMap as $key => $v) {
                $stock[$key] = StoreStock::find()->where(['prize_id' => $key])->asArray()->sum('stock');
            }
        }
        
        foreach ($prizesMap as $k => $v) {
            $sum[$k] = 0;
        }

        foreach ($appointment_all as $v) {
            if (!empty($v['prize_id'])) {
                $sum[$v['prize_id']]++;
            }
        }

        // $this->bp($appointment_all);
        
        $appointment_user = User::find()->select('id, redeem_at, change_at, mobile')->where(['>', 'result', '0'])->distinct()->addSelect('result')->orderBy(['result' => SORT_ASC])->asArray()->all();
        // $appointment_user = \yii\helpers\ArrayHelper::map($appointment_user, 'mobile', ['result', 'id']);
        $n_a_u = [];
        foreach ($appointment_user as $k => $v) {
            $n_a_u[$v['mobile']] = [
                'user_id' => $v['id'],
                'redeem_at' => $v['redeem_at'],
                'change_at' => $v['change_at'],
            ];
        }

        $stores = Store::find()->asArray()->all();
        $stores = \yii\helpers\ArrayHelper::map($stores, 'id', 'storename');

        foreach ($appointment_all as $k => $v) {
            if (!empty($n_a_u[$v['mobile']]['user_id'])) {
                $appointment_all[$k]['user_id'] = $n_a_u[$v['mobile']]['user_id'];
                $appointment_all[$k]['redeem_at'] = $n_a_u[$v['mobile']]['redeem_at'];
                $appointment_all[$k]['change_at'] = $n_a_u[$v['mobile']]['change_at'];
                $appointment_all[$k]['prize_id'] = $prizesMap[$v['prize_id']];
                $appointment_all[$k]['store_id'] = $stores[$v['store_id']];
            }
        }
        // $this->bp($appointment_all);
        $dataProvider = new ArrayDataProvider([
            'allModels' => $appointment_all,
            'pagination' => [
                'pageSize' => 10,
            ],
            // 'sort' => [
            //     'attributes' => ['id', 'name'],
            // ],
        ]);

        $prizes_model = Prize::allPrizes();
        $prizes = \yii\helpers\ArrayHelper::map($prizes_model, 'id', 'name');
        $stores_model = Store::find()->all();               
        $stores = \yii\helpers\ArrayHelper::map($stores_model, 'id', 'storename'); 

        // $searchModel = new AppointmentSearch();
        // $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $searchModel = new \common\models\AppointmentArraySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
//         echo "<pre/>";
//         var_dump($dataProvider);die;
        return $this->render('index', [
            'prizes' => $prizes,
            'stores' => $stores,
            'store' => $store,
            'prizesMap' => $prizesMap,
            'sum' => $sum,
            'searchModel' => $searchModel,
            // 'dataProvider' => $dataProvider,
            'dataProvider' => $dataProvider,
            'stock' => $stock
        ]);
    }

    /**
     * Displays a single Appointment model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /***
     * @author bob.qiu
     * 导入(更新)门店商品库存
     */
    public function actionImportStock() 
    {
        $model = new UploadForm();
        if (Yii::$app->request->isPost) {
            $model->file = UploadedFile::getInstance($model, 'file');
            // 上传路径
            $filePath = Yii::$app->basePath.'/uploads/';
            if ($model->file && $model->validate()) {
                $model->file->saveAs($filePath . $model->file->baseName . '.' . $model->file->extension);
            }
            $file_url = $filePath.$model->file->name;
            $config = [
                'table' => 'store_stock',
                'field' => [
                   'store_id',
                   'prize_id',
                   'stock',
                   'update_at',
                ],
            ];
            $result = Excel::importstorestock($file_url,$config);
            if ($result) {
                Yii::$app->session->setFlash('success', '操作成功！');
            } else{
                Yii::$app->session->setFlash('error', '操作失败');
            }
            return $this->redirect('index');
        }
        $store = Store::find()->all();
        if (empty($store)) {
            Yii::$app->session->setFlash('error', '请先导入门店');
            return $this->redirect('index');
        }else {
            return $this->render('import',[
                'model'=> $model
            ]);
        }
        
    }

    /**
     * Creates a new Appointment model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Appointment();

        if ($model->load(Yii::$app->request->post())) {
            $model->created_at = time();
            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Appointment model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Appointment model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Appointment model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Appointment the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Appointment::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    //找出所有门店预约的单个奖品总量
    public function actionTest()
    {
        $appointments = Appointment::find()->asArray()->all();
        foreach ($appointments as $appointment) {
            $user = User::find()->where(['mobile' => $appointment['mobile']])->select(['result'])->asArray()->all();
//            $user =
        }
    }
    
    /**
     * @author bob.qiu
     * 总门店查看各个门店预约情况和已补库存 计算显示各个门店需要补充的库存
     */
    public function actionStoreStock()
    {
        $searchModel = new StockArraySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('storeStock',[
            'dataProvider'=> $dataProvider,
            'searchModel'=> $searchModel,
        ]);
    }

    //添加门店库存剩余情况
    public function addStock()
    {
        $prizes = [1,2,3,4,5,6];
        $stores = Store::find()->asArray()->all();
        if ($stores) {
            foreach ($stores as $k => $store) {
                foreach ($prizes as $prize) {
                    $res[$store['id']][] = Store::get_store_stock($store['id'], $prize);
                }
            }
            if ($res) {
                foreach ($res as $storeId => $re) {
                    $Store = Store::findOne($storeId);
                    $Store->dyson = $re[0];
                    $Store->tool = $re[1];
                    $Store->cup = $re[2];
                    $Store->startcoffe = $re[3];
                    $Store->rag = $re[4];
                    $Store->magnet = $re[5];
                    $Store->save(false);
                }
            }
        }
    }

    // 更新为0的预约记录
    // public function actionUpdateUserPrize()
    // {
    //     $appointment_all = Appointment::find()->select('id, prize_id, mobile')->asArray()->all();
    //     $appointment_user = User::find()->select('result, mobile')->where(['>', 'result', '0'])->distinct()->addSelect('result')->orderBy(['result' => SORT_ASC])->asArray()->all();
    //     // $appointment_user = \yii\helpers\ArrayHelper::map($appointment_user, 'mobile', ['result', 'id']);
    //     $n_a_u = [];
    //     foreach ($appointment_user as $k => $v) {
    //         $n_a_u[$v['mobile']] = [
    //             'result' => $v['result'],
    //         ];
    //     }

    //     foreach ($appointment_all as $k => $v) {
    //         if (!empty($n_a_u[$v['mobile']]['result'])) {
    //             $appointment_all[$k]['result'] = $n_a_u[$v['mobile']]['result'];
    //         }
    //     }

    //     // $userprize = User::find()->select('mobile, result')->where(['mobile' => $usermobile])->andWhere(['>', 'result', 0])->asArray()->all();
    //     // $this->bp($appointment_all);

    //     foreach ($appointment_all as $prize) {
    //         $re = \common\models\Appointment::updateAll(['prize_id' => $prize['result']], ['mobile' => $prize['mobile']]);
    //     }
    //     var_dump($re);die;
    // }

    public function actionUpdateUserPrize()
    {
        $usermobiel = \common\models\Appointment::find()->select('mobile')->asArray()->all();
        $userprize = User::find()->select('mobile, min(result) as highest')->where(['mobile'=>$usermobiel])->andWhere(['>', 'result', 0])->groupBy('mobile')->asArray()->all();
        
        foreach ($userprize as $prize) {
            $re = \common\models\Appointment::updateAll(['prize_id' => $prize['highest']], ['mobile'=>$prize['mobile']]);
        }
        var_dump($re);die;
    }

    /**
     * 导出各门店奖品预约量
     * @author bob.qiu
     * 
     */
    public function actionImportAppointment()
    {
        // 查询出已经预约的门店
        $store = Appointment::find()->select('store_id')->groupBy('store_id')->asArray()->all();
        $prize = Prize::find()->select('id,name')->asArray()->all();
        $prize_ids = array_column($prize,'name','id');
        $store_ids = array_column($store,'store_id');
        // 设置门店奖品初始量为零
        foreach ($prize_ids as $key=>$value) {
            $sum[$key] = 0;
        }
        $appointment_all = Appointment::find()->select('prize_id,count(prize_id)')->groupBy('prize_id')->asArray()->all();
        //组装各个门店奖品的数量
        foreach ($store_ids as $store_id) {
            // 查询出当前门店的预约量
            $appointment_store = Appointment::find()->select('prize_id,count(prize_id) sum')->where(['store_id'=>$store_id])->groupBy('prize_id')->asArray()->all();
            $prize_sum = array_column($appointment_store,'sum','prize_id');
            $res = $this->get_array($sum,$prize_sum);
            $res['store_name'] = Store::find()->select('zh_storename')->where(['id'=>$store_id])->scalar();
            $data[] = $res;
        }
        // echo "<pre>";
        // var_dump($data);die;
        $filed = [
            'A' => 'store_name',
            'B' => '1',
            'C' => '2',
            'D' => '3',
            'E' => '4',
            'F' => '5',
            'G' => '6',
        ];
        $filename = 'jebsen_store';
        $result =  Excel::get_export($data,$filed,$filename);
    } 

    public function get_array($sum,$prize_sum) {
        foreach ($sum as $key=>$val) {
            if (array_key_exists($key,$prize_sum)) {
                $sum[$key] = $prize_sum[$key];
            }
        }
        return $sum;
    }

    // 查询所有已预约未核销的用户，导出excel
    public function actionExportSmsCustomer()
    {
        $user = Appointment::find()->select('appointment.name, appointment.mobile')->join('inner join', 'user u', 'u.mobile = appointment.mobile')->where(['u.redeem_prize' => null])->groupBy('appointment.mobile')->asArray()->all();
        $this->bp($user);
        Excel::get_export($user, ['A' => 'name', 'B' => 'mobile'], 'noredeemuser');
    }

    // 紧急任务
    public function actionSwf()
    {
        $filePath = Yii::$app->basePath.'/uploads/JebsenURLs.xlsx';
        
        $objReader = \PHPExcel_IOFactory::createReader('Excel2007'); //确定excel版本
        $objPHPExcel=$objReader->load($filePath); 
        $sheet = $objPHPExcel->getSheet(0); //获取第一个工作表
        $highestRow = $sheet->getHighestRow(); //获取总的行数
        $highestColumn = $sheet->getHighestColumn(); //获取得总列数
        
        //循环获取总的列表数
        for($row = 2; $row<=$highestRow; $row++){
            $array[] = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE)[0];
        }

        $na = [];
        foreach($array as $k => $v) {
            preg_match('/.*(http.*) 领奖.*/', $v['1'], $match);
            $short_url = $match[1];
            $hash_url = 'http://campaigns.xgatecorp.com?hash=' . md5($v[0]);
            $na[$k] = [
                'mobile' => $v[0],
                'message' => $v[1],
                'short_url' => $short_url,
                'target_url' => $hash_url,
            ];
            // $array[$k][2] = $short_url;
            // $array[$k][3] = $hash_url;
        }
        // $this->bp($array);
        // $this->bp($na);
        Excel::get_export($na, ['A' => 'mobile', 'B' => 'message', 'C' => 'short_url', 'D' => 'target_url'], 'hashed_user'); 
    }

    // 紧急任务
    public function actionSwff()
    {
        $filePath = Yii::$app->basePath.'/uploads/noredeemuser.xls';
        
        $objReader = \PHPExcel_IOFactory::createReader('Excel2007'); //确定excel版本
        $objPHPExcel=$objReader->load($filePath); 
        $sheet = $objPHPExcel->getSheet(0); //获取第一个工作表
        $highestRow = $sheet->getHighestRow(); //获取总的行数
        $highestColumn = $sheet->getHighestColumn(); //获取得总列数
        
        //循环获取总的列表数
        for($row = 2; $row<=$highestRow; $row++){
            $array[] = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE)[0];
        }
        // $this->bp($array);
        $na = [];
        foreach($array as $k => $v) {
            $na[$k] = [
                // 'name' => $v[0],
                // 'mobile' => $v[1],
                'mobile' => '86'.$v[1],
                'hash' => md5($v[1]),
            ];
            // $array[$k][2] = $short_url;
            // $array[$k][3] = $hash_url;
        }
        // $this->bp($array);
        // $this->bp($na);
        Excel::get_export($na, ['A' => 'mobile', 'B' => 'hash'], 'no_redeem_user');
    }
}
