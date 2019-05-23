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
         $this->registerCssFile(Url::to('@web/static/css/testModule.css?t=' . time(), true), ['depends' => [\frontend\assets\AppAsset::classname()], 'position' => \yii\web\View::POS_END]);
        ?>
      
    </head>
    <!-- <body> -->
    <body onmousewheel="return false">
        <div class="slide-container">
           
            <div class="slide">
                <img src="<?=Yii::$app->request->baseUrl.'/static/img/P1_0.png'?>" class="img" alt="Responsive image">
                <div class="js-next nextBtn"><span class="glyphicon glyphicon-chevron-down"> </span></div>
            </div>
           
            <div class="slide">
               
                   <img src="<?=Yii::$app->request->baseUrl.'/static/img/P2.png'?>" class="img" alt="Responsive image">
                   <a id="testBtn"  href="<?php echo Url::to('member/qus-one')?>"></a>
            </div>
        </div> 
    </body>
</html>

  
<?php
    $this->registerJsFile(Url::to('@web/static/js/jquery.js?t=' . time(), true), ['depends' => [\frontend\assets\AppAsset::classname()], 'position' => \yii\web\View::POS_END]);
    $this->registerJsFile(Url::to('@web/static/js/testModule.js?t=' . time(), true), ['depends' => [\frontend\assets\AppAsset::classname()], 'position' => \yii\web\View::POS_END]);
?> 
  <script>//weaponTest  专用js
    checkAnswer()
    function checkAnswer(){
        console.log(123);
        var  notAnswer= <?= $notAnswer?>;//notAnswer 是一个数组
        var  testBtn=document.getElementById("testBtn"); 
             console.log(notAnswer,notAnswer[0]);
       
        if(notAnswer[0].indexOf('one')!=-1){
            testBtn.setAttribute('href',"<?php echo Url::to('member/qus-one')?>")//跳转至问题1的页面路径
        }else if(notAnswer[0].indexOf('two')!=-1){
            //testBtn.textContent='继续测试';
            testBtn.setAttribute('href',"<?php echo Url::to('member/qus-two')?>")//跳转至问题1的页面路径
        }else if(notAnswer[0].indexOf('three')!=-1){
            //testBtn.textContent='继续测试';
            testBtn.setAttribute('href',"<?php echo Url::to('member/qus-three')?>")//跳转至问题1的页面路径
        }
    }
    </script>



<div class="keyOption" id="Sexy">
                       <img src="<?=Yii::$app->request->baseUrl.'/static/img/P2-sexy.png'?>" class="img" alt="Responsive image">
                       <div class=".anwser"><img src="<?=Yii::$app->request->baseUrl.'/static/img/P2-fontS.png'?>" class="img" alt="Responsive image"></div>
                    </div>
                    <div class="keyOption" id="Sporty">
                       <img src="<?=Yii::$app->request->baseUrl.'/static/img/P2-sexy.png'?>" class="img" alt="Responsive image">
                       <div class=".anwser"><img src="<?=Yii::$app->request->baseUrl.'/static/img/P2-fontSp.png'?>" class="img" alt="Responsive image"></div>
                    </div>
                    <div class="keyOption" id="Smart">
                       <img src="<?=Yii::$app->request->baseUrl.'/static/img/P2-smart.png'?>" class="img" alt="Responsive image">
                       <div class=".anwser"><img src="<?=Yii::$app->request->baseUrl.'/static/img/P2-fontSm.png'?>" class="img" alt="Responsive image"></div>
                    </div>
                    <div class="keyOption" id="Cute">
                        <img src="<?=Yii::$app->request->baseUrl.'/static/img/P2-free.png'?>" class="img" alt="Responsive image">
                        <div class=".anwser"><img src="<?=Yii::$app->request->baseUrl.'/static/img/P2-fontC.png'?>" class="img" alt="Responsive image"></div>
                    </div>
