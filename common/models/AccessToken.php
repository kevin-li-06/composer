<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "wx_accesstoken".
 *
 * @property string $id
 * @property string $access_token
 * @property integer $expires_in
 */
class AccessToken extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'wx_accesstoken';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['access_token', 'expires_in'], 'required'],
            [['expires_in'], 'integer'],
            [['access_token'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'access_token' => 'Access Token',
            'expires_in' => 'Expires In',
        ];
    }
}
