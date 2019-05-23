<?php

namespace backend\components;

use Yii;
use common\components\BaseController;
use backend\components\Api;
use backend\components\Wechat;
use backend\components\Loyalty;

class BaseAdminController extends BaseController
{
    public $defaultAction = 'index';

    // public $layout = 'admin';

    public function init()
    {
        if (!Yii::$app->user->isGuest) {
            $id_role = Yii::$app->user->identity->id_role;
            if ($id_role == 2) {
                $this->layout = 'jebsen';
            } elseif ($id_role == 3) {
                $this->layout = 'staff';
            } else {
                $this->layout = 'admin';
            }
        } else {
            $this->layout = 'admin';
        }
    }

}
