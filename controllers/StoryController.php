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
            $storys = Story::find()->select(['id','create_at','type'])->where(['user_id'=>$uInfo['id'], 'status'=>1])
                ->orderBy(['create_at'=>SORT_DESC])->offset($offset)->limit($limit)->asArray()->all();
            BaseModule::success(200,array_map(function ($row){
                $pater = \Yii::$app->redis->ZREVRANGE(self::$REDIS_PUBLISH_PATER.':'.$row['id'], 0, 9);
                $row['create_at'] = date('Y/m/d H:i:s', $row['create_at']);
//                $row['parter'] = \Yii::$app->redis->ZREVRANGE(self::$REDIS_PUBLISH_PATER.':'.$row['id'], 0, -1);
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
            $storys = $storyids ? Story::find()->select(['id','create_at','type'])->where(['and',['in','id',$storyids], ['status'=>1]])
                ->orderBy(['create_at'=>SORT_DESC])->asArray()->all() : [];
            BaseModule::success(200,array_map(function ($row){
                $pater = \Yii::$app->redis->ZREVRANGE(self::$REDIS_PUBLISH_PATER.':'.$row['id'], 0, 9);
                $row['create_at'] = date('Y/m/d H:i:s', $row['create_at']);
                $row['parter'] = $pater ? User::find()->select(['avatarUrl'])->where(['in', 'id', $pater])->column() : [];
                return $row;
            }, $storys));
        }catch (\Exception $ex){
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