<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/3
 * Time: 16:48
 */

namespace app\src;


use app\models\Story;
use app\models\User;
use yii\base\Model;

class UploadForm extends Model
{
    public $file;

    public static function upload($during)
    {
        $fileInfo = $_FILES['aac'];
        $name = $fileInfo['name'];
        $target_path = \Yii::$app->params['AAC_PATH_PRE'].User::$_OPENID . '/';
        $target_entity = $target_path . $name;
        if(!file_exists($target_entity) && move_uploaded_file($fileInfo['tmp_name'], $target_entity)) {
            //上传成功,可进行进一步操作,将路径写入数据库等.
            $storyModel = new Story();
            $storyModel->user_id = User::find()->select(['id'])->where(['openid'=>User::$_OPENID])->scalar();
            $storyModel->entity = $name;
            $storyModel->during = $during;
            $storyModel->save();
            return true;
        }else{
            return false;
        }
    }
}