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
use app\module\BaseModule;
use yii\web\Controller;

class WechatController extends Controller
{
    public function actionTest()
    {
        var_dump(Wechat::getAccessToken());die;
    }

    /**
     * @desc 登陆拿session_key和openid
     * @author lijiaxu
     * @date 2019/1/2
     * @return array
     */
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
            $rd_session = \Yii::$app->getSecurity()->generateRandomString(168);
            \Yii::$app->redis->setex($rd_session, 3600*24*2, json_encode($wxSession));
            if($old_session = User::find()->select(['rd_session'])->where(['openid'=>$wxSession['openid']])->scalar()){
                \Yii::$app->redis->del($old_session);//删掉失效的rd_session
            }
            BaseModule::success(1,['rd_session'=>$rd_session]);
        }catch (\Exception $ex){
            BaseModule::error($ex->getCode(), $ex->getMessage());
        }
    }

    /**
     * @desc 把前端传过来的用户信息验证并存储
     * @author lijiaxu
     * @date 2019/1/3
     * @return array|int
     */
    public function actionSetUserInfo()
    {
        try{
            $post = \Yii::$app->request->post();
            if(!isset($post['iv']) || !isset($post['encryptData'])){
                return -1;
            }
            $result = Wechat::setUserInfo($post['iv'], $post['encryptData']);
            if($result === true){
                BaseModule::success();
            }else{
                throw new \Exception('', $result);
            }
        }catch (\Exception $exception){
            BaseModule::error($exception->getCode(), $exception->getMessage());
        }
    }

    public function actionCallback()
    {
        file_put_contents('/tmp/test.log', json_encode(\Yii::$app->request->get()) . "\n", FILE_APPEND);
        file_put_contents('/tmp/test.log', json_encode(\Yii::$app->request->post()) . "\n", FILE_APPEND);
    }
}
