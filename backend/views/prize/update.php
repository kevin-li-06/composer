<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Prize */

$this->title = '修改奖品: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => '奖品配置', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '更新';
?>
<div class="prize-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
