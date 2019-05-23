    //设备判断模块 
    function bindEvent() {
        var _this = this;
        var device = (/android|webos|iphone|ipad|ipod|blackberry|iemobile|opera mini/i.test(navigator.userAgent.toLowerCase()));
        var startName = device ? 'touchstart' : 'mousedown';
        var moveName = device ? 'touchmove' : 'mousemove';
        var endName = device ? 'touchend' : 'mouseup';
        return {
            device,
            startName,
            moveName,
            endName
        }
    };

    var bE = bindEvent();
//刮刮卡模块
class lottery {
    //初始化参数(canvas的id,覆盖图片,canvas的容器,奖品图片列表)
    constructor(id, coverUrl, container, prizeList, ajaxUrl,user_id,callback) {
        this.canvasID = id;
        this.c1 = document.getElementById(this.canvasID);
        this.ismousedown = null;
        this.width = parseFloat(window.getComputedStyle(this.c1).width);
        this.height = parseFloat(window.getComputedStyle(this.c1).height);
        this.ctx = this.c1.getContext('2d')
        this.coverUrl = coverUrl;
        this.first = true;
        this.endFirst = true;
        this.container = container;
        this.prizeList = prizeList;
        this.delay = false;
        this.url = ajaxUrl;
        this.oneMoreFlag = true;
        this.user_id = user_id;
        this.callback = callback;
        this.isRequest = false;
    }
    //初始化表层覆盖物
    initialCover() {
        // var _this = this
        // var img = new Image();
        // img.src = _this.coverUrl;
        // img.onload = function () {
        //     _this.ctx.drawImage(img, 0, 0, _this.width, _this.height);
        // }
        this.ctx.save();
        this.ctx.fillStyle =this.coverUrl.color;
        this.ctx.fillRect(0, 0,  this.width, this.height)
        if(this.coverUrl.text){
            this.ctx.fillStyle ="#fff";
            this.ctx.font="2rem Arial";
            this.ctx.fillText(this.coverUrl.text,this.width/2-40,this.height/2+6);
        }
        this.ctx.restore()
        
    }
    //绘制背景
    drawBg(lvl, list) {
        //设置字体样式
        $(this.c1).after("<div style='position:absolute;left:4.5rem;top:1.5rem;font-size:3rem;z-index:0;background:'>"+list['lv'+lvl][0]+"</div>");
        this.delay = true;
    }
    //添加事件监听
    addE() {
        this.c1.addEventListener(bE.moveName, this.eventMove.bind(this), false);
        this.c1.addEventListener(bE.startName, this.eventStart.bind(this), false);
        this.c1.addEventListener(bE.endName, this.eventEnd.bind(this), false);
    }
    //删除事件监听
    delE(){
        this.c1.removeEventListener(bE.moveName, this.eventMove.bind(this), false);
        this.c1.removeEventListener(bE.startName, this.eventStart.bind(this), false);
        this.c1.removeEventListener(bE.endName, this.eventEnd.bind(this), false);
    }
    eventStart(e) {
        e.preventDefault();
        var _this = this;
        this.ismousedown = true;
        if (this.first) {
            // 绘制背景
            this.getPrizeLvl()
        }
        this.first = false;
    }
    //获取奖励等级
    getPrizeLvl() {
        if(!this.isRequest){
            var _this = this
            this.isRequest = true;
            $.ajax({
                method: 'POST', //请求数据方式
                data:{user_id:this.user_id},
                url: _this.url, //'./js/test.php', //请求数据地址
                async: 'false', //是否异步
                success: function (data) {
                    var d = data
                    if(d.status=='success'){
                        var i = null;
                        if(d.data.prize_level){
                            i = d.data.prize_level
                        }
                        if(!d.data.prize_level){
                            i = 0
                        }
                        _this.drawBg(i, _this.prizeList)
                        // setTimeout(function () { //假设 服务器如果出现500ms延时
                        this.isRequest = false;   
                        // }, 500)
                    }
                }
            });
        }
        var _this = this;
       
    }
    eventEnd(e) {
        e.preventDefault();
        var _this = this;
        //得到canvas的全部数据 要服务器才能正常使用
        var j = this.getTransparentPercent(_this.ctx, _this.c1.width, _this.c1.height)
        // console.log(j)
        //当被刮开的区域等于一半时，则可以开始处理结果
        if ((j > 30) && _this.endFirst) {
            _this.ctx.clearRect(0, 0, _this.c1.width, _this.c1.height); //清空覆盖
            _this.endFirst = false; // 禁止第二次触发事件
            _this.oneMoreFlag = true; //开启再来一次按钮
            this.delE()//删除事件准备下次启用
            this.callback() //改变页面抽奖次数

        }
        this.ismousedown = false;
        
    }
    getTransparentPercent(ctx, width, height) { //刮开百分比计算
        var imgData = ctx.getImageData(0, 0, width, height),
            pixles = imgData.data,
            transPixs = [];
            for (var i = 0, j = pixles.length; i < j; i += 4) {
            var a = pixles[i + 3];
            if (a < 128) {
                transPixs.push(i);
            }
        }
        return (transPixs.length / (pixles.length / 4) * 100).toFixed(2);
    }
    eventMove(e) {
        e.preventDefault();
        var _this = this;
        //按下鼠标货触摸时,并且已经绘制好背景,才允许刮奖
        if (_this.ismousedown && _this.delay) {
            if (e.changedTouches) {
                e = e.changedTouches[e.changedTouches.length - 1];
            }
            var topY = document.getElementById(this.container).offsetTop;
            var leftX = document.getElementById(this.container).offsetLeft;
            var oX = c1.offsetLeft + leftX,
                oY = c1.offsetTop + topY;
            var mozTop = document.documentElement.scrollTop;
            var mozLeft = document.documentElement.scrollLeft;
            var x = (e.clientX + (document.body.scrollLeft || mozLeft) || e.pageX) - oX || 0,
                y = (e.clientY + (document.body.scrollTop || mozTop) || e.pageY) - oY || 0;

            //画360度的弧线，就是一个圆，因为设置了ctx.globalCompositeOperation = 'destination-out';
            _this.ctx.globalCompositeOperation = 'destination-out';
            //画出来是透明的
            _this.ctx.beginPath();
            var fontem = parseInt(window.getComputedStyle(document.documentElement, null)["font-size"]);
            //清除覆盖
            _this.ctx.arc(x, y, fontem * 1.2, 0, Math.PI * 2, true);

            //下面3行代码是为了修复部分手机浏览器不支持destination-out;不清楚原理
            _this.c1.style.display = 'none';
            _this.c1.offsetHeight;
            _this.c1.style.display = 'inherit';
            _this.ctx.fill();
        }

    }
    //运行程序参数(进入页面状态,奖励等级,奖励图片url列表,兑换后奖励图片url列表 )
    init(code, prizeLvl, prizeList, prizeUsedList) {
        // 重置canvas的宽高
        this.c1.width = this.width;
        this.c1.height = this.height;
        if (code == 3) {
            this.drawBg(prizeLvl, prizeUsedList);
            return;
        }
        //进入页面 如果抽过奖
        if (code == 2) {
            this.drawBg(prizeLvl, prizeList);
            return;
        }
        //如果未抽过奖
        if (code == 1) {
            //初始化
            this.oneMoreFlag = false;//关闭再来一次
            this.initialCover();
            $(this.c1).siblings().remove()
            //添加事件
            this.addE();
            return;
        }
    }
}

