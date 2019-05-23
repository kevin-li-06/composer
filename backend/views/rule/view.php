<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\ActiveForm;
use common\models\Rule;

/* @var $this yii\web\View */
/* @var $model common\models\Rule */

$this->title = $model->id;
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

<div class="rule-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('修改', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('删除', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => '您确定要删除此项吗?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

<div class="rule-form">

    <?php $form = ActiveForm::begin(); ?>


    <?php foreach($prizes as $k => $v) { ?>
    <div class="row">


        <div class="form-group col-xs-5 multiple-input-left">
            <label class="control-label">奖品</label>
            <input type="text" class="form-control" readonly="readonly" name="" placeholder="<?= $v->name ?>">
        </div>
        <div class="form-group col-xs-5 multiple-input-right">
            <label class="control-label">概率</label>
                <div class="input-group">
                <input type="text" class="form-control" name="Rate[<?= $v->id ?>]" readonly="readonly" value="<?= isset($rates[$v->id]) ? $rates[$v->id] : 0 ?>">
                <span class="input-group-addon">%</span>
            </div>
        </div>
    </div>    
    <?php } ?>

    <?php ActiveForm::end(); ?>

</div>

</div>
