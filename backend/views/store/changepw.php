<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>
<h1>店名:<?= $model->zh_storename ?></h1>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($usermodel, 'new_password')->passwordInput(['maxlength' => true])->label('新密码') ?>

    <?= $form->field($usermodel, 're_password')->passwordInput(['maxlength' => true])->label('确认密码') ?>

    <div class="form-group">
        <?= Html::submitButton('更新', ['class' =>'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>