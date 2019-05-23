<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/16
 * Time: 15:09
 */
namespace  common\components;

use yii\helpers\ArrayHelper;


class ArrayDataProvider extends \yii\data\ArrayDataProvider
{
    public $key;
    public $allModels;
    public $modelClass;

    protected function prepareModels()
    {
        if (($models = $this->allModels) === null){
            return [];
        }
        if (($sort = $this->getSort()) !== false) {
            $models = $this->sortModels($models, $sort);
        }
        if (($pagination = $this->getPagination()) !== false){
            $pagination->totalCount = $this->getTotalCount();
        }

        return $models;
    }

    protected function prepareKeys($models){
        if ($this->key !== null){
            $keys = [];
            foreach ($models as $model){
                if (is_string($this->key)){
                    $keys[] = $model[$this->key];
                } else {
                    $keys[] = call_user_func($this->key, $model);
                }
            }
            return $keys;
        }
        return  array_keys($models);
    }
}