var doc = document.querySelectorAll.bind(document);
var nextBtn = doc(".js-next");
var slides = doc(".slide");
var $slides = $(".slide");
//var arr = {'topic_one' : };
var arr = [];
var hobby = { key: "" };
var obj1 = { topic: 'topic_one', answer: '' };
var obj2 = { topic: 'topic_two', answer: '' };
var obj3 = { topic: 'topic_three', answer: '' };
var obj4 = { topic: 'topic_four', answer: '' };

var isRequst = false;

window.onload = function() {
    initSlideStyle(); //初始化style
    setSrc() //将img的图片的懒加载
    bindNextBtn(); //所有下拉按钮的绑定事件
    initQuestions(); //初始化相关问题的数据
    nextPage();
}
window.onresize = function() {
    initSlideStyle()
}

function initSlideStyle() {
    var W = window.innerWidth;
    var H = window.innerHeight;
    console.log(W, H);
    slides.forEach(function(item, index, arr) {
        item.style.width = W + "px";
        item.style.height = H + 1 + "px";
    });


    //$("#qustionGroup2").css("top": tans(0.27));
}

function tans(i) {
    return parseInt((W * i) / H) + "px";
}

// function nextPage() { //翻页

//     nextBtn.forEach(function(item, index, arr) {
//         console.log(item);
//         var i = ++index;
//         console.log(item, i);
//         item.onclick = function() {
//             $('html,body').animate({ scrollTop: parseInt(H * i) + "px" }, 500);
//         }
//     });
// }

function setSrc() {
    for (let i = 0; i < $("img[data-src]").length; i++) {
        $("img[data-src]").eq(i).attr("src", $("img[data-src]").eq(i).attr("data-src"));
    }
}

function switchPage(i, j) {
    console.log(i, j);
    if (i == j) {
        $(".slide").eq(i).fadeIn(500);
    } else {
        $(".slide").eq(i).fadeOut(500, function() {
            $(".slide").eq(j).fadeIn(500);
        })
    }
};

function bindNextBtn() {
    nextBtn.forEach(function(item, index, arr) {
        if (index == 0) {
            item.onclick = function() {
                console.log(0);
                switchPage(0, 1)
            };
        } else if (index == 1) {

            item.onclick = function() {
                console.log(index);
                switchPage(6, 7)
            };
        } else if (index == 2) {
            item.onclick = function() {
                console.log(index);
                switchPage(7, 8)
            };
        }
    });
    $("#qustionSubmit").on('click', function() {
        var data = { "one": obj1.answer, "two": obj2.answer, 'three': obj3.answer, 'four': obj4.answer, 'hobby': hobby.key }
            // submitPost();
        switchPage(5, 6);

        function submitPost() {
            if (!isRequst && obj4.answer) { //
                isRequst = true;
                $.post("member/add-answer", data, function(data) {
                    if (data.status == 'success') {
                        switchPage(5, 6); //跳转到问题2的页面
                        isRequst = false;
                    } else if (data.status == 'error') {
                        tipProp("你的选择发送失败,请再试一次!");
                        isRequst = false;
                    }
                }, 'json')
            } else if (!obj4.answer) {
                tipProp("您还没有选择呢,my queen!");
            }
        }
    })

}



function initQuestions() {
    $(".keyOption").on('click', function() {
        console.log(2);
        hobby.key = this.getAttribute('key');
        $(".keyOption").removeClass("keyAnswerSelected");
        $(this).addClass("keyAnswerSelected");
        var key = hobby.key;
        var src = $("#showYourStyle").attr("data-src");
        // console.log(src);
        src = src.slice(0, -5);
        console.log(src);

        if (key == "free" || key == "sexy") {
            $("#showYourStyle").attr("src", src + "1.png");
            $("#bottomImg").attr("src", src + "12.png");
        } else if (key == "sporty" || key == "smart") {
            $("#showYourStyle").attr("src", src + "3.png");
            $("#bottomImg").attr("src", src + "32.png");
        } else {
            $("#showYourStyle").attr("src", src + "2.png");
            $("#bottomImg").attr("src", src + "22.png");
        }
        switchPage(1, 2)
    })
    $("#answerDiv1 .answer").on('click', function() {
        obj1.answer = this.getAttribute('key');
        $("#answerDiv1 .answer").removeClass("answerSelected");
        $(this).addClass("answerSelected");
        switchPage(2, 3)
    })
    $("#answerDiv2 .answer").on('click', function() {
        console.log(2);
        obj2.answer = this.getAttribute('key');
        $("#answerDiv2 .answer").removeClass("answerSelected");
        $(this).addClass("answerSelected");
        switchPage(3, 4)
    })
    $("#answerDiv3 .answer").on('click', function() {
        obj3.answer = this.getAttribute('key');
        $("#answerDiv3 .answer").removeClass("answerSelected");
        $(this).addClass("answerSelected");
        switchPage(4, 5)
    })
    $("#answerDiv4 .answer").on('click', function() {
        obj4.answer = this.getAttribute('key');
        $("#answerDiv4 .answer").removeClass("answerSelected");
        $(this).addClass("answerSelected");
    })
}

function postForQus(obj, url) {

    if (!isRequst && obj.answer) { //
        isRequst = true;
        $.post("member/add-answer", obj, function(data) {

            if (data.status == 'success') {
                window.location.href = url; //跳转到问题2的页面
                isRequst = false;
            } else if (data.status == 'error') {
                tipProp("你的选择发送失败,please once more again");
                isRequst = false;
            }
        }, 'json')
    } else if (!obj.answer) {
        tipProp("您还没有选择呢,my queen!");
    }
}



/*  testqusion  end*/
/*  ToolTip start   //http://www.jb51.net/article/137700.htm*/
//弹窗模块
function tipProp(text) {
    $(".tipProp p").text(text);
    $(".tipProp").fadeIn(500);

    setTimeout(function() {
        $(".tipProp").fadeOut(500);
    }, 2000);
};

//弹窗模块*************************************************