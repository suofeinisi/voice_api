<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/3
 * Time: 15:07
 */

namespace app\module;

class BaseModule extends \yii\base\Module
{
    /**
     * @desc
     * @author lijiaxu
     * @date 2018/8/15
     * @param null $data
     * @param int $code
     * @return array
     */
    public static function success($code = 1, $data = [])
    {
        self::repsJson($data, '', $code);
//        return ['code'=>$code, 'data'=>$data];
    }

    /**
     * @desc
     * @author lijiaxu
     * @date 2018/8/15
     * @param $code
     * @param null $msg
     * @return array
     */
    public static function error($code=0, $msg = '')
    {
        $codeMessage = \Yii::$app->params['messageCode'];
        $msg = $msg ?: (isset($codeMessage[$code]) ?$codeMessage[$code]:'');
        self::repsJson([], $msg, $code);
//        return ['code'=>$code, 'msg'=>$msg];
    }

    private static function repsJson($data, $msg, $code)
    {
        $resp = \Yii::$app->getResponse();
        $resp->format = \yii\web\Response::FORMAT_JSON;
        if($code == 1){
            $resp->content = json_encode(['code'=>$code, 'data'=>$data], JSON_UNESCAPED_UNICODE);
        }else{
            $resp->content = json_encode(['code'=>$code, 'msg' => (string)$msg], JSON_UNESCAPED_UNICODE);
        }
        $resp->send();
        exit();
    }
}