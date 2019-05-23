<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "user".
 *
 * @property string $id
 * @property string $dms_id
 * @property string $username
 * @property string $auth_key
 * @property string $mobile
 * @property string $mobile_hash
 * @property string $card
 * @property string $openid
 * @property integer $small_chance
 * @property integer $big_chance
 * @property integer $continuous
 * @property integer $created_at
 * @property integer $updated_at
 */
class User extends ActiveRecord implements IdentityInterface
{

    public $source;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['small_chance', 'big_chance', 'continuous', 'created_at', 'updated_at', 'result'], 'integer'],
            // [['created_at', 'updated_at'], 'required'],
            [['username'], 'string', 'max' => 10],
            [['region'], 'string', 'max' => 25],
            [['dms_id', 'auth_key', 'mobile', 'mobile_hash', 'card', 'openid'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'dms_id' => 'DMS ID',
            'username' => '姓名',
            'region' => '地区',
            'auth_key' => 'Auth Key',
            'mobile' => '手机号',
            'mobile_hash' => '加密手机号',
            'card' => '会员卡',
            'openid' => 'Openid',
            'small_chance' => '小奖机会',
            'big_chance' => '大奖机会',
            'continuous' => '连续签到',
            'redeem_prize' => '核销奖品',
            'redeem_at' => '核销时间',
            'change_prize' => '改变奖品',
            'change_at' => '改变时间',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
            'result' => '中奖结果',
            'source' => '会员类型',
            'lottery_chance' => '抽奖剩余次数'
        ];
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }

    /**
     * @chris
     */
    public static function findByMobilehash($mobile_hash)
    {
        return static::findOne(['mobile_hash' => $mobile_hash]);
    }

    /**
     * @chris
     */
    public static function findByOpenid($openid)
    {
        return static::findOne(['openid' => $openid]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates mobile
     *
     * @param string $mobile mobile to validate
     * @return bool if mobile provided is valid for current user
     */
    public function validateMobile($mobile)
    {
        if (md5($mobile) == $this->mobile_hash) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Generates mobile hash from mobile and sets it to the model
     *
     * @param string $mobile
     */
    public function setMobile($mobile)
    {
        // $this->mobile_hash = Yii::$app->security->generatePasswordHash($mobile);
        $this->mobile_hash = md5($mobile);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * 查询当前用户的最高奖品，如果没有返回null
     */
    public static function getHighestPrize($user_id)
    {
        $records = \common\models\Record::find()->select('result')->where(['and', 'user_id=:user_id', 'status=2', 'result>0'])->addParams([':user_id' => $user_id])->asArray()->all();
        if (!$records) {
            return null;
        }
        $ids = array_column($records, 'result');

        $ids = array_unique($ids);

        $prize = \common\models\Prize::find()->where(['id' => $ids])->orderBy('level asc')->one();
        return $prize;
    }

    public function getPrize()
    {
        return $this->hasOne(Prize::classname(), ['id' => 'result']);
    }

    /**
     * 查询当相同手机号中最高奖的用户
     */
    public static function getHighestPrizeUser($mobile)
    {        
        $users = \common\models\User::find()->select('id, result, redeem_at, change_at')->where(['mobile' => $mobile])->andWhere(['>', 'result', 0])->asArray()->all();
        
        if ($users) {

            if (count($users) == 1) {
                return $users[0];
            }

            foreach ($users as $k => $v) {
                if ($v['result'] == 0) {
                    unset($users[$k]);
                }
            }
    
            $max = 7;
            $user_id = 0;
    
            foreach ($users as $k => $v) {
                if ($v['result'] < $max) {
                    $user_id = $v['id'];
                    $max = $v['result'];
                }
            }
            $user = \common\models\User::find()->where(['id' => $user_id])->asArray()->one();
    
            return $user;
        }
        
        return null;
        
    }
}
