<?php
namespace frontend\models;

use yii;



/**
 * Signup form
 */
class Token extends \yii\db\ActiveRecord
{ 
    public static function tableName()
    {
        return 'token';
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            
            // [['username', 'password', 'account_id'], 'required'],
           

        ];
    }
}
