<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\store */

$this->title = '新建门店';
$this->params['breadcrumbs'][] = ['label' => '门店配置', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="store-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
