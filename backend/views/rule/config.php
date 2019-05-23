<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Rule */

$this->title = '配置第' . $model->id . '天的奖品';
$this->params['breadcrumbs'][] = ['label' => '概率配置', 'url' => ['index']];
// $this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '第' . $model->id . '天';
?>
<div class="rule-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <!-- <button class="btn btn-success" id="create-prize">创建奖品</button> -->

    <div class="rule-form">
    
        <?php $form = ActiveForm::begin(); ?>

        <!-- <?= $form->field($model, 'day')->textInput(['readonly' => 'readonly']) ?> -->

        <!-- <?= $form->field($model, 'prize_chance')->textInput(['maxlength' => true]) ?> -->

        <!-- <?= $form->field($model, 'prize_chance')->dropdownList(['maxlength' => true]) ?> -->

        <?php foreach($prizes as $k => $v) { ?>
            <div class="form-group col-xs-6">
                <label class="control-label">奖品</label>
                <input type="text" class="form-control" readonly="readonly" name="" placeholder="<?= $v->name ?>">
            </div>
            <div class="form-group col-xs-6">
                <label class="control-label">概率</label>
                <?php if ($preview) { ?>
                    <div class="input-group <?= (isset($chance[$v->id]) && ($chance[$v->id] != 0)) ? 'has-success' : '' ?>">
                <?php } else { ?>
                    <div class="input-group <?= (isset($chance[$v->id]) && ($chance[$v->id] != 0)) ? 'has-error' : '' ?>">
                <?php } ?>
                    <input type="text" class="form-control" name="Chance[<?= $v->id ?>]" value="<?= isset($chance[$v->id]) ? $chance[$v->id] : 0 ?>">
                    <span class="input-group-addon">%</span>
                </div>
            </div>
        <?php } ?>
        

        <div class="form-group">
            <?= Html::submitButton('配置奖品', ['class' => 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>

<?php

$js = <<<JS

JS;

$this->registerJs($js, \yii\web\View::POS_END);
?>
