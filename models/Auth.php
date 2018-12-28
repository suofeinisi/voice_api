<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/27
 * Time: 16:17
 */

namespace app\models;


use yii\db\ActiveRecord;

class Auth extends ActiveRecord
{
    public static function tableName()
    {
        return 'auth';
    }
}