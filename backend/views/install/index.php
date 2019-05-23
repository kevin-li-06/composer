<?php

use common\widgets\Alert;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;

$this->title = '数据库';
?>

<?php if ($installed): ?>
    <h2 class="page-header">您的数据库已安装成功</h2>

    <!-- <?= Html::a('删除整个数据库', ['install/drop'], ['class' => 'btn btn-default']) ?> -->

    <?= Html::a('更新数据库', ['install/update'], ['class' => 'btn btn-primary']) ?>

    <?= Html::a('前往后台', ['site/login'], ['class' => 'btn btn-primary']) ?>

    <div class="raw" style="color:#999;">
        You may login with <strong>xgate/123</strong> in the backend.
    </div>

<?php else: ?>
    <h2 class="page-header">输入您的数据库用户名和密码</h2>

    <?= Html::beginForm(['install/run-installation'], 'post', ['class' => 'form', 'style' => 'width:30%']) ?>
        <div class="form-group">
            <label>数据库 用户名</label>
            <?= Html::input('text', 'db_username', '', ['class' => 'form-control', 'required' => 'required']) ?>
        </div>
        <div class="form-group">
            <label>数据库 密码</label>
            <?= Html::input('text', 'db_password', '', ['class' => 'form-control']) ?>
        </div>
        <?= Html::submitInput('初始化安装', ['class' => 'btn btn-default']) ?>
    <?= Html::endForm() ?>
<?php endif; ?>