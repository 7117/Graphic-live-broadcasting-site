<?php

namespace app\index\controller;

use app\common\lib\ali\Sms;
use app\common\lib\Util;
use app\common\lib\Redis;
use think\swoole\facade\Task;

class Send
{
    public function index()
    {
        $tel = $_GET['tel'];

        if (empty($tel)) {
            return Util::show(config('code.error'), 'error', []);
        }
        $code = rand(1000, 9999);


        $task=new \app\lib\TestTask();
        Task::async($task);
        try {
            $response = Sms::sendSms($tel, $code);
        } catch (\Exception $e) {
            print_r($e);
        }

        if ($response->Code == 'OK') {
            $redis = new \Swoole\Coroutine\Redis();
            $redis->connect(config('redis.host'), config('redis.port'));
            $redis->set(Redis::smsKey($tel), $code, config(('redis.out_time')));
            return Util::show(config('code.success'), 'success', []);
        } else {
            return Util::show(config('code.error'), 'error', []);

        }

    }

}