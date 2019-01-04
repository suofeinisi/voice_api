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
use BaseModule;

class BaseController extends Controller
{
    public function beforeAction($action)
    {
        if(!\Yii::$app->wechat->getIsWechat()){
            BaseModule::error(100001);
        }
        $rd_session = \Yii::$app->request->headers->get('rd_session');
        if(!$rd_session || !$openid = \Yii::$app->redis->get($rd_session)){
            BaseModule::error(-2);
        }
        if(!User::find()->where(['openid'=>$openid])->exists()){
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