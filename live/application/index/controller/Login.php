<?php

namespace app\index\controller;

use app\common\lib\ali\Sms;
use app\common\lib\Util;
use app\common\lib\redis\Predis;

class Login
{
    public function index()
    {
        //    获取手机  验证码
        //    验证 正确登录  不正确不登录
        $tel = $_GET['tel'];
        $pageCode = $_GET['code'];

        if (empty($tel) || empty($pageCode)) {
            return Util::show(config('code.error'), 'is null');
        }

        $saveCode = Predis::getInstance()->get($tel);

        if ($saveCode == $pageCode) {
            $data = [
                'tel' => $tel,
                'code' => $pageCode,
                'time' => time(),
                'status' => 1
            ];

            Predis::getInstance()->set('save'.$tel, $data);

            return Util::show(config('code.success'), 'success', $data);
        } else {
            return Util::show(config('code.error'), 'error', $data = []);
        }
    }

}