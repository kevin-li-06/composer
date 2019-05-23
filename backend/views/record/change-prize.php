<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Record */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="redeem-form">

    <?= Html::beginForm(); ?>

    <div class="form-group col-xs-3">
        <label for="">确认改变奖品</label>
        <?= Html::dropDownList('change_prize', '', $prizes, ['class' => 'form-control']) ?>
    </div>

    <div class="form-group col-xs-12">
        <?= Html::submitButton('确认改变', ['class' => 'btn btn-success']) ?>
    </div>

    <?= Html::endForm(); ?>

</div>
