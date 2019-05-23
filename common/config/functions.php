<?php
/**
 * 公共方法专用文件
 */
use common\models\Prize;
/**
 * @author bob qiu
 * @param $array 奖品id及奖品概率的一个数组
 * @param $pow 根据奖品概率需要计算出的倍数
 * @return 奖品id
 */
if (!function_exists('get_prize'))
{
    function get_prize($array, $pow)
    {
        // 所有奖品都抽完的情况
        if ($pow === 0) {
            return 0;
        }

        // 检查是否为array格式
        if (!is_array($array)) {
            exit('传入格式错误');
        }

        // echo '<pre>';print_r($array);exit;
        // 如果概率数组和不为100 将不中奖的概率与id push到$array
        if (array_sum($array) !== 100) {
            $value = 100 - array_sum($array);
            $array = \yii\helpers\ArrayHelper::merge((array)$value, $array);
        }
        
        // 取随机数
        $prosum = array_sum($array)*$pow;
        $randNum = random_int(1, $prosum);
        // 循环数组得出落点
        $a = 0;
        foreach($array as $k => $v) {
            $a += $v*$pow;
            if ($randNum <= $a) {
                $result = $k;
                break;
            }
        }
        return $result;


        // foreach ($array as $prizeid => $value) {
        //     // echo '<pre>';print_r($array);exit;
        //     $randNum = random_int(1, $prosum); 
        //     if ($randNum <= $value*$pow){
        //         $result = $prizeid; 
        //         break;
        //     } else {
        //         $prosum -= $value*$pow;
        //     }
        // }
        // unset ($array); 
        // return $result;
    }
}


/**
 * @author bob qiu
 * @param $arr 奖品id及奖品概率的一个数组
 * @return 返回概率中需要扩大的倍数 int
 */

if (!function_exists('get_pow'))
{
    function get_pow($arr)
    {
        if (count($arr)) {
            $row = [];
            
            //循环找出数组中小数点最大的位数
            foreach ($arr as $key => $value) {
               $row[] = get_float_length($value);
            }
            $result = pow(10,max($row));
        } else { // 所有奖品都抽完的情况
            $result = 0;
        }
        
        return $result;
    }
}
if (!function_exists('get_float_length'))
{
    function get_float_length($num)
    {
        $count = 0;
		$temp = explode ( '.', $num );
		if (sizeof ( $temp ) > 1) {
			$decimal = end ( $temp );
			$count = strlen ( $decimal );
		}
		return $count;
    }
}

/**
 * @author bob qiu
 * @param $array
 * @return 在执行抽奖逻辑之前,审核抽奖数据库的库存。
 */
if (!function_exists('get_audit_prize'))
{
    function get_audit_prize($array)
    {
        // print_r($array);die;
        //查询出所有已经抽奖数量的和库存相等的id
        $connection  = Yii::$app->db;
        $sql = "SELECT * FROM prize p WHERE p.stock_num = p.gain_num";
        $command = $connection->createCommand($sql);
        $res     = $command->queryAll();
        if (empty($res)) {
            return $array;
        }
        $ids = array_flip(array_column($res, 'id'));
        // 利用函数对比 将要溢出库存的奖品从奖池中剔除
        $result = array_diff_key($array, $ids);
        return $result;
    }
}

/**
 * @author bob qiu
 * @param $file_url 文件的上传地址 
 * @param $config[table] 导入到数据库的表名,$config[filed] 导入的字段
 * @param $num 默认首次导入数据库的条数
 * @return 导入成功的条数
 */
if (!function_exists('get_import'))
{
    function get_import($file_url,$config,$num=10000)
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
//            $mobilehash = $val[0][4];
//            $val[0][5] = Yii::$app->security->generatePasswordHash($mobilehash);
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
}
/**
 * @author bob qiu
 * @param $data 需要导出的数据array
 * @param $filed 设置导出数据的字段名array
 * @param $title 设置工作表名 
 * @param $filename 导出文件的名字  
 * 
 */
if (!function_exists('get_export'))
{
    function get_export($data,$filed,$filename,$title='xgate')
    {
        // echo "<pre>";var_dump($data);die;
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
        $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
        $objWriter->save('php://output');
    }
}


/**
* 数字转换为中文
* @param  string|integer|float  $num  目标数字
* @param  integer $mode 模式[true:金额（默认）,false:普通数字表示]
* @param  boolean $sim 使用小写（默认）
* @return string
*/
if (!function_exists('number2chinese')) 
{
    function number2chinese ($num, $mode = true, $sim = true) 
    {
        if(!is_numeric($num)) return '含有非数字非小数点字符！';
        $char    = $sim ? array('零','一','二','三','四','五','六','七','八','九')
        : array('零','壹','贰','叁','肆','伍','陆','柒','捌','玖');
        $unit    = $sim ? array('','十','百','千','','万','亿','兆')
        : array('','拾','佰','仟','','萬','億','兆');
        $retval  = $mode ? '元':'点';
        //小数部分
        if(strpos($num, '.')){
            list($num,$dec) = explode('.', $num);
            $dec = strval(round($dec,2));
            if($mode){
                $retval .= "{$char[$dec['0']]}角{$char[$dec['1']]}分";
            }else{
                for($i = 0,$c = strlen($dec);$i < $c;$i++) {
                    $retval .= $char[$dec[$i]];
                }
            }
        }
        //整数部分
        $str = $mode ? strrev(intval($num)) : strrev($num);
        for($i = 0,$c = strlen($str);$i < $c;$i++) {
            $out[$i] = $char[$str[$i]];
            if($mode){
                $out[$i] .= $str[$i] != '0'? $unit[$i%4] : '';
                    if($i>1 and $str[$i]+$str[$i-1] == 0){
                    $out[$i] = '';
                }
                    if($i%4 == 0){
                    $out[$i] .= $unit[4+floor($i/4)];
                }
            }
        }
        $retval = join('',array_reverse($out)) . $retval;
        return $retval;
    }
}


/**
* @author ja颂 
* 把数字1-1亿换成汉字表述，如：123->一百二十三
* @param [num] $num [数字]
* @return [string] [string]
*/
if (!function_exists('numToWord')) 
{
    function numToWord($num)
    {
        $chiNum = array('零', '一', '二', '三', '四', '五', '六', '七', '八', '九');
        $chiUni = array('','十', '百', '千', '万', '亿', '十', '百', '千');
        
        $chiStr = '';
        
        $num_str = (string)$num;
        
        $count = strlen($num_str);
        $last_flag = true; //上一个 是否为0
        $zero_flag = true; //是否第一个
        $temp_num = null; //临时数字
        
        $chiStr = '';//拼接结果
        if ($count == 2) {//两位数
            $temp_num = $num_str[0];
            $chiStr = $temp_num == 1 ? $chiUni[1] : $chiNum[$temp_num].$chiUni[1];
            $temp_num = $num_str[1];
            $chiStr .= $temp_num == 0 ? '' : $chiNum[$temp_num]; 
        } elseif ($count > 2) {
            $index = 0;
            for ($i = $count-1; $i >= 0 ; $i--) { 
                $temp_num = $num_str[$i];
                if ($temp_num == 0) {
                    if (!$zero_flag && !$last_flag ) {
                        $chiStr = $chiNum[$temp_num] . $chiStr;
                        $last_flag = true;
                    }
                }else{
                    $chiStr = $chiNum[$temp_num] . $chiUni[$index%9] . $chiStr;
            
                    $zero_flag = false;
                    $last_flag = false;
                }
                $index ++;
            }
        } else {
            $chiStr = $chiNum[$num_str[0]]; 
        }
        return $chiStr;
    }
}

