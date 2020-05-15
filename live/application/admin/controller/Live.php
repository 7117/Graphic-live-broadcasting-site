<?php

namespace app\admin\controller;


use app\common\lib\Util;
use \app\common\lib\redis\Predis;

class Live
{
    public function push()
    {

        $info = [
            'type' => intval($_GET['type']),
            'title' => '比赛了',
            'content' => '正在第三节',
            'image' => '/live/imgs/1.png',
        ];

        $taskData = [
            'method' => 'live',
            'data' => $info
        ];
        $_POST['ws_server']->task($taskData);


        return Util::show(config('code.success'), 'success', []);

    }
}