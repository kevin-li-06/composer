<?php
use yii\helpers\Url;
$this->title = '捷成五周年 签到赢大奖';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1, maximum-scale=1, user-scalable=no">
    <!-- 删除苹果默认的工具栏和菜单栏 -->
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <!-- 设置苹果工具栏颜色 -->
    <meta name="format-detection" content="telephone=no, email=no">
    <!-- 忽略页面中的数字识别为电话，忽略email识别 -->
    <!-- 启用360浏览器的极速模式(webkit) -->
    <meta name="renderer" content="webkit">
    <!-- 避免IE使用兼容模式 -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <!-- 针对手持设备优化，主要是针对一些老的不识别viewport的浏览器，比如黑莓 -->
    <meta name="HandheldFriendly" content="true">
    <!-- 微软的老式浏览器 -->
    <meta name="MobileOptimized" content="320">
    <!-- uc强制竖屏 -->
    <meta name="screen-orientation" content="portrait">
    <!-- QQ强制竖屏 -->
    <meta name="x5-orientation" content="portrait">
    <!-- UC强制全屏 -->
    <meta name="full-screen" content="yes">
    <!-- QQ强制全屏 -->
    <meta name="x5-fullscreen" content="true">
    <style>
        .animated2s {
            -webkit-animation-duration: 2s;
            animation-duration: 2s;
            -webkit-animation-fill-mode: both;
            animation-fill-mode: both
        }

        .animated {
            -webkit-animation-duration: 1s;
            animation-duration: 1s;
            -webkit-animation-fill-mode: both;
            animation-fill-mode: both
        }

        .animated600 {
            -webkit-animation-duration: 0.6s;
            animation-duration: 0.6s;
            -webkit-animation-fill-mode: both;
            animation-fill-mode: both
        }

        .animated.infinite {
            -webkit-animation-iteration-count: infinite;
            animation-iteration-count: infinite
        }

        .infinite {
            -webkit-animation-iteration-count: infinite;
            animation-iteration-count: infinite
        }

        @-webkit-keyframes fadeIn {
            0% {
                opacity: 1
            }
            50% {
                opacity: 0
            }
            100% {
                opacity: 1
            }
        }

        @keyframes fadeIn {
            0% {
                opacity: 1
            }
            50% {
                opacity: 0
            }
            100% {
                opacity: 1
            }
        }

        .fadeIn {
            -webkit-animation-name: fadeIn;
            animation-name: fadeIn
        }

        @-webkit-keyframes pulse {
            from {
                -webkit-transform: scale3d(1, 1, 1);
                transform: scale3d(1, 1, 1)
            }
            50% {
                -webkit-transform: scale3d(1.1, 1.1, 1.1);
                transform: scale3d(1.1, 1.1, 1.1)
            }
            to {
                -webkit-transform: scale3d(1, 1, 1);
                transform: scale3d(1, 1, 1)
            }
        }

        @keyframes pulse {
            from {
                -webkit-transform: scale3d(1, 1, 1);
                transform: scale3d(1, 1, 1)
            }
            50% {
                -webkit-transform: scale3d(1.1, 1.1, 1.1);
                transform: scale3d(1.1, 1.1, 1.1)
            }
            to {
                -webkit-transform: scale3d(1, 1, 1);
                transform: scale3d(1, 1, 1)
            }
        }

        .pulse {
            -webkit-animation-name: pulse;
            animation-name: pulse
        }

        html,
        body {
            width: 100%;
            height: 100%
        }

        .wrap {
            position: relative;
            width: 100%;
            height: 100%;
            left: 0;
            top: 0;
            background-color: #1d1b1c
        }

        .hide {
            display: none
        }

        .video {
            position: absolute;
            width: 100%;
            height: 100%;
            left: 0;
            top: 0;
            z-index: 99
        }

        .video video {
            width: 100%;
            height: 100%;
            object-fit: fill
        }


        @media all and (orientation: portrait) {
            .wrap>div {
                left: 100%;
                transform: rotate(90deg);
                -webkit-transform: rotate(90deg);
                transform-origin: 0 0;
                -webkit-transform-origin: 0 0
            }
            .wrap .share .share-img-portrait {
                display: inline-block
            }
            .wrap .share .share-img {
                display: none
            }
        }
        .firstLoading{
            position: absolute;
            z-index: 100;
        }
        .warp{
            position: absolute;
            top:0
        }
    </style>
    <link rel="stylesheet" href="<?= Url::to('@web/static/css/tomes.css', true) ?>">
