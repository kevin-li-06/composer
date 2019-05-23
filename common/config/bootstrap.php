<?php
Yii::setAlias('@common', dirname(__DIR__));
Yii::setAlias('@frontend', dirname(dirname(__DIR__)) . '/frontend');
Yii::setAlias('@backend', dirname(dirname(__DIR__)) . '/backend');
Yii::setAlias('@console', dirname(dirname(__DIR__)) . '/console');

require_once('functions.php');
require_once('functions-local.php');

// 核销的授权码
define('AUTH_CODE', 1128);

// 每天小奖的抽奖次数上限
define('SMALL_MAX', 10000);

// 每天大奖的抽奖次数上限
define('BIG_MAX', 10000);