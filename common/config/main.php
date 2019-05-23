<?php
return [
    'language' => 'zh-CN', // 默认中文
    'timeZone'=>'Asia/Shanghai', // 默认上海
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',            
        ],
        'errorHandler' => [
                'maxSourceLines' => 20,
                'errorAction' => 'site/error',
            ],
    ],
    'aliases' => [
        '@bower' => dirname(dirname(__DIR__)) . '/vendor/bower-asset',
    ],
];
