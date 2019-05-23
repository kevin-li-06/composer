<?php

namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\db\Connection;
use backend\components\BaseAdminController;
use app\components\Log;
use backend\models\Admin;
use backend\models\Migration;
use yii\base\ErrorException;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;


class InstallController extends BaseAdminController
{   
    // 用户权限控制
    // public function behaviors()
    // {
    //     return [
    //         'access' => [
    //             'class' => AccessControl::className(),
    //             'only' => ['drop'],
    //             'rules' => [
    //                 [
    //                     'actions' => ['drop'],
    //                     'allow' => true,
    //                     'roles' => ['@'],
    //                 ],
    //             ],
    //         ],
    //     ];
    // }

    // 登录安装
    public function actionLogin()
    {
        $install = $this->session('install');
        
        if ($install === true) {
            $this->redirect(['install/index']);
        }

        if (Yii::$app->request->isPost) {
            $code = Yii::$app->request->post('code');
            if ($code == 'xgate') {
                $this->session('install', true);
                Yii::$app->session->setFlash('success', '身份验证通过，请输入数据库信息来完成安装');
                return $this->redirect(['install/index']);
            } else {
                Yii::$app->session->setFlash('error', '身份验证失败');
            }
        }

        return $this->render('login');
    }

    // 安装GUI
    // 必须先通过激活码的认证
    public function actionIndex()
    {
        $this->checkLogin();

        $config = require(dirname(Yii::$app->basePath) . '/common/config/main-local.php');

        $dsn = new Connection([
            'dsn' => 'mysql:host=localhost',
		    'username' => $config['components']['db']['username'],
		    'password' => $config['components']['db']['password'],
		    'charset' => 'utf8',
        ]);
        
        // 查询数据库是否存在
        $sql = 'SELECT * FROM information_schema.SCHEMATA where SCHEMA_NAME="lottery"';
        $re = $dsn->createCommand($sql)->queryOne();

        if (!empty($re) && ($re['SCHEMA_NAME'] == 'lottery')) {
            $dbExists = true;
            // 已有数据新dsn
            $dsn = new Connection([
                'dsn' => 'mysql:host=localhost;dbname=lottery',
                'username' => $config['components']['db']['username'],
                'password' => $config['components']['db']['password'],
                'charset' => 'utf8',
            ]);
            // 查询数据表是否存在
            $sql = 'SHOW TABLES LIKE "%admin%"';
            $re = $dsn->createCommand($sql)->queryOne();
            if ($re === false) {
                $installed = false;
            } else {
                $installed = true;
            }
        } else {
            $dbExists = false;
            // 新建数据库
            $sql = 'CREATE DATABASE `lottery`;';
            if (!$dsn->createCommand($sql)->execute()) {
                exit('Install Failed');
            }
            $installed = false;
        }
        
        return $this->render('index', [
            'installed' => $installed
        ]);
    }

    // 运行安装
    // 必须先通过激活码的认证
    public function actionRunInstallation()
    {
        $this->checkLogin();

        // 1.获取用户输入的数据库配置
        $db_username = Yii::$app->request->post('db_username');
        $db_password = Yii::$app->request->post('db_password');

        // 2.连接数据库
        try {
            $dsn = new Connection([
                'dsn' => 'mysql:host=localhost',
                'username' => $db_username,
                'password' => $db_password,
                'charset' => 'utf8',
            ]);
        } catch (ErrorException $e) {
            exit("DB Connection Failed!");
        }
        
        // 3.获取原始SQL语句
        $build_sql = file_get_contents(dirname(Yii::$app->basePath) . '/db/build.sql');
        $city_sql = file_get_contents(dirname(Yii::$app->basePath) . '/db/city.sql');
        
        // 4.安装数据库
		$transaction = $dsn->beginTransaction();
		try {
            $dsn->createCommand($build_sql)->execute();
            $dsn->createCommand($city_sql)->execute();
			$transaction->commit();
		} catch (ErrorException $e) {
			$transaction->rollBack();
			exit("Install Failed!");
        }
        
        // 5.修改db.php配置文件
		// $db_path = Yii::$app->basePath . '/config/db.php';
		// $origin = file_get_contents($db_path);
		// $new = preg_replace('/dbname=(.*)\'/', 'dbname=wechatmembercenter\'', $origin);
        // file_put_contents($db_path, $new);

        // 6.创建管理员
        $admin = new Admin();
        $admin->id_role = 1;
        $admin->username = 'xgate';
        $admin->email = 'xgate@xgate.com';
        $admin->setPassword('xgate1234');
        $admin->generateAuthKey();
        $admin->save(false);

//         $jebsen = new Admin();
//         $jebsen->id_role = 2;
//         $jebsen->username = 'liber';
//         $jebsen->email = 'liber@liber.com';
//         $jebsen->setPassword('liber1234');
//         $jebsen->generateAuthKey();
//         $jebsen->save(false);
        
        // 7.显示管理员账号
		return $this->redirect(['install/index']);
    }

