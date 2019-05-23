<?php

use yii\helpers\Url;

$this->title = 'Backend';
$this->params['breadcrumbs'][] = ['label' => 'Log', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div style="margin-bottom:20px;">
    <a href="<?= Url::to(['log/frontend']) ?>" class="btn btn-success">Frontend</a>
    <a href="<?= Url::to(['log/backend']) ?>" class="btn btn-info">Backend</a>
</div>

<div style="margin-bottom:20px;">
    <a href="<?= Url::to(['log/clear-backend']) ?>" class="btn btn-danger">清空</a>
</div>

<div class="inner cover" style="word-break:break-word;">
    <?= nl2br($contents) ?>
</div>