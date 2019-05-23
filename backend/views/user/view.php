<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = $model->username;
$this->params['breadcrumbs'][] = ['label' => '用户列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('更新', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('删除', ['delete', 'id' => $model->id], [
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
            'username',
            [
                'attribute' => 'region',
                'value' => function ($data) {
                    if (isset($data->region)) {
                        switch ($data->region) {
                            case 'EC':
                                $re = '东区';
                                break;
                            case 'SC':
                                $re = '南区';
                                break;
                            case 'WC':
                                $re = '西区';
                                break;
                            case 'NC':
                                $re = '北区';
                                break;
                            default:
                                $re = '';
                                break;
                        }
                        return $re;
                    } else {
                        return '';
                    }
                }
            ],
            // 'auth_key',
            'mobile',
            'mobile_hash',
            'card',
            'openid',
            [
                'attribute' => 'group',
                'value' => function ($data) {
                    if (isset($data->group)) {
                        switch ($data->group) {
                            case 1:
                                $re = 'A';
                                break;
                            case 2:
                                $re = 'B';
                                break;
                            case 3:
                                $re = 'C';
                                break;
                            default:
                                $re = '';
                                break;
                        }
                        return $re;
                    } else {
                        return '';
                    }
                }
            ],
            'small_chance',
            'big_chance',
            'continuous',
//             [
//                 'attribute' => 'result',
//                 'value' => function ($data) {
//                     if ($data->result) {
//                         echo "<pre/>";
//                         var_dump($data);die;
//                         return $data->prize->name;
//                     }
//                     return '未中奖';
//                 }
//             ],
            [
                'attribute' => 'redeem_prize',
                'value' => function ($data) {
                    if ($data->redeem_at) {
                        return \common\models\Prize::find()->select('name')->where(['id' => $data->redeem_prize])->scalar();
                    }
                    return '未中奖';
                }
            ],
            'redeem_at:datetime',
            [
                'attribute' => 'change_prize',
                'value' => function ($data) {
                    if ($data->change_at) {
                        return \common\models\Prize::find()->select('name')->where(['id' => $data->change_prize])->scalar();
                    }
                    return '未改变';
                }
            ],
            'change_at:datetime',
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>

</div>
