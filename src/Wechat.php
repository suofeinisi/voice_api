<?php
/**
 * Created by PhpStorm.
 * User: jiaxu
 * Date: 18-12-31
 * Time: 下午3:01
 */

namespace app\src;

use app\models\User;

class Wechat
{
    public static $REDIS_ACCESS_TOKEN = 'REDIS_ACCESS_TOKEN';
    public static $REDIS_RD_SESSION = 'REDIS_RD_SESSION';

    public static function getAccessToken()
    {
        if (!$token = \Yii::$app->redis->get(self::$REDIS_ACCESS_TOKEN)) {
            $accessToken = \Yii::$app->wechat->miniProgram->access_token;
            $tokenArr = $accessToken->getToken();
            if (!isset($tokenArr['access_token']) || !isset($tokenArr['expires_in'])) {
                return false;
            }
            $token = $tokenArr['access_token'];
            \Yii::$app->redis->setex(self::$REDIS_ACCESS_TOKEN, $tokenArr['expires_in'], $token);

        }
        return $token;
    }

    public static function setUserInfo($iv, $encryptData)
    {
        $rd_session = \Yii::$app->request->post('rd_session');
        if(!$rd_session || !$openid = \Yii::$app->redis->get($rd_session)){
            return -2;
        }
        if (!$session_data = \Yii::$app->redis->get($rd_session)) {
            return 100002;
        }
        $decryptedData = \Yii::$app->wechat->miniProgram->encryptor->decryptData($session_data['session_key'], $iv, $encryptData);

        if (!$decryptedData) {
            return 0;
        }
        if (User::find()->where(['openid' => $decryptedData['openid']])->exists()) {
            $userModel = User::find()->where(['openid' => $decryptedData['openid']])->one();
        } else {
            $userModel = new User();
        }
        $userModel->openid = $decryptedData['openid'];
        $userModel->rd_session = $rd_session;
        $userModel->nickname = $decryptedData['nickname'];
        $userModel->gender = $decryptedData['gender'] == '男' ? 2 : 1;
        $userModel->city = $decryptedData['city'];
        $userModel->province = $decryptedData['province'];
        $userModel->country = $decryptedData['country'];
        $userModel->avatarUrl = $decryptedData['avatarUrl'];
        $userModel->unionId = $decryptedData['unionId'];
        $userModel->save();
        return true;
    }
}