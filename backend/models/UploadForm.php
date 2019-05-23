<?php

namespace backend\models;

use yii\base\Model;
use yii\web\UploadedFile;

/**
 * UploadForm 文件上传model
 */
class UploadForm extends Model
{
    /**
     * @var UploadedFile file attribute
     */
    public $file;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['file'], 'file'],
        ];
    }
}