<?php

use \app\common\lib\redis\Predis;

class Ws
{
    public $ws;

    public function __construct()
    {
        $this->ws = new Swoole\WebSocket\Server("l2.l2.l2", 8811);
        $this->ws->listen("l2.l2.l2", 8812, SWOOLE_SOCK_TCP);

        $this->ws->set([
            'enable_static_handler' => true,
            'document_root' => "/home/wwwroot/l2.l2.l2/live/public/static",
            'worker_num' => 5,
            'task_worker_num' => 4
        ]);

        $this->ws->on('open', [$this, 'onOpen']);
        $this->ws->on('start', [$this, 'onStart']);
        $this->ws->on('message', [$this, 'onMessage']);
        $this->ws->on('workerstart', [$this, 'onWorkerStart']);
        $this->ws->on('request', [$this, 'onRequest']);
        $this->ws->on('task', [$this, 'onTask']);
        $this->ws->on('finish', [$this, 'onFinish']);
        $this->ws->on('close', [$this, 'onClose']);
        $this->ws->start();
    }

    public function onStart($server){
        swoole_set_process_name('liveMaster');
    }


    public function onMessage($server, $frame)
    {
        return '';
    }

    public function onOpen($ws, $request)
    {
        // print_r($ws);

        $obj = Predis::getInstance();

        $obj->sadd(config('redis.live_game_key'), $request->fd);


        return $request->fd;
    }


    public function onWorkerStart()
    {
        // 定义应用目录
        define('APP_PATH', __DIR__ . '/../../../application/');
        // 加载基础文件
        // require __DIR__ . '/../thinkphp/base.php';
        require __DIR__ . '/../../../thinkphp/start.php';
    }

    public function onRequest($request, $response)
    {
        //不记录进去日志
        if ($request->server['request_uri'] == '/favicon.ico') {
            $response->status(404);
            $response->end();
            return;
        }


        // 把swoole接收的信息转换为thinkphp可识别的
        $_SERVER = [];
        if (isset($request->server)) {
            foreach ($request->server as $key => $value) {
                $_SERVER[strtoupper($key)] = $value;
            }
        }

        if (isset($request->header)) {
            foreach ($request->header as $key => $value) {
                $_SERVER[strtoupper($key)] = $value;
            }
        }

        // swoole对于超全局数组：$_SERVER、$_GET、$_POST、define不会释放
        $_GET = [];
        if (isset($request->get)) {
            foreach ($request->get as $key => $value) {
                $_GET[$key] = $value;
            }
        }

        $_POST = [];
        if (isset($request->post)) {
            foreach ($request->post as $key => $value) {
                $_POST[$key] = $value;
            }
        }

        $this->writeLog();

        $_POST['ws_server'] = $this->ws;

        $_FILES = [];
        if (isset($request->files)) {
            foreach ($request->files as $key => $value) {
                $_FILES[$key] = $value;
            }
        }

        // ob函数输出打印
        ob_start();
        try {
            think\Container::get('app', [APP_PATH])->run()->send();

        } catch (\Exception $e) {
            // print_r($e);
        }

        $res = ob_get_contents();
        ob_end_clean();
        $response->end($res);
    }

    public function onTask($serv, $taskId, $workId, $data)
    {
        $task = new app\common\lib\task\Task;

        $method = $data['method'];
        $flag = $task->$method($data['data'], $serv);

        return $flag;
    }

    public function onClose($ws, $fd)
    {
        $obj = Predis::getInstance();
        $obj->srem(config('redis.live_game_key'), $fd);

        return $fd;
    }

    public function onFinish($serv, $taskId, $data)
    {
        return 'onFinish';
    }

    public function writeLog()
    {
        $datas = array_merge(['date' => date('Y-m-d H:i:s')], $_GET, $_POST, $_SERVER);

        $logs = '';

        foreach ($datas as $k => $v) {
            $logs .= $k . " : " . $v . ' ' . PHP_EOL;
        }

        $fp = fopen(__DIR__ . "/../../../log/access.log", "a+");
        go(function () use ($fp, $logs) {
            Swoole\Coroutine\System::fwrite($fp, $logs, 0);
        });

    }

}

$ws = new Ws();
