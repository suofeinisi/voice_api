<?php
/**
 * Created by PhpStorm.
 * User: jiaxu
 * Date: 19-1-5
 * Time: 下午5:03
 */

namespace app\models;


class Story extends BaseModel
{
    public static function tableName()
    {
        return 'story';
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

    public static function getStatus($storyId)
    {
        return self::find()->select(['status'])->where(['id'=>$storyId])->scalar();
    }

    public static function getDetailStoryById($storyId)
    {
        return self::find()->select(['nickName', 'user_id', 'avatarUrl', 'entity', 'during', self::tableName().'.created_at'])
            ->leftJoin(User::tableName() .' as u', 'u.id=story.user_id')
            ->where(['story.id'=>$storyId])
            ->andWhere(['in', self::tableName().'.status', [1,2]])
            ->asArray()->all();
    }
}