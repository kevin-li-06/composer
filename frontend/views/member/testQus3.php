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
        <?php
            $this->registerCssFile(Url::to('@web/static/css/index.css?t=' . time(), true), ['depends' => [\frontend\assets\AppAsset::classname()], 'position' => \yii\web\View::POS_END]);
            $this->registerCssFile(Url::to('@web/static/css/testModule.css?t=' . time(), true), ['depends' => [\frontend\assets\AppAsset::classname()], 'position' => \yii\web\View::POS_END]);
        ?>
      
    </head>

    <body>
        <div class="container">
            <div class=" qustioBox"  id="qustionGroup3">
                <h4>Q3： 失恋时，你会选择哪一种发泄的方式？</h4>
                <div class="answerDiv" id="answerDiv3">
                    <p class="answer" key="A">A 和闺蜜血拼</p>
                    <p class="answer" key="B">B.来一次说走就走的旅行</p>
                    <p class="answer" key="C">C.寄情工作</p>
                </div>
                <div>
                    <a  href=" member/home">跳过</a><br>
                    <button class=" btn btn-primary qustionSub" id="qustion3Submit">提交</button>
                   
                </div>
            </div>
        </div> 
        <div class="tipProp">
            <p> 您还没有!</p>
        </div>
       
    </body>

</html>
<?php
    // $this->registerJsFile(Url::to('@web/static/js/jquery.js?t=' . time(), true), ['depends' => [\frontend\assets\AppAsset::classname()], 'position' => \yii\web\View::POS_END]);
    $this->registerJsFile(Url::to('@web/static/js/question.js?t=' . time(), true), ['depends' => [\frontend\assets\AppAsset::classname()], 'position' => \yii\web\View::POS_END]);
?> 
       