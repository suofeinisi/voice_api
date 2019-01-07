<?php
/**
 * 记录错误码对应的信息
 * Created by PhpStorm.
 * User: jiaxu
 * Date: 18-8-18
 * Time: 下午7:08
 */

return [
    200 => 'success',
    0 => 'error',
    -1 => 'param error',
    -2 => 'no login',
    -3 => 'param error',
    -4 => '频繁请求',

    100001 => 'not wechat',
    100002 => 'rd session expired',
    100003 => 'get wx user error'
];