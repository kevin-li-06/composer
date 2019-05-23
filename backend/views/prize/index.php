<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\PrizeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '奖品配置';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="prize-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('创建奖品', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            // ['class' => 'yii\grid\SerialColumn'],

            // 'id',
            // [
            //     'attribute' => 'id',
            //     'options' => ['width' => '40'],
            // ],
            'name',
            [
                'attribute' => 'level',
                'options' => ['width' => '60'],
                'value' => function ($data) {
                    if ($data->level == 1){
                        return "特等奖";
                    }else{
                        return numToWord($data->level-1) . '等奖';
                    }
                }
            ],
            [
                'attribute' => 'stock_num',
                'options' => ['width' => '50'],
            ],
            [
                'attribute' => 'gain_num',
                'options' => ['width' => '50'],
            ],
            [
                'attribute' => 'exchange_num',
                'options' => ['width' => '50'],
            ],
            'created_at:datetime',
            'updated_at:datetime',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
