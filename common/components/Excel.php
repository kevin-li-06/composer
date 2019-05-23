<?php

namespace common\components;

use Yii;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Writer_Excel2007;
use PHPExcel_Style_Alignment;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\components\Log;
use backend\models\Admin;

class Excel
{

/**
 * @author bob qiu
 * @param $file_url 文件的上传地址 
 * @param $config[table] 导入到数据库的表名,$config[filed] 导入的字段
 * @param $num 默认首次导入数据库的条数
 * @return 导入成功的条数
 */

    public  static function get_import($file_url,$config,$num=10000)
    {

        $objReader = PHPExcel_IOFactory::createReader('Excel2007'); //确定excel版本
        $objPHPExcel=$objReader->load($file_url); 
        $sheet = $objPHPExcel->getSheet(0); //获取第一个工作表
        $highestRow = $sheet->getHighestRow(); //获取总的行数
        $highestColumn = $sheet->getHighestColumn(); //获取得总列数
        //循环获取总的列表数
        for($row = 2; $row<=$highestRow; $row++){
            $array[] = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
        }

        foreach ($array as $val) {
            $mobile = $val[0][2];
            $val[0][4] = md5($mobile);
            $datas[] = $val[0];
        }
        // var_dump($config['field']);die;
        // 如果数据太大，需要把数据分批
		$count = count($datas);
		$size = $num;
		if ($count > $size) {
			$page = floor($count / $size);
            $left = $count - ($page * $size);
            for ($i = 0; $i < $page; $i++) {
                //依次增加1W的数据
                $data = array_slice($datas, $i*$size, $size);
                $result = Yii::$app->db->createCommand()->batchInsert($config['table'],$config['field'], $data)->execute();
                //最后一次循环添加剩余的数据
                if (($i == ($page-1)) && ($left != 0)) {
                    $data = array_slice($datas, $page*$size);
                    $result = Yii::$app->db->createCommand()->batchInsert($config['table'],$config['field'], $data)->execute();
                }
            }
        // 如果小于10000直接写入
		} else {
            $result = Yii::$app->db->createCommand()->batchInsert($config['table'],$config['field'], $datas)->execute();
        }
        return $result;
    }

/**
 * @author bob qiu
 * @param $data 需要导出的数据array
 * @param $filed 设置导出数据的字段名array
 * @param $title 设置工作表名 
 * @param $filename 导出文件的名字  
 * 
 */
    public  static function get_export($data,$filed,$filename,$title='xgate')
    {
        $excel = new PHPExcel();
        // 设置列名
        foreach ($filed as $k =>$v) {
            $excel->getActiveSheet()->setCellValue($k.'1',$v);
        } 
        // 循环写入数据
        $i = 2;
        foreach($data as $key => $val){   
            foreach ($filed as $k =>$v) {
                $excel->getActiveSheet()->setCellValue($k.$i,$val[$v]);
            } 
         $i++;
        }
        $excel->getActiveSheet()->setTitle($title);
        $fileName = $filename;
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
        header("Content-Type:application/force-download");
        header("Content-Type:application/vnd.ms-execl");
        header("Content-Type:application/octet-stream");
        header("Content-Type:application/download");;
        header('Content-Disposition: attachment;filename="'.$fileName.'.csv"');
        header("Content-Transfer-Encoding:binary");
        $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        $objWriter->save('php://output');
    }

    /**
 * @author bob qiu
 * @param $file_url 文件的上传地址 
 * @param $config[table] 导入到数据库的表名,$config[filed] 导入的字段
 * @param $num 默认首次导入数据库的条数
 * @return 导入成功的条数
 */

