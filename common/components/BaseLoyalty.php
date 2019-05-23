<?php

namespace common\components;

use yii;
use yii\base\ErrorException;
use common\components\BaseApi;
use common\components\BaseWechat;

/**
 * all loyalty api config and functions
 */
class BaseLoyalty 
{
    /**
     * return the loyalty api basic config parameters
     * @return array or string
     */
    public static function config($config = null)
    {           
        // ------------------------------------------------------------------
        // Get Loyalty Config by appid and access_token
        // ------------------------------------------------------------------
        // https://proxcrm.xgate.com/front/index.php/site/getTenantWechatConfig?tenantId=[tenantId]&appid=[appId]&accesstoken=[accessToken]
        // @method Get
        // @author Raymond NG
        // https://dev.xgate.com/issues/3568?tab=history_comments
        $wechat_configs = BaseWechat::config();
        $url = 'https://proxcrm.xgate.com/front/index.php/site/getTenantWechatConfig?tenantId=[tenantId]&appid=' . $wechat_configs['appid'] . '&accesstoken=' . $wechat_configs['access_token'];
        $re = BaseApi::get($url);
        $re = BaseApi::parseJSON($re);
        $loyalty_url = $re['loyalty_url'];
        $loyalty_accountid = $re['loyalty_accountid'];
        $loyalty_username = $re['loyalty_username'];
        $loyalty_password = $re['loyalty_password'];
        $tenant_id = $re['tenant_id'];
        $webform_url = $re['webform_url'];
        $subdomain = $re['subdomain'];

        // validate basic parameters
//        if (filter_var($loyalty_url, FILTER_VALIDATE_URL) === false) {
//            throw new ErrorException('Error: the loyalty api url [' . $loyalty_url . '] is not validate');
//        }
//
//        if (empty($loyalty_accountid) || empty($loyalty_username) || empty($loyalty_password)) {
//            throw new ErrorException('Error: the loyalty api basic param is null');
//        }

        //正式 地址
//        $loyalty_url = 'http://202.177.204.24/';
//        $loyalty_accountid = 'JSelect';
//        $loyalty_username = 'admin@chn.jselect.com';
//        $loyalty_password = '59jfl438fc';
        //海港城地址
        $loyalty_url = 'http://loyalty-uat.xgate.com:8080/index.php';
        $loyalty_accountid = 'demo';
        $loyalty_username = 'admin@harbourcity.com';
        $loyalty_password = 'xg1234';
        $configs = [
            'loyalty_url' => $loyalty_url,
            'authentication' => ['account_id' => $loyalty_accountid, 'username' => $loyalty_username, 'password' => $loyalty_password],
            'tenant_id' => $tenant_id,
            'webform_url' => $webform_url,
            'subdomain' => $subdomain, 
        ];

        return is_null($config) ? $configs : $configs[$config];
    }

    /**
     * magic static call loyalty api
     */
    public static function __callStatic($name, $arguments)
    {
        // 检查Call的方法是否在API Maps
        $apiMap = static::apiMap();

        if (!array_key_exists($name, $apiMap)) {
            throw new ErrorException('Error: the static method [ Loyalty::' . $name . '() ] don`t exists.');
        } else {
            $api = $apiMap[$name];
        }

        
        // 如果不需要传post字段，则allowEmpty为true
        if (isset($api['allowEmpty']) && $api['allowEmpty'] == true) {
            $validate_result = true;
            $data = [];
        } else {
            // 如果是post请求，需要验证post字段是否正确
            if ($api['method'] == 'post') {
                $validate_result = static::validate($api, $arguments);
                $data = $arguments[0];
            } else {
                // get请求
                $validate_result = true;
                $data = [];
            }
        }
        
        // 执行API Call
        if ($validate_result === true) {
            return static::call($api, $data);
        }
    }

    /**
     * validate the params
     * @param string $api
     * @param array $data
     * @return string $param or boolean true
     */
    public static function validate($api, $arguments)
    {
        if (empty($arguments[0])) {
            throw new ErrorException('Error: post data is **empty** when call [ Loyalty::' . $api['controller'] . '_' . $api['action'] . '() ]');
        }
        $data = $arguments[0];

        $params = $api['params'];

        // build a new array without "*"
        $match = [];
        foreach ($params as $param) {
            $default_param = strstr($param, '*', true);
            if ($default_param !== false) {
                $match[] = $default_param;
            } else {
                $match[] = $param;
            }
        }

        // validate the data unavailable parameter and null parameter
        foreach ($data as $key => $value) {
            if (!in_array($key, $match)) {
                throw new ErrorException('Error: the param: [' . $key . '] is **unavailable** when call [ Loyalty::' . $api['controller'] . '_' . $api['action'] . '() ]');
            }
            if ($value === '' || $value === null || $value === false) {
                throw new ErrorException('Error: the param: [' . $key . '] is **null** when call [ Loyalty::' . $api['controller'] . '_' . $api['action'] . '() ]');
            }
        }

        // validate the reuired parameter
        foreach ($params as $param) {
            $param = strstr($param, '*', true);
            if ($param !== false) {
                if (empty($data[$param])) {
                    throw new ErrorException('Error: the param: [' . $param . '] is **required** when call [ Loyalty::' . $api['controller'] . '_' . $api['action'] . '() ]');
                }
            }
        }

        return true;
    }

