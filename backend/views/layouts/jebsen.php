<?php

/* @var $this \yii\web\View */
/* @var $content string */

use backend\assets\AppAsset;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use common\widgets\Alert;

AppAsset::register($this);
Yii::$app->homeUrl = Url::to(['report/ranking']);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <!-- <meta name="referrer" content="never"> -->
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <style>
        .wrap > .container {
            padding: 0px 15px 20px;
        }
        .page-header {
            padding-bottom: 9px;
            margin: 20px 0 30px;
            border-bottom: 1px solid #eee;
        }
    </style>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => 'liber',
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-static-top',
        ],
    ]);
    $menuItems = [
        // ['label' => '主页', 'url' => ['/site/index']],
    ];
    // print_r(Yii::$app->user);exit;
    if (Yii::$app->user->isGuest) {
        $menuItems[] = ['label' => '登录', 'url' => ['/site/login']];
    } else {
        $menuItems[] = ['label' => '统计报表', 'url' => ['report/ranking']];
        $menuItems[] = ['label' => '库存调动', 'url' => ['appointment/store-stock']];
        $menuItems[] = ['label' => '增加刮奖机会', 'url' => ['/user/index']];
        $menuItems[] = ['label' => '预约核销', 'url' => ['/appointment/index']];
        $menuItems[] = ['label' => '门店配置', 'url' => ['/store/index']];
        $menuItems[] = '<li>'
            . Html::beginForm(['/site/logout'], 'post')
            . Html::submitButton(
                '登出 (' . Yii::$app->user->identity->username . ')',
                ['class' => 'btn btn-link logout']
            )
            . Html::endForm()
            . '</li>';
    }
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => $menuItems,
    ]);
    NavBar::end();
    ?>

    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
<div class="container">
    <p class="pull-left">&copy; XGATE Chengdu <?= date('Y') ?></p>
    <p class="pull-right">技术支持 XGATE Chengdu Co.Ltd </p>
</div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
