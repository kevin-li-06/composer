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
        r = a.baseUrl + '/static/src/jebsen.mp4';;
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

var video,
    u = navigator.userAgent,
    loadTimer;
var isAndroid = u.indexOf('Android') > -1 || u.indexOf('Adr') > -1;

$(function () {


    var progress = 0,
        $progress =  $(".videoPercent .videoPercentI");
    loadTimer = setInterval(function () {
        progress += (Math.random() > 0.5 ? 1 : 2);
        if (progress > 99) {
            clearInterval(loadTimer);
        } else {
            $progress.text(progress + "%");
        }
    }, 100);

    //切换视频流
    if (isAndroid) {

        setBlob();
    } else {
        setTimeout(function () {
            loadEnd();
        }, 4000);
    }



    var ww = window.innerWidth,
        wh = window.innerHeight;

    //初始化
    if (wh > ww) {
        //以竖屏方式进入
        $(".videoBox").css({
            width: (wh + 1) + "px",
            height: ww + "px"
        });
    }



    video = document.getElementById('video');

    listenVideo();

    $(".index").on("click", function () {
        _smq.push(['custom', 'honor1111_WAP', 'ClicktoseeKevin']);
        $(this).addClass('hide');
        video.play();
        //					WeixinJSBridge.call('hideOptionMenu');
        //					wx.hideOptionMenu();
    });


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

function listenVideo() {
    video.onended = function () {
        $(".video").addClass('hide');
        video.currentTime = 0;
        $(".final").removeClass('hide');

        if (isAndroid) {
            var _setIndex = 0,
                setFinal = setInterval(function () {
                    if (_setIndex++ <= 10) {
                        //视频播放完后把被视频干扰的页面宽高修正回来
                        if (!isPortrait()) {
                            $(".final,.share,.index").css({
                                width: "100%",
                                height: "100%"
                            });
                            clearInterval(setFinal);
                        }
                    } else {
                        clearInterval(setFinal);
                    }
                }, 150);
        }

        //					 WeixinJSBridge.call('showOptionMenu');
        //					 wx.showOptionMenu();
    }
}

//旋转角度
function isPortrait() {
    return (window.orientation == 0 || window.orientation == 180) ? true : false;
}

//加载解析完毕
function loadEnd() {
    clearInterval(loadTimer);
    $(".index-img-wrap").animate({
        width: "39.3333333333rem"
    }, 100);
    $(".index-progress").text("100%");
    $(".index-head").fadeIn();
    setTimeout(function () {
        $(".index-click").removeClass('hide');
        $(".index-progress").hide();
    }, 600);
}

