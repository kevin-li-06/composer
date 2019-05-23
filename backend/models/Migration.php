<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "migrations".
 *
 * @property string $id ID
 * @property string $filename 文件名称
 * @property int $status 0 - 未使用 1 - 已使用
 * @property string $env dev - 本地环境 prod - 线上环境
 * @property int $migrated_at 应用时间戳
 */
class Migration extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'migration';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['filename', 'status', 'env'], 'required'],
            [['status', 'migrated_at'], 'integer'],
            [['filename', 'env'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'filename' => '文件名称',
            'status' => '状态',
            'env' => '环境',
            'migrated_at' => '迁移时间',
        ];
    }
}
