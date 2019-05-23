<?php

namespace common\models;

use Yii;

class Answer extends \yii\db\ActiveRecord
{
    
    public static function tableName()
    {
        return 'answer';
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'topic_one' => '第一题',
            'topic_two' => '第二题',
            'topic_three' => '第三题',
        ];
    }
}
