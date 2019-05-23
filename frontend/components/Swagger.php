<?php

namespace frontend\components;

use  Yii;
use yii\helpers\ArrayHelper;
use frontend\components\Auth;
/**
 * @OA\Info(
 *     version="1.0",
 *     title="Access Token Controller",
 *     description = "this is API description"
 * )
 */
class Swagger{
    /**
     * @abstract swagger检索所有api function并生成jason文件
     * @author aaron.luo
     **/
    public static function CreateJson()
    {
        $frontend_path = Yii::getAlias('@app');
        $backend_path = Yii::getAlias('@backend');
//        echo  dirname(dirname(dirname(__FILE__)));die;
        $frontend_dir = $frontend_path.'/controllers/';
        $backend_dir= $backend_path.'/controllers/';
        $frontend_controllers = self::getControllerName('frontend',$frontend_dir);
        $backend_controllers = self::getControllerName('backend',$backend_dir);
        unset($frontend_controllers[0]);
        unset($frontend_controllers[1]);
        unset($backend_controllers[0]);
        unset($backend_controllers[1]);
        $controllers_path = ArrayHelper::merge($frontend_controllers,$backend_controllers);
        //过滤掉不是swagger注释的类
        foreach ($controllers_path as $k => $v){
            $class_name = $v[3]."\controllers\\".substr($v[2],0,strpos($v[2], '.'));
            $reflection = new \ReflectionClass($class_name);
            //通过反射获取类的注释
            $doc = $reflection->getDocComment ();
            if (preg_match("/@OA/",$doc)){
                $class[] = $v;
            }
        }
        //生成json文件
        if (isset($class)){
            foreach ($class as  $key => $value){
                $controller_name = explode('.',$value[0]);
                $openapi = \OpenApi\scan($value[1]);
//                $doc_handler = fopen(dirname(dirname(dirname(__FILE__))).'\swagger-ui\docs\chris\\'.$controller_name[0].'.json', 'w');
                // $doc_handler = fopen('E:/PHPTutorial/www/swagger-ui/swagger-ui/docs/swagger/'.$controller_name[0].'.json', 'w');
                // $data_string = $openapi->toJson();
                $data = ['project'=>'LotteryApi', 'controller'=>'TokenController.json', 'content'=>$openapi->toJson()];
                $path = 'http://chris.onthemooner.com/swagger-ui/web/index.php/site/create-or-update-doc';                           
                $curl = curl_init();
                curl_setopt($curl, CURLOPT_URL, $path);
                curl_setopt($curl, CURLOPT_HEADER, 1);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($curl, CURLOPT_POST, 1);               
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                //执行命令
                $data = curl_exec($curl);
                //关闭URL请求
                curl_close($curl);
                //显示获得的数据
                echo '<pre>';
                print_r($data);
                        
            }
        }
    }

    public static function getControllerName($type,$file_path)
    {
        if (is_dir($file_path)) {
            if ($dh = opendir($file_path)) {
                while (($file = readdir($dh)) !== false) {
                    $controllersName[] = [$type.'_'.$file,$file_path.$file,$file,$type,];
                }
                closedir($dh);
            }
            return $controllersName;
        }else{
            throw new \Exception('Folder does not exist!');
        }
    }
}