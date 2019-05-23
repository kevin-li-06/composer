<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use common\models\Store;

$this->title = '用户列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
    .glyphicon {
        font-size: 16px;
        padding: 1.4px;
    }
</style>

<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php if (Yii::$app->user->identity->id_role == 1) {?>
        <?= Html::a('创建用户', ['create'], ['class' => 'btn btn-success']) ?>

        <?php } ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            // ['class' => 'yii\grid\SerialColumn'],
            // [
            //     'attribute' => 'id',
            //     'options' => ['width' => '20'],
            // ],
            [
                'attribute' => 'source',
                'options' => ['width' => '20'],
                // 'filter' => Html::activeDropDownList(
                //     $searchModel,
                //     'source',
                //     ['1' => '微信会员', '2' => '短信会员'],
                //     ['class' => 'form-control','prompt'=>'请选择']
                // ),
                'value' => function ($data) {
                    if ($data->openid) {
                        return '微信会员';
                    } else {
                        return '短信会员';
                    }
                }
            ],
            [
                'attribute' => 'username',
                'options' => ['width' => '40'],
            ],
            [
                'attribute' => 'mobile',
                'options' => ['width' => '60'],
            ],
            [
                'attribute' => 'card',
                'options' => ['width' => '60'],
            ],
            // [
            //     'attribute' => 'openid',
            //     'options' => ['width' => '60'],
            // ],
            // 'mobile_hash',
            // 'openid',
            // [
            //     'attribute' => 'group',
            //     'filter' => Html::activeDropDownList(
            //         $searchModel,
            //         'group',
            //         ['1' => 'A', '2' => 'B', '3' => 'C'],
            //         ['class' => 'form-control', 'prompt' => '请选择', 'style' => 'width:90px;']
            //     ),
            //     'value' => function ($data) {
            //         if (isset($data->group)) {
            //             switch ($data->group) {
            //                 case 1:
            //                     $re = 'A';
            //                     break;
            //                 case 2:
            //                     $re = 'B';
            //                     break;
            //                 case 3:
            //                     $re = 'C';
            //                     break;
            //                 default:
            //                     $re = '';
            //                     break;
            //             }
            //             return $re;
            //         } else {
            //             return '';
            //         }
            //     }
            // ],
            // [
            //     'attribute' => 'region',
            //     // 'options' => ['style' => 'width:20px;'],
            //     'filter' => Html::activeDropDownList(
            //         $searchModel,
            //         'region',
            //         ['EC' => '东区', 'SC' => '南区', 'WC' => '西区', 'NC' => '北区'],
            //         ['class' => 'form-control', 'prompt' => '请选择', 'style' => 'width:90px;']
            //     ),
            //     'value' => function ($data) {
            //         if (isset($data->region)) {
            //             switch ($data->region) {
            //                 case 'EC':
            //                     $re = '东区';
            //                     break;
            //                 case 'SC':
            //                     $re = '南区';
            //                     break;
            //                 case 'WC':
            //                     $re = '西区';
            //                     break;
            //                 case 'NC':
            //                     $re = '北区';
            //                     break;
            //                 default:
            //                     $re = '';
            //                     break;
            //             }
            //             return $re;
            //         } else {
            //             return '';
            //         }
            //     }
            // ],
            // [
            //     'attribute' => 'lottery_chance',
            //     'options' => ['width' => '80'],
            //     'value' => function ($data) {
            //         return $data->lottery_chance . '次';
            //     }
            // ],
//            [
//                'attribute' => 'small_chance',
//                'options' => ['width' => '80'],
//                'value' => function ($data) {
//                    return $data->small_chance . '次';
//                }
//            ],
//            [
//                'attribute' => 'big_chance',
//                'options' => ['width' => '80'],
//                'value' => function ($data) {
//                    return $data->big_chance . '次';
//                }
//            ],
            // [
            //     'attribute' => 'continuous',
            //     'value' => function ($data) {
            //         return $data->continuous . '天';
            //     }
            // ],
            [
                'attribute' => 'result',
                'filter' => Html::activeDropDownList(
                    $searchModel,
                    'result',
                    $prizes,
                    ['class' => 'form-control','prompt'=>'请选择']
                ),
                'options' => ['width' => '80'],
                'value' => function ($data) {
                    $prize = \common\models\User::getHighestPrize($data->id);
                    if ($prize) {
                        return $prize->name;
                    } else {
                        return '未中奖';
                    }
                }
            ],
//            [
//                'attribute' => 'redeem_prize',
//                'options' => ['width' => '20'],
//                'filter' => Html::activeDropDownList(
//                    $searchModel,
//                    'redeem_prize',
//                    $prizes,
//                    ['class' => 'form-control','prompt'=>'请选择']
//                ),
//                'value' => function ($data) {
//                    if ($data->redeem_prize) {
//                        $prize = \common\models\Prize::findOne($data->redeem_prize);
//                        return $prize->name;
//                    } else {
//                        return '未核销';
//                    }
//                }
//            ],
            // [
            //     'attribute' => 'redeem_at',
            //     'options' => ['width' => '20'],
            //     'value' => function ($data) {
            //         if ($data->redeem_at) {
            //             return date('Y-m-d H:i:s', $data->redeem_at);
            //         } else {
            //             return '未核销';
            //         }
            //     }
            // ],
