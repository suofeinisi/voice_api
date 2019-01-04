<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/3
 * Time: 16:19
 */

namespace app\controllers;


use app\models\User;

class UserController extends BaseController
{
    public function actionGetUserInfo()
    {
        $userInfo = User::findByRDSession();
    }
}