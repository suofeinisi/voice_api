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

        $this->updated_at = time();
        if($this->isNewRecord){
            $this->created_at = time();
        }
        return true;
    }

    public static function getDetailStoryById($storyId, $offset, $limit)
    {
        return self::find()->select(['nickName','user_id', 'avatarUrl', 'entity', 'during', self::tableName().'.created_at'])
            ->leftJoin(User::tableName() .' as u', 'u.id=story_reply.user_id')
            ->where(['story_reply.story_id'=>$storyId])
            ->andWhere(['in', self::tableName().'.status', [1,2]])
            ->orderBy([self::tableName().'.created_at'=>SORT_ASC])
            ->offset($offset)->limit($limit)
            ->asArray()->all();
    }
}