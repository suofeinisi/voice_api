<?php

namespace app\models;

use yii\db\ActiveRecord;

class User extends ActiveRecord implements \yii\web\IdentityInterface
{

    public static $id = null;
    public static $_RD_SESSION = null;
    public static $_OPENID = null;

    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    public static function findByRdSession()
    {
        return static::findOne(['rdSession'=>self::$_RD_SESSION]);
    }

    /**
     * @param $openid
     * @return User
     */
    public static function findByOpenid($openid)
    {
        return static::findOne(['openid'=>$openid]);
    }

    public static function openidExists($openid)
    {
        return self::find()->where(['openid'=>$openid])->exists();
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access_token'=>$token]);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->authKey;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return $this->password === $password;
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