    public  static function get_storeimport($file_url,$config,$num=10000)
    {
        $objReader = PHPExcel_IOFactory::createReader('Excel2007'); //确定excel版本
        $objPHPExcel=$objReader->load($file_url); 
        $sheet = $objPHPExcel->getSheet(0); //获取第一个工作表
        $highestRow = $sheet->getHighestRow(); //获取总的行数
        $highestColumn = 'B'; //获取得总列数
        //循环获取总的列表数
        for($row = 2; $row<=$highestRow; $row++){
            $array[] = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
        }
        foreach ($array as $k => $v) //循环excel表
        {
//             echo "<pre/>";
//             var_dump($v);die;
//             $k = $k - 1;//addAll方法要求数组必须有0索引
            $data[$k]['storename'] = $v[0][0];//创建二维数组
            $data[$k]['address'] = $v[0][1];
            $str="QWERTYUIOPASDFGHJKLZXCVBNM1234567890qwertyuiopasdfghjklzxcvbnm";
            str_shuffle($str);
            $store_code = substr(str_shuffle($str),26,6);
            $data[$k]['store_code'] = $store_code;
        }
//         foreach ($array as $val) {
//             // 省份id转换
//             $val[0][5] = \common\models\Province::find()->where(['like','ProvinceName',$val[0][5]])->one()->ProvinceID;
//             // 城市id转换
//             $val[0][4] = \common\models\City::find()->where(['like','CityName',$val[0][4]])->one()->CityID;
//             $datas[] = $val[0];
//             $pw = Excel::make_password();
//             $user[] = [
//                 'id_role' => 3,
//                 'username' => $val[0][0],
//                 'auth_Key' => $sign->generateRandomString(16), 
//                 'email' => $pw,
//                 'password' => $sign->generatePasswordHash($pw, 6),
//             ];
//         }
        // 插入注册店名
//         $res = Yii::$app->db->createCommand()->batchInsert('admin',['id_role','username','auth_key','email','password_hash'], $user)->execute();
//         if (!$res) {
//             return $res;
//         }
//         $count = count($datas);
//         $size = $num;
//         if ($count > $size) {
//             $page = floor($count / $size);
//             $left = $count - ($page * $size);
//             for ($i = 0; $i < $page; $i++) {
//                 //依次增加1W的数据
//                 $data = array_slice($datas, $i*$size, $size);
//                 $result = Yii::$app->db->createCommand()->batchInsert($config['table'],$config['field'], $data)->execute();
//                 //最后一次循环添加剩余的数据
//                 if (($i == ($page-1)) && ($left != 0)) {
//                     $data = array_slice($datas, $page*$size);
//                     $result = Yii::$app->db->createCommand()->batchInsert($config['table'],$config['field'], $data)->execute();
//                 }
//             }
//         // 如果小于10000直接写入
//         } else {
        $result = Yii::$app->db->createCommand()->batchInsert($config['table'],$config['field'], $data)->execute();
//         }

        return $result;
    }


/**
 * 门店库存店导入
 * @author bob qiu
 * @param $file_url 文件的上传地址 
 * @param $config[table] 导入到数据库的表名,$config[filed] 导入的字段
 * @param $num 默认首次导入数据库的条数
 * @return 导入成功的条数
 */

    public  static function importstorestock($file_url,$config,$num=10000)
    {

        $objReader = PHPExcel_IOFactory::createReader('Excel2007'); //确定excel版本
        $objPHPExcel=$objReader->load($file_url); 
        $sheet = $objPHPExcel->getSheet(0); //获取第一个工作表
        $highestRow = $sheet->getHighestRow(); //获取总的行数
        $highestColumn = $sheet->getHighestColumn(); //获取得总列数
        //循环获取总的列表数
        for($row = 2; $row<=$highestRow; $row++){
            $array[] = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
        }
        $time = date('y-m-d');
        foreach ($array as $val) {
            $store_id = \common\models\Store::get_sotre_id($val[0][0]);
            // var_dump($store_id);die;
            for ($i = 2;$i<=7;$i++ ) {
                $datas[] = [
                    'store_id' => $store_id,
                    'prize_id' => $i-1,
                    'stock' => is_null($val[0][$i]) ? 0 :$val[0][$i],
                    'update_at' => $time
                ];
            }
        }
        // echo "<pre>";
        // var_dump($data);die;
        // // 如果数据太大，需要把数据分批
        $count = count($datas);
        $size = $num;
        // 插入之前清空数据表
        Yii::$app->db->createCommand()->delete($config['table'])->execute();
        if ($count > $size) {
            $page = floor($count / $size);
            $left = $count - ($page * $size);
            for ($i = 0; $i < $page; $i++) {
                //依次增加1W的数据
                $data = array_slice($datas, $i*$size, $size);            
                $result = Yii::$app->db->createCommand()->batchInsert($config['table'],$config['field'], $data)->execute();
                //最后一次循环添加剩余的数据
                if (($i == ($page-1)) && ($left != 0)) {
                    echo 234;die;
                    $data = array_slice($datas, $page*$size);
                    $result = Yii::$app->db->createCommand()->batchInsert($config['table'],$config['field'], $data)->execute();
                }
            }
        // 如果小于10000直接写入
        } else {
            // echo 324;die;
            $result = Yii::$app->db->createCommand()->batchInsert($config['table'],$config['field'], $datas)->execute();
        }
        return $result;
    }
    /**
     * @author bob.qiu
     * 密码生成
     */
    public static function make_password() 
    {
        // $str="QWERTYUIOPASDFGHJKLZXCVBNM1234567890qwertyuiopasdfghjklzxcvbnm";
        $str="1234567890";
        $password=substr(str_shuffle($str),0,6);
        return $password;
    }
}