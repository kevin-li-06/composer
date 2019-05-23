<?php

use yii\helpers\Url;
use yii\helpers\Html;

?>


<?= Html::beginForm(Url::to(['/appointment-create'])) ?>

<?= Html::hiddenInput('name', 'Bluce') ?>
<?= Html::hiddenInput('mobile', '15308029844') ?>
<?= Html::hiddenInput('openid', 'bluce') ?>
<?= Html::hiddenInput('store_id', '1') ?>
<!-- <?= Html::hiddenInput('record_id', '1') ?> -->

<?= Html::submitButton() ?>
<?= Html::endForm() ?>