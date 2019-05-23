<?php

use \yii\helpers\Url;

$this->title = 'Liber签到赢大奖';

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
        <?php echo "<script>var data=".json_encode($data)."</script>" ?>
        <div id='myWarp'>
            <!-- 1 头部签到信息 -->
            <div id="header" class="clear">
                <div class='headL lf'>
                    <div class="header_img"></div>
                    <div class="header_img2"></div>
                </div>

                <!--  签到进度开始  -->
                <div class='headR lf'>
                    <div class="header_title">
                        <h1>我的签到：</h1>
                        <span class="gift"></span>
                    </div>
                    <div class="sign-progress">

                        <p class="spanlis clear">
                            <span class="circle live-circle ">1</span>
                            <span class="circle live-circle">2</span>
                            <span class="circle live-circle">3</span>
                            <span class="circle live-circle">4</span>
                            <span class="circle live-circle">5</span>
                        </p>
                    </div>
                    <div class='signBox clear modalS'>
                        <div class="btnp lf">
                            <?php if (!$data['disableCheckin']) { ?>
                                <span class="signbtn toolbtn" id="signbutton">点击签到</span>
                            <?php } else { ?>
                                <span class="signbtn" id="signbutton">签到结束</span>
                            <?php } ?>
                        </div>
                        <?php if (!$data['disableCheckin']) { ?>
                        <div class="sign-desc lf">
                            每天签到可刮小奖，连续签到5天可刮大奖，大奖等你来！<br>
                            <span class="wechatMsg"> 将微信活动分享到朋友圈， <br>可获得一次刮奖机会 <br>（每日限一次）</span>
                        </div>
                        <?php } ?>
                    </div>

                    <div class="bg-modal sign">
                        <div class='content'>
                            <div class="modalHead">
                                
                                <a type="btn" class="close">
                                    <span></span>
                                </a>
                                <div class="modalHeadImg"></div>
                            </div>
                            <div class="modalFoot">
                                <p>每天签到即可抽奖!</p>
                                <p>连续签满5天,即可抽一次大奖!</p>
                                <ul class="signInlist clear">
                                    <li class="active"></li>
                                    <li class="active">2</li>
                                    <li class="active">3</li>
                                    <li class="active">4</li>
                                    <li class="active">5</li>
                                </ul>
                                <div class="signInDays">
                                    已连续签到
                                    <span id='signTimes'>1</span>
                                    天
                                </div>
                                <div>
                                    <button class="bg-modal-ok" id="signInBtn">今日签到</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--  弹框结束 -->
                </div>

            </div>
            <!-- 2 刮奖区 -->

            <div id="lottery" class="lottery clear">
                
                <div class="lotteryBox clear">
                    <div id='con' class="lf">
                        <canvas id="c1"></canvas>
                    </div>

                </div>
                <div class="lotteryMsg clear ">
                    <div class='clear'>
                        <div class="lf remainingTimes ">
                            <!-- <span class="lf">
                                <i class="icon "></i>
                            </span> -->
                            <span class="lf">
                                剩余次数:
                                <br> 大奖:
                                <span id='big'>0</span>
                                小奖:
                                <span id='small'>0</span>
                            </span>

                        </div>
                        <div class="lf oneMore ">
                            再刮一次
                        </div>
                        <div class="lf yourGift ">
                            <i class="icon"></i> 您的奖品
                        </div>
                    </div>
                 
                    
                </div>
                <div class="giftPrompt">
                        提示：最终领取奖品以最高奖项为准
                </div>
            </div>
            <!-- 3 中奖奖品展示区 -->

            <?php if ($this->context->source == 'wechat') { ?>
                <iframe id='userMsg' name='userMsg' src="<?= Yii::$app->params['webform']; ?>"
                    frameborder="0" width=0 height=0>
                </iframe>
            <?php } ?>
            
            <div id="prize" class="prize icon">
                <div id="lv1" style="text-align:center">
                    没有获奖记录
                </div>
            </div>
            <div class="bg-modal policy-clause" id="clause-modal">
                <div class='content'>
                    <div class="modalHead">
                        <a type="btn" class="close">
                            <span></span>
                        </a>
                        <h3>活动细则</h3>
                    </div>
                    <div class="modalFoot">
                        <ul class="pol-list">
                            <li>
                                1）签到刮奖：每日签到即可获得一次刮小奖机会，100%中奖；
                            </li>
                            <li>
                                2）分享有奖：将活动页面分享至微信朋友圈，可加赠一次刮奖机会（仅限微信公众号进入分享）；
                            </li>
                            <li>
                                3）签到五天赢大奖：连续签到五天，即可获得一次刮大奖机会，100%中奖；
                            </li>
                            <li>
                                4）消费加赠：活动期间在门店消费，单张小票满5000元即可加赠一次刮大奖机会，每人每日最高加赠两次刮大奖机会（奖品核销后不可加赠，请在核销前告知门店工作人员增加刮奖机会）；
                            </li>
                            <li>
                                5）奖品规则：最终奖品将以获得的最高奖项为准，每人限领取一份；
                            </li>
                            <li>
                                6）如需更改领奖门店，请致电捷成中国服务热线：021-23164724（工作日9:00-18:00）；
                            </li>
                            <li>
                                7）活动有效期：2017年12月1日—2017年12月14日。
                            </li>

                        </ul>
                    </div>
                    <div class="modalConfirm">
                        <p class="bg-modal-ok ">确&nbsp;&nbsp;&nbsp;定 </p>
                    </div>
                </div>
            </div>
            <!-- 4 奖品设置 -->
            <div id="prizeList">
                <div class="prizeListTitle">
                    <span>六重好礼送不停 </span>
                    <span class="prince clause">活动细则</span>
                </div>
                <ul class="prizeListul clear">
                    <li class="prizeListLI">
                        <p>特等奖
                        </p>
                        <div class="imgDiv">
                            <img src="<?=Yii::$app->request->baseUrl.'/static/img/lv1_s.png'?>" alt="">
                        </div>
                        <div>戴森电吹风</div>
                        <div class="prizeRemain">
                            5套 (剩余<span class="remain-lv1">0</span>套)
                        </div>
                    </li>
                    <li class="prizeListLI">
                        <p>一等奖
                        </p>
                        <div class="imgDiv">
                            <img src="<?=Yii::$app->request->baseUrl.'/static/img/lv2_s.png'?>" alt="">
                        </div>
                        <div>双立人刀具</div>
                        <div class="prizeRemain">
                            20套 (剩余<span class="remain-lv2">0</span>套)
                        </div>
                    </li>
                    <li class="prizeListLI">
                        <p>二等奖
                        </p>
                        <div class="imgDiv">
                            <img src="<?=Yii::$app->request->baseUrl.'/static/img/lv3_s1.png'?>" alt="">
                        </div>
                        <div>康宁保温杯</div>
                        <div class="prizeRemain">
                            80只 (剩余<span class="remain-lv3">0</span>只)
                        </div>
                    </li>
                    <li class="prizeListLI">
                        <p>三等奖
                        </p>
                        <div class="imgDiv">
                            <img src="<?=Yii::$app->request->baseUrl.'/static/img/lv4_s.png'?>" alt="">
                        </div>
                        <div>星巴克中杯咖啡</div>
                        <div class="prizeRemain">
                            200份 (剩余<span class="remain-lv4">0</span>份)
                        </div>
                    </li>
                    <li class="prizeListLI">
                        <p>四等奖
                        </p>
                        <div class="imgDiv">
                            <img src="<?=Yii::$app->request->baseUrl.'/static/img/lv5_s.png'?>" alt="">
                        </div>
                        <div>定制专用抹布</div>
                        <div class="prizeRemain">
                            2000份 (剩余<span class="remain-lv5">0</span>份)
                        </div>
                    </li>
                    <li class="prizeListLI">
                        <p>五等奖
                        </p>
                        <div class="imgDiv">
                            <img src="<?=Yii::$app->request->baseUrl.'/static/img/lv6.png'?>" alt="">
                        </div>
                        <div>限量版冰箱贴</div>
                        <div class="prizeRemain"></div>
                    </li>

                </ul>
            </div>
            <!-- 4 奖品设置 for liber -->
            <div id="prizeList2">
               
                    <div class="list2_box">
                        <img class="tag tag1"/>
                        <div class="imgsBox">
                       <img class="logosImg logosImg1"/> 
                       <img class="productImg productImg1"/> 
                       </div>
                       
                    </div>
                    
                    <div class="list2_box">
                       <img class="tag tag2"/>
                       <img class="productImg productImg2"/> 
                       <img class="logosImg logosImg2"/>
                       <div class="clear"></div> 
                    </div>
                    
                    <div class="list2_box">
                        <img class="tag tag3"/>
                       <img class="logosImg logosImg3"/> 
                       <img class="productImg productImg3"/>
                       <div class="clear"></div> 
                    </div>
                   
                
            </div>
        </div>
        <!-- <div class="page2" id="page2">
            <div class="toLottery">
            </div>
            <div class="replay">
        </div> -->
        <div class="bg-modal redeem" id="redeem">
            <div class='content'>
                <div class="modalHead">
                    <a type="btn" class="close">
                        <span></span>
                    </a>
                    <h3>领取奖品</h3>
                </div>
                <div class="modalFoot">
                    <label style="padding:1rem 0;font-size:1.5rem">请联系工作人员输入领奖密码</label>
                    <input type="text" id="redeemCode">
                </div>
                <div class="modalConfirm">
                    <button class="redeemConfirmBtn">确&nbsp;&nbsp;&nbsp;定 </button>
                    <p id='redeemMsg'></p>
                </div>
            </div>
        </div>
        <div class="bg-modal shareMsg" id="shareMsg">
            <div class='content'>
                <div class="modalHead">
                    <a type="btn" class="close">
                        <span></span>
                    </a>
                    <h3>分享</h3>
                </div>
                <div class="modalFoot shareStatus">
                   
                </div>
                <div class="modalConfirm">
                    <button class="shareMsgBtn bg-modal-ok">确&nbsp;&nbsp;&nbsp;定 </button>
                </div>
            </div>
        </div>
