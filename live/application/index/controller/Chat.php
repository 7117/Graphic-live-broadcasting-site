<?php

namespace app\index\controller;

use app\common\lib\Util;

class Chat
{
    public function index()
    {

        $data = [
            'user' => 'jim',
            'content' => '我是内容'
        ];

        $taskData = [
            'method'=>'chat',
            'data'=>$data
        ];

        $_POST['ws_server']->task($taskData);

        return Util::show(config('code.success'), 'success', []);
    }

}