<?php

Co::set(['hook_flags' => SWOOLE_HOOK_TCP]);

go(function (){
    for ($c = 100; $c--;) {
        echo $c.' ';
    }
});