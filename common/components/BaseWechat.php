<?php

namespace common\components;

use yii;
use yii\base\ErrorException;
use common\components\BaseApi;
use backend\components\Log;
use common\models\AccessToken;

/**
 * all wechat api config and functions
 */
class BaseWechat
{
    const XGATE_COMPONENT_APPID = 'wx5aec7706b086aa5b'; // XGATE 第三方平台

    const APPID = 'wxeca2e361be1de14c'; // 昊祥科技

//     const APPID = 'wx0e2e69eda478ec0e'; // liber服务号appid

    const APPSECRET = '28e9d3fb572104e1bef285d20fa760c5'; // 昊祥科技

    /**
     * return wechat config or configs
     * @param string @config
     * @return string or array or false
     */
    public static function config($config = null)
    {
        $appid = static::APPID; // 昊祥科技
        if ($config == 'appid') {
            return $appid;
        }
        
        $isThirdParty = Yii::$app->params['isThirdParty'];
        if ($isThirdParty) {
            // ------------------------------------------------------------------
            // Get Wechat Thrid Party AccessToken API
            // ------------------------------------------------------------------
            // Url https://wcapi.xgatecorp.com/core/api/token?component_app_id={thridPartyAppId}&app_id={wechatAppId}
            // @method Get
            // @author Jason Lee
            // https://dev.xgate.com/issues/3569?tab=history_comments
            // ------------------------------------------------------------------
            $url = 'https://wcapi.xgatecorp.com/core/api/token?component_app_id=' . static::XGATE_COMPONENT_APPID . '&app_id=' . $appid;
            $re = BaseApi::get($url);
            $re = BaseApi::parseJSON($re);
            if (empty($re) || ($re['errmsg'] != 'ok')) {
                throw new ErrorException('Error: ' . $re['errmsg']);
            }
            $access_token = $re['access_token'];
            $component_access_token = $re['component_access_token'];
        } else {
            $access_token = static::access_token();
            $component_access_token = '';
        }
        
        $configs = [
            'appid' => $appid,
            'access_token' => $access_token,
            'component_appid' => static::XGATE_COMPONENT_APPID,
            'component_access_token' => $component_access_token,
        ];

        // print_r($configs);exit;

        return is_null($config) ? $configs : $configs[$config];
    }

    /**
     * magic static call wechat api
     */
    public static function __callStatic($name, $arguments)
    {
        // validate the api exists
        $apiMap = static::apiMap();

        if (!array_key_exists($name, $apiMap)) {
            throw new ErrorException('Error: the static method [ Wechat::' . $name . '() ] don`t exists.');
        } else {
            $api = $apiMap[$name];
        }

        // validate arguments
        if (isset($arguments[0])) {
            $validate_result = static::validate($name, $arguments);
            $data = $arguments[0];
        } else {
            $validate_result = true;
            $data = null;
        }
        
        // execute api call
        if ($validate_result === true) {
            return static::call($api, $data);
        }
    }

    /**
     * validate the params
     * @param string $name
     * @param array $data
     * @return string $param or boolean true
     */
    public static function validate($name, $arguments)
    {
        // only simple validate because wechat api provide validate
        if (empty($arguments[0])) {
            throw new ErrorException('Error: post data is **empty** when call [ Wechat::' . $name . '() ]');
        }

        if (!is_array($arguments[0])) {
            throw new ErrorException('Error: post data type must be **array** when call [ Wechat::' . $name . '() ]');
        }

        return true;
    }

    /**
     * use Api component call wechat api
     * @param array $api
     * @param array | null $data
     */
    public static function call($api, $data = null)
    {
        $access_token = static::config('access_token');
        
        $url = $api['url'];
        if (strstr($url, '?')) {
            $url .= '&access_token=' . $access_token;
        } else {
            $url .= '?access_token=' . $access_token;
        }

        if ($api['method'] == 'post') {
            $re = BaseApi::post($url, BaseApi::toJSON($data));
        } else {
            // use vsprinf to inject data into GET url
            if (is_null($data)) {
                $re = BaseApi::get($url);
            } else {
                $url = vsprintf($url, $data);
                $re = BaseApi::get($url);
            }
        }
        if ($re) {
            $re = BaseApi::parseJSON($re);
        } else {
            $re = false;
        }

        return $re;

        // 自行在业务流程中判断Api的返回结果
        // 不抛出错误，因为业务流程中我们需要在报错信息
        // if (isset($re['errmsg']) && ($re['errmsg'] != 'ok')) {
        //     throw new ErrorException('Wechat API Error, Error code: [' . $re['errcode'] . '], Error msg: [' . $re['errmsg'] . '], When call [' . $url . ']');
        // } else {
        //     return $re;
        // }
    }

