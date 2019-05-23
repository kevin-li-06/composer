/*  testqusion  start*/
var obj1 = { topic: 'topic_one', answer: '' };
var obj2 = { topic: 'topic_two', answer: '' };
var obj3 = { topic: 'topic_three', answer: '' };
var isRequst = false;

console.log("btn111111");
initQuestions();

function initQuestions() {
    $("#answerDiv1 .answer").on('click', function() {
        obj1.answer = this.getAttribute('key');
        $("#answerDiv1 .answer").removeClass("answerSelected");
        $(this).addClass("answerSelected");
    })
    $("#answerDiv2 .answer").on('click', function() {
        console.log(2);
        obj2.answer = this.getAttribute('key');
        $("#answerDiv2 .answer").removeClass("answerSelected");
        $(this).addClass("answerSelected");
    })
    $("#answerDiv3 .answer").on('click', function() {
        obj3.answer = this.getAttribute('key');
        $("#answerDiv3 .answer").removeClass("answerSelected");
        $(this).addClass("answerSelected");
    })
    $("#qustion1Submit").on('click', function() {
        var url = "member/qus-two"; //这里设置跳转question2页面的路径
        postForQus(obj1, url);
    });
    $("#qustion2Submit").on('click', function() {
        var url = "member/qus-three"; //这里设置跳转question3页面的路径
        postForQus(obj2, url);
    });
    $("#qustion3Submit").on('click', function() {
        var url = "member/home"; //这里设置跳转questionResult页面的路径
        postForQus(obj3, url);
    });
}

function postForQus(obj, url) {

    if (!isRequst && obj.answer) { //
        isRequst = true;
        $.post("add-answer", obj, function(data) {

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