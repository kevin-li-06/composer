<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "signin".
 *
 * @property string $id
 * @property integer $user_id
 * @property string $date
 * @property integer $continuous
 */
class Signin extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'signin';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'date'], 'required'],
            [['user_id', 'continuous'], 'integer'],
            [['date'], 'string', 'max' => 25],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'date' => 'Date',
            'continuous' => 'Continuous',
        ];
    }
}
