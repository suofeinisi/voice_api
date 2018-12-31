<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/28
 * Time: 16:47
 */

namespace app\controllers;


use yii\web\Controller;
use yii\web\Response;

class BaseController extends Controller
{
//    public function beforeAction($action)
//    {
//        if(!\Yii::$app->wechat->getIsWechat()){
//            return false;
//        }
//        return parent::beforeAction($action); // TODO: Change the autogenerated stub
//    }

    public function afterAction($action, $result)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return parent::afterAction($action, $result); // TODO: Change the autogenerated stub
    }

    /**
     * @desc
     * @author lijiaxu
     * @date 2018/8/15
     * @param null $data
     * @param int $code
     * @return array
     */
    protected function success($code = 1, $data = [])
    {
        return ['code'=>$code, 'data'=>$data];
    }

    /**
     * @desc
     * @author lijiaxu
     * @date 2018/8/15
     * @param $code
     * @param null $msg
     * @return array
     */
    protected function error($code=0, $msg = '')
    {
        $codeMessage = \Yii::$app->params['messageCode'];
        $msg = $msg ?: (isset($codeMessage[$code]) ?$codeMessage[$code]:'');
        return ['code'=>$code, 'msg'=>$msg];
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