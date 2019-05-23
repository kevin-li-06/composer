<?php

namespace backend\controllers;

use Yii;
use common\models\Store;
use backend\models\StoreSearch;
use common\models\Province;
use common\models\City;
use common\models\District;
use common\models\Appointment;
use common\models\ChangeStore;
use backend\components\BaseAdminController;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\components\Excel;
use backend\models\Admin;
use backend\models\UploadForm;
use yii\web\UploadedFile;


/**
 * StoreController implements the CRUD actions for store model.
 */

class StoreController extends BaseAdminController
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
     * Lists all store models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new StoreSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        // echo "<pre>";var_dump($searchModel);die;
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single store model.
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
     * Creates a new store model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Store();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            // return $this->redirect(['view', 'id' => $model->id]);
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing store model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            // return $this->redirect(['view', 'id' => $model->id]);
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }
    
    // 改变门店视图
    public function actionChangeStore()
    {
        $searchModel = new StoreSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('change', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    // 改变门店数据操作
    public function actionDoChangeStore()
    {
        $store_id = Yii::$app->request->get('id');
        $mobile = Yii::$app->request->get('mobile');

        if (empty($store_id) || empty($mobile)) {
            Yii::$app->session->setFlash('error', '参数错误');
            return $this->redirect(['appointment/index']);
        }

        // 查询用户
        $user = \common\models\User::getHighestPrizeUser($mobile);
        if (!$user) {
            Yii::$app->session->setFlash('error', '用户不存在');
            return $this->redirect(['appointment/index']);
        }

        // 查询新门店
        $store = Store::findOne($store_id);
        if (!$store) {
            Yii::$app->session->setFlash('error', '门店不存在');
            return $this->redirect(['appointment/index']);
        }

        // 查询预约记录
        $appointment = Appointment::find()->where(['mobile' => $mobile])->one();
        if (!$appointment) {
            Yii::$app->session->setFlash('error', '预约记录不存在');
            return $this->redirect(['appointment/index']);
        }

        // 增加变更记录
        $changeStore = new ChangeStore();
        $changeStore->from = $appointment->store_id;
        $changeStore->to = $store->id;
        $changeStore->user_id = $user['id'];

        // 变更预约门店
        $appointment->store_id = $store->id;

        if ($changeStore->save() && $appointment->save()) {
            Yii::$app->session->setFlash('success', '门店变更完成');
        } else {
            Yii::$app->session->setFlash('error', '门店变更失败');
        }
        return $this->redirect(['appointment/index']);
    }

    /**
     * Deletes an existing store model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    // 隐藏门店
    public function actionHide($id)
    {   
        $model = $this->findModel($id);

        $model->status = 2;
        if ($model->save()) {
            Yii::$app->session->setFlash('success', '操作成功');
        } else {
            Yii::$app->session->setFlash('error', '操作失败');
        }
        return $this->redirect(['store/index']);
    }

    // 显示门店
    public function actionShow($id)
    {   
        $model = $this->findModel($id);

        $model->status = 1;
        if ($model->save()) {
            Yii::$app->session->setFlash('success', '操作成功');
        } else {
            Yii::$app->session->setFlash('error', '操作失败');
        }
        return $this->redirect(['store/index']);
    }

    /**
     * 门店excel的导入.
     * @author bob.qiu
     * @param 
     */
    public function actionImport()
    {
        // $model = new UploadForm();
        // if (Yii::$app->request->isPost) {
            // $model->file = UploadedFile::getInstance($model, 'file');
            // // 上传路径
            // $filePath = Yii::$app->basePath.'/uploads/';
            // if ($model->file && $model->validate()) {
            //     $model->file->saveAs($filePath . $model->file->baseName . '.' . $model->file->extension);
            // }
            // $file_url = $filePath.$model->file->name;
            $file_url = Yii::$app->basePath.'/uploads/'.'liberstore.xlsx';
            // var_dump($file_url);die;
            $config = [
                'table' => 'store',
                'field' => [
                    'A' => 'storename',
                    'B' => 'address',
                    'C' => 'store_code'
                ],
            ];
            $result = Excel::get_storeimport($file_url,$config);
            if ($result) {
                Yii::$app->session->setFlash('success', '操作成功！');
            } else{
                Yii::$app->session->setFlash('error', '操作失败');
            }
            return $this->redirect('index');
        // }
        // return $this->render('import',[
        //     'model'=> $model
        // ]);
        
    }
    /**
     * 门店excel的导出.
     * @author bob.qiu
     * @param 
     */
    public function actionExport()
    {
        $storepw = Admin::find()->where(['>','id',2])->select('username,email')->asArray()->all();
        $store = Store::find()->asArray()->select('zh_storename,store_code')->all();
        foreach ($store as $key =>$val) {
            if  ($storepw[$key]['username']==$val['store_code']) {
                $val['password'] = $storepw[$key]['email'];
                $val['username'] = $storepw[$key]['username'];
                $data[] = $val;
            }
        }
        $field = [
            'A' => 'zh_storename',
            'B' => 'username',
            'C' => 'password',
        ];
        
        $filename = 'jebsenstore';
        $result =  Excel::get_export($data,$field,$filename);
       
    }

    /**
     * Finds the store model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return store the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Store::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * @author bob.qiu
     * ajax-city
     * 查询出省份下面的城市
     * 
     */
    public function actionAjaxCity()
    {
        $provinceId = Yii::$app->request->post();
        $city = City::find()->where(['ProvinceID' => $provinceId])->asArray()->all();
        echo json_encode($city);
    }

    //获取所有区县
    public function actionAjaxDistrict()
    {
        $CityID = Yii::$app->request->post();
        $district = District::find()->where(['CityID' => $CityID])->asArray()->all();
        echo json_encode($district);
    }

     /**
     * 各门店用户密码更改
     * @author bob.qiu
     * 
     */
    public function actionChangepw($id)
    {
        $model = Store::findOne($id);
        $usermodel = Admin::find()->where(['username'=>$model->store_code])->one();
        // var_dump($usermodel);die;
        if (Yii::$app->request->isPost) {
            echo "<pre>";
            $password = Yii::$app->request->post('Admin')['new_password'];
            $usermodel->email = $password;
            $usermodel->setPassword($password);
            if ($usermodel->save(false)) {
                return $this->redirect(['store/index']);
            }
        }
        return $this->render('changepw',[
            'model' => $model,
            'usermodel' => $usermodel
        ]);
    }
}
