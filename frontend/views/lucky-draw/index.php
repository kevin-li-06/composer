
<?php 
use yii\helpers\Html;
use yii\helpers\Url;
?>
<div class="text-center">
<h2>前台抽奖页面</h2>
</div>

<div>
   用户名: <b><?=  $user['username']?></b><br/>
   剩余抽奖: 大奖<?=  $user['big_chance']?>次
            小奖<?=  $user['small_chance']?>次
</div>
<button type="button" class="btn btn-info" onclick="lucky()">抽奖</button>
<script>

</script>



<?php
$shareUrl = Url::to(['lucky-draw/ajax-share']);

$js = <<<JS
//抽奖代码
function lucky(){
    
}

//微信js代码
 wx.config({
    debug: true, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
    appId: "{$signPackage['appId']}", // 必填，公众号的唯一标识
    timestamp: "{$signPackage['timestamp']}", // 必填，生成签名的时间戳
    nonceStr: "{$signPackage['nonceStr']}", // 必填，生成签名的随机串
    signature: "{$signPackage['signature']}",// 必填，签名，见附录1
    jsApiList: [
                'onMenuShareTimeline',
                'onMenuShareAppMessage',
                'onMenuShareQQ',
                'onMenuShareWeibo',
                'onMenuShareQZone',
                'startRecord',
                'stopRecord',
                'onVoiceRecordEnd',
                'playVoice',
                'pauseVoice',
                'stopVoice',
                'onVoicePlayEnd',
                'uploadVoice',
                'downloadVoice',
                'chooseImage',
                'previewImage',
                'uploadImage',
                'downloadImage',
                'translateVoice',
                'getNetworkType',
                'openLocation',
                'getLocation',
                'hideOptionMenu',
                'showOptionMenu',
                'hideMenuItems',
                'showMenuItems',
                'hideAllNonBaseMenuItem',
                'showAllNonBaseMenuItem',
                'closeWindow',
                'scanQRCode',
                'chooseWXPay',
                'openProductSpecificView',
                'addCard',
                'chooseCard',
                'openCard',
                ] // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
});

wx.ready(function(){
// 在这里调用 API
    wx.onMenuShareAppMessage({
        title: '测试', // 分享标题
        desc: '测测测测测测测额测测', // 分享描述
        link: 'http://jebsen.onthemooner.com/', // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
        imgUrl: '', // 分享图标
        type: '', // 分享类型,music、video或link，不填默认为link
        dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
        success: function () { 
            $.post("{$shareUrl}", {source: 'share'}, function(result){
                var re = JSON.parse(result);
                alert(re['message']);
            });
        },
        cancel: function () { 
            // 用户取消分享后执行的回调函数
        }
    });

    wx.onMenuShareTimeline({
        title: '测试朋友圈', // 分享标题
        link: 'http://jebsen.onthemooner.com/', // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
        imgUrl: '', // 分享图标
        success: function () { 
            // 用户确认分享后执行的回调函数
            $.post("{$shareUrl}", {source: 'share'}, function(result){
                var re = JSON.parse(result);
                alert(re['message']);
            });
        },
        cancel: function () { 
            // 用户取消分享后执行的回调函数
        }
    });

});


JS;

$this->registerJs($js, \yii\web\View::POS_END);
$this->registerJsFile('http://res.wx.qq.com/open/js/jweixin-1.2.0.js', ['position' => \yii\web\View::POS_END]);
?>
