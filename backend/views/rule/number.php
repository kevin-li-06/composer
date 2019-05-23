<?php

use yii\helpers\Url;
?>
<form action="<?php echo Url::to(['rule/test-view']); ?>" method="post">
  <div class="form-group">
    <label for="exampleInputEmail1">模拟次数</label>
    <input type="text" class="form-control" id="moni" placeholder="请输入模拟次数" name="count">
  </div>
  <input type="hidden" value="<?php echo Yii::$app->getRequest()->getCsrfToken(); ?>" name="_csrf-backend" />  
  <input type="hidden" value="<?php echo $rule_id; ?>" name="rule_id" />  
  <button type="submit" class="btn btn-default">开始模拟</button>
</form>