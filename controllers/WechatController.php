<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/28
 * Time: 16:16
 */

namespace app\controllers;


use app\models\User;
use app\src\Wechat;

class WechatController extends BaseController
{
    public function actionSignin()
    {
        try{
            $code = \Yii::$app->request->get('code');
            if(!$code){
                throw new \Exception('', -1);
            }
            $miniProgram = \Yii::$app->wechat->miniProgram;
            $wxSession = $miniProgram->auth->session($code);
            if(isset($wxSession['errcode'])){
                throw new \Exception($wxSession['errmsg'], $wxSession['errcode']);
            }

            $wxSession = '{"session_key":"JLzDaybIEysf3Jo82uh0Ww==","openid":"owEzy5K5pTSvSEmMjOcKJ04m6vIo"}';
            $wxSession = json_decode($wxSession, true);

            $userInfo = \Yii::$app->wechat->getApp();

            if(!User::openidExists($wxSession['openid'])){
                $userModel = new User();
                $userModel->username = 'wechat_user_' . (User::find()->max('id')+1);
                $userModel->openid = $wxSession['openid'];
                $userModel->save();
            }
            return $this->success(1);

        }catch (\Exception $ex){
            return $this->error($ex->getCode(), $ex->getMessage());
        }
    }

    public function actionTest()
    {
        var_dump(Wechat::getAccessToken());die;
    }

    public function actionCallback()
    {
        file_put_contents('/tmp/test.log', json_encode(\Yii::$app->request->get()) . "\n", FILE_APPEND);
        file_put_contents('/tmp/test.log', json_encode(\Yii::$app->request->post()) . "\n", FILE_APPEND);
    }
}
