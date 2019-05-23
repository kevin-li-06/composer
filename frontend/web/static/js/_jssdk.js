wx.config({
debug: true, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
appId: signPackage.appId, // 必填，公众号的唯一标识
timestamp: signPackage.timestamp, // 必填，生成签名的时间戳
nonceStr: signPackage.nonceStr, // 必填，生成签名的随机串
signature: signPackage.signature,// 必填，签名，见附录1
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
    wx.getLocation({
        type: 'wgs84', // 默认为wgs84的gps坐标，如果要返回直接给openLocation用的火星坐标，可传入'gcj02'
        success: function (res) {
            var latitude = res.latitude; // 纬度，浮点数，范围为90 ~ -90
            var longitude = res.longitude; // 经度，浮点数，范围为180 ~ -180。
            var speed = res.speed; // 速度，以米/每秒计
            var accuracy = res.accuracy; // 位置精度
            alert(latitude);
            console.log(longitude);
        }
    });
    wx.onMenuShareAppMessage({
        title: '捷成五周年庆，签到赢大奖', // 分享标题
        desc: '周年欢庆，签到赢取戴森电吹风大奖~', // 分享描述
        link: 'http://liber.onthemooner.com/index.php/member/wechat-share', // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
        imgUrl: 'https://mmbiz.qpic.cn/mmbiz_png/UzsLSJicrzHfPLJI5Mu4sdtaAuib8CKHQ1lyWjgCWGSW7oykXDicc4r6jlGBq1GMpM4Kx2b5hEkBbicGz7EFicR8xlg/0', // 分享图标
        type: 'link', // 分享类型,music、video或link，不填默认为link
        dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
        success: function () {
            // $.post(shareUrl, {user_id: user_id, scene: 'friend'}, function(re) {
            //     console.log(re);
            //     if (re.status == 'success') {
            //         shareProp('分享成功');
            //     } else {
            //         shareProp('你今天已分享过了', 1);
            //     }
            // }, 'json');
        },
        cancel: function () { 
            // 用户取消分享后执行的回调函数
        }
    });

    wx.onMenuShareTimeline({
        title: '捷成五周年庆，签到赢大奖', // 分享标题
        link: 'http://liber.onthemooner.com/index.php/member/wechat-share', // 分享链接，该链接域名必须与当前企业的可信域名一致
        imgUrl: 'https://mmbiz.qpic.cn/mmbiz_png/UzsLSJicrzHfPLJI5Mu4sdtaAuib8CKHQ1lyWjgCWGSW7oykXDicc4r6jlGBq1GMpM4Kx2b5hEkBbicGz7EFicR8xlg/0', // 分享图标
        success: function () {
            // $.post(shareUrl, {user_id: user_id, scene: 'timeline'}, function(re) {
            //     console.log(re);
            //     if (re.status == 'success') {
            //         share();
            //         shareProp('分享成功');
            //     } else {
            //         shareProp('你今天已分享过了', 1);
            //     }
            // }, 'json');
            // 用户确认分享后执行的回调函数
        },
        cancel: function () {
            // shareProp('取消分享', 1);
            // 用户取消分享后执行的回调函数
        }
    });

    // wx.hideMenuItems({
    //     menuList: ['menuItem:share:qq',
    //                 'menuItem:share:appMessage',
    //                 'menuItem:share:weiboApp',
    //                 'menuItem:favorite',
    //                 'menuItem:share:facebook',
    //                 'menuItem:share:QZone',
    //                 'menuItem:editTag',
    //                 'menuItem:copyUrl',
    //                 'menuItem:originPage',
    //                 'menuItem:readMode',
    //                 'menuItem:openWithQQBrowser',
    //                 'menuItem:openWithSafari',
    //                 'menuItem:share:email',
    //     ] // 要隐藏的菜单项，只能隐藏“传播类”和“保护类”按钮，所有menu项见附录3

    // });
    // config信息验证后会执行ready方法，所有接口调用都必须在config接口获得结果之后，config是一个客户端的异步操作，所以如果需要在页面加载时就调用相关接口，则须把相关接口放在ready函数中调用来确保正确执行。对于用户触发时才调用的接口，则可以直接调用，不需要放在ready函数中。
});

wx.error(function(res){
    console.log(res);
    // config信息验证失败会执行error函数，如签名过期导致验证失败，具体错误信息可以打开config的debug模式查看，也可以在返回的res参数中查看，对于SPA可以在这里更新签名。
});

wx.checkJsApi({
    jsApiList: ['onMenuShareAppMessage'], // 需要检测的JS接口列表，所有JS接口列表见附录2,
    success: function(res) {
        console.log(res);
        // 以键值对的形式返回，可用的api值true，不可用为false
        // 如：{"checkResult":{"chooseImage":true},"errMsg":"checkJsApi:ok"}
    }
});
