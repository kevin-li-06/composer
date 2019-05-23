<?php

namespace frontend\components;

use yii;
use yii\base\ErrorException;
use frontend\models\Token;


class Auth 
{
    private $data;
    private $access_token;
    private $response = ['status' => 'success', 'message' => 'validate successfully'];

    public function __construct($controller, $action, $data, $access_token = null)
    {        
        $this->setData($data);
        $this->setAccessToken($access_token);       
        $this->validate($controller, $action, $this->data); // 常规验证
    }

    protected function validate($controller, $action, $data)
    {
        $flag = true; // 默认通过access_token 验证
    //    echo $this->access_token;die;
        if ($controller !== 'token') // access_token 相关的API 都不需要验证access_token
        {   
            if (!$this->validateAccessToken($this->access_token)) $flag = false;
        }

        if ($flag) // 常规验证
        {
            $map = $this->validateMap($controller, $action);

            foreach ($map as $key => $val) 
            {           
                $validateMethod = 'validate'. ucwords(end($val));  
                
                if (!$this->$validateMethod(array_shift($val), $data)) break;                   
            }
        }
    }

    protected function setData($data)
    {
        return $this->data = $data;
    }

    protected function setAccessToken($access_token)
    {
        return $this->access_token = $access_token;
    }

    public function getResponse()
    {
        return $this->response;
    }

    // acccess token 验证
    protected function validateAccessToken($access_token)
    {
        if (!$access_token) 
        {
            $this->response = ['status' => 'error', 'message' => 'Access Token is required'];
            return false;
        }
        else 
        {
            // token 是否存在
            $token_model = Token::find()->andwhere(['or', 'access_token = "'.$access_token.'"', 'alternate_token = "'.$access_token.'"'])->one();
            
            if ($token_model) // token 存在
            {
                // 判断当前用的token 是access_token 还是alternate_token
                if ($token_model->access_token == $access_token) 
                {
                    if ($token_model->access_expires_at < time())
                    {
                        $this->response = ['status' => 'error', 'message' => 'Access Token is expired'];
                        return false;
                    }
                }

                if ($token_model->alternate_token == $access_token) 
                {
                    if ($token_model->alternate_expires_at < time())
                    {
                        $this->response = ['status' => 'error', 'message' => 'Access Token is expired'];
                        return false;
                    }
                }

                return true;
            } 
            else 
            {
                $this->response = ['status' => 'error', 'message' => 'Access Token is invalid'];
                return false;
            }
        }
    }

    // 验证必填字段
    protected function validateRequired($criteria, $data)
    {        
        foreach ($criteria as $key => $val)
        {            
            if (!array_key_exists($val, $data)) 
            {
                $this->response = ['status' => 'error', 'message' => 'Parameter '. $val .' is required'];
                return false;
            } 
            else 
            {
                if (empty($data[$val])) 
                {
                    $this->response = ['status' => 'error', 'message' => 'Parameter '. $val .' cannot be empty'];
                    return false;
                }
            }
        }
        return true;
    }

    // 验证字符类型
    protected function validateString($criteria, $data)
    {
        
        foreach ($criteria as $key => $val) 
        {   
            if (isset($ata[$val]) && !empty($data[$val]))
            {
                if (!is_string($data[$val])) 
                {
                    $this->response = ['status' => 'error', 'message' => 'Parameter '. $val .' is not a string'];
                    return false;
                }
            }
            
        }        
        return true;
    }

    // 验证日期时间类型
    protected function validateDateTime($criteria, $data)
    {
        
        foreach ($criteria as $key => $val) 
        {   
            if (strtotime(date('Y-m-d H:i:s', strtotime($data[$val]))) !== strtotime($data[$val]))
            {
                $this->response = ['status' => 'error', 'message' => 'Parameter '. $val .' is not a dateTime'];
                return false;
            }
        }        
        return true;
    }

    // 验证SET类型 ==> 尚未完成
    protected function validateSet($criteria, $data)
    {          
        $flag = false; // 默认未通过验证
        foreach ($criteria as $key => $val) 
            if (array_key_exists($val, $data))  $flag = true; 

        if (!$flag) 
        {
            $this->response = ['status' => 'error', 'message' => 'At lease one Parameter of '. join(',', $criteria) .' is required'];
            return false;
        } 
        return true;
    }

    protected function validateMap($controller, $action)
    { 
        return  Yii::$app->params['api_hash'][$controller][$action];
    }   
}