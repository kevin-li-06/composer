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
           
            <div class=" qustioBox"  id="qustionGroup1">
                <h4>Q1： 假设会议上一位同级的同事毫不畏惧地反驳和职责你的项目提案，你会如何应对？</h4>
                <ul class="answerDiv" id="answerDiv1">
                    <li class="answer" key="A">A 改变初衷，低头接受</li>
                    <li class="answer" key="B">B.坚持自我，顺势反驳</li>
                    <li class="answer" key="C">C. 一言不发，事后找领导解释</li>
                </ul>
                <div>
                    <a  href=" member/qus-two">跳过</a><br>
                    <button class=" btn btn-primary qustionSub" id="qustion1Submit">提交</button>
                    
                </div>
            </div>
        </div>
        <div class="tipProp">
            <p> 您还没有!</p>
        </div>
    </body>  
</html>
<?php
        
        // $this->registerJsFile(Url::to('@web/static/js/testModule.js?t=' . time(), true), ['depends' => [\frontend\assets\AppAsset::classname()], 'position' => \yii\web\View::POS_END]);
        $this->registerJsFile(Url::to('@web/static/js/question.js?t=' . time(), true), ['depends' => [\frontend\assets\AppAsset::classname()], 'position' => \yii\web\View::POS_END]);
        
   ?>   
   
      
