<?php


namespace app\common\lib\task;


use app\common\lib\ali\Sms;
use app\common\lib\redis\Predis;
use app\common\lib\Util;

class Task
{
    //task任务接口：发送信息
    public function sendSms($data, $serv)
    {
        try {
            $response = Sms::sendSms($data['phone'], $data['code']);
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
}