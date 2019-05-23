<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;


$this->title = '会员签到列表';
$this->params['breadcrumbs'][] = $this->title;

$signin_count = count($signinData['signinList']);

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
   
.sign-date > .signed {
	background: #c33838;
	border:1px solid #c33838;
}    


</style>


<div>
	<div class="sign-in">
		<span style="font-style: 24px; color: red"><?=ucfirst($signinData['username'])?></span>,
		已经连续签到 <span style="font-style: 24px; color: red"><?= $signin_count;?></span> 天, 
		再签到 <span style="font-style: 24px; color: red"><?= (7 - $signin_count)?></span>
		天就可以抽大奖啦!
	</div>
	<div class="sign-date">
	<?php 

		foreach ($calendar as $date) {
			if (in_array($date, array_column($signinData['signinList'],'signin_date'))) {
				echo "<p class='signed' data-date-value='".date('Y-m-d',$date)."'>".date('m-d',$date)."</p>";
			} else {
				echo "<p data-date-value='".date('Y-m-d',$date)."'>".date('m-d',$date)."</p>";
			}
		}
	?>	

	</div>

</div>

