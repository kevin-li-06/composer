<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\Province;
use common\models\City;
use common\models\District;

/* @var $this yii\web\View */
/* @var $model app\models\store */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Stores', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="store-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'storename',
            'address',
            'store_code',
            [
                'attribute'=> 'status',
                'value' => function ($data) {
                   $status = $data->status;
                   switch ($status) {
                    case 1:
                        return '正常';
                        break;
                    case 0:
                        return '异常';
                        break;
                    }
                }
            ],
        ],
    ]) ?>

</div>
