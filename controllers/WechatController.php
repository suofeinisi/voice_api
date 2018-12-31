<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/28
 * Time: 16:16
 */

namespace app\controllers;


class WechatController extends BaseController
{
    public function actionSignin()
    {
        echo 333;die;
        $code = \Yii::$app->request->get('code');
        return 111;die;
    }

    public function actionTest()
    {
        $code = \Yii::$app->request->get('code');
        $miniProgram = \Yii::$app->wechat->miniProgram;
        $response = $miniProgram->auth->session();
        $userInfo = $miniProgram->access_token;
        if(\Yii::$app->wechat->isWechat &&!\Yii::$app->wechat->isAuthorized){
            return \Yii::$app->wechat->authorizeRequired()->send();
        }

        $miniProgram = \Yii::$app->wechat->miniProgram;
    }
}