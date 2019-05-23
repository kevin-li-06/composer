
<?php
use yii\helpers\Html;
use yii\helpers\Url;
//use yii\bootstrap\Modal;
//Modal::begin();
?>

<?php

//Modal::begin([
//    'id' => 'page-modal',
//    'header' => '<h5>这里是标题</h5>',
//    'toggleButton' => ['label' => 'click me'],
//    'footer' => '<button/> 关闭',
//    'closeButton' => ['label' => '关闭']
//]);
\common\widgets\Modal::begin();
\common\widgets\Modal::end();
echo '这里是模态内容...';

?>

<script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
<script>
$.post("<?= Url::to(['lucky-draw/ajax-share'])?>", { source: 'share' },function(result){
            var re =  JSON.parse(result);
                alert(re['message']);
        });
</script>        