<?php

use yii\helpers\Html;
use yii\helpers\Url;
use common\models\Rule;

?>

<h2>
    模拟 [<?= Rule::groups($model->group) ?>] 组抽奖 [<?= Rule::types($model->type) ?>] 概率 
    <a class="btn btn-primary pull-right" href="<?= Url::to(['rule/group', 'group' => $model->group]) ?>">返回</a>
</h2>


<p class="text-muted"><hr></p>

<div>
    <?= Html::beginForm() ?>
    <div class="form-group col-xs-2" style="float:none;padding-left:0;">
        <label class="control-label text-warning">请输入模拟抽奖的次数</label>
        <input type="text" class="form-control" name="times" value="<?= isset($times) ? $times : 100 ?>">
    </div>
    <div class="form-group">
        <?= Html::submitButton('开始模拟', ['class' => 'btn btn-warning']) ?>
    </div>
    <?= Html::endForm() ?>
</div>

<?php if (isset($times)) { ?>
<h3>总抽奖次数为:<?php echo $times; ?></h3>

<table class="table table-hover table-bordered">
    <tr class="info">
        <th>奖品名称</th>
        <th>模拟中奖次数</th>
        <th>模拟中奖概率</th>
        <th>配置中奖概率</th>
        <th>误差</th>
    </tr>
    
    <?php foreach ($result as $key =>$value) {?>
    <tr>        
        <td><?= $value['name']; ?></td>
        <td><?= $value['score']; ?></td>
        <td><?= round(($value['score']/$times),4)*100 ?>%</td>
        <td><?= round($value['rate'],4); ?>%</td>
        <td><?php
                $a = round(($value['score']/$times)*100 - $value['rate'], 4);
                if ($a > 0) {
                    $a = '+' . $a;
                }
                echo $a;
            ?>%
        </td>
    </tr>
    <?php }?>
</table>

<?php } ?>