    /**
     * 获取第一方access_token
     */
    public static function access_token()
    {
        $model = AccessToken::find()->orderBy('id desc')->one();
        if (isset($model) && ($model->expires_in > time())) {
            $access_token = $model->access_token;
        } else {
            $appid = static::APPID;
            $secret = static::APPSECRET;
            $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $appid . '&secret=' . $secret;
            $re = BaseApi::get($url);
            $re = BaseApi::parseJSON($re);
            // print_r($re);exit;
            if (isset($re['access_token'])) {
                $model = new AccessToken();
                $model->access_token = $re['access_token'];
                $model->expires_in = time()+7000;
                $re = $model->save();
                $access_token = $model->access_token;
            } else {
                return false;
            }
        }

        return $access_token;
    }

    /**
     * 强制刷新第一方access_token
     */
    public static function force_access_token()
    {
        $appid = static::APPID;
        $secret = static::APPSECRET;
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $appid . '&secret=' . $secret;
        $re = BaseApi::get($url);
        $re = BaseApi::parseJSON($re);
        // print_r($re);exit;
        if (isset($re['access_token'])) {
            $model = new AccessToken();
            $model->access_token = $re['access_token'];
            $model->expires_in = time()+7000;
            $re = $model->save();
            $access_token = $model->access_token;
            return $access_token;
        } else {
            return false;
        }
    }

