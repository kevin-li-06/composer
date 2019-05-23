<?php

namespace common\components;

use Yii;
use common\models\JsTicket;
use backend\components\Wechat;
use frontend\components\Log;
/**
 * @author Aaaron.luo
 * jssd后台配置文件
 */
class Jssdk
{
    //得到签证等配置信息
    public static function getSignPackage() 
    { 
        $jsapiTicket = self::getJsTicket();
        // 注意 URL 一定要动态获取，不能 hardcode.
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $timestamp = time();
        $nonceStr = self::createNonceStr();

        // 这里参数的顺序要按照 key 值 ASCII 码升序排序
        $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";
       // Log::debug('string', $string);

        $signature = sha1($string);
       // Log::debug('signature', $signature);
//         $appId = "wx0e2e69eda478ec0e";
        $appId = "wxeca2e361be1de14c";
        

        $signPackage = array(
            "appId"     => $appId,
            "nonceStr"  => $nonceStr,
            "timestamp" => $timestamp,
            "url"       => $url,
            "signature" => $signature,
            "rawString" => $string
        );
        return $signPackage; 
    }

    //获取js ticket
    public static function getJsTicket()
    {
        $time = JsTicket::find()->select('expiration_time,ticket')->orderBy('id DESC')->limit(1)->asArray()->one();
        if($time['expiration_time'] > time()){
            return $time['ticket'];
        }else{
            $ticket = Wechat::get_js_ticket();
            // $this->bp($ticket);exit;
            if($ticket['errmsg'] == "ok"){
                $model = new JsTicket();
                $model->expiration_time = time()+7000;
                $model->ticket = $ticket['ticket'];
                $model->save();
                return $ticket['ticket'];
            }else{
                return false;
            }
        }  
    }

    //得到16位随机字符串
    private static function createNonceStr($length = 16) 
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }
}