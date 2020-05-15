<?php


namespace app\common\lib\task;


use app\common\lib\ali\Sms;
use app\common\lib\redis\Predis;
use app\common\lib\Util;

class Task
{
    //task任务接口：发送信息
    public function sendMessage($data)
    {
        try {
            $response = Sms::sendSms($data['tel'], $data['code']);
        } catch (\Exception $e) {
            return Util::show(config('code.error'), 'error', []);
        }

        // 如果发送成功 把验证码记录到redis里面
        if ($response->Code === "OK") {
            Predis::getInstance()->set($data['tel'], $data['code'], config('redis.out_time'));
            return Util::show(config('code.success'), 'success', []);
        } else {
            return Util::show(config('code.error'), 'error', []);
        }
    }

    public function live($taskData, $ws)
    {
        $sMembers = Predis::getInstance()->sMembers(config('redis.live_game_key'));

        print_r($taskData);

        foreach ($sMembers as $fd) {
            //给js的消息监控的
            $ws->push($fd, json_encode($taskData));
        }
    }

    public function chat($taskData,$ws)
    {
        foreach ($ws->ports[1]->connections as $fd) {
            $ws->push($fd, json_encode($taskData));
        }

    }
}