    /**
     * wechat api map
     */
    public static function apiMap()
    {
        $apiMap = [
            'ips' => [
                'url' => 'https://api.weixin.qq.com/cgi-bin/getcallbackip',
                'method' => 'get',
            ],
            'menu_get' => [
                'url' => 'https://api.weixin.qq.com/cgi-bin/menu/get',
                'method' => 'get',
            ],
            'menu_set' => [
                'url' => 'https://api.weixin.qq.com/cgi-bin/menu/create',
                'method' => 'post',
            ],
            'userinfo' => [
                'url' => 'https://api.weixin.qq.com/cgi-bin/user/info?openid=%s&lang=zh_CN',
                'method' => 'get',
            ],
            // 生成微信公众号二维码 https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1443433542
            'qrcode_create' => [
                'url' => 'https://api.weixin.qq.com/cgi-bin/qrcode/create',
                'method' => 'post',
            ],
            // 图文分析数据接口 https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421141084
            'get_article_summary' => [
                'url' => 'https://api.weixin.qq.com/datacube/getarticlesummary', // 获取图文群发每日数据
                'method' => 'post',
            ],
            'get_article_total' => [
                'url' => 'https://api.weixin.qq.com/datacube/getarticletotal', // 获取图文群发总数据
                'method' => 'post',
            ],
            'get_user_read' => [
                'url' => 'https://api.weixin.qq.com/datacube/getuserread', // 获取图文统计数据
                'method' => 'post',
            ],
            'get_user_read_hour' => [
                'url' => 'https://api.weixin.qq.com/datacube/getuserreadhour', // 获取图文统计分时数据
                'method' => 'post',
            ],
            'get_user_share' => [
                'url' => 'https://api.weixin.qq.com/datacube/getusershare', // 获取图文分享转发数据
                'method' => 'post',
            ],
            'get_user_share_hour' => [
                'url' => 'https://api.weixin.qq.com/datacube/getusersharehour', // 获取图文分享转发分时数据
                'method' => 'post',
            ],
            //查看指定文章的评论数据
            'comment_list' => [
                'url' => 'https://api.weixin.qq.com/cgi-bin/comment/list',
                'method' => 'post',
            ],
            //打开图文的留言功能
            'comment_open' => [
                'url' => 'https://api.weixin.qq.com/cgi-bin/comment/open',
                'method' => 'post',
            ],
            // 获取永久素材 https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1444738730
            'material_get' => [
                'url' => 'https://api.weixin.qq.com/cgi-bin/material/get_material',
                'method' => 'post',
            ],
            // 获取素材列表 https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1444738734
            'material_batch_get' => [
                'url' => 'https://api.weixin.qq.com/cgi-bin/material/batchget_material',
                'method' => 'post',
            ],
            // 获取素材总数 https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1444738733
            'material_count' => [
                'url' => 'https://api.weixin.qq.com/cgi-bin/material/get_materialcount',
                'method' => 'get',
            ],
            // 获取验签秘钥API
            'get_signkey' => [
                'url' => 'https://api.mch.weixin.qq.com/sandboxnew/pay/getsignkey',
                'method' => 'post',
            ],
            // 统一下单API
            'unified_order' => [
                'url' => 'https://api.mch.weixin.qq.com/sandboxnew/pay/unifiedorder',
                'method' => 'post',
            ],
            // 微信下载对账单API
            'download_bill' => [
                'url' => 'https://api.mch.weixin.qq.com/sandboxnew/pay/downloadbill',
                'method' => 'post',
            ],
            // 微信长链接转换短链接API
            // 微信支付
            'short_url' => [
                'url' => 'https://api.mch.weixin.qq.com/tools/shorturl',
                'method' => 'post',
            ],
            'short' => [
                'url' => 'https://api.weixin.qq.com/cgi-bin/shorturl',
                'method' => 'post',
            ],
            // 拉取会员卡概况数据接口
            'membercard_all' => [
                'url' => 'https://api.weixin.qq.com/datacube/getcardmembercardinfo',
                'method' => 'post',
            ],
            // 删除会员卡
            'card_delete' => [
                'url' => 'https://api.weixin.qq.com/card/delete',
                'method' => 'post'
            ],
            // 创建会员卡 https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1466494654_K9rNz
            'card_create' => [
                'url' => 'https://api.weixin.qq.com/card/create',
                'method' => 'post',
            ],
            // 更新会员卡
            'card_update' => [
                'url' => 'https://api.weixin.qq.com/card/update',
                'method' => 'post',
            ],
            // 查询会员卡
            'card_get' => [
                'url' => 'https://api.weixin.qq.com/card/get',
                'method' => 'post',
            ],
            // 获取用户已领取的会员卡
            'card_user_list' => [
                'url' => 'https://api.weixin.qq.com/card/user/getcardlist',
                'method' => 'post',
            ],
            // 激活会员卡
            'card_activate' => [
                'url' => 'https://api.weixin.qq.com/card/membercard/activate',
                'method' => 'post',
            ],
            // 设置激活会员卡字段 https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1451025283
            'card_activate_form' => [
                'url' => 'https://api.weixin.qq.com/card/membercard/activateuserform/set',
                'method' => 'post',
            ],
            // 获取会员卡用户信息 [已激活]
            'card_userinfo_get' => [
                'url' => 'https://api.weixin.qq.com/card/membercard/userinfo/get',
                'method' => 'post',
            ],
            // 获取会员卡用户信息 [未激活Temp信息]
            'card_temp_userinfo_get' => [
                'url' => 'https://api.weixin.qq.com/card/membercard/activatetempinfo/get',
                'method' => 'post',
            ],
            // 设置支付即会员规则
            // 目前该功能仅支持微信支付商户号主体和制作会员卡公众号主体一致的情况下配置，否则报错
            // 开发者可以登录“公众平台”-“公众号设置”、“微信支付商户平台首页”插卡企业主体信息是否一致
            'paygiftcard_create' => [
                'url' => 'https://api.weixin.qq.com/card/paygiftcard/add',
                'method' => 'post',
            ],
            // 删除支付即会员规则
            'paygiftcard_delete' => [
                'url' => 'https://api.weixin.qq.com/card/paygiftcard/delete',
                'method' => 'post',
            ],
            // 查看支付即会员规则
            'paygiftcard_get' => [
                'url' => 'https://api.weixin.qq.com/card/paygiftcard/getbyid',
                'method' => 'post',
            ],
            // code解码接口 https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1451025239
            'code_decrypt' => [
                'url' => 'https://api.weixin.qq.com/card/code/decrypt',
                'method' => 'post',
            ],
            // 获取js_ticket
            'get_js_ticket' => [
                'url' => 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi',
                'method' => 'get',
            ],
        ];

        return $apiMap;
    }

}