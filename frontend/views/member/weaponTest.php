<?php

use \yii\helpers\Url;

$this->title = '探索属于您的女神利器';

?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1,user-scalable=no">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title></title>
        <style>
            .wrap{
                padding-bottom:0;
            }
        </style>
        <?php
         $this->registerCssFile(Url::to('@web/static/css/index.css?t=' . time(), true), ['depends' => [\frontend\assets\AppAsset::classname()], 'position' => \yii\web\View::POS_END]);         
         $this->registerCssFile(Url::to('@web/static/css/testModule.css?t=' . time(), true), ['depends' => [\frontend\assets\AppAsset::classname()], 'position' => \yii\web\View::POS_END]);
        ?>
      
    </head>
    
    <body  >
        <div class="slide-container">
           
            <div class="slide" id="slidesHome">
                <img src="<?=Yii::$app->request->baseUrl.'/static/img/P1.png'?>" class="img-responsive img" />
                <div class="js-next nextBtn"><span class="glyphicon glyphicon-chevron-down"> </span></div>
            </div>
            <div class="slide" id="chooseKey">
               
                <div id="optionsDiv">
                    <div class="keyOption" id="free" key="free">
                        
                       <img src="<?=Yii::$app->request->baseUrl.'/static/img/P2_free.png'?>" />
                      
                    </div>
                   <div class="keyOption" id="sexy"  key="sexy">
                      
                       <img src="<?=Yii::$app->request->baseUrl.'/static/img/P2_sexy.png'?>"  />
                       
                    </div>
                    <div class="keyOption" id="sporty" key="sporty">
                      
                       <img src="<?=Yii::$app->request->baseUrl.'/static/img/P2_sporty.png'?>"  />
                       
                    </div>
                    <div class="keyOption" id="smart" key="smart">
                       
                       <img src="<?=Yii::$app->request->baseUrl.'/static/img/P2_smart.png'?>"  />
                      
                    </div>
                    <div class="keyOption" id="cute" key="cute">

                        <img src="<?=Yii::$app->request->baseUrl.'/static/img/P2_cute.png'?>"  />
                    </div>
                    
                </div>
                <img src="<?=Yii::$app->request->baseUrl.'/static/img/P2.png'?>" class="img-responsive img" />
                <!-- <a   href="<?php //echo Url::to('member/qus-one')?>"></a> -->
                <!-- <div class="nextBtn" id="testBtn"><span class="glyphicon glyphicon-chevron-down"> </span></div> -->
            </div>
            <div class="slide">
                <img src="" data-src="<?=Yii::$app->request->baseUrl.'/static/img/P3.png'?>" class="img-responsive img" />
                <div class=" qustioBox"  id="qustionGroup1">
                    <p>假设会议上一位同级的同事毫不畏</p><p>惧地反驳和职责你的项目提案，你</p><p>会如何应对？</p>
                    <div class="answerDiv" id="answerDiv1">
                        <div class="answer" key="A">
                            
                            <p> <i>A</i>&nbsp;改变初衷，低头接受</p>
                        </div>
                        <div class="answer" key="B">
                           
                            <p> <i>B</i>&nbsp;坚持自我，顺势反驳</p>
                       </div>
                        <div class="answer" key="C">
                           
                            <p> <i>C</i>&nbsp;一言不发，事后找领导解释</p>
                        </div>
                    </div>
                   
                </div>
                
                <!-- <div class="js-next nextBtn" ><span class="glyphicon glyphicon-chevron-down"> </span></div> -->
            </div>
            <div class="slide">
                <img src="" data-src="<?=Yii::$app->request->baseUrl.'/static/img/P4.png'?>" class="img-responsive img" >
                <div class=" qustioBox"  id="qustionGroup2">
                    <p>隔壁部门男同事第一次邀约你一起晚</p><p>餐，你会选择以下哪种穿衣风格。</p>
                    <div class="answerDiv" id="answerDiv2">
                        <div class="answer" key="A">
                            <img class="img-responsive" src="" data-src="<?=Yii::$app->request->baseUrl.'/static/img/P4-1.png'?>"  />
                            <p> <i>A</i>&nbsp;知性淑女</p>
                        </div>
                        <div class="answer" key="B">
                            <img class="img-responsive" src="" data-src="<?=Yii::$app->request->baseUrl.'/static/img/P4-2.png'?>"  />
                            <p> <i>B</i>&nbsp;甜美可爱</p>
                       </div>
                        <div class="answer" key="C">
                            <img class="img-responsive" src="" data-src="<?=Yii::$app->request->baseUrl.'/static/img/P4-3.png'?>"  />
                            <p> <i>C</i>&nbsp;性感迷人</p>
                        </div>
                        <div class="answer" key="D">
                            <img class="img-responsive" src="" data-src="<?=Yii::$app->request->baseUrl.'/static/img/P4-4.png'?>"  />
                            <p> <i>C</i>&nbsp;运动活力</p>
                        </div>
                        
                        <div class="clear"></div>
                    </div>
                </div>
                <!-- <div class="js-next nextBtn"><span class="glyphicon glyphicon-chevron-down"> </span></div> -->
            </div>
            <div class="slide">
                <img src="" data-src="<?=Yii::$app->request->baseUrl.'/static/img/P5.png'?>" class="img-responsive img" >
                <div class="qustioBox"  id="qustionGroup3">
                    <p>不论刮风下雨,以下哪种鞋</p>
                    <p>款依旧是你的选择?</p>
                    <div class="answerDiv" id="answerDiv3">
                        <div class="answer" key="A">
                            <img class="img-responsive" src="" data-src="<?=Yii::$app->request->baseUrl.'/static/img/P5-1.png'?>"  />
                            <p> <i>A</i>&nbsp;奢华高跟鞋</p>
                        </div>
                        <div class="answer" key="B">
                            <img class="img-responsive" src="" data-src="<?=Yii::$app->request->baseUrl.'/static/img/P5-2.png'?>"  />
                            <p> <i>B</i>&nbsp;舒适平底鞋</p>
                       </div>
                        <div class="answer" key="C">
                            <img class="img-responsive" src="" data-src="<?=Yii::$app->request->baseUrl.'/static/img/P5-3.png'?>"  />
                            <p> <i>C</i>&nbsp;清爽凉鞋</p>
                        </div>
                        
                    </div>
                </div>
                <!-- <div class="js-next nextBtn"><span class="glyphicon glyphicon-chevron-down"> </span></div> -->
            </div>
            <div class="slide">
                <img src="" data-src="<?=Yii::$app->request->baseUrl.'/static/img/P6.png'?>" class="img-responsive img" >
                <div class="qustioBox"  id="qustionGroup4">
                    <p>失恋时，你会选择哪一种发泄的方式?</p>
                    <div class="answerDiv" id="answerDiv4">
                    <div class="answer" key="A">
                            <img class="img-responsive" src="" data-src="<?=Yii::$app->request->baseUrl.'/static/img/P6-1.png'?>"  />
                            <p> <i>A</i>&nbsp;与闺蜜谈心</p>
                        </div>
                        <div class="answer" key="B">
                            <img class="img-responsive" src="" data-src="<?=Yii::$app->request->baseUrl.'/static/img/P6-2.png'?>"  />
                            <p> <i>B</i>&nbsp;旅行看看世界</p>
                       </div>
                        <div class="answer" key="C">
                            <img class="img-responsive" src="" data-src="<?=Yii::$app->request->baseUrl.'/static/img/P6-3.png'?>"  />
                            <p> <i>C</i>&nbsp;性感迷人</p>
                        </div>
                    </div>  
                </div>
                <div class="nextBtn" id="qustionSubmit">查看结果</div>
            </div>
            <div class="slide">
                <img src="" data-src="<?=Yii::$app->request->baseUrl.'/static/img/P7_back.png'?>" class="img-responsive img" >
                <img id="showYourStyle" data-src="<?=Yii::$app->request->baseUrl.'/static/img/P7_1.png'?>" class="img-responsive img" >
                <img id="bottomImg"  class="img-responsive img" >
                <div class="js-next nextBtn"><span class="glyphicon glyphicon-chevron-down"> </span></div>
            </div>
            <div class="slide">
                <img src="" data-src="<?=Yii::$app->request->baseUrl.'/static/img/P7_back.png'?>" class="img-responsive img" >
                <img src="" data-src="<?=Yii::$app->request->baseUrl.'/static/img/P8.png'?>" class="img-responsive img" >
                <div class="js-next nextBtn"><span class="glyphicon glyphicon-chevron-down"> </span></div>
            </div>
            <div class="slide">
                <img src="" data-src="<?=Yii::$app->request->baseUrl.'/static/img/P9_back.png'?>" class="img-responsive img" >
                <a  href="">
                   <img src="" id="tranSubmit" data-src="<?=Yii::$app->request->baseUrl.'/static/img/P9_btn.png'?>" class="img-responsive img" >
                </a>
            </div>
            <div class="tipProp">
                <p> 您还没有!</p>
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
        var j=ndivl;
        if(notAnswer[0].indexOf('one')!=-1|| notAnswer.length==0){
            j=2;
        }else if(notAnswer[0].indexOf('two')!=-1){
            j=3;
        }else if(notAnswer[0].indexOf('three')!=-1){
            j=4;
        }else if(notAnswer[0].indexOf('four')!=-1){
            j=5;
        }
        testBtn.oncdivck=function(){
            switchPage(1,2);
        }
    }
</script>