    /**
     * use Api component call loyalty api
     * @param array $api
     * @param array | null $data
     */
    public static function call($api, $data)
    {
        $configs = static::config();

        $data = array_merge($data, $configs['authentication']);
        
        // 判断连接的最后是否带 "/"
        if (substr($configs['loyalty_url'], -1) == '/') {
            $url = $configs['loyalty_url'] . $api['controller'] . '/' . $api['action'];
        } else {
            $url = $configs['loyalty_url'] . '/' . $api['controller'] . '/' . $api['action'];
        }
//        echo "<pre/>";
//        var_dump($data);die;
        if ($api['method'] == 'post') {

            $re = BaseApi::post($url, $data);

        } else {
            $re = BaseApi::get($url);
        }
        
        if ($re) {
            $re = BaseApi::parseJSON($re);
        } else {
            $re = false;
        }
        
        return $re;

        // 自行在业务流程中判断Api的返回结果
        // 不抛出错误，因为业务流程中我们需要在报错信息
        // if (isset($re['status']) && $re['status'] == 'error') {
            // throw new ErrorException('Loyalty API Error, Error msg: [' . $re['message'] . '], When call [' . $url . ']');
        // } else {
            // return $re;
        // }
    }

    /**
     * loyalty api map
     * 必填参数在后面加上*
     * 如果没有post参数，则添加 'allowEmpty' => true
     */
    public static function apiMap()
    {
        $apiMap = [
            'customer_search' => [
                'controller' => 'customer',
                'action' => 'search',
                'method' => 'post',
                'params' => ['mobile', 'code', 'first_name', 'last_name', 'email', 'phone', 'wechat_openid','mobilePhone'],
                'allowEmpty' => true,
            ],
            'customer_list' => [
                'controller' => 'customer',
                'action' => 'list_customers',
                'method' => 'post',
                'params' => ['mobile', 'from_date', 'offset',  'limit', 'code', 'first_name', 'last_name', 'email', 'phone', 'wechat_openid','mobilePhone'],
//                'allowEmpty' => true,
            ],
            'customer_get' => [
                'controller' => 'customer',
                'action' => 'get',
                'method' => 'post',
                'params' => ['code', 'email', 'wechat_openid','mobile'],
            ],
            'customer_balance' => [
                'controller' => 'customer',
                'action' => 'customer_balance',
                'method' => 'post',
                'params' => ['campaign_id*', 'code*', 'card_number'],
            ] ,
            'customer_create' => [
                'controller' => 'customer',
                'action' => 'create',
                'method' => 'post',
                'params' => ['code*', 'first_name', 'last_name', 'phone', 'email', 'birthday', 'address1', 'wechat_openid', 'gender'],
            ],
            'customer_update' => [
                'controller' => 'customer',
                'action' => 'update',
                'method' => 'post',
                'params' =>['code*', 'card_number', 'last_name', 'email', 'birthday', 'mobile', 'phone', 'gender', 'address1', 'address2', 'address3', 'output', 'wechat_openid', 'birth_yyyy', 'birth_mm', 'birth_dd'],
            ],
            'customer_tnx_detail' => [
                'controller' => 'customer',
                'action' => 'gettransactionbycampaign',
                'method' => 'post',
                'params' => ['campaign_id*', 'code*', 'pagesize*', 'pageno*', 'type'],
            ],
            'customer_delete' => [
                'controller' => 'customer',
                'action' => 'delete',
                'method' => 'post',
                'params' => ['code*'],
            ],
            'campaign_new' => [
                'controller' => 'campaign',
                'action' => 'new_campaign',
                'method' => 'post',
                'params' => ['campaign_type*', 'campaign_name*', 'points_ratio*', 'output'],
            ],
            'campaign_list' => [
                'controller' => 'campaign',
                'action' => 'list_campaigns',
                'method' => 'post',
                'params' => [],
                'allowEmpty' => true,
            ],
            'coupon_list' => [
                'controller' => 'coupon',
                'action' => 'getCoupon',
                'method' => 'post',
                'params' => ['campaign_id*', 'customer_id', 'code', 'card_number', 'detail'],
            ],
            'coupon_detail' => [
                'controller' => 'coupon',
                'action' => 'getCouponGroup',
                'method' => 'post',
                'params' => ['campaign_id*', 'coupongroup_id', 'identifier'],
            ]
        ];
        return $apiMap;
    }
}