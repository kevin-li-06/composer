<?php

namespace common\components;

use Yii;
use yii\web\Controller;
use yii\web\Response;

class BaseController extends Controller
{

    // public function init()
    // {
    //     exit;
    //     $link = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxeca2e361be1de14c&redirect_uri=https%3A%2F%2Fwcapi.xgatecorp.com%2Fwechat%2Fauth%2Furl&response_type=code&scope=snsapi_base&state=https://campaigns.xgatecorp.com&component_appid=wx5aec7706b086aa5b#wechat_redirect';
    //     return $this->redirect($link);exit;
    // }

    /**
     * 断点调试
     * @param mix $data
     * @param boolean $break
     */
    public function bp($data, $break = true)
    {
        echo "<pre>";
        if (is_string($data)) echo $data; else print_r($data);
        echo "</pre>";
        if ($break) exit;
    }

    /**
     * 调用session
     * @param string @key
     * @param string @value
     */
    public function session($key, $value = null)
    {
        $session = Yii::$app->session;

        // open session
        if (!$session->isActive) {
            $session->open();
        }

        if ($session->has($key)) {

            // return the existing key when value is null
            if (is_null($value)) {
                return $session->get($key);
            }

            // replace the old value
            $old = $session->get($key);
            if ($value != $old) {
                $session->set($key, $value);
            }
        } else {
            $session->set($key, $value);
        }
    }

    public function init()
    {
        Yii::$app->user->setReturnUrl(Yii::$app->request->referrer);
    }

}