    // 更新数据库
    public function actionUpdate()
    {
        $filepath = dirname(Yii::$app->basePath) . '/db';
        $files = scandir($filepath);
        $new_files = [];
        foreach($files as $v) {
            if ($v != '.' && $v != '..') {
                $new_files[]['filename'] = substr($v, 0, -4);
            }
        }

        $fileProvider = new ArrayDataProvider([
            'allModels' => $new_files,
        ]);

        $dataProvider = new ActiveDataProvider([
            'query' => Migration::find(),
        ]);

        return $this->render('update', [
            'fileProvider' => $fileProvider,
            'dataProvider' => $dataProvider,
        ]);
    }

    // 执行SQL文件更新数据库
    public function actionRunUpdate()
    {
        $filename = Yii::$app->request->get('id');
        if (!empty($filename) || Yii::$app->request->isPost) {
            // 读取更新文件
            if (empty($filename)) {
                $filename = Yii::$app->request->post('sql');
            }
            
            // 检查SQL文件的状态
            $migration = Migration::find()->where(['filename' => $filename])->one();
            if ($migration) {
                Yii::$app->session->setFlash('error', '该SQL文件已经更新完成');
                return $this->redirect(['install/update']);
            }

            // 不能输入build
            if ($filename == 'build') {
                Yii::$app->session->setFlash('error', '请求错误build');
                return $this->redirect(['install/update']);
            }

            $file = dirname(Yii::$app->basePath) . '/db/' . $filename . '.sql';
            if (file_exists($file)) {
                // SQL 源文件内容
                $update_sql = file_get_contents($file);

                // 执行更新操作
                $db = Yii::$app->db;
                $transaction = $db->beginTransaction();
                try {
                    $re = $db->createCommand($update_sql)->execute();
                    $transaction->commit();
                } catch (ErrorException $e) {
                    $transaction->rollBack();
                    exit("Update Failed!");
                }

                // SQL ERROR
                if (empty($re)) {
                    Yii::$app->session->setFlash('warning', 'SQL ERROR');
                    return $this->redirect(['install/update']);
                }

                // 新增一条迁移记录
                $migration = new Migration();
                $migration->filename = $filename;
                $migration->status = 1;
                $migration->env = YII_ENV;
                $migration->migrated_at = time();
                $migration->save();

                Yii::$app->session->setFlash('success', '更新成功');
            } else {
                Yii::$app->session->setFlash('error', '请求错误2');
            }
        } else {
            Yii::$app->session->setFlash('error', '请求错误1');
        }
        
        return $this->redirect(['install/update']);
    }

    // 查看SQL语句
    public function actionShow()
    {
        $id = Yii::$app->request->get('id');
        $file = dirname(Yii::$app->basePath) . '/db/' . $id . '.sql';
        if (file_exists($file)) {
            $sql = file_get_contents($file);
        }

        return $this->render('show', [
            'sql' => $sql,
        ]);
    }

    // 删除数据库
    // 必须是登录管理员用户才可以删除
    public function actionDrop()
    {
        $this->checkLogin();

        $db = require(dirname(Yii::$app->basePath) . '/common/config/main-local.php');

        $dsn = new Connection([
		    'dsn' => 'mysql:host=localhost',
		    'username' => $db['components']['db']['username'],
		    'password' => $db['components']['db']['password'],
		    'charset' => 'utf8',
        ]);

        $sql = "DROP DATABASE IF EXISTS lottery";

        $dsn->createCommand($sql)->execute();

        Yii::$app->session->destroy();

        return $this->redirect(['install/index']);
    }

    // 检查登录状态，如果不是登录状态则跳转到登录页面
    private function checkLogin()
    {
        $install = $this->session('install');
        if ($install != true) {
            $this->redirect(['install/login']);
        }
    }

}
