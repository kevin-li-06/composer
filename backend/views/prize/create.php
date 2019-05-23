<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\Prize */

$this->title = '创建奖品';
$this->params['breadcrumbs'][] = ['label' => '奖品配置', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="prize-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
