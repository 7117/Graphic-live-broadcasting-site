<?php

namespace app\index\controller;

use app\common\lib\ali\Sms;

class Index
{
    public function index()
    {
        return '';
    }

    public function hello($name = 'ThinkPHP5')
    {

        return '133333,' . time();
    }

    // public function sms()
    // {
    //     try {
    //         Sms::sendSms(15600271767, 12345);
    //     } catch (\Exception $e) {
    //         return json(['code'=>'-1','message'=>'failed']);
    //     }
    //
    //     return json(array('code'=>'1','message'=>'success'));
    // }
}