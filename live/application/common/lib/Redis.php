<?php

namespace app\common\lib;
class Redis
{
    public static function smsKey($tel)
    {
        return $tel;
    }
}