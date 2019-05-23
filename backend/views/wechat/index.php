<?php

use yii\helpers\Url;

?>

<div class="btn-group">
    <a class="btn btn-default" href="<?= Url::to(['wechat/menu-get']) ?>">获取微信菜单</a>
    <a class="btn btn-default" href="<?= Url::to(['wechat/menu-set']) ?>">设置微信菜单</a>
</div>