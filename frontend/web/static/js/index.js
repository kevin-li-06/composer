//主要js文件
var share, shareProp;
var baseUrl;
$(function() {
        //获取初始值
        // console.log(data)
        var a = {
            id: data.user_id,
            big: data.big_chance || 0, //大奖次数
            bigUsed: data.big_use_sum, //大奖已使用次数
            bigMax: data.bigMax,
            small: data.small_chance || 0, //小奖次数
            smallUsed: data.small_use_sum, //小奖已使用次数
            smallMax: data.smallMax,
            signTimes: data.continuous, //连续签到次数
            maxSignTimes: 5, //最大连续签到次数
            todaySign: !data.whether_checkin, //今天是否签到 true 可以签到 false 不能签到
            mobile: data.mobile || '',
            showMobile: '',
            pried: {}, //已中奖列表 1中奖 0未中奖
            redeem: data.isRedeem || false, //核销
            baseUrl: data.baseUrl,
            openID: data.openid || '',
            firstLoad: data.firstLoad || true,
            userName: data.username || '',
            store_id: data.store.id || '',
            store_name: data.store.zh_storename || 'Liber Fashion 广州高德置地冬广场门店',
            store_phone: data.store.phone || '',
            //store_address: data.store.address　 || '',
            store_address: data.store.address || '广州市珠江新城珠江东路高德置地冬广场1, 2F',
            viewed: +data.viewed || false,
            // viewed:0,
            isAppointment: data.is_appointment, //是否填写领奖地址 
            appointmentDate: data.is_appointment.created_at || '',
            header_img: data.headimg || null,
            fristLoadingTime: 7000, //视频最大等待时间
            // 是否禁止
            disableCheckin: !!data.disableCheckin,
            disableAppointment: !!data.disableAppointment,
            disableRedeem: !!data.disableRedeem,
            disableLottery: !!data.disableLottery,
        }
        baseUrl = a.baseUrl
        console.log(a)
            // 刮刮卡初始化
        var oneMoreFlag = false //oneMore按钮的 使用与禁用
        var lot = {};
        var ww = window.innerWidth,
            wh = window.innerHeight;
        var video,
            u = navigator.userAgent,
            loadTimer;
        var isAndroid = u.indexOf('Android') > -1 || u.indexOf('Adr') > -1;
        //初始化奖品列表
        var prizeList = {
                'lv-1': ['机会用光了'],
                lv0: ['没有中奖'],
                lv1: ['特等奖', '戴森吹风机', 'lv1.png'],
                lv2: ['一等奖', '双立人刀具', 'P10-prizeImg1.png'],
                lv3: ['二等奖', '康宁保温杯', 'P10-prizeImg2.png'],
                lv4: ['三等奖', '星巴克中杯咖啡', 'P10-prizeImg3.png'],
                lv5: ['四等奖', '定制专用抹布', 'lv5.png'],
                lv6: ['五等奖', '限量版冰箱贴', 'lv6.png'],
            }
            //配置页面初始值
        function pageInit() {
            // userViewed() //首次登陆
            window.scrollTo(0, 0)
            changeHeadImg() //更换头像
            remainingTimes() //更新次数
            signInit() //更新签到
            getPried() //更新中奖列表
                // $(".replay").click(function(){
                //     location = videoUrl+'?user_id='+a.id;
                // })
                // $("#page2").on("click","div",function(){
                //     if($(this).hasClass("toLottery")){
                //         $("#page2").fadeOut();
                //     }
                // })
            if (a.mobile) {
                showMobile() //电话号码
            }
            if ($.isEmptyObject(a.isAppointment)) { //未填写填写信息的用户
                //getStoreId() //获取门店
            }
            if (!a.redeem) { //未核销的用户
                // 未禁止签到
                if (!a.disableCheckin) {
                    singIn() //签到
                }
            }
            if (a.redeem) { //已核销用户
                $('#signbutton').removeClass("toolbtn") //禁止签到
            }
            if (a.openID) { //微信显示信息

                $(".wechatMsg").removeClass("wechatMsg")

            } else {
                $(".sign-desc.rf").css({
                    marginTop: '.9rem'
                })
                $("#bottomImg").css({
                    bottom: '2rem'
                });
            }

            if (!a.todaySign) {
                $('#signInBtn').css({
                    color: '#fff',
                    backgroundColor: "#41719C"
                });
            }
            //分享
            share = function() {
                a.small++;
                remainingTimes();
                oneMoreFlag = lot.oneMoreFlag !== undefined ? lot.oneMoreFlag : true;
                if (oneMoreFlag) {
                    useChance()
                }
            }

            oneMoreTimes() //再来一次

            useChance() //进入页面如果可以抽奖

            showGift() //展示奖品列表

            userMsgFormShow() //表单显示

            userMsgSubmit() // 提交用户信息

            propBox() //弹窗模块

            clauseProp() //条款细则

            redeemProp() //核销

            prizeRemain() //更新剩余奖品数量

            // addressDropDown() //监控鼠标状态
        }
        pageInit()
            //获取用户中奖列表
        function getPried() {
            $.post(recordUrl, {
                user_id: a.id
            }, function(data) {
                //console.log(data)
                if (data.status == 'success') {
                    var pried = data.data.data
                    var arr = [];
                    for (var k in pried) {
                        if (pried[k]) {
                            arr.push(pried[k].level)
                        }
                    }
                    arr.sort();
                    priedInit(arr) //更新中奖列表
                }
            })
        }
        //分享弹窗
        shareProp = function(msg, e) {
                $(".shareStatus").removeClass("text-danger");
                $(".shareStatus").removeClass("text-success");
                $(".shareStatus").html(msg)
                if (e) {
                    $(".shareStatus").addClass("text-danger")
                } else {
                    $(".shareStatus").addClass("text-success")
                }

                $("body").css({
                    "overflow": "hidden"
                });
                $("#shareMsg").show();
                return true;
            }
            // 首次登陆
            // function userViewed() {
            //     if (!a.viewed) {
            //         // $('#firstLoading').show()
            //         //首次加载

        //         var video = document.getElementById("myVideo");
        //         var timer = null,
        //             timer1 = null;
        //         var flag = 1;
        //         video.src = a.baseUrl + '/static/src/jebsen.mp4';
        //         video.load()
        //         var range = 0;
        //         timer = setInterval(function () {
        //             $(".videoPercent .videoPercentI").html(range + "%")
        //             range++
        //             if (range == 100) {
        //                 clearInterval(timer);
        //                 videoPlay();

        //             }
        //         }, 30)
        //         timer1 = setTimeout(function () {
        //             $('.firstClose').removeClass('firstClose')
        //         }, a.fristLoadingTime)
        //         videoResize()

        //         //初始化
        //         if (wh > ww) {
        //             //以竖屏方式进入
        //             $("body>div,#myWarp").css({
        //                 width: "100%",
        //                 height: "100%"
        //             })
        //             $(".videoBox").css({
        //                 width: (wh + 1) + "px",
        //                 height: ww + "px"
        //             });
        //         }
        //         alert(wh+","+ww)
        //         function videoPlay() {
        //             clearInterval(timer);
        //             clearTimeout(timer1); //停止跳过
        //             timer = null;
        //             timer1 = null;
        //             $(".videoPercent .videoPercentI").html("100%")
        //             $(".videoStart").removeClass('hide')
        //             $("#firstLoading .content").click(function () {
        //                 $('#myVideo').css({
        //                     'display': 'block',
        //                 });
        //                 if (flag) {
        //                     $(".videoPercent").addClass("hide")
        //                     video.play();
        //                     $("html body").addClass("full")
        //                     flag = 0
        //                 }

        //             })
        //         }

        //         // videoEnd();
        //         $.post(viewedUrl, {
        //             user_id: a.id
        //         }, function (data) {})
        //     } else {
        //         $('#firstLoading').remove()
        //     }
        // }

        // function videoResize() {
        //     //旋转屏幕
        //     function isPortrait() {
        //         return (window.orientation == 0 || window.orientation == 180) ? true : false;
        //     }

        //     var landTimer;
        //     window.addEventListener('orientationchange', function (res) {
        //         //以竖屏方式进入
        //         if (wh > ww) {
        //             //切换成横屏
        //             if (!isPortrait()) {
        //                 alert(1)
        //                 $("body>div,#myWarp,.videoBox").css({
        //                     width: "100%",
        //                     height: "100%",
        //                 });
        //             } else {
        //                 //切成竖屏
        //                 alert(2)
        //                 $("body>div,#myWarp,.videoBox").css({
        //                     width: (wh + 1) + "px",
        //                     height: ww + "px"
        //                 });
        //             }
        //             //横屏进入页面
        //         } else {
        //             //切换成竖屏
        //             if (isPortrait()) {
        //                 var landIndex = 0;
        //                 clearInterval(landTimer);
        //                 landTimer = setInterval(function () {
        //                     //检测旋转猴，轮询宽高值
        //                     if (landIndex++ <= 10) {
        //                         //直到获取的宽度小于高度后重置宽高
        //                         if (window.innerWidth < window.innerHeight) {
        //                             $("body>div,#myWarp,.videoBox").css({
        //                                 width: (window.innerHeight + 1) + "px",
        //                                 height: window.innerWidth + "px"
        //                             });
        //                             clearInterval(landTimer);
        //                         }
        //                     } else {
        //                         clearInterval(landTimer);
        //                     }
        //                 }, 150);
        //             } else {
        //                 //换回横屏
        //                 $("body>div,#myWarp,.videoBox").css({
        //                     width: "100%",
        //                     height: "100%"
        //                 });
        //             }
        //         }
        //     });
        // }
        // //播放结束时
        // function videoEnd() {
        //     myVideo.onended = function () {
        //         $("html body").removeClass("full")
        //         $(window).unbind('resize')
        //         $('#myVideo').animate({
        //             marginLeft: '0%'
        //         }, 1000, function () {
        //             $('#myVideo').remove();
        //         })
        //         $(".page2").removeClass('hide')
        //             .animate({
        //                 left: '0'
        //             }, 1000, function () {
        //                 $(".page2").click(function () {
        //                     // var video2 = $('<video id="myVideo" poster="" -webkit-playsinline webkit-playsinline="true" x-webkit-airplay="true" playsinline="true" x5-video-player-type="h5" x5-video-orientation="landscape" x5-video-player-fullscreen="true"  class="video rotate">您的浏览器不支持 video 标签。</video>')
        //                     // $("#canvas1").after(video2) 
        //                     // videoResize()                   
        //                     $('#firstLoading').remove()
        //                 })
        //             })
        //     }
        // }

        //更换用户头像
        function changeHeadImg() {
            if (a.header_img) {
                $(".header_img").css("background-image", "url('" + a.header_img + "')")
            }
        }
        //遮盖用户电话
        function showMobile() {
            a.showMobile = a.mobile.substr(0, 3) + "*****" + a.mobile.substr(-3)
        }
        //更新页面抽奖次数
        function remainingTimes() {
            $('#big').html(a.big) //大奖次数
            $('#small').html(a.small) //小奖次数
        }

        //配置签到状态
        function signInit() {

            var pageSign = '',
                propSign = '';
            for (var i = 1; i <= a.maxSignTimes; i++) {
                if (i <= a.signTimes) {
                    if (a.signTimes == a.maxSignTimes && i == a.maxSignTimes) {
                        pageSign +=
                            '<span class="circle live-circle ">' + i + '</span>'
                        propSign += '<li class="active">' + i + '</li>'
                    } else {
                        if (i < a.signTimes) {
                            pageSign +=
                                '<span class="circle live-circle ">' + i + '</span>' +
                                '<span class="line live-circle "></span>'
                        } else {
                            pageSign +=
                                '<span class="circle live-circle ">' + i + '</span>' +
                                '<span class="line live-line "></span>'
                        }
                        propSign += '<li class="active">' + i + '</li>'

                    }
                } else if (i < a.maxSignTimes && i > a.signTimes) {
                    pageSign +=
                        '<span class="circle">' + i + '</span>' +
                        '<span class="line"></span>'
                    propSign += '<li>' + i + '</li>'
                } else {
                    pageSign +=
                        '<span class="circle">' + i + '</span>'
                    propSign += '<li>' + i + '</li>'
                }
            }
            $('.spanlis').html(pageSign);
            $('.signInlist').html(propSign);
            $('#signTimes').html(a.signTimes);
            //配置是否允许签到
            if (!a.todaySign) {
                $('#signInBtn').attr('disabled', !a.todaySign).html('今日已签到')
            }
        }
        //奖品列表配置
        function priedInit(arr) {
            var pried = '';
            var obj = {};
            $.each(arr, function(k, v) {
                if (v) {
                    obj['lv' + v] = 1
                }
            })
            var max1 = true,
                max2 = true; //只有最高奖品显示领奖与核销
            var isAppontment = true; //预约后只显示最高奖
            //console.log(a)
            $.each(obj, function(k, v) {
                    if (v && isAppontment) {
                        pried +=
                            '<div id="' + k + '"> ' + //1
                            '   <div class="card clear">' + //1-1
                            '       <div class="lf cardImg">' + //1-1-1
                            '           <img src="' + a.baseUrl + '/static/img/' + k + '.png">' +
                            '       </div>' + //1-1-1
                            '       <div class="lf cardDesc '
                        if (!max1) {
                            pried += "center"
                        } //非首项加margin居中
                        pried +=
                            '                   ">' + //1-1-2
                            '           <img class="listGift"/>' +
                            '           <div class="cardDescHead"> { ' + prizeList[k][0] + ' }</div>' + //1-1-2-1
                            // '           <div class="cardDescBody">' + prizeList[k][1] + '</div>' + //1-1-2-2
                            '           <div class="cardDescFoot">' //1-1-2-3
                            // 测试取反**************************************************************!!$.isEmptyObject(a.isAppointment) && !a.redeem
                        if (max1) {
                            //console.log(!$.isEmptyObject(a.isAppointment) && !a.redeem)
                            if (!$.isEmptyObject(a.isAppointment) && !a.redeem) {
                                //console.log("领取奖品")
                                // 是否禁止核销
                                if (a.disableRedeem) {
                                    pried += '<button class="signbtn">已停止核销</button>'
                                } else {
                                    pried += '<button class="signbtn redeemBtn">领取奖品</button>'
                                }
                                pried +=
                                    '           </div>' + //1-1-2-3
                                    '       </div>' + //1-1-2
                                    '   </div>' + //1-1
                                    '</div>' //1
                            } else if (a.redeem) {
                                //console.log('已领取')
                                pried += '<button class="signbtn">已领取</button>'
                                pried +=
                                    '           </div>' +
                                    '       </div>' +
                                    '   </div>' +
                                    '</div>'
                            } else {
                                //console.log('填写领奖信息')
                                // 是否开启预约功能
                                if (a.disableAppointment) {
                                    pried += '<button class="signbtn pried">已停止预约</button>'
                                } else {
                                    pried += '<button class="signbtn pried">填写领奖信息</button>'
                                }
                                pried +=
                                    '         </div>' +
                                    '    </div>' +
                                    '</div>'
                                createFrom(k)
                                pried += '</div>'
                            }
                            max1 = false
                        } else {
                            pried +=
                                '           </div>' +
                                '       </div>' +
                                '   </div>' +
                                '</div>'
                        }

                        if (!$.isEmptyObject(a.isAppointment)) {
                            isAppontment = false
                        }
                    }
                    // 如果预约过，跳出循环
                    if (a.redeem) {
                        return false
                    }
                }) //ecah结束
                //生成奖品列表
            function createFrom(k) { //创建填写信息表单
                // 是否开启禁止预约功能
                if (a.disableAppointment) {
                    return false
                }

                if (max2) {
                    max2 = false;
                    if (a.openID) { //如果有openID
                        pried +=
                            ' <div class="formdiv">' +
                            '<h2>恭喜中奖！请填写您的领奖信息:</h2>' +
                            '<form  class="peizeform" alt="' + k + '">' +
                            '    <label class="prizelabel" for="userName">' +
                            '        姓名：' +
                            '    </label>' +
                            '   <input type="text" class="prizeInput" name="userName"/>' +
                            '   <label class="prizelabel" for="userNumber">' +
                            '        手机号码：' +
                            '    </label>' +
                            '    <input type="tel" class="prizeInput" name="mobile" value="' + a.showMobile + '"'
                        if (a.mobile) {
                            pried += 'readonly'
                        }

                        pried += '/>' +
                            /*'    <label class="prizelabel" for="shopName">' +
                            '        请选择领奖门店：' +
                            '    </label>' +
                            '    <br>' +
                             '    <div class="storeSelect">' +
                             '        <select class="list1 storeItem" "><option disabled selected> 省</option></select>' +
                             '        <select class="list2 storeItem""><option disabled selected> 市</option></select>' +
                             '        <select class="list3 storeItem" "><option disabled selected> 请选择具体门店</option></select>' +
                             '    </div>' +
                            '    <p class="conAgree">' +
                            '        <input type="checkbox" checked/> 本人已阅读并接受捷成中国本次<span class="prince clause" >活动条款</span>' +
                            '    </p>' +*/
                            '    <div style="text-align:center">' +
                            '        <button type="button" class="signbtn userMsgBtn">提 交</button>' +
                            '    </div>' +
                            '</form>' +
                            '</div>'

                    } else {
                        pried +=
                            ' <div class="formdiv">' +
                            '<h2>亲爱的' + a.userName + '（' + a.showMobile + '），恭喜您中奖，请选择领奖门店</h2>' +
                            '<form  class="peizeform" alt="' + k + '">' +
                            /* '    <label class="prizelabel sms" for="shopName">' +
                             '        请选择领奖门店：' +
                             '    </label>' +
                             '    <br>' +
                             '    <div class="storeSelect">' +
                             '        <select class="list1 storeItem" "><option disabled selected> 省</option></select>' +
                             '        <select class="list2 storeItem""><option disabled selected> 市</option></select>' +
                             '        <select class="list3 storeItem" "><option disabled selected> 请选择具体门店</option></select>' +
                             '    </div>' +
                             '    <p class="conAgree">' +
                             '        <input type="checkbox" checked/> 本人已阅读并接受捷成中国本次<span class="prince clause" >活动条款</span>' +
                             '    </p>' +*/
                            '    <div style="text-align:center">' +
                            '        <button type="button" class="signbtn userMsgBtn">提交</button>' +
                            '    </div>' +
                            '</form>' +
                            '</div>'
                    }

                }
            }

            $('#prize').html(pried) //添加奖品列表
            if ($.isEmptyObject(a.isAppointment)) { //未填写填写信息的用户
                // getStoreId() //获取门店
            }
            if (!$.isEmptyObject(a.isAppointment) && !a.redeem) { //填写了信息未核销用户展示领奖地址
                var date = new Date(a.appointmentDate * 1000 + 3 * 24 * 60 * 60 * 1000);
                var month = date.getMonth() + 1,
                    day = date.getDate();
                var lv = $(".cardDescHead").html()
                lv = lv.slice(3, -2);
                var store =
                    '<div id = "storeMsg" class="storeMsg">' +
                    '   <p class="prince clause">活动细则</p>' +
                    '   <div  class="storeMsgUser" >亲爱的' + a.userName + '（' + a.showMobile + '）,恭喜您获得' + lv + '，<br>请持本页面至以下门店领取</div>' +
                    '   <div class="storeMsgStoreName"><b>门店:</b>' + a.store_name + '</div>' +
                    '   <div class="storeMsgStoreAddress"><b>地址:</b>' + a.store_address + '</div>' +
                    // '   <div class="storeMsgDate">温馨提示：您的奖品将在' + month + '月' + day + '日配送到店哦！</div>' +
                    '</div>'
                var html = $(store)
                $("#prize").append(html) //添加提示信息
            }
        }

        function init() {
            //给定兑奖后奖励图片地址列表   
            // var cover = a.baseUrl + '/static/img/4_03.png'
            var cover = a.big != 0 ? {
                url: a.baseUrl + '/static/img/big.png',
                text: '大奖刮出来'
            } : {
                url: a.baseUrl + '/static/img/P10-lotteryImg.png',
                text: null
            }
            var times = a.smallUsed + a.bigUsed;
            var can = $('<canvas id="c1"></canvas>') //更新页面元素,消除旧事件影响
            $('#con').html(can)
            lot = new lottery('c1', cover, 'con', prizeList, lotteryUrl, a.id, changeTimes)
            lot.init(1, null, prizeList, prizeList)
        }
        //使用抽奖机会
        function useChance() {
            // 是否禁止刮奖
            if (a.disableLottery) {
                $('#c1').css('background-color', '#ebebeb').parent().addClass('disableAppointment')
                return;
            }

            if (((+a.big && a.bigUsed < a.bigMax) || (+a.small && a.smallUsed < a.smallMax)) && !a.redeem) { //满足条件才刷新刮刮卡
                $('#c1').css('background-color', 'transparent').parent().removeClass('noChance')
                init()
                oneMoreFlag = false //关闭再来一次
            } else if (a.redeem) {
                var can = $('<canvas id="c1"></canvas>') //更新页面元素,消除旧事件影响
                $('#con').html(can)
                $('#c1').css('background-color', '#ebebeb').parent().removeClass('noChance').addClass('isRedeem')
            } else {
                $('#c1').css('background-color', '#ebebeb').parent().addClass('noChance')
            }
        }
        // 改变现存抽奖次数
        function changeTimes() {
            if (+a.big && a.bigUsed < a.bigMax) {
                +a.big--;
                a.bigUsed++;
                remainingTimes() //更新次数
                getPried() //更新中奖列表
                return;
            }
            if (+a.small && a.smallUsed < a.smallMax) {
                +a.small--;
                a.smallUsed++;
                remainingTimes() //更新次数
                getPried() //更新中奖列表
                return;
            }
        }

        // 点击再来一次,如果可以抽奖
        function oneMoreTimes() {
            $('.oneMore').click(function() {
                // console.log(1)
                oneMoreFlag = lot.oneMoreFlag;
                // console.log(oneMoreFlag)
                if (oneMoreFlag) {
                    // console.log(2)
                    useChance()
                }
            })
        }

        //展示奖品列表
        function showGift() {
            var giftFlag = true; //防止连续操作
            $('.yourGift').on('click', function() {
                if (giftFlag) {
                    giftFlag = false;
                    $('#prize').slideToggle('fast', function() {
                        giftFlag = true
                    })

                }
            })
            $('.yourGift').click() //进入页面后展示奖品
        }

        var priedFlag = true;
        //领奖信息表单的显示
        function userMsgFormShow() {
            $('#prize').on('click', 'button.pried', function() {
                if (priedFlag) {
                    //console.log($(this).parents('.card').next())
                    var lv = $(this).parents('.card').parent().attr('id');
                    priedFlag = false;
                    $(this).parents('.card').next().slideToggle('fast', function() {
                        priedFlag = true
                    })
                }
            })
        }


        //签到模块****************************************
        function singIn() {
            $("#signInBtn").click(function() {
                var livel = $(".live-circle").length;
                var $lines = $(".line");
                var $circles = $(".circle");
                if (0 <= livel < a.maxSignTimes) { //签到0~6天的点击会变色
                    $(".spanlis").children().eq(livel * 2).addClass("live-circle")
                    $(".spanlis").children().eq(livel * 2 + 1).addClass("live-line");
                    $(this).parent().siblings('.signInlist').children().eq(livel).addClass('active');
                    a.todaySign = !a.todaySign;
                    $('#signInBtn').attr('disabled', !a.todaySign).html('今日已签到')
                    $('#signInBtn').css({
                        color: '#fff',
                        backgroundColor: "#f19aa0"
                    });
                    $.post(checkinUrl, {
                        user_id: a.id
                    })
                    if (a.signTimes == 6) {
                        a.big++
                    }
                    if (a.signTimes < 6) {
                        a.small++
                    }
                    if (a.signTimes == a.maxSignTimes) {
                        a.small++
                    }
                    a.signTimes = a.signTimes == a.maxSignTimes ? 1 : ++a.signTimes;
                    signInit()
                    remainingTimes() //签到后页面更新抽奖次数
                    $('#signTimes').html(a.signTimes)
                    oneMoreFlag = lot.oneMoreFlag ? lot.oneMoreFlag : true;
                    if (oneMoreFlag) {
                        useChance(oneMoreFlag)
                    } //更新挂挂卡
                } else { //签到满4天签到数就返回为0；
                    $(".spanlis span").removeClass("live-circle");
                    $(".spanlis span").removeClass("live-line");
                }
            });
        }


        //弹窗模块
        function propBox() {
            $(".toolbtn").click(function() {
                $(this).parents('.modalS').siblings(".bg-modal").show();
                $("body").css({
                    "overflow": "hidden"
                });
            });

            if (a.todaySign) {
                // 是否禁止签到
                if (!a.disableCheckin) {
                    $('.signbtn').click() //页面进入后如果未签到显示弹窗
                }
            }

            $(".bg-modal-ok").click(
                function() {
                    $(this).parents(".bg-modal").hide();
                    $("body").css({
                        "overflow": "visible"
                    });
                }).hover();
            $(".close").click(
                function() {
                    $(this).parents(".bg-modal").hide();
                    $("body").css({
                        "overflow": "visible"
                    });
                }
            );
            //弹窗模块*************************************************
        }

        // 提交用户信息
        function userMsgSubmit() {
            // 电话号码正则
            var reg = /^[1][3-8](\d{9}|\d\*{5}\d{3})$|^([6|9])\d{7}$|^[0][9]\d{8}$|^[6]([8|6])\d{5}$/;
            $("#prize").on('click', '.userMsgBtn', function() {
                console.log(11)
                var parent = $(this).parents(".peizeform");
                var name = (parent.find('[name="userName"]').val() || a.userName).trim(),
                    mobile = (parent.find('[name="mobile"]').val() || a.mobile).trim(),
                    prize_id = parent.attr("alt");
                store_id = a.store_id;
                // 姓名非空
                //                if (!name) {
                //                    parent.find('[name="userName"]').addClass('err')
                //                    return
                //                } else if (name) {
                //                    parent.find('[name="userName"]').removeClass('err')
                //                }
                //                //电话号码验证
                //                if (!reg.test(mobile)) {
                //                    parent.find('[name="mobile"]').addClass('err')
                //                    return;
                //                } else if (reg.test(mobile)) {
                //                    parent.find('[name="mobile"]').removeClass('err')
                //                }
                // //地址非空
                //                if (!store_id) {
                //                    $('.list3').addClass('err')
                //                    return
                //                } else if (store_id) {
                //                    $('.list3').removeClass('err')
                //                }
                data = {
                    name: name,
                    mobile: a.mobile ? a.mobile : mobile,
                    //                    store_id: a.store_id,
                    store_id: 1,
                    openID: a.openID,
                    mobileHash: md5(mobile)
                }
                console.log(data)
                var arg = a.openID ? '&src=wechat' : '&src=sms';
                if (!a.openID) {
                    $.post(appointmentCreateUrl, data, function(data) {

                        if (data.status == "success") {
                            console.log("success");
                            a.isAppointment = {
                                store_id: a.store_id
                            }
                            a.appointmentDate = (+new Date()) / 1000;
                            getPried() //成功返回消息后更新奖品列表
                        }
                    })
                } else {
                    window.frames.userMsg.postMessage(data, "*")
                }

            })

            window.addEventListener("message", function(e) {
                if (e.data == "isAppointmentSuccess") {
                    a.userName = data.name;
                    a.mobile = data.mobile;
                    showMobile()
                    a.appointmentDate = (+new Date()) / 1000;
                    a.isAppointment = {
                        store_id: a.store_id
                    }
                    getPried() //成功返回消息后更新奖品列表
                }
            })
        }


        // 获取门店信息
        // var inTheBox = false;

        // function getStoreId() {
        //     var storeList = ''
        //     $.get(storeUrl, function(data) {
        //         storeList = data

        //         // $("#prize").on('click', '[name=store_id]', function () {
        //         // inTheBox = true;
        //         // var list1, list2, list3; //省 市 门店
        //         var ak1, ak2, ak3; //选中的门店
        //         // if (!list1) {
        //         //     list1 = $("<select class='list1 storeItem' style='left:0'></select>")
        //         //     $(this).parent().append(list1)
        //         // }
        //         var list1Lis = '',
        //             list2Lis = '',
        //             list3Lis = '';
        //         list1Lis += '<option disabled selected> 省</option>'
        //         for (var k in storeList) {
        //             list1Lis += '<option>' + k + '</option>'
        //         }
        //         $('.list1').html(list1Lis).on('change', function() {
        //                 $('.list2').html('')
        //                 $('.list3').html('')
        //                     // if (!list2) {
        //                     //     list2 = $("<select class='list2 storeItem' style='left:25%'></select>")
        //                     //     $(this).parents('.storeSelect').append(list2)
        //                     // }
        //                 list2Lis = ''
        //                 ak1 = $('.list1').val()
        //                 list2Lis += '<option disabled selected> 市</option>'
        //                 list3Lis += '<option disabled selected> 请选择具体门店</option>'
        //                 for (var k2 in storeList[ak1]) {
        //                     list2Lis += '<option>' + k2 + '</option>'
        //                 }
        //                 $('.list3').html(list3Lis);
        //                 $('.list2').html(list2Lis).on('change', function() {
        //                     // if (!list3) {
        //                     //     list3 = $("<select class='list3 storeItem' style='left:50%'></select>")
        //                     //     $(this).parents('.storeSelect').append(list3)
        //                     // }
        //                     list3Lis = ''
        //                     ak2 = $('.list2').val()
        //                     list3Lis += '<option disabled selected> 请选择具体门店</option>'
        //                     for (var k3 in storeList[ak1][ak2]) {
        //                         list3Lis += '<option class="' + k3 + '">' + storeList[ak1][ak2][k3].zh_storename + '</option>'
        //                     }
        //                     $('.list3').html(list3Lis).on('change', function() {
        //                         var nk3;
        //                         ak3 = $(this).val();
        //                         nk3 = $(this).find('option:selected').attr("class")
        //                         a.store_id = storeList[ak1][ak2][nk3].id
        //                         a.store_name = ak3
        //                         a.store_phone = storeList[ak1][ak2][nk3].phone
        //                         a.store_address = storeList[ak1][ak2][nk3].address
        //                             // if (ak1 == ak2) {
        //                             //     $('[name=store_id]').val(ak2 + '/' + ak3)
        //                             // } else {
        //                             //     $('[name=store_id]').val(ak1 + '/' + ak2 + '/' + ak3)
        //                             // }
        //                             // $('.storeItem').remove();
        //                     })
        //                 })
        //             })
        //             // })
        //     })


        // }

        //监控鼠标与下拉框关系
        function addressDropDown() {
            $('#prize').on('mouseenter', '.storeSelect', function() {
                inTheBox = true;
            })
            $('#prize').on('mouseleave', '.storeSelect', function() {
                inTheBox = false
            })
            $(document).click(function() {
                if (!inTheBox) {
                    $('.storeItem').remove()
                }
            })
        }

        //条款细则
        function clauseProp() {

            //条款细则确认
            $("#prize").on('click', '.conAgree input', function() {
                    var thisBtn = $(this).parent().next().children()
                    $(this).is(':checked') ? thisBtn.attr("disabled", false) : thisBtn.attr("disabled", true)
                })
                // 弹窗
            $('#prize').on('click', '.policy', function() {
                $("body").css({
                    "overflow": "hidden"
                });
                $("#pol-modal").show();
                return true;
            });
            $('#prize').on('click', ".clause", function() {
                $("body").css({
                    "overflow": "hidden"
                });
                $("#clause-modal").show();
                return true;
            });
            $(".clause").click(function() {
                $("body").css({
                    "overflow": "hidden"
                });
                $("#clause-modal").show();
                return true;
            })
        }

        //核销
        function redeemProp() {
            $('#prize').on('click', '.redeemBtn', function() {
                $("body").css({
                    "overflow": "hidden"
                });
                $("#redeem").show();
                return true;
            })
            $(".redeemConfirmBtn").on('click', function() {
                var code = $("#redeemCode").val()
                $.post(redeemUrl, {
                    auth_code: code,
                    user_id: a.id,
                }, function(data) {
                    console.log(data)
                    if (data.status == "success") {
                        $("#redeemMsg").html("输入正确").css('color', 'green')
                        a.redeem = true;
                        setTimeout(function() {
                            $("#redeem").hide()
                            $("#storeMsg").remove() //移除领奖提示
                            $('#signbutton').removeClass("toolbtn") //禁止签到
                            useChance() //更新刮刮卡元素
                        }, 2000)
                        $('.redeemBtn').html('已领取').removeClass('redeemBtn')
                    } else {
                        $("#redeemMsg").html("输入错误").css('color', 'red')
                    }
                })


            })
        }

        // 奖品剩余库存
        function prizeRemain() {
            $.get(PrizeStockUrl, function(data) {
                //                $(".remain-lv1").html(data["1"]["left"])
                $(".remain-lv2").html(data["2"]["left"])
                $(".remain-lv3").html(data["3"]["left"])
                $(".remain-lv4").html(data["4"]["left"])
                    //                $(".remain-lv5").html(data["5"]["left"])
            })
        }
    }) //0