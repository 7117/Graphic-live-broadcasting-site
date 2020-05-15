<?php


class http
{
    public $http;

    public function __construct()
    {
        $this->http = new Swoole\WebSocket\Server("0.0.0.0", 8811);


        $this->http->set([
            'enable_static_handler' => true,
            'document_root' => "/home/wwwroot/l2.l2.l2/live/public/static",
            'worker_num' => 5,
            'task_worker_num' => 4
        ]);

        $this->http->on('workerstart', [$this, 'onWorkerStart']);

        $this->http->on('request', [$this, 'onRequest']);

        $this->http->on('task', [$this, 'onTask']);

        $this->http->on('finish', [$this, 'onFinish']);

        $this->http->on('close', [$this, 'onClose']);

        $this->http->on("message", [$this, 'onMessage']);


        $this->http->start();
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

        $_POST['http_server'] = $this->http;

        // ob函数输出打印
        ob_start();
        try {
            think\Container::get('app', [APP_PATH])->run()->send();
            $res = ob_get_contents();
            ob_end_clean();
            $response->end($res);
        } catch (\Exception $e) {
            print_r($e);
        }
    }

    //app\common\lib\task\Task里面有要执行的任务   onTask是所有任务的集合
    public function onTask($serv, $taskId, $workId, $data)
    {
        $task = new app\common\lib\task\Task;

        $method = $data['method'];
        $flag = $task->$method($data['data']);

        return $flag;
    }

    public function onClose($serv, $taskId, $data)
    {
        return $data;

    }

    public function onFinish($serv, $taskId, $data)
    {
        return $data;

    }

    public function onMessage($serv, $taskId, $data)
    {
        return $data;
    }
}

$s = new http();