//            [
//                'attribute' => 'change_prize',
//                'options' => ['width' => '20'],
//                'filter' => Html::activeDropDownList(
//                    $searchModel,
//                    'change_prize',
//                    $prizes,
//                    ['class' => 'form-control','prompt'=>'请选择']
//                ),
//                'value' => function ($data) {
//                    if ($data->change_prize) {
//                        $prize = \common\models\Prize::findOne($data->change_prize);
//                        return $prize->name;
//                    } else {
//                        return '未变更';
//                    }
//                }
//            ],
            // [
            //     'attribute' => 'change_at',
            //     'options' => ['width' => '20'],
            //     'value' => function ($data) {
            //         if ($data->change_at) {
            //             return date('Y-m-d H:i:s', $data->change_at);
            //         } else {
            //             return '未变更';
            //         }
            //     }
            // ],
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => '操作',
                'options' => ['width' => '60'],
                'template' => '{consumption_two} {audit} {redeem} {view} {update} {delete} {test}',
                'buttons' => [
//                     'consumption_two' => function ($url, $model, $key) {
//                         return Html::a('<span style="color:#5cb85c" class="glyphicon glyphicon-plus-sign" onclick="consumption(\''.$model->id.'\',\''.$model->username.'\',\''."1".'\')"></span>', '#', ['title' => '消费5000增加机会','data-toggle' => 'modal','data-target' => '#page-modal',] ) ;
//                     },
                    // 'consumption' => function ($url, $model, $key) {
                    //     return Html::a('<span style="color:#f0ad4e" class="glyphicon glyphicon-plus-sign" onclick="consumption(\''.$model->id.'\',\''.$model->username.'\',\''."2".'\')"></span>', '#', ['title' => '消费10000增加机会','data-toggle' => 'modal','data-target' => '#page-modal',] ) ;
                    // },
                    'audit' => function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-calendar"></span>', Url::to(['/record/index', 'id' => $model->id]), ['title' => '抽奖记录']);
                    },
                    // 'redeem' => function ($url, $model, $key) {
                    //     return Html::a('<span class="glyphicon glyphicon-saved"></span>', Url::to(['/record/change-prize', 'id' => $model->id]), ['title' => '变更奖品'] ) ;
                    // },
                    'test' => function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-share-alt"></span>', Url::to('http://www.localhost.com/Jebsen/frontend/web/?hash=' . $model->mobile_hash), ['title' => '测试抽奖', 'target' => '_blank']);
                    }
                ],
                'visibleButtons' => [
					// 'redeem' => function ($model, $key, $index) {
                    //     $prize = \common\models\User::getHighestPrize($model->id);
                    //     $userstore_id = \common\models\Appointment::find()->where(['mobile'=>$model->mobile])->select('store_id')->scalar();
                    //     $username = Yii::$app->user->identity->username;$username = Yii::$app->user->identity->username;
                    //     $rolestatus = Yii::$app->user->identity->id_role;
                    //     $storeid = Store::find()->where(['store_code'=>$username])->select('id')->scalar();
                    //     if ($prize && !$model->redeem_prize && $rolestatus !=3) {     
                    //         return true;
                    //     } elseif ($prize && !$model->redeem_prize && $rolestatus = 3 && $storeid ==$userstore_id) {
                    //         return true;
                    //     } else {
                    //         return false;
                    //     }
                    // },
                    'audit' => function ($model, $key, $index) {
                        if (Yii::$app->user->identity->id_role == 3) {
                            return false;
                        }
                        return true;
                    },
                    'view' => function ($model, $key, $index) {
                        if (Yii::$app->user->identity->id_role == 1) {
                            return true;
                        }
                        return false;
                    },
                    'update' => function ($model, $key, $index) {
                        if (Yii::$app->user->identity->id_role == 1) {
                            return true;
                        }
                        return false;
                    },
                    'delete' => function ($model, $key, $index) {
                        if (Yii::$app->user->identity->id_role == 1) {
                            return true;
                        }
                        return false;
                    },
                    'test' => function ($model, $key, $index) {
                        if (Yii::$app->user->identity->id_role == 1) {
                            return true;
                        }
                        return false;
                    }
				],
            ],

        ],
    ]); ?>
</div>

<!--模态框组件-->
<?php Modal::begin([
        'id' => 'page-modal',
        'header' => '<h4 class="modal-title"><span class="consumption"></span><br/>用户【<span class="username"></span>】</h4>',
        //'toggleButton' => ['label' => 'click me'],
        'footer' => '<button type="button" onclick="submitReceipts()" class="btn btn-primary audit" value="">创建</button>',
        // 'closeButton' => ['label' => '关闭']
    ]);
    //小票输入框
    $form = \yii\bootstrap\ActiveForm::begin(['action' => ['record/record-consumption'], 'method' => 'post']);
    echo $form->field($record, 'user_id')->hiddenInput()->label(false);
    echo $form->field($record, 'receipts_type')->hiddenInput()->label(false);
    echo $form->field($record, 'receipts')->textInput()->label('消费小票号');
    \yii\bootstrap\ActiveForm::end();
    //模态框组件结束
    Modal::end();
?>
<script>

function consumption(id, name ,receipts_type){
    $(".username").html(name);
    $('#record-user_id').val(id);
    $('#record-receipts_type').val(receipts_type);
    if (receipts_type == 1) {
        $('.consumption').html("消费满5000增加抽奖机会");
    } else {
        $('.consumption').html("消费满10000增加抽奖机会");
    }
}

function submitReceipts() {
    var user_id = $('#record-user_id').val();
    var receipts_type = $('#record-receipts_type').val();
    var receipts = $('#record-receipts').val();
    $.post("<?php echo yii\helpers\Url::to(['record/record-consumption'])?>", {user_id : user_id, receipts_type : receipts_type, receipts : receipts},
    function(re){
        window.location.href="<?= \yii\helpers\Url::to(['user/index'])?>"
    });
}   
</script>
