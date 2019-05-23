<?php

namespace backend\controllers;

use Yii;
use backend\components\BaseAdminController;
use yii\web\Response;
use yii\filters\AccessControl;

class LogController extends BaseAdminController
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
        ];
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionBackend()
    {
        $contents = \backend\components\Log::contents();
        return $this->render('backend', [
            'contents' => $contents
        ]);
    }

    public function actionFrontend()
    {
        $contents = \frontend\components\Log::contents();
        return $this->render('frontend', [
            'contents' => $contents
        ]);
    }

    public function actionClearBackend()
    {
        \backend\components\Log::clear();
        $this->redirect(['log/backend']);
    }

    public function actionClearFrontend()
    {
        \frontend\components\Log::clear();
        $this->redirect(['log/frontend']);
    }
}
