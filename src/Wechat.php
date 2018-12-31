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
            $token = $accessToken->getToken();
            \Yii::$app->redis->set(self::$REDIS_ACCESS_TOKEN, 3600*2);
        }
        return $token;

    }
}