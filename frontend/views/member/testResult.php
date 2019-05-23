<?php

use \yii\helpers\Url;

$this->title = '探索属于您的女神利器';

?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title></title>
       
      
    </head>

    <body>
        <div class="container">
            <div class="logoDiv"></div>
            <h3>Liber Fashion满足客户自由选择，属于自己的时尚</h3>
            <div>
                <div class="leftBox"><img src="<?=Yii::$app->request->baseUrl.'/static/img/test14.png'?>" class="img-responsive" alt="Responsive image"></div>
                <div class="rightBox"><img src="<?=Yii::$app->request->baseUrl.'/static/img/test17.png'?>"></div>
            </div>
            <a href="#" class="btn btn-danger">签到赢大奖</a>
        </div> 
    </body>

</html>
<?php
    $this->registerJsFile(Url::to('@web/static/js/jquery.js?t=' . time(), true), ['depends' => [\frontend\assets\AppAsset::classname()], 'position' => \yii\web\View::POS_END]);
    $this->registerJsFile(Url::to('@web/static/js/testModule.js?t=' . time(), true), ['depends' => [\frontend\assets\AppAsset::classname()], 'position' => \yii\web\View::POS_END]);
?> 