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
        $miniProgram = \Yii::$app->wechat->miniProgram;
	$response = $miniProgram->auth->session(\Yii::$app->request->get('code'));
file_put_contents('/tmp/test.log', json_encode($response) . "\n", FILE_APPEND);

die;
        if(\Yii::$app->wechat->isWechat && !\Yii::$app->wechat->isAuthorized()){
            return \Yii::$app->wechat->authorizeRequired()->send();
        }

    }
       public function actionCallback()
    {
        file_put_contents('/tmp/test.log', json_encode(\Yii::$app->request->get()) . "\n", FILE_APPEND);
        file_put_contents('/tmp/test.log', json_encode(\Yii::$app->request->post()) . "\n", FILE_APPEND);
    }
}
