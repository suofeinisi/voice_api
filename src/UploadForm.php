<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/3
 * Time: 16:48
 */

namespace app\src;


use app\models\User;
use yii\base\Model;

class UploadForm extends Model
{
    public $file;

    public static function upload($filename = '')
    {
        $fileInfo = $_FILES['aac'];
        $name = $filename ? $filename : md5($fileInfo['name']) . pathinfo($fileInfo['name'])['extension'];
        $target_path = \Yii::$app->params['AAC_PATH_PRE'] . '/';
        if(!is_dir($target_path)){
            mkdir($target_path, 0777, true);
        }
        $target_entity = $target_path . $name;
        if(!file_exists($target_entity) && move_uploaded_file($fileInfo['tmp_name'], $target_entity)) {
            //上传成功,可进行进一步操作,将路径写入数据库等.
            return $name;
        }else{
            return false;
        }
    }

    public static function checkEntity($entity)
    {
        $target_path = \Yii::$app->params['AAC_PATH_PRE'] . '/';
        if(is_file($target_path . $entity)){
            return true;
        }else{
            return false;
        }
    }

    public static function downEntity($entity)
    {
        $target_path = \Yii::$app->params['AAC_PATH_PRE'] . '/';
        $fp = fopen($target_path . $entity, 'rb');

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');

        ob_clean();
        ob_end_flush();
        set_time_limit(0);

        $chunkSize = 1024 * 1024;
        while (!feof($fp)) {
            $buffer = fread($fp, $chunkSize);
            echo $buffer;
            ob_flush();
            flush();
        }
        fclose($fp);
        exit;
    }
}