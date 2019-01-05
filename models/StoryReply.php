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

        $this->update_at = time();
        if($this->isNewRecord){
            $this->create_at = time();
        }
        return true;
    }
}