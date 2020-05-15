<?php


use function foo\func;

class Server
{
    const PORT = 8811;

    public function port()
    {
        $shell = 'netstat -anp |grep 8811 |grep LISTEN |wc -l';

        $line = shell_exec($shell);

        if($line != 1){
            echo 'error'.date("Y-m-d H:i:s");
            echo PHP_EOL.$line.PHP_EOL;
        }else{
            echo 'success'.date("Y-m-d H:i:s");
            echo PHP_EOL.$line.PHP_EOL;
        }
    }
}

\swoole_timer_tick(2000,function ($t_id){
    $client = new Server();
    $client->port();
});
