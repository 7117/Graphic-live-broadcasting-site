<?php

// 面向过程写法
$http = new swoole_http_server('0.0.0.0', 8811);

$http->set([
    // 开启静态资源请求
    'enable_static_handler' => true,
    'document_root' => "/home/wwwroot/l2.l2.l2/live/public/static",
    'worker_num' => 5,
]);

/**
 * WorkerStart事件在Worker进程/Task进程启动时发生。这里创建的对象可以在进程生命周期内使用
 * 目的：加载thinkphp框架中的内容
 */
$http->on('WorkerStart', function (swoole_server $server, $worker_id) {
// 定义应用目录
    define('APP_PATH', __DIR__ . '/../application/');
// 加载基础文件
    require __DIR__ . '/../thinkphp/base.php';
});

$http->on('request', function ($request, $response) {

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

});

$http->start();