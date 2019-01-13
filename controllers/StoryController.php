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
    public $enableCsrfValidation = false;

    public function actionTest()
    {
        BaseModule::error(0, "['aa'=>123,'bb'=>234]");
        BaseModule::success(200, [
            'aa'=>123,
            'bb'=>234,
        ]);
    }

    //ext = aac
    public function actionPublish()
    {
        try {
            $post = \Yii::$app->request->post();
            $during = $post['during'];
            if ($name = UploadForm::upload()) {
                $storyModel = new Story();
                $storyModel->user_id = User::find()->select(['id'])->where(['openid' => User::$_OPENID])->scalar();
                $storyModel->entity = $name;
                $storyModel->during = $during;
                $storyModel->save();
                BaseModule::success(200, ['storyId'=>$storyModel->attributes['id']]);
            } else {
                BaseModule::error();
            }
        } catch (\Exception $ex) {
            BaseModule::error($ex->getCode(), $ex->getMessage());
        }
    }

    public function actionReply()
    {
        try{
            $during = \Yii::$app->request->post('during', 0);
            $story_id = \Yii::$app->request->post('storyId', 0);
            if(!$during || !$story_id){
                throw new \Exception('', -1);
            }
            if ($name = UploadForm::upload()) {
                $replayModel = new StoryReply();
                $replayModel->user_id = User::find()->select(['id'])->where(['openid' => User::$_OPENID])->scalar();
                $replayModel->story_id = $story_id;
                $replayModel->entity = $name;
                $replayModel->during = $during;
                $replayModel->save();
                BaseModule::success();
            } else {
                BaseModule::success();
            }
        }catch (\Exception $ex){
            BaseModule::error($ex->getCode(), $ex->getMessage());
        }
    }

    /**
     * 我发起的列表
     */
    public function actionPublishList()
    {
        try{
            $uInfo = User::findByRdSession();
            $storys = Story::find()->select(['id','create_at','type'])->where(['user_id'=>$uInfo['id']])->orderBy(['create_at'=>SORT_DESC])->asArray()->all();
            BaseModule::success(200,$storys);
        }catch (\Exception $ex){
            BaseModule::error($ex->getCode(), $ex->getMessage());
        }
    }

    /**
     * 封面参与者列表
     */
    public function actionJoinUser()
    {
        try{
            $storyId = \Yii::$app->request->post('storyId', 0);
            if(!$storyId){
                throw new \Exception('', -1);
            }
            $joinUid = StoryReply::find()->select(['user_id'])->where(['story_id'=>$storyId])->orderBy(['create_at'=>SORT_DESC])->indexBy('user_id')->limit(10)->asArray()->all();
            $userInfo = User::find()->select(['avatarUrl'])->where(['in', 'id', array_keys($joinUid)])->asArray()->column();
            BaseModule::success(200, $userInfo);
        }catch (\Exception $ex){
            BaseModule::error($ex->getCode(), $ex->getMessage());
        }
    }

    public function actionReplyList()
    {
        try{
            $uInfo = User::findByRdSession();
            $storys = StoryReply::find()->select(['story_id','']);
        }catch (\Exception $ex)
        {
            BaseModule::error($ex->getCode(), $ex->getMessage());
        }
    }

    public function actionPublishRow()
    {
        try{
            $post = \Yii::$app->request->post();
            $story_id = $post['storyId'] ?: 0;
            $model = Story::find()->select(['user_id','entity','during']);
            if($story_id){
                $model = $model->where(['id'=>$story_id, 'status'=>1]);
            }else{
                $model = $model->where(['type'=>3,'status'=>1]);
            }
            $data = $model->asArray()->one();
            BaseModule::success(200, $data);
        }catch(\Exception $ex){
            BaseModule::error($ex->getCode(), $ex->getMessage());
        }
    }
}