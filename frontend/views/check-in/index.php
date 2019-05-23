<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;


$checkin_count = count($checkinData['checkinList']);
 ?>
<style type="text/css">
.sign-in {font-size: 30px;text-align: center;}
.sign-date >p {    
	display: inline-block;
    background: #807b7b;
    float: left;
    margin: 10px;
    border-radius: 17px;
    border: 1px solid #807b7b;
    height: 80px;
    width: 80px;
    line-height: 80px;
    text-align: center;
    font-size: 20px;}

.sign-date >p:hover {
	cursor: pointer;
	background: #c33838;
	border:1px solid #c33838;
}    
.sign-date > .signed {
	background: #c33838;
	border:1px solid #c33838;
}    


</style>

<div>
	<div class="sign-in">
		<span style="font-style: 24px; color: red"><?=ucfirst($checkinData['username'])?></span>,
		你已经连续签到 <span style="font-style: 24px; color: red"><?= $checkin_count;?></span> 天, 
		再签到 <span style="font-style: 24px; color: red"><?= (7 - $checkin_count)?></span>
		天就可以抽大奖啦!
	</div>
	<div class="sign-date">
	<?php 
		foreach ($calendar as $date) {
			if (in_array($date, array_column($checkinData['checkinList'],'date'))) {
				echo "<p class='signed' data-date-value='".date('Y-m-d',$date)."'>".date('m-d',$date)."</p>";
			} else {
				echo "<p data-date-value='".date('Y-m-d',$date)."'>".date('m-d',$date)."</p>";
			}
		}
	?>	
	</div>

</div>
<script src="http://libs.baidu.com/jquery/2.1.4/jquery.min.js"></script>
<script type="text/javascript">
var _csrf = "<?=yii::$app->request->csrfToken?>";

$('.sign-date > p').click(function(){
	var thisDom = $(this);
	var checkinTime = Date.parse(new Date());
	var checkinDate = Date.parse(new Date($(this).attr('data-date-value')));
	var user_id = <?=$checkinData['id']?>;

	if (!thisDom.hasClass('signed')) {
		if (checkinTime - checkinDate < 24*3600*1000 && checkinDate < checkinTime) {
			$.post("<?=Url::to(['check-in/ajax-do-sign'])?>", {user_id:user_id,date:checkinDate,_csrf:_csrf}, function(re){
								
				if (re=='success') {
					thisDom.addClass('signed');
				} else {
					return alert('你今天已经签到了,明天再来吧!O(∩_∩)O哈哈~');
				}
			},'json')
		} else {
			return alert('你只能签到今天( ⊙ o ⊙ )！');
		}
	} else {
		return alert('你今天已经签到了,明天再来吧!O(∩_∩)O哈哈~');
	}

	
	

	
	
})
</script>