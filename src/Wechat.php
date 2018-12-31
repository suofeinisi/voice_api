<?php
/**
 * Created by PhpStorm.
 * User: jiaxu
 * Date: 18-12-31
 * Time: 下午3:01
 */

namespace app\src;

class Wechat
{
    public static $REDIS_ACCESS_TOKEN = 'REDIS_ACCESS_TOKEN';
    public static function getAccessToken()
    {
        if(!$token = \Yii::$app->redis->get(self::$REDIS_ACCESS_TOKEN)){
            $accessToken = \Yii::$app->wechat->miniProgram->access_token;
            $tokenArr = $accessToken->getToken();
            if(!isset($tokenArr['access_token']) || !isset($tokenArr['expires_in'])){
                return false;
            }
            $token = $tokenArr['access_token'];
            \Yii::$app->redis->setex(self::$REDIS_ACCESS_TOKEN, $tokenArr['expires_in'], $token);

        }
        return $token;

    }
}