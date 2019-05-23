<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Rule;

/* @var $this yii\web\View */
/* @var $model backend\models\Rule */

$this->title = '创建抽奖配置';
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

<div class="rule-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="rule-form">

        <?php $form = ActiveForm::begin(); ?>

        <?php foreach($prizes as $k => $v) { ?>
        <div class="row">
            <div class="form-group col-xs-6 col-md-6">
                <label class="control-label">奖品</label>
                <input type="text" class="form-control" readonly="readonly" name="" placeholder="<?= $v->name ?>">
            </div>
            <div class="form-group col-xs-6 col-md-6">
                <label class="control-label">概率</label>
                <div class="input-group <?= (isset($rates[$v->id]) && ($rates[$v->id] != 0)) ? 'has-error' : '' ?>">
                    <input type="text" class="form-control" name="Rate[<?= $v->id ?>]" value="<?= isset($rates[$v->id]) ? $rates[$v->id] : 0 ?>">
                    <span class="input-group-addon">%</span>
                </div>
            </div>
        </div>   
        <?php } ?>
        

        <div class="form-group">
            <?= Html::submitButton('分配奖品及概率', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>

