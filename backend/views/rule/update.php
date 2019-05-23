<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Rule;
use common\models\Prize;

/* @var $this yii\web\View */
/* @var $model common\models\Rule */

$this->title = '更新配置: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => '抽奖配置', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
.multiple-input-left {
    padding-left: 0;
}
.multiple-input-right {
    padding-right: 0;
}
</style>

<div class="rule-update">

<h1><?= Html::encode($this->title) ?></h1>

<div class="rule-form">

    <?php $form = ActiveForm::begin(); ?>


    <?php foreach($prizes as $k => $v) { ?>
	<div class="row">
        <div class="form-group col-xs-6 col-md-6 multiple-input-left">
            <label class="control-label">奖品</label>
            <input type="text" class="form-control" readonly="readonly" name="" placeholder="<?= $v->name ?>">
        </div>

        <div class="form-group col-xs-6 col-md-6 multiple-input-right">
            <label class="control-label">概率</label>
            <?php if ($preview) { ?>
                <div class="input-group <?= (isset($rates[$v->id]) && ($rates[$v->id] != 0)) ? 'has-success' : '' ?>">
            <?php } else { ?>
                <div class="input-group <?= (isset($rates[$v->id]) && ($rates[$v->id] != 0)) ? 'has-error' : '' ?>">
            <?php } ?>
                <input type="text" class="form-control" name="Rate[<?= $v->id ?>]" value="<?= isset($rates[$v->id]) ? $rates[$v->id] : 0 ?>">
                <span class="input-group-addon">%</span>
            </div>
        </div>
     </div>   
    <?php } ?>

    <div class="form-group">
        <?= Html::submitButton('更新奖品及概率', ['class' => 'btn btn-info']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

</div>

<?php

$js = <<<JS

// (function(){
//     // Your base, I'm in it!
//     var originalAddClassMethod = jQuery.fn.addClass;

//     jQuery.fn.addClass = function(){
//         // Execute the original method.
//         var result = originalAddClassMethod.apply( this, arguments );

//         // trigger a custom event
//         jQuery(this).trigger('cssClassChanged');

//         // return the original result
//         return result;
//     }
// })();

// document ready function
$(function(){
 
});

JS;

$this->registerJs($js, \yii\web\View::POS_END);
?>

