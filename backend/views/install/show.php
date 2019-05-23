<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Tenant */

$this->title = '查看';
?>
<div class="tenant-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('返回', ['update'], ['class' => 'btn btn-primary']) ?>
    </p>

    <div class="inner cover" style="word-break:break-word;">
        <?= nl2br($sql) ?>
    </div>
    
</div>
