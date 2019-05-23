<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css',
        'static/css/index.css',
        'static/css/tomes.css',
        'static/css/lisa.css',
    ];
    public $js = [
    ];   
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];

    // public $jsOptions = ['position' => \yii\web\View::POS_HEAD];
}
