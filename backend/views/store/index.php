<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\Province;
use common\models\City;
use common\models\District;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $searchModel app\models\StoreSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '门店配置';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="store-index">

    <h1>门店配置</h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php if (Yii::$app->user->identity->id_role == 1) {?>
        <?= Html::a('导入门店', ['import'], ['class' => 'btn btn-danger']) ?>
        <?php } ?>
        <?= Html::a('新建门店', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('导出门店用户名和密码', ['export'], ['class' => 'btn btn-warning']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            // ['class' => 'yii\grid\SerialColumn'],
            'storename',
            'address',
            'store_code',
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => '操作',
                'template' => ' {changepw} {view} {update} {delete} {hide} {show}',
//                 'buttons' => [
//                     'changepw' => function ($url, $model, $key) {
//                         return Html::a('<span class="glyphicon glyphicon-user"></span>', Url::to(['store/changepw', 'id' => $model->id]), ['title' => '更改门店密码'] ) ;
//                     },
//                     'hide' => function ($url, $model, $key) {
//                         return Html::a('<span class="glyphicon glyphicon-ban-circle"></span>', Url::to(['store/hide', 'id' => $model->id]), ['title' => '隐藏门店'] ) ;
//                     },
//                     'show' => function ($url, $model, $key) {
//                         return Html::a('<span class="glyphicon glyphicon-ok-sign"></span>', Url::to(['store/show', 'id' => $model->id]), ['title' => '开启门店'] ) ;
//                     },
//                 ],
                'visibleButtons' => [
                    'view' => function ($model, $key, $index) {
                        if (Yii::$app->user->identity->id_role == 1) {
                            return true;
                        }
                        return true;
//                         return false;
                    },
                    'delete' => function ($model, $key, $index) {
                        if (Yii::$app->user->identity->id_role == 1) {
                            return true;
                        }
                        return true;
//                         return false;
                    },
                    'hide' => function ($model, $key, $index) {
                        if ($model->status == 1 && Yii::$app->user->identity->id_role <= 2) {
                            return true;
                        }
                        return true;
//                         return false;
                    },
                    'show' => function ($model, $key, $index) {
                        if ($model->status == 2 && Yii::$app->user->identity->id_role <= 2) {
                            return true;
                        }
                        return true;
//                         return false;
                    }
                ],
            ],
        ],
    ]); ?>
</div>
