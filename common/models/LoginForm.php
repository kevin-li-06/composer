<?php
namespace common\models;

use Yii;
use yii\base\Model;

/**
 * Login form
 */
class LoginForm extends Model
{
    public $username;
    public $password;
    public $mobile_hash;
    public $openid;
    public $rememberMe = false;

    private $_user;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // username and password are both required
            // [['username', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();                 
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect username or password.');
            }
        }
    }

    // 验证手机号
    public function validateMobile()
    {
        $user = $this->getUserByMobilehash();                 
        if (!$user) {
            return false;
        }
        return true;
    }

     // 验证openid
     public function validateOpenid()
     {
         $user = $this->getUserByOpenid();                 
         if (!$user) {
             return false;
         }
         return true;
     }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return bool whether the user is logged in successfully
     */
    public function login()
    { 
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
        } else {
            return false;
        }
    }

    /**
     * Logs in a user using the provided mobile hash value.
     *
     * @return bool whether the user is logged in successfully
     */
    public function loginFromMobile()
    {        
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUserByMobilehash(), $this->rememberMe ? 3600 * 24 * 30 : 0);
        } else {
            return false;
        }
    }

    /**
     * Logs in a user using the provided wechat openid value.
     *
     * @return bool whether the user is logged in successfully
     */
    public function loginFromWechat()
    {        
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUserByOpenid(), $this->rememberMe ? 3600 * 24 * 30 : 0);
        } else {
            return false;
        }
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    protected function getUser()
    {       
        if ($this->_user === null) {
            $this->_user = User::findByUsername($this->username);
        }

        return $this->_user;
    }

    /**
     * Finds user by [[mobile hash value]]
     *
     * @return User|null
     */
    protected function getUserByMobilehash()
    {
        if ($this->_user === null) {
            $this->_user = User::findByMobilehash($this->mobile_hash);
        }

        return $this->_user;
    }

    /**
     * Finds user by [[wechat openid]]
     *
     * @return User|null
     */
    protected function getUserByOpenid()
    {
        if ($this->_user === null) {
            $this->_user = User::findByOpenid($this->openid);
        }
        return $this->_user;
    }
}
