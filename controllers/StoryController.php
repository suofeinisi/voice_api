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

    public static $REDIS_PUBLISH_PATER = 'REDIS_PUBLISH_PATER';//存储故事参与者id

    public static $REDIS_USER_REPLY = 'REDIS_USER_REPLY';//存储用户参与的故事id

    public function actionTest()
    {
        BaseModule::error(0, "['aa'=>123,'bb'=>234]");
        BaseModule::success([
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
                BaseModule::success(['storyId'=>$storyModel->attributes['id']]);
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
            file_put_contents('/tmp/voice.log', $story_id . "\n", FILE_APPEND);
            if(!$during || !$story_id){
                throw new \Exception('', -1);
            }
            if ($name = UploadForm::upload()) {
                $uid = User::find()->select(['id'])->where(['openid' => User::$_OPENID])->scalar();
                $replayModel = new StoryReply();
                $replayModel->user_id = $uid;
                $replayModel->story_id = $story_id;
                $replayModel->entity = $name;
                $replayModel->during = $during;
                $replayModel->save();
                \Yii::$app->redis->zadd(self::$REDIS_PUBLISH_PATER.':'.$story_id, time(), $uid);
                \Yii::$app->redis->zadd(self::$REDIS_USER_REPLY.':'.$uid, time(), $story_id);
                BaseModule::success();
            } else {
                BaseModule::error();
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
            $offset = \Yii::$app->request->post('offset', 0);
            $limit = \Yii::$app->request->post('limit', 5);
            $uInfo = User::findByRdSession();
            $storys = Story::find()->select(['id','create_at','type','status'])->where(['user_id'=>$uInfo['id']])
                ->andWhere(['in', 'status', [1, 2]])
                ->orderBy(['create_at'=>SORT_DESC])->offset($offset)->limit($limit)->asArray()->all();
            BaseModule::success(array_map(function ($row){
                $pater = \Yii::$app->redis->ZREVRANGE(self::$REDIS_PUBLISH_PATER.':'.$row['id'], 0, 9);
                $row['create_at'] = date('Y/m/d H:i:s', $row['create_at']);
                $row['parter'] = $pater ? User::find()->select(['avatarUrl'])->where(['in', 'id', $pater])->column() : [];
                return $row;
            }, $storys));
        }catch (\Exception $ex){
            BaseModule::error($ex->getCode(), $ex->getMessage());
        }
    }

    public function actionReplyList()
    {
        try{
            $offset = \Yii::$app->request->post('offset', 0);
            $limit = \Yii::$app->request->post('limit', 5);
            $uInfo = User::findByRdSession();
            $storyids = \Yii::$app->redis->ZREVRANGE(self::$REDIS_USER_REPLY.':'.$uInfo['id'],$offset,$limit);
            $storys = $storyids ? Story::find()->select(['id','create_at','type', 'status'])->where(['and',['in','id',$storyids], ['status'=>1]])
                ->orderBy(['create_at'=>SORT_DESC])->asArray()->all() : [];
            BaseModule::success(array_map(function ($row){
                $pater = \Yii::$app->redis->ZREVRANGE(self::$REDIS_PUBLISH_PATER.':'.$row['id'], 0, 9);
                $row['create_at'] = date('Y/m/d H:i:s', $row['create_at']);
                $row['parter'] = $pater ? User::find()->select(['avatarUrl'])->where(['in', 'id', $pater])->column() : [];
                return $row;
            }, $storys));
        }catch (\Exception $ex){
            BaseModule::error($ex->getCode(), $ex->getMessage());
        }
    }

    /**
     * 返回故事详细信息
     */
    public function actionDetail()
    {
        try{
            $story_id = \Yii::$app->request->post('storyId', 0);
            $offset = \Yii::$app->request->post('offset', 0);
            $limit = \Yii::$app->request->post('limit', 5);
            if(!$story_id){
                throw new \Exception('', -1);
            }
            if(!in_array(Story::getStatus($story_id), [1, 2])){
                throw new \Exception(100101);
            }
            if($offset==0){//第一页,第一条是发布者
                $mainStory = Story::getDetailStoryById($story_id);
                $replyStory = StoryReply::getDetailStoryById($story_id, $offset, $limit-1);
            }else{//只有回复
                $mainStory = [];
                $replyStory = StoryReply::getDetailStoryById($story_id, $offset-1, $limit);
            }
            BaseModule::success(array_merge($mainStory, $replyStory));
        }catch (\Exception $ex){
            BaseModule::error($ex->getCode(), $ex->getMessage());
        }
    }

    public function actionEntity()
    {
        try{
            $uid = \Yii::$app->request->get('user_id', '');
            $entity = \Yii::$app->request->get('entity', '');
            if(!$entity || !$uid || !UploadForm::checkEntity($entity, $uid)){
                throw new \Exception('', -1);
            }
            UploadForm::downEntity($entity, $uid);
        }catch (\Exception $ex){
            BaseModule::error($ex->getCode(), $ex->getMessage());
        }
    }
}