</head>

<body>
    <div class="firstLoading" id="firstLoading" style="width: 100%; height: 100%;">
        <div class='content'>
            <a type="btn" class="firstClose hide">
                <span style="font-size:2rem;color:#fff">Skip</span>
                <i class="icon"></i>
            </a>
            <div class="videoPercent clear">
                <i class="videoPercentI">0%</i>
                <ul class="videoLoading">
                    <li>
                        <i class="animations"></i>
                        <b class="animations"></b>
                    </li>
                    <li>
                        <i class="animations"></i>
                        <b class="animations"></b>
                    </li>
                    <li>
                        <i class="animations"></i>
                        <b class="animations"></b>
                    </li>
                </ul>
                <div class="videoStart hide">
                    <i></i>
                    开启捷成生活之旅
                </div>
            </div>
        </div>
    </div>
    <div class="wrap">

        <div class="video" style="width: 100%; height: 100%;">
            <video id="video" width="100%" height="100%" webkit-playsinline="true" playsinline="true" type="video/mp4" preload="" x5-video-player-type="h5"
                src="<?= $videoLink ?>"></video>
        </div>
    </div>
    <script src="<?= Url::to('@web/static/js/jquery.js', true) ?>"></script>
    <script type="text/javascript">
        var user_id = "<?= $user_id ?>";
        var viewedUrl = "<?= Url::to(['user-viewed']) ?>";
        var homeUrl = "<?= Url::to(['member/home']) ?>";

        function setBlob() {
            function t() {
                function t() {
                    o.pause(), o.removeEventListener("playing", t)
                }

                function i() {
                    n(s), e(l), p && (clearTimeout(p), p = null)
                }

                function a(e) {
                    var n = e.buffered.length > 0 ? e.buffered.end(0) : 0;
                    return n = parseInt(1e3 * n + 1) / 1e3
                }

                function d() {
                    var e = a(o),
                        n = o.duration; + new Date - c > u || e >= n || y === e ? i() : (y = e, p = setTimeout(function () {
                        d()
                    }, 500))
                }
                o.src = r;
                var c = +new Date,
                    u = 4e3,
                    p = null;
                o.play(), o.addEventListener("playing", t);
                var y = -1;
                d()
            }

            var i = document.getElementById.bind(document),
                o = i("video"),
                r = "<?= $videoLink ?>";
            var c = new XMLHttpRequest;
            c.open("GET", r, !0), c.responseType = "blob", c.onload = function () {
                if (200 === this.status && "video/mp4" === this.response.type) {
                    var i = this.response,
                        a = (window.URL || window.webkitURL || window || {}).createObjectURL(i);
                    o.src = a;

                    //load end
                    loadEnd();
                } else t()
            }
            c.onerror = function (e) {
                t();
            }
            c.send();
        }
    </script>
    <script type="text/javascript">
        $("body").on("touchmove", function (event) {event.preventDefault();});
        document.addEventListener('touchmove', function(e){e.preventDefault()}, false);
        var timer = null;
        var video,
            u = navigator.userAgent,
            loadTimer;
        var isAndroid = u.indexOf('Android') > -1 || u.indexOf('Adr') > -1;

        $(function () {


            var progress = 0,
                $progress = $(".videoPercentI");
            loadTimer = setInterval(function () {
                progress += (Math.random() > 0.5 ? 1 : 2);
                if (progress > 99) {
                    clearInterval(loadTimer);
                } else {
                    $progress.text(progress + "%");
                }
            }, 100);

            function isWeiXin(){ 
                var ua = window.navigator.userAgent.toLowerCase(); 
                if(ua.match(/MicroMessenger/i) == 'micromessenger'){ 
                    return true; 
                }else{ 
                    return false; 
                } 
            } 
            //切换视频流
            if (isAndroid && !isWeiXin()) {
                setBlob();
            } else {
                setTimeout(function () {
                    loadEnd();
                }, 4000);
            }
            timer = setTimeout(function () {
                $('.firstClose').removeClass('hide')
            }, 7000)

            var ww = window.innerWidth,
                wh = window.innerHeight;

            //初始化
            if (wh > ww) {
                //以竖屏方式进入
                $(".wrap >div").css({
                    width: (wh + 1) + "px",
                    height: ww + "px"
                });
            }

            video = document.getElementById('video');

            listenVideo();


            //旋转屏幕
            var landTimer;
            window.addEventListener('orientationchange', function (res) {
                //以竖屏方式进入
                if (wh > ww) {
                    //切换成横屏
                    if (!isPortrait()) {

                        $(".wrap >div").css({
                            width: "100%",
                            height: "100%"
                        });
                        if (isAndroid) {
                            $(".video").css({
                                width: wh + "px",
                                height: ww + "px"
                            });
                        }
                    } else {
                        //切成竖屏
                        $(".wrap >div").css({
                            width: (wh + 1) + "px",
                            height: ww + "px"
                        });
                    }
                    //横屏进入页面
                } else {
                    //切换成竖屏
                    if (isPortrait()) {
                        var landIndex = 0;
                        clearInterval(landTimer);
                        landTimer = setInterval(function () {
                            //检测旋转猴，轮询宽高值
                            if (landIndex++ <= 10) {
                                //直到获取的宽度小于高度后重置宽高
                                if (window.innerWidth < window.innerHeight) {
                                    $(".wrap >div").css({
                                        width: (window.innerHeight + 1) + "px",
                                        height: window.innerWidth + "px"
                                    });
                                    clearInterval(landTimer);
                                }
                            } else {
                                clearInterval(landTimer);
                            }
                        }, 150);
                    } else {
                        //换回横屏
                        $(".wrap >div").css({
                            width: "100%",
                            height: "100%"
                        });
                    }
                }
            });
        });
        $(".firstClose ").click(function(){
            $(".firstLoading").unbind('click')
            setTimeout(function() {
                    $.post(viewedUrl, {
                        user_id: user_id
                    }, function(){
                        location.href = homeUrl+'?user_id='+user_id;
                    });
                }, 1000);
        })
        function listenVideo() {
            video.onended = function () {
                setTimeout(function() {
                    $.post(viewedUrl, {
                        user_id: user_id
                    }, function(){
                        location.href = homeUrl+'?user_id='+user_id;
                    });
                }, 1000);
                // $(".video").addClass('hide');
                // video.currentTime = 0;
                // if (isAndroid) {
                //     var _setIndex = 0,
                //         setFinal = setInterval(function () {
                //             if (_setIndex++ <= 10) {
                //                 //视频播放完后把被视频干扰的页面宽高修正回来
                //                 if (!isPortrait()) {
                //                     $(".final,.share,.index").css({
                //                         width: "100%",
                //                         height: "100%"
                //                     });
                //                     clearInterval(setFinal);
                //                 }
                //             } else {
                //                 clearInterval(setFinal);
                //             }
                //         }, 150);
                // }
            }
        }

        function loadEnd() {
            clearInterval(loadTimer);
            $(".videoPercent .videoPercentI").html("100%")
            $(".videoStart").fadeIn();
            setTimeout(function () {
                $(".firstLoading").on("click", function () {
                    $(this).addClass('hide');
                    video.play();
                });
            }, 600);
        }
        //旋转角度
        function isPortrait() {
            return (window.orientation == 0 || window.orientation == 180) ? true : false;
        }
    </script>
    <html>