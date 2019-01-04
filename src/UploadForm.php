<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/3
 * Time: 16:48
 */

namespace app\src;


use yii\base\Model;

class UploadForm extends Model
{
    public $file;

    public function upload()
    {
        if($this->validate()){
            $this->file->saveAs('uploads/' . $this->imageFile->baseName . '.' . $this->imageFile->extension);
            return true;
        }else{
            return false;
        }
    }
}