<?php

use yii\helpers\Url;

$this->title = '日记';
$this->params['breadcrumbs'][] = $this->title;
?>

<div style="margin-bottom:20px;">
    <a href="<?= Url::to(['log/frontend']) ?>" class="btn btn-success">Frontend</a>
    <a href="<?= Url::to(['log/backend']) ?>" class="btn btn-info">Backend</a>
</div>