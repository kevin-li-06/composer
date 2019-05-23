<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel common\models\RecordSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '抽奖记录';
$this->params['breadcrumbs'][] = ['label'=> '增加刮奖机会','url' => ['../index.php/user']];
$this->params['breadcrumbs'][] = $this->title;

?>
<style media="screen">
    .label-status-one {background-color: #555;}
    .label-status-two {background-color: #c55;}
</style>
<div class="record-index">

    <?php if (empty($user->username)) { ?>
        <h1><?= Html::encode('【' . $user->openid . '】' . $this->title) ?></h1>
    <?php } else { ?>
        <h1><?= Html::encode('【' . $user->username . '】' . $this->title) ?></h1>
    <?php } ?>
    
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [


            [
                'attribute'=> "status",
                'format' => 'raw',
                'options' => ['width' => '150'],
                'filter' => Html::activeDropDownList(
                    $searchModel,
                    'status',
                    \common\models\Record::statusMap(),
                    ['class' => 'form-control','prompt'=>'请选择']
                ),
                'value'=> function($data) {
                    $data = $data->status;
                    
                    // switch ($data) {
                    //     case 1:
                    //         $class = 'label-status-one';
                    //         break;
                    //     case 2:
                    //         $class = 'label-status-two';
                    //         break;
                    //     case 3:
                    //         $class = 'label-status-three';
                    //         break;
                    //     default:
                    //         $class = 'label-default';
                    //         break;
                    // }
                    $re = \common\models\Record::statusMap($data);
                    return $re;
                    // return Html::tag('label', $re, ['class' => 'label ' . $class]);
                }
            ],
            [
                'attribute' => 'result',
                'options' => ['width' => '140'],
                'filter' => Html::activeDropDownList(
                    $searchModel,
                    'result',
                    $prizes,
                    ['class' => 'form-control','prompt'=>'请选择']
                ),
                'value' => function ($data) {
                    if (empty($data->result)) {
                        return '未中奖';
                    }
                    return $data->prize->name;
                    // if ($data->result == 1){
                    //     return "特等奖";
                    // }else{
                    //     return numToWord($data->result-1) . '等奖';
                    // }
                }
            ],

            'date',
            [
                'attribute' => 'get_at',
                'options' => ['width' => '80'],
                'value' => function ($data) {
                    return isset($data->get_at) ? date("Y-m-d H:i", $data->get_at) : "";
                }
            ],

            // [
            //     'attribute' => 'lottery_at',
            //     'options' => ['width' => '80'],
            //     'value' => function ($data) {
            //         return  isset($data->lottery_at) ? date("Y-m-d H:i", $data->lottery_at) : "";
            //     }
            // ],
            // 'receipts',
            // [
            //     'class' => 'yii\grid\ActionColumn',
            //     'header' => '操作',
            //     'options' => ['width' => '70'],
            //     'template' => ' {view} {update} {delete}',
            //     'buttons' => [
            //         'delete' => function ($url, $model, $key) {
            //             return  Html::a('<span  class="glyphicon glyphicon-trash"></span>', Url::to(['/record/delete', 'id' => $model->id, 'user_id' => $model->user_id]), ['title' => '抽奖记录'] ) ;
            //         },
            //     ],
            // ],
        ],
    ]); ?>
</div>
