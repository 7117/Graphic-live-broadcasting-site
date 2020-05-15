<?php

namespace app\index\controller;

use app\common\lib\ali\Sms;
use app\common\lib\Util;
use app\common\lib\Redis;

class Send
{
    public function index()
    {
        $tel = $_GET['tel'];

        if (empty($tel)) {
            return Util::show(config('code.error'), 'error', []);
        }

        $code = rand(1000, 9999);

        $taskData = [
            'method' => 'sendMessage',
            'data'=>[
                'tel' => $tel,
                'code' => $code
            ]
        ];
        $_POST['http_server']->task($taskData);
        return Util::show(config('code.success'), 'success', []);

    }

}