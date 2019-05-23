<?php

use yii\helpers\Url;

?>

<form class="form" action="<?= Url::to(['install/login']) ?>" method="post">
    <div class="form-group">
        <label for="code">身份验证</label>
        <input type="text" class="form-control" id="code" name="code" placeholder="请验证您的身份" required>            
    </div>
    <input name="_csrf-backend" type="hidden" id="_csrf-backend" value="<?= Yii::$app->request->csrfToken ?>">
    <button type="submit" class="btn btn-default">验证</button>
</form>
