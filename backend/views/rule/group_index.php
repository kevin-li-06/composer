<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use common\models\Rule;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Rule::groups($group) . '组';
$this->params['breadcrumbs'][] = ['label' => '抽奖配置', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
    .glyphicon{
        font-size: 16px;
    }
</style>

<div class="rule-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php if (Rule::find()->where(['group' => $group])->count() < 2) { ?>
        <?= Html::a('新增一个配置', ['create', 'group' => $group], ['class' => 'btn btn-success']) ?>
        <?php } ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            // ['class' => 'yii\grid\SerialColumn'],

            // 'id',
            [
                'attribute' => 'type',
                'value' => function ($data) {
                    return Rule::types($data->type);
                }
            ],
            [
                'attribute' => 'group',
                'value' => function ($data) {
                    return Rule::groups($data->group);
                }
            ],
            // 'prize_rate',

            [
                'class' => 'yii\grid\ActionColumn',
                'header' => '操作',
                'template' => " {view} {update} {delete} {test}",
                'buttons' => [
                    'test' => function ($url,$model,$key) {
                        return  Html::a('<span class="glyphicon glyphicon-random"></span>', Url::to(['rule/test','id'=>$model->id]), ['title' => '抽奖概率测试'] ) ;
                    }
                ]
            ],
        ],
    ]); ?>
</div>
