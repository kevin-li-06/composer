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

$this->title = '更改预约门店';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="store-index">

    <h1><?= $this->title ?> <small>找到需要改变的门店后，在右侧点击确认改变按钮</small></h1>

    <p>

    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            // ['class' => 'yii\grid\SerialColumn'],
            'zh_storename',
            'en_storename',
            [
                'attribute' => 'address',
                'value' => function($data) {
                    if ($data->address) {
                       $province = Province::get_province($data->province);
                       return $province.'-'.$data->address;
                    } else {
                        return '未绑定';
                    }
                }
            ],
            'store_code',
            'phone',
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => '操作',
                'template' => ' {confirm}',
                'buttons' => [
                    'confirm' => function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-ok"></span>', Url::to(['store/do-change-store', 'id' => $model->id, 'mobile' => $_GET['mobile']]), 
                                [
                                    'title' => '确认更改',
                                    'data' => [
                                        'confirm' => '确认更改为此门店?',
                                    ],
                                ]);
                    },
                ],
                'visibleButtons' => [
                    'confirm' => function ($model, $key, $index) {
                        if (Yii::$app->user->identity->id_role <= 2) {
                            return true;
                        }
                        return false;
                    }
                ],
            ],
        ],
    ]); ?>
</div>
