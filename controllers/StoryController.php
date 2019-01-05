<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/3
 * Time: 15:30
 */

namespace app\controllers;


use app\module\BaseModule;
use app\src\UploadForm;

class StoryController extends BaseController
{
    //ext = aac
    public function actionPublish()
    {
        $post = \Yii::$app->request->post();
        $during = $post['during'];
        if(UploadForm::upload($during)){
            BaseModule::success();
        }else{
            BaseModule::error();
        }
    }
}