<?php

use yii\helpers\Url;
use yii\helpers\Html;

?>

<a href="<?= Url::to(['wechat/index']) ?>" class="btn btn-primary">返回</a>


<pre>
    <?php 
        if (!empty($re)) {
            print_r($re);
        } else {
            print_r($data);
        }
    ?>
</pre>

<?= Html::beginForm(); ?>
<input type="submit" class="btn btn-success" value="设置">
<?= Html::endForm(); ?>