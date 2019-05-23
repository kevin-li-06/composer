<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\datetime\DateTimePicker;
use common\models\AppointmentSearch;
use common\models\User;
use backend\models\PrizeSearch;
use backend\components\Log;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $searchModel common\models\AppointmentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '预约核销';
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
    .btn-cancel, .btn-submit {
        /* float: right; */
        margin-top: 30px;
        margin: 2px;
    }
</style>

<div class="appointment-index">

    <h1>
        <?php 
            if (Yii::$app->user->identity->id_role == 3) {
                echo '【' . $store->zh_storename . '】';
            } 
        ?>
        <?= Html::encode($this->title) ?>
    </h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php if (Yii::$app->user->identity->id_role == 1) { ?>
            <?= Html::a('Create Appointment', ['create'], ['class' => 'btn btn-success']) ?>
            <?= Html::a('导入门店商品库存', ['import-stock'], ['class' => 'btn btn-success']) ?>
        <?php } elseif (Yii::$app->user->identity->id_role == 2) { ?>
            <?= Html::a('导入门店商品库存', ['import-stock'], ['class' => 'btn btn-success']) ?>
        <?php } ?>
    </p>

    <!-- 奖品数量展示 -->
    <!-- <div style="border:1px solid #ccc;padding: 10px 16px;border-radius: 6px;">
        <h3>活动统计</h3>
        <!-- <?php // foreach ($sum as $k => $v) { ?>
            <button class="btn btn-default" type="button">
            <? //= // $prizesMap[$k] ?> <span class="badge"><? //= // $v ?></span>
            </button>
        <?php // } ?> -->
         <table class="table table-hover">
            <tr>
                <th>奖品名称</th>
                <?php foreach ($prizesMap as $value ) {?>
                    <td><?php echo $value ?></td>
                <?php }?>
            </tr>
            <tr>
                <th>预约量</th>
                <?php foreach ($sum as $value ) {?>
                    <td><?php echo $value ?></td>
                <?php }?>
            </tr>
            <tr>
                <th>库存量</th>
                <?php foreach ($stock as $key=>$value ) {?>
                    <td class="<?= ($sum[$key] > $value) ?  'danger' : ''?>"><?php echo $value ?></td>
                <?php }?>
            </tr>
        </table>
    </div>
    

    <br><br>

    <?=
     GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            // [
            //     'class' => 'yii\grid\SerialColumn',
            //     'options' => ['width' => '20'],
            // ],
            [
                'attribute' => 'name',
                'options' => ['width' => '120'],
            ],
            [
                'attribute' => 'prize_id',
                'options' => ['width' => '120'],
            ],
            [
                'attribute' => 'mobile',
                'options' => ['width' => '120'],
            ],
            // [
            //     'attribute' => 'openid',
            //     'options' => ['width' => '120'],
            // ],
            [
                'attribute' => 'store_id', 
                'options' => ['style'=>'width:20%'],     
                // 'filter' => Html::activeDropDownList(
                //     $searchModel,
                //     'store_id',
                //     $stores,
                //     ['class' => 'form-control', 'prompt' => '请选择']
                // ),
                'visible' => (Yii::$app->user->identity->id_role != 3)
            ],            
            [
                'attribute' => 'created_at',
                'options' => ['style'=>'width:20%'],
                'format' => 'raw',    
                'value' => function($data){                    
                    return date('Y-m-d H:i:s',$data['created_at']);
                },
                // 'filter' =>  DateTimePicker::widget([
                //     'model'=> $searchModel,
                //     'name' => 'AppointmentSearch[created_at]',
                //     //value值更新的时候需要加上                     
                //     'value' => $searchModel->created_at, 
                //     'pluginOptions' => [ 
                //         'minView'=> "month",
                //         'autoclose' => true, 
                //         'format' => 'yyyy-mm-dd', 
                //         'todayHighlight' => true, 
                //     ] 
                // ]),                
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => '操作',
                'options' => ['width' => '50'],
                'template' => '{redeem} {change} {changestore}',
                'buttons' => [
                    'redeem' => function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-saved redeem-action" data-id="' . $model['user_id'] . '"></span>', '#', ['title' => '核销']);
                    },
                    'change' => function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-edit"></span>', Url::to(['/record/change-prize', 'id' => $model['user_id']]), ['title' => '变更奖品'] ) ;
                    },
                    'changestore' => function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-object-align-bottom"></span>', Url::to(['/store/change-store', 'mobile' => $model['mobile']]), ['title' => '变更门店', 'style' => 'margin-left:6px;'] ) ;
                    },
                ],
                'visibleButtons' => [
					'redeem' => function ($model, $key, $index) {
                        if ($model['redeem_at']) {
                            return false;
                        } else {
                            if ($model['prize_id']) {
                                return true;
                            } else {
                                return false;
                            }
                        }
                    },
                    'change' => function ($model, $key, $index) {
                        if ($model['redeem_at'] && !$model['change_at']) {
                            return true;
                        } else {
                            return false;
                        }
                    },
                    'changestore' => function ($model, $key, $index) {
                        if (Yii::$app->user->identity->id_role <= 2) {
                            return true;
                        } else {
                            return false;
                        }
                    },
				],
            ],
        ],
    ]);
    
    ?>

    <?php
    // GridView::widget([
    //     'dataProvider' => $dataProvider,
    //     'filterModel' => $searchModel,
    //     'columns' => [
    //         // [
    //         //     'class' => 'yii\grid\SerialColumn',
    //         //     'options' => ['width' => '20'],
    //         // ],
    //         [
    //             'attribute' => 'name',
    //             'options' => ['width' => '120'],
    //         ],
    //         [
    //             'attribute' => 'prize_id',
    //             'options' => ['width' => '120'],
    //             'value' => function ($data) {
    //                 if ($data->prize_id > 0) {
    //                     return $data->prize->name;
    //                 }
    //                 return '';
    //             }
    //         ],
    //         [
    //             'attribute' => 'mobile',
    //             'options' => ['width' => '120'],
    //         ],
    //         // [
    //         //     'attribute' => 'openid',
    //         //     'options' => ['width' => '120'],
    //         // ],
    //         [
    //             'attribute' => 'store_id', 
    //             'options' => ['style'=>'width:20%'],     
    //             'filter' => Html::activeDropDownList(
    //                 $searchModel,
    //                 'store_id',
    //                 $stores,
    //                 ['class' => 'form-control', 'prompt' => '请选择']
    //             ),
    //             'value' => function ($data) { 
    //                 return AppointmentSearch::storeMap($data->store_id);
    //             },
    //             'visible' => (Yii::$app->user->identity->id_role != 3)
    //         ]

    //         ,            
    //         [
    //             'attribute' => 'created_at',
    //             'options' => ['style'=>'width:20%'],
    //             'format' => 'raw',    
    //             'value' => function($data){                    
    //                 return date('Y-m-d H:i:s',$data->created_at);
    //             },
    //             'filter' =>  DateTimePicker::widget([
    //                 'model'=> $searchModel,
    //                 'name' => 'AppointmentSearch[created_at]',
    //                 //value值更新的时候需要加上                     
    //                 'value' => $searchModel->created_at, 
    //                 'pluginOptions' => [ 
    //                     'minView'=> "month",
    //                     'autoclose' => true, 
    //                     'format' => 'yyyy-mm-dd', 
    //                     'todayHighlight' => true, 
    //                 ] 
    //             ]),                
    //         ],
    //         [
    //             'class' => 'yii\grid\ActionColumn',
    //             'header' => '操作',
    //             'options' => ['width' => '50'],
    //             'template' => '{redeem} {change}',
    //             'buttons' => [
    //                 'redeem' => function ($url, $model, $key) {
    //                     return Html::a('<span class="glyphicon glyphicon-saved redeem-action" data-id="' . $key['id'] . '"></span>', '#', ['title' => '核销']);
    //                 },
    //                 'change' => function ($url, $model, $key) {
    //                     return Html::a('<span class="glyphicon glyphicon-edit"></span>', Url::to(['/record/change-prize', 'id' => $key['id']]), ['title' => '变更奖品'] ) ;
    //                 },
    //             ],
    //             'visibleButtons' => [
	// 				'redeem' => function ($model, $key, $index) {
    //                     if ($key['redeem_at']) {
    //                         return false;
    //                     } else {
    //                         if ($key['result']) {
    //                             return true;
    //                         } else {
    //                             return false;
    //                         }
    //                     }
    //                 },
    //                 'change' => function ($model, $key, $index) {
    //                     if ($key['redeem_at'] && !$key['change_at']) {
    //                         return true;
    //                     } else {
    //                         return false;
    //                     }
    //                 },
	// 			],
    //         ],
    //     ],
    // ]); 
    ?>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="redeem-modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">输入授权码完成核销</h4>
            </div>
            <div class="modal-body">
                <form action="<?= Url::to(['record/redeem']) ?>" method="post">
                    <div class="form-group">
                        <label for="">授权码</label>
                        <input class="form-control" type="text" name="auth_code" placeholder="请输入授权码...">
                        <input class="form-control" type="hidden" name="user_id">
                        <input name="_csrf-backend" type="hidden" id="_csrf-backend" value="<?= Yii::$app->request->csrfToken ?>">
                    </div>
                    <button type="button" class="btn btn-default btn-cancel" data-dismiss="modal">取消</button>
                    <button type="submit" class="btn btn-primary btn-submit">确认核销</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php

$js = <<<JS

$(".redeem-action").click(function(){
    var id = $(this).attr("data-id");
    $("#redeem-modal [name='user_id']").val(id);
    $("#redeem-modal").modal('show');
});

JS;

$this->registerJs($js, \yii\web\View::POS_END);

?>