<?php
    $this->registerJsFile(Url::to('@web/static/js/md5.min.js?t=' . time(), true), ['depends' => [\frontend\assets\AppAsset::classname()], 'position' => \yii\web\View::POS_END]);
    $this->registerJsFile(Url::to('@web/static/js/card_es5.js?t=' . time(), true), ['depends' => [\frontend\assets\AppAsset::classname()], 'position' => \yii\web\View::POS_END]);
    $this->registerJsFile(Url::to('@web/static/js/index.js?t=' . time(), true), ['depends' => [\frontend\assets\AppAsset::classname()], 'position' => \yii\web\View::POS_END]);
?>

<script>
var shareUrl = "<?= Url::to(['member/share-once']) ?>";
var oauthUrl = "<?= Url::to(['member/test']) ?>";
var user_id = <?= $data['user_id'] ?>;
</script>

<?php
    //微信jssdk
    if ($this->context->source == 'wechat') {
        $signPackage = \common\components\Jssdk::getSignPackage();

        $js = "var signPackage = " . json_encode($signPackage);

        $this->registerJs($js, \yii\web\View::POS_HEAD);

        $this->registerJsFile('http://res.wx.qq.com/open/js/jweixin-1.2.0.js', ['position' => \yii\web\View::POS_END]);
        $this->registerJsFile(Url::to('@web/static/js/_jssdk.js?t=' . time(), true), ['depends' => [\frontend\assets\AppAsset::classname()], 'position' => \yii\web\View::POS_END]);

        // $this->render('_jssdk', [
        //     'signPackage' => $signPackage,
        //     'user_id' => $data['user_id'],
        // ]);
    }
?>
</body>

</html>