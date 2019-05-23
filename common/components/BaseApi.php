<?php

namespace common\components;

use yii;
use yii\base\ErrorException;

/**
 * simply api function like curl get post and so on...
 */
class BaseApi 
{
    /**
     * array convert to json
     * @param array $data
     * @return string $json | false
     */
    public static function toJSON($data)
    {
        if (is_array($data)) {
			$json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
			return $json;
        } else {
            return false;
        }
    }

    /**
     * json convert to array
     * @param array $json
     * @return string $data | false
     */
    public static function parseJSON($json)
    {
        if (is_string($json)) {
			$data = json_decode($json, true);
			return $data;
        } else {
            return false;
        }
    }

    /**
	 * GET Request
	 * @param string $url
	 * @param int $connect_timeout
	 * @param int $timeout
	 * @param boolean $ssl
	 * @return mix $re
	 */
	public static function get($url, $data = null, $connect_timeout = 10, $timeout = 10, $ssl = false)
	{		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		// \backend\components\Log::debug('url', $url);

		if (!$ssl) {
			curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		}
		
		// post data
		if (!empty($data)) {
			curl_setopt($ch, CURLOPT_POST, TRUE);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		}
		
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $connect_timeout);
		curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		$re = curl_exec($ch);
		// \backend\components\Log::debug('re', $re);
//        echo "<pre/>";
//        var_dump($re);die;
		// curl error breakpoint
		if ($re === false) {
			throw new ErrorException('Curl Execute Error: [error code: ' . curl_errno($ch) . ', error message:  ' . curl_error($ch) . ']');
		}
		
		curl_close($ch);

		return $re;
	}
	
	/**
	 * POST Request
	 * @param string $url
	 * @param mix $data
	 * @param int $connect_timeout
	 * @param int $timeout
	 * @param boolean $ssl
	 * @return mix $re
	 */
	public static function post($url, $data, $connect_timeout = 10, $timeout = 10, $ssl = false)
	{		
		if (empty($data)) {
			throw new ErrorException('Curl Post Error: [no post data]');
		}

		return static::get($url, $data, $connect_timeout, $timeout, $ssl);
	}

	/**
	 * array convert to xml
	 * @param array $arr
	 * @return string $xml | false
	 */
	public static function toXML($arr)
	{
		$xml = "<root>"; 
		foreach ($arr as $key=>$val){ 
			if(is_array($val)){ 
				$xml.="<".$key.">".toXML($val)."</".$key.">"; 
			}else{ 
				$xml.="<".$key.">".$val."</".$key.">"; 
			} 
		} 
		$xml.="</root>"; 
		return $xml; 
	}

	/**
     * xml convert to arry
     * @param array $xml
     * @return string $values | false
     */
	public static function  xmlToArray($xml)
	{    
		//禁止引用外部xml实体
		libxml_disable_entity_loader(true);
		$values = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);        
		return $values;
	}
}