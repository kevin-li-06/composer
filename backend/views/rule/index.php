<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '抽奖配置';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="rule-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php  if (\common\models\Rule::find()->count() < 2){?>
    <?= Html::a('新增一个配置', ['create'], ['class' => 'btn btn-success']) ?>
    <?php } ?>
    
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            [
                'attribute'=> "prize_rate",
                'format' => 'raw',
                'contentOptions' =>[
                    'style' => 'white-space:normal;',
                ],
                'value'=> function($data) {
                    $re = \common\models\Rule::showPrizeRate($data->id);
                    return $re;
                }
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
