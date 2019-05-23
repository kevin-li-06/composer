<?php

use yii\helpers\Html;
use yii\grid\GridView;
use backend\widgets\StockGridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '库存调动';
$this->params['breadcrumbs'][] = $this->title;
?>

<style>
    table{text-align:center;}
</style>

<div class="rule-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="panel panel-warning">
    <div class="panel-heading">说明</div>
        <div class="panel-body">
            <!-- 1. 本表所有数值代表每个门店各个奖项的预约数减去库存数，大于0代表指定门店及奖项的库存有可能不足（如果所有预约都核销）<br> -->
            1. 本表所有数值代表每个门店各个奖项对应的库存数量、已预约数量、已核销数量<br>
            2. 点击表头【库存 / 预约 / 核销】可对指定奖品数量进行排序，查看需要注意的门店
        </div>
    </div>
    
    <?= StockGridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            // ['class' => 'yii\grid\SerialColumn'],
            // 'store_id',
            [
                'attribute' => 'zh_storename',
                'label' => '门店',
            ],
            // 戴森
            [
                'attribute' => '1_appointment',
                'label' => '预约',
                'format' => 'raw',
                'value' => function ($data) {
                    $n = $data['1_appointment'];
                    return ($n > 0) ? '<span class="badge" style="color:#333;background-color:#a0d911;width:100%">' . $n . '</span>' : 0;
                },
            ],
            [
                'attribute' => '1_redeem',
                'label' => '核销',
                'format' => 'raw',
                'value' => function ($data) {
                    $n = $data['1_redeem'];
                    return ($n > 0) ? '<span class="badge" style="color:#333;background-color:#1890ff;width:100%">' . $n . '</span>' : 0;
                },
            ],
            [
                'attribute' => '1_stock',
                'label' => '库存',
                'format' => 'raw',
                'value' => function ($data) {
                    $n = $data['1_stock'];
                    return ($n > 0) ? '<span class="badge" style="color:#333;background-color:#f5222d;width:100%">' . $n . '</span>' : 0;
                },
            ],
            // 双立人
            [
                'attribute' => '2_appointment',
                'label' => '预约',
                'format' => 'raw',
                'value' => function ($data) {
                    $n = $data['2_appointment'];
                    return ($n > 0) ? '<span class="badge" style="color:#333;background-color:#a0d911;width:100%">' . $n . '</span>' : 0;
                },
            ],
            [
                'attribute' => '2_redeem',
                'label' => '核销',
                'format' => 'raw',
                'value' => function ($data) {
                    $n = $data['2_redeem'];
                    return ($n > 0) ? '<span class="badge" style="color:#333;background-color:#1890ff;width:100%">' . $n . '</span>' : 0;
                },
            ],
            [
                'attribute' => '2_stock',
                'label' => '库存',
                'format' => 'raw',
                'value' => function ($data) {
                    $n = $data['2_stock'];
                    return ($n > 0) ? '<span class="badge" style="color:#333;background-color:#f5222d;width:100%">' . $n . '</span>' : 0;
                },
            ],
            // 康宁
            [
                'attribute' => '3_appointment',
                'label' => '预约',
                'format' => 'raw',
                'value' => function ($data) {
                    $n = $data['3_appointment'];
                    return ($n > 0) ? '<span class="badge" style="color:#333;background-color:#a0d911;width:100%">' . $n . '</span>' : 0;
                },
            ],
            [
                'attribute' => '3_redeem',
                'label' => '核销',
                'format' => 'raw',
                'value' => function ($data) {
                    $n = $data['3_redeem'];
                    return ($n > 0) ? '<span class="badge" style="color:#333;background-color:#1890ff;width:100%">' . $n . '</span>' : 0;
                },
            ],
            [
                'attribute' => '3_stock',
                'label' => '库存',
                'format' => 'raw',
                'value' => function ($data) {
                    $n = $data['3_stock'];
                    return ($n > 0) ? '<span class="badge" style="color:#333;background-color:#f5222d;width:100%">' . $n . '</span>' : 0;
                },
            ],
            // 星巴克
            [
                'attribute' => '4_appointment',
                'label' => '预约',
                'format' => 'raw',
                'value' => function ($data) {
                    $n = $data['4_appointment'];
                    return ($n > 0) ? '<span class="badge" style="color:#333;background-color:#a0d911;width:100%">' . $n . '</span>' : 0;
                },
            ],
            [
                'attribute' => '4_redeem',
                'label' => '核销',
                'format' => 'raw',
                'value' => function ($data) {
                    $n = $data['4_redeem'];
                    return ($n > 0) ? '<span class="badge" style="color:#333;background-color:#1890ff;width:100%">' . $n . '</span>' : 0;
                },
            ],
            [
                'attribute' => '4_stock',
                'label' => '库存',
                'format' => 'raw',
                'value' => function ($data) {
                    $n = $data['4_stock'];
                    return ($n > 0) ? '<span class="badge" style="color:#333;background-color:#f5222d;width:100%">' . $n . '</span>' : 0;
                },
            ],
            // 抹布
            [
                'attribute' => '5_appointment',
                'label' => '预约',
                'format' => 'raw',
                'value' => function ($data) {
                    $n = $data['5_appointment'];
                    return ($n > 0) ? '<span class="badge" style="color:#333;background-color:#a0d911;width:100%">' . $n . '</span>' : 0;
                },
            ],
            [
                'attribute' => '5_redeem',
                'label' => '核销',
                'format' => 'raw',
                'value' => function ($data) {
                    $n = $data['5_redeem'];
                    return ($n > 0) ? '<span class="badge" style="color:#333;background-color:#1890ff;width:100%">' . $n . '</span>' : 0;
                },
            ],
            [
                'attribute' => '5_stock',
                'label' => '库存',
                'format' => 'raw',
                'value' => function ($data) {
                    $n = $data['5_stock'];
                    return ($n > 0) ? '<span class="badge" style="color:#333;background-color:#f5222d;width:100%">' . $n . '</span>' : 0;
                },
            ],
            // 冰箱贴
            [
                'attribute' => '6_appointment',
                'label' => '预约',
                'format' => 'raw',
                'value' => function ($data) {
                    $n = $data['6_appointment'];
                    return ($n > 0) ? '<span class="badge" style="color:#333;background-color:#a0d911;width:100%">' . $n . '</span>' : 0;
                },
            ],
            [
                'attribute' => '6_redeem',
                'label' => '核销',
                'format' => 'raw',
                'value' => function ($data) {
                    $n = $data['6_redeem'];
                    return ($n > 0) ? '<span class="badge" style="color:#333;background-color:#1890ff;width:100%">' . $n . '</span>' : 0;
                },
            ],
            [
                'attribute' => '6_stock',
                'label' => '库存',
                'format' => 'raw',
                'value' => function ($data) {
                    $n = $data['6_stock'];
                    return ($n > 0) ? '<span class="badge" style="color:#333;background-color:#f5222d;width:100%">' . $n . '</span>' : 0;
                },
            ],
        ],
    ]); ?>
</div>
