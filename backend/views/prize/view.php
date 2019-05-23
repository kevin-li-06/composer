<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\Prize */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => '奖品配置', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="prize-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('修改', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('删除', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            [
                'attribute' => 'level',
                'options' => ['width' => '60'],
                'value' => function ($data) {
                    return numToWord($data->level) . '等奖';
                }
            ],
            'stock_num',
            'gain_num',
            'exchange_num',
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>

</div>
