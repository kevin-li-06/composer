<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = '第' . $model->id . '天的奖品配置';
$this->params['breadcrumbs'][] = ['label' => '概率配置', 'url' => ['index']];
// $this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '第' . $model->id . '天';
?>
<div class="rule-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="rule-form">
    
        <?php $form = ActiveForm::begin(); ?>

        <?php foreach($prize_chance as $k => $v) { ?>
            <div class="form-group col-xs-6">
                <label class="control-label">奖品</label>
                <input type="text" class="form-control" readonly="readonly" name="" placeholder="<?= $v['name'] ?>">
            </div>
            <div class="form-group col-xs-6">
                <label class="control-label">概率</label>
                <div class="input-group">
                    <input type="text" class="form-control" readonly="readonly" name="Chance[<?= $k ?>]" value="<?= $v['chance'] ?>">
                    <span class="input-group-addon">%</span>
                </div>
            </div>
        <?php } ?>
        

        <!-- <div class="form-group">
            <?= Html::submitButton('配置奖品', ['class' => 'btn btn-primary']) ?>
        </div> -->

        <?php ActiveForm::end(); ?>

        <p><?= Html::a('返回', ['index'], ['class' => 'btn btn-primary']) ?></p>

    </div>

</div>

<?php

$js = <<<JS

JS;

$this->registerJs($js, \yii\web\View::POS_END);
?>
