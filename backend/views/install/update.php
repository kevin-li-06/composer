<?php

use app\widgets\Alert;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\grid\GridView;

$this->title = '数据库';
?>

<h2 class="page-header">请输入您需要更新的SQL Filename</h2>

<?= Html::beginForm(['install/run-update'], 'post', ['class' => 'form', 'style' => 'width:30%']) ?>
    <div class="form-group">
        <label>SQL</label>
        <?= Html::input('text', 'sql', '', ['class' => 'form-control', 'required' => 'required']) ?>
    </div>
    <?= Html::submitInput('更新', ['class' => 'btn btn-default']) ?>
<?= Html::endForm() ?>

<h2 class="page-header">SQL Filename List</h2>

<?= GridView::widget([
    'dataProvider' => $fileProvider,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        'filename',
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{show} {up}',
            'buttons' => [
                'show' => function ($url, $model, $key) {
                    $url = Url::to(['install/show', 'id' => $model['filename']]);
                    $options = [
                        'title' => '查看',
                        'aria-label' => 'show',
                    ];
                    return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, $options);
                },
                'up' => function ($url, $model, $key) {
                    $url = Url::to(['install/run-update', 'id' => $model['filename']]);
                    $options = [
                        'title' => '更新',
                        'aria-label' => 'show',
                    ];
                    return Html::a('<span class="glyphicon glyphicon-import"></span>', $url, $options);
                },
            ],
        ],
    ],
]); ?>

<h2 class="page-header">Migrations List</h2>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        // ['class' => 'yii\grid\SerialColumn'],

        'id',
        'filename',
        // 'status',
        [
            'attribute' => 'status',
            'value' => function ($data) {
                return $data->status == 0 ? '未更新' : '已更新';
            }
        ],
        'env',
        'migrated_at:datetime',

        // ['class' => 'yii\grid\ActionColumn'],
    ],
]); ?>
