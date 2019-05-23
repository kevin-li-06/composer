<?php
namespace frontend\models;

use yii;



/**
 * Signup form
 */
class CheckIn extends \yii\db\ActiveRecord
{ 
    public static function tableName()
    {
        return 'checkin';
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            
            // ['username', 'required'],
           

        ];
    }


    public function getUser()
    {       
        return $this->hasOne(\common\models\User::className(), ['id' => 'user_id']);
    }

}
