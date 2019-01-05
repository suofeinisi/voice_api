<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/3
 * Time: 15:30
 */

namespace app\controllers;


use app\models\Story;
use app\models\StoryReply;
use app\models\User;
use app\module\BaseModule;
use app\src\UploadForm;

class StoryController extends BaseController
{
    public $enableCsrfValidation=false;
    //ext = aac
    public function actionPublish()
    {
        $post = \Yii::$app->request->post();
        $during = $post['during'];
        if($name = UploadForm::upload()){
            $storyModel = new Story();
            $storyModel->user_id = User::find()->select(['id'])->where(['openid'=>User::$_OPENID])->scalar();
            $storyModel->entity = $name;
            $storyModel->during = $during;
            $storyModel->save();
            BaseModule::success();
        }else{
            BaseModule::error();
        }
    }

    public function actionReply()
    {
        $post = \Yii::$app->request->post();
        $during = $post['during'];
        $story_id = $post['story_id'];
        if($name = UploadForm::upload()){
            $replayModel = new StoryReply();
            $replayModel->user_id = User::find()->select(['id'])->where(['openid'=>User::$_OPENID])->scalar();
            $replayModel->story_id = $story_id;
            $replayModel->entity = $name;
            $replayModel->during = $during;
            $replayModel->save();
            BaseModule::success();
        }else{
            BaseModule::success();
        }
    }

    public function actionPublishList()
    {
        $publish = Story::find()->select(['id','user_id','']);
    }
}