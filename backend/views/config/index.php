<?php

use yii\helpers\Url;

?>

<div class="btn-group">
    <a class="btn btn-default" href="<?= Url::to(['scene/index']) ?>">场景配置</a>
    <a class="btn btn-default" href="<?= Url::to(['rule/index']) ?>">概率配置</a>
</div>