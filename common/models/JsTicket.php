<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "wx_js_ticket".
 *
 * @property integer $id
 * @property string $ticket
 * @property string $expiration_time
 */
class JsTicket extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'wx_js_ticket';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ticket', 'expiration_time'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ticket' => 'Ticket',
            'expiration_time' => 'Expiration Time',
        ];
    }
}
