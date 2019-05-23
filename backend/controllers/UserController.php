<?php

namespace backend\controllers;

use backend\components\Loyalty;
use common\components\BaseLoyalty;
use common\components\Excel;
use Yii;
use common\models\User;
use common\models\UserSearch;
use common\models\Record;
use common\models\Prize;
use backend\components\BaseAdminController;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use backend\models\UploadForm;



/**
 * UserController implements the CRUD actions for User model.
 */

class UserController extends BaseAdminController
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
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {

        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $prizes = Prize::allPrizes(null, true);
        $prizes = \yii\helpers\ArrayHelper::map($prizes, 'id', 'name');
        $record = new Record();
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'record' => $record,
            'prizes' => $prizes,
        ]);
    }

    /**
     * Displays a single User model.
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
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new User();

        if ($model->load(Yii::$app->request->post())) {
            
            $model->setMobile($model->mobile);
            $model->generateAuthKey();
            
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
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {

            $model->setMobile($model->mobile);

            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                return $this->render('create', [
                    'model' => $model,
                ]); 
            }
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }

    /***
     * 批量导入用户数据
     * @author bob qiu
     * 
     */
    public function actionImport()
    {
        ini_set('max_execution_time', '0');
        ini_set('memory_limit', '1024M');
//        $model = new UploadForm();
//        if (Yii::$app->request->isPost) {
        //    $model->file = UploadedFile::getInstance($model, 'file');
//            //上传路径
//            $filePath = $this->fileExists(Yii::$app->basePath.'/uploads/');
//            if ($model->file && $model->validate()) {
//                $model->file->saveAs($filePath . $model->file->baseName . '.' . $model->file->extension);
//            }
//            $file_url = $filePath.$model->file->name;
            $file_url = Yii::$app->basePath.'/uploads/'.'liberUser.xlsx';
            // 配置需要导入的数据库信息
            $config = [
                'table' => 'user',
                'field' => [
                    'card',
                    'username',
                    'gender',
                    'mobile',
//                     'region',
                    'mobile_hash',
                ],
            ];
//            var_dump($file_url);die;
            // 设定参数后导入表名
            $result = Excel::get_import($file_url,$config);
            if ($result) {
                Yii::$app->session->setFlash('success', '操作成功！');
            } else{
                Yii::$app->session->setFlash('error', '操作失败');
            }
//        }
        return $this->redirect('index');
    }

    /***
     * 批量导出用户数据
     * @author bob qiu
     * 
     */
    public function actionExport()
    {
        ini_set('max_execution_time', '0');
        ini_set('memory_limit', '1024M');
        $usersOne = User::find()->asArray()->all();
//        $usersTow = User::find()->where([ '<', 'id', '50000'])->asArray()->all();
        $field = [
            'A' => 'mobile',
            'B' => 'mobile_hash',
        ];
       $filename = 'jebsen';
       $result =  Excel::get_export($usersOne,$field,$filename);
//            $re =  Excel::get_export($usersTow,$field,$filename);
        var_dump($result);
    }

    public function actionHash()
    {
        ini_set('max_execution_time', '0');
        ini_set('memory_limit', '300M');
        $hash = Yii::$app->security->generatePasswordHash(18723836813);
        $users = User::find()->asArray()->all();
        foreach ($users as $k => $y){

        }
//        foreach ($users)
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionRecord($id)
    {
        $record = Record::find()->where(['user_id' => $id])-> groupBy('date')->asArray()->all();  
        echo "<pre/>";
        var_dump($record);
    }

    // 生成测试连接
    public function actionLinks()
    {
        $users = User::find()->all();
        foreach ($users as $k => $v) {
            echo '名称：' . $v->username;
            echo '<br>';
            echo '网址：http://jebsen.onthemooner.com?hash=' . $v->mobile_hash;
            echo '<br>';
        }
        // $this->bp($users);
    }

    // 生成测试用户
    public function actionBuildTest()
    {
        Yii::$app->db->createCommand()->batchInsert('user', ['username', 'mobile', 'mobile_hash', 'card', 'group'], [
            ['William', '17713607873', md5('17713607873'), '17713607873', '1'],
            ['Chris', '18227693267', md5('18227693267'), '18227693267', '1'],
            ['Bob', '15928713252', md5('15928713252'), '15928713252', '1'],
            ['Aaron', '18723836813', md5('18723836813'), '18723836813', '1'],
            ['Lisa', '18483629341', md5('18483629341'), '18483629341', '1'],
            ['Maire', '18628272611', md5('18628272611'), '18628272611', '1'],
            ['Java', '18650109562', md5('18650109562'), '18650109562', '1'],
            ['Sara', '13908066330', md5('13908066330'), '13908066330', '1'],
            ['Coy', '13980949494', md5('13980949494'), '13980949494', '1'],
            ['Monica', '15828297403', md5('15828297403'), '15828297403', '1'],
            ['Joyce', '17608103057', md5('17608103057'), '17608103057', '1'],
            ['Andy', '18123352697', md5('18123352697'), '18123352697', '1'],
            ['Bluce', '15308029844', md5('15308029844'), '15308029844', '1'],
            ['Jason', '15883283040', md5('15883283040'), '15883283040', '1'],
            ['秦涛', '13880343848', md5('13880343848'), '13880343848', '1'],
            ['Thomas', '15196359975', md5('15196359975'), '15196359975', '1'],
            //捷成内部测试
            ['鲍晔', '13917304308', md5('13917304308'), '13917304308', '1'],
            ['毛露娜', '13564182386', md5('13564182386'), '13564182386', '1'],
            ['李小姐', '13925085891', md5('13925085891'), '13925085891', '1'],
            ['许敏洁', '13671965763', md5('13671965763'), '13671965763', '1'],
            ['李小姐', '18616329974', md5('18616329974'), '18616329974', '1'],
            ['栾永兴', '18612770620', md5('18612770620'), '18612770620', '1'],
        ])->execute();
        echo 'ok';
    }

    // 清空测试用户
    public function actionClearAll()
    {
        $sql = 'truncate table user';
        Yii::$app->db->createCommand($sql)->execute();
        $sql = 'truncate table record';
        Yii::$app->db->createCommand($sql)->execute();
        $sql = 'truncate table checkin';
        Yii::$app->db->createCommand($sql)->execute();
        $sql = 'truncate table appointment';
        Yii::$app->db->createCommand($sql)->execute();
        echo 'ok';
    }


    /***
     * 导出最新的核销用户
     * @author bob.qiu
     */
    public function actionExported()
    {
        $results = User::find()
                ->where(['exported'=>0])
                ->andwhere(['NOT',['redeem_at'=>null]])
                ->andwhere(['NOT',['username'=>null]])
                ->andwhere(['NOT',['mobile'=>null]])
                ->select('username,mobile,redeem_prize,redeem_at')
                ->asArray()
                ->all();
        $prizesMap = [
            '1' => '戴森吹风机',
            '2' => '双立人刀具',
            '3' => '康宁保温杯',
            '4' => '星巴克咖啡',
            '5' => '抹布',
            '6' => '冰箱贴',
        ];  
        // 奖品id 转换为奖品名称      
        foreach ($results as $values) {
            $values['redeem_prize'] = $prizesMap[$values['redeem_prize']];
            $values['redeem_at'] = date("Y-m-d H:i:s",$values['redeem_at']);
            $data[] = $values;
        }
        $field = [
            'A' => 'username',//用户名
            'B' => 'mobile',// 手机号
            'C' => 'redeem_prize',// 核销的奖品
            'D' => 'redeem_at',// 核销时间
        ];
        $filename = 'jebsenUserverification';
        $result =  Excel::get_export($data,$field,$filename);
        if ($result) {
            Yii::$app->session->setFlash('success', '操作成功！');
        } else{
            Yii::$app->session->setFlash('error', '操作失败');
        }
        return $this->redirect('index');
    }
}
