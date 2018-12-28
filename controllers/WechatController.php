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
    public function actionTest()
    {
        if(\Yii::$app->wechat->isWechat &&!\Yii::$app->wechat->isAuthorized){
            return \Yii::$app->wechat->authorizeRequired()->send();
        }

        $miniProgram = \Yii::$app->wechat->miniProgram;
    }
}