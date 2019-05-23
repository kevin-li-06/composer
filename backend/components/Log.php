<?php

namespace backend\components;

use Yii;
use common\components\BaseLog;

class Log extends BaseLog
{
    // 日记文件
    public static $debug_file = "/backend/runtime/logs/debug.log";

    /**
     * 一个简单的日记
     */
    public static function debug($message = '', $data = '')
	{
		// 判断日记文件状态
		$contents = '';
		$run_time = gettimeofday(true);
		if ($message && is_scalar($message)) {
			$contents = '[' . date("Y-m-d H:i:s") . '] ---> [ ' . $message . ' ]';
		} else {
			$contents = '[' . date("Y-m-d H:i:s") . ']';
		}
		// 判断数据类型
		if (!empty($data)) {
			if (is_string($data)) {
				$data = $data;
			} elseif (is_object($data)) {
				$data = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
			} elseif (is_array($data)) {
				$data = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
			} elseif (is_bool($data)) {
				if ($data === true) {
					$data = 'true';
				} else {
					$data = 'false';
				}
			} elseif (is_resource($data)) {
				$data = 'resource';
			}
			$contents .= ' ---> [ ' . $data . ' ]';
		} else {
			$contents .= ' ---> [ NULL ]';
		}

		// 找到函数被调用位置
		$backtrace = debug_backtrace();
		$contents .= " ---> [ " . $backtrace[0]['file'] . ' (' . $backtrace[0]['line'] . ') ]' . PHP_EOL . PHP_EOL;

		// 写入日记文件
		file_put_contents(dirname(Yii::$app->basePath) . static::$debug_file, $contents, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * 删除日记文件
     */
    public static function clear()
    {
        if (is_file(dirname(Yii::$app->basePath) . static::$debug_file) === true) {
            @unlink(dirname(Yii::$app->basePath) . static::$debug_file);
        }
    }

    /**
     * 返回日记内容
     */
    public static function contents()
    {
        if (is_file(dirname(Yii::$app->basePath) . static::$debug_file)) {
            $contents = file_get_contents(dirname(Yii::$app->basePath) . static::$debug_file);
            return $contents;
        }
        return;
    }
}