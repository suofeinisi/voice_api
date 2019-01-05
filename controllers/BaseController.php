<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/28
 * Time: 16:47
 */

namespace app\controllers;


use app\models\User;
use yii\web\Controller;
use app\module\BaseModule;

class BaseController extends Controller
{
    public function beforeAction($action)
    {
        User::$_RD_SESSION = \Yii::$app->request->post('rd_session');
        if(!User::$_RD_SESSION || !User::$_OPENID = @json_decode(\Yii::$app->redis->get(User::$_RD_SESSION),true)['openid']){
            BaseModule::error(-2);
        }
        file_put_contents('/tmp/test.log', json_encode(User::$_OPENID). "\n", FILE_APPEND);
        if(!User::find()->where(['openid'=>User::$_OPENID])->exists()){
            BaseModule::error(100003);
        }
        return parent::beforeAction($action); // TODO: Change the autogenerated stub
    }

    /**
     * 限制频繁访问
     * @param $key
     * @param $timeLimit
     * @param $tryTimes
     * @return int
     */
    public static function tryLimit($key, $timeLimit, $tryTimes)
    {
        $times = \Yii::$app->redis->get($key) ?: 0;
        if ($times >= $tryTimes) {
            return -1;
        } else {
            \Yii::$app->redis->setex($key, $timeLimit, $times + 1);
        }
    }
}