<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\UserSearch1 */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'username') ?>

    <?= $form->field($model, 'email') ?>

    <?= $form->field($model, 'auth_key') ?>

    <?= $form->field($model, 'mobile') ?>

    <?php // echo $form->field($model, 'mobile_hash') ?>

    <?php // echo $form->field($model, 'card') ?>

    <?php // echo $form->field($model, 'openid') ?>

    <?php // echo $form->field($model, 'group') ?>

    <?php // echo $form->field($model, 'small_chance') ?>

    <?php // echo $form->field($model, 'big_chance') ?>

    <?php // echo $form->field($model, 'continuous') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
