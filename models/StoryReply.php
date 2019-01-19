<?php
/**
 * Created by PhpStorm.
 * User: jiaxu
 * Date: 19-1-5
 * Time: 下午5:03
 */

namespace app\models;


class StoryReply extends BaseModel
{
    public static function tableName()
    {
        return 'story_reply';
    }

    /**
     * 保存前
     * @param bool $insert
     * @return bool
     * @throws \yii\base\Exception
     */
    public function beforeSave($insert)
    {
        if(!parent::beforeSave($insert)){
            return false;
        }

        $this->update_at = microtime(true)*1000;
        if($this->isNewRecord){
            $this->create_at = microtime(true)*1000;
        }
        return true;
    }

    public static function getDetailStoryById($storyId, $offset, $limit)
    {
        return self::find()->select(['nickName', 'avatarUrl', 'entity', 'during', self::tableName().'.create_at'])
            ->leftJoin(User::tableName() .' as u', 'u.id=story_reply.user_id')
            ->where(['story_reply.story_id'=>$storyId])->orderBy([self::tableName().'.create_at'=>SORT_ASC])
            ->offset($offset)->limit($limit)
            ->asArray()->all();
    }
}