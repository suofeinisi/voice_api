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
        $code = \Yii::$app->request->get('code');
        $miniProgram = \Yii::$app->wechat->miniProgram;
        $response = $miniProgram->auth->session($code);
    }

    public function actionCallback()
    {
        file_put_contents('/tmp/test.log', json_encode(\Yii::$app->request->get()) . "\n", FILE_APPEND);
        file_put_contents('/tmp/test.log', json_encode(\Yii::$app->request->post()) . "\n", FILE_APPEND);
    }
}
