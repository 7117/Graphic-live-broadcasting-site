<?php


class Server
{
    public function __construct()
    {

        $serv = new Swoole\Server("0.0.0.0", 8811);

        $serv->set(array('task_worker_num' => 4));

        $serv->on('receive', function ($serv, $fd, $from_id, $data) {
            echo "add task" . PHP_EOL;
            $task_id = $serv->task($data);
            echo "sended task  $task_id" . PHP_EOL;
        });

        $serv->on('task', function ($serv, $task_id, $from_id, $data) {
            //模拟发送邮件
            echo "kai shi le task".PHP_EOL;
            sleep(15);
            $serv->finish("$data -> OK");
            echo "wan cheng le $task_id" . PHP_EOL;
        });

        $serv->on('finish', function ($serv, $task_id, $data) {
            echo "finish task $task_id" . PHP_EOL;
        });

        $serv->start();
    }
}


$net = new Server();