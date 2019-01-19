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

        $this->update_at = microtime(true)*1000;
        if($this->isNewRecord){
            $this->create_at = microtime(true)*1000;
        }
        return true;
    }

    public static function getStatus($storyId)
    {
        return self::find()->select(['status'])->where(['id'=>$storyId])->scalar();
    }

    public static function getDetailStoryById($storyId)
    {
        return self::find()->select(['nickName', 'avatarUrl', 'entity', 'during', self::tableName().'.create_at'])
            ->leftJoin(User::tableName() .' as u', 'u.id=story.user_id')
            ->where(['story.id'=>$storyId])
            ->andWhere(['in', self::tableName().'.status', [1,2]])
            ->asArray()->all();
    }
}