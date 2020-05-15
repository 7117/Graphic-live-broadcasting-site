<?php

Co::set(['hook_flags' => SWOOLE_HOOK_TCP]);

go(function(){
    for ($c = 10; $c--;) {
        go(function () {//创建100个协程
            $redis = new Redis();
            $redis->connect('127.0.0.1', 6379);//此处产生协程调度，cpu切到下一个协程，不会阻塞进程
            $redis->set('key', 'a');//此处产生协程调度，cpu切到下一个协程，不会阻塞进程
            print_r($redis->get('key'));
        });
    }